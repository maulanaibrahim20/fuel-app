<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ReceiptScan;
use App\Services\ReceiptOcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;

class ReceiptController extends Controller
{
    private ReceiptOcrService $ocrService;

    public function __construct(ReceiptOcrService $ocrService)
    {
        $this->ocrService = $ocrService;
    }
    public function index()
    {
        return view('pages.user.receipt.index');
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'receipt' => 'required|image|mimes:jpeg,png,jpg|max:8192', // max 8MB
        ]);

        DB::beginTransaction();

        try {
            // Create receipt scan record
            $receiptScan = ReceiptScan::create([
                'user_id' => Auth::id(),
                'original_image_path' => '',
                'status' => 'processing',
                'ocr_raw_data' => [],
                'extracted_data' => []
            ]);

            // Store original image
            $originalPath = $request->file('receipt')->store(
                'receipts/' . Auth::id() . '/' . date('Y/m'),
                'public'
            );


            // Process and optimize image
            $processedPath = $this->processImage($originalPath);

            // Update paths
            $receiptScan->update([
                'original_image_path' => $originalPath,
                'processed_image_path' => $processedPath
            ]);

            // Extract text using OCR
            $imagePath = storage_path('app/public/' . $processedPath);
            $rawText = $this->ocrService->extractText($imagePath);

            if (empty(trim($rawText))) {
                throw new \Exception('Tidak dapat membaca teks dari gambar. Pastikan gambar struk jelas dan tidak buram.');
            }

            // Parse receipt data
            $parsedResult = $this->ocrService->parseReceipt($rawText);

            // Validate extracted data
            $validation = $this->ocrService->validateReceiptData($parsedResult['extracted_data']);
            $parsedResult['extracted_data']['validation'] = $validation;

            // Update receipt scan with results
            $receiptScan->update([
                'ocr_raw_data' => [
                    'raw_text' => $rawText,
                    'processing_timestamp' => now(),
                    'image_info' => [
                        'original_size' => filesize(storage_path('app/public/' . $originalPath)),
                        'processed_size' => filesize($imagePath),
                    ]
                ],
                'extracted_data' => $parsedResult['extracted_data'],
                'confidence_score' => $parsedResult['confidence_score'],
                'scan_type' => $parsedResult['scan_type'],
                'status' => 'completed'
            ]);

            DB::commit();

            Log::info('Receipt OCR completed successfully', [
                'receipt_scan_id' => $receiptScan->id,
                'confidence_score' => $parsedResult['confidence_score'],
                'scan_type' => $parsedResult['scan_type']
            ]);

            // Response based on request type
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $receiptScan->id,
                        'extracted_data' => $parsedResult['extracted_data'],
                        'confidence_score' => $parsedResult['confidence_score'],
                        'scan_type' => $parsedResult['scan_type'],
                        'warnings' => $validation['warnings'] ?? []
                    ],
                    'message' => 'Struk berhasil dipindai'
                ]);
            }

            dd($parsedResult);

            // Redirect to detail page for web interface
            return redirect()->route('user.receipt.show', $receiptScan->id)
                ->with('success', 'Struk berhasil dipindai. Periksa hasil ekstraksi data.');
        } catch (\Exception $e) {
            DB::rollback();

            if (isset($receiptScan)) {
                $receiptScan->markAsFailed($e->getMessage());
            }

            Log::error('Receipt OCR failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses struk: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Gagal memproses struk: ' . $e->getMessage());
        }
    }

    private function processImage(string $originalPath): string
    {
        try {
            // Check if Intervention Image is available
            if (!class_exists('\Intervention\Image\ImageManager')) {
                Log::info('Intervention Image not available, using original image');
                return $originalPath;
            }

            // Use Intervention Image if available
            $manager = new ImageManager('gd');
            $image = $manager->make(storage_path('app/public/' . $originalPath));

            // Optimize for OCR
            $image->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Enhance contrast and brightness for better OCR
            $image->contrast(15);
            $image->brightness(5);

            // Save processed image
            $processedPath = str_replace('.', '_processed.', $originalPath);
            $image->save(storage_path('app/public/' . $processedPath), 90);

            return $processedPath;
        } catch (\Exception $e) {
            Log::warning('Image processing failed, using original', ['error' => $e->getMessage()]);

            // Fallback: Basic image processing using GD
            return $this->basicImageProcessing($originalPath);
        }
    }

    private function basicImageProcessing(string $originalPath): string
    {
        try {
            $sourcePath = storage_path('app/public/' . $originalPath);

            // Check if file exists
            if (!file_exists($sourcePath)) {
                return $originalPath;
            }

            // Get image info
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) {
                return $originalPath;
            }

            // Create image resource based on type
            switch ($imageInfo[2]) {
                case IMAGETYPE_JPEG:
                    $sourceImage = imagecreatefromjpeg($sourcePath);
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($sourcePath);
                    break;
                case IMAGETYPE_GIF:
                    $sourceImage = imagecreatefromgif($sourcePath);
                    break;
                default:
                    return $originalPath;
            }

            if (!$sourceImage) {
                return $originalPath;
            }

            // Get original dimensions
            $originalWidth = imagesx($sourceImage);
            $originalHeight = imagesy($sourceImage);

            // Calculate new dimensions (max width 1200px)
            $maxWidth = 1200;
            if ($originalWidth > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = ($originalHeight * $maxWidth) / $originalWidth;
            } else {
                $newWidth = $originalWidth;
                $newHeight = $originalHeight;
            }

            // Create new image
            $processedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Handle transparency for PNG
            if ($imageInfo[2] == IMAGETYPE_PNG) {
                imagealphablending($processedImage, false);
                imagesavealpha($processedImage, true);
                $transparent = imagecolorallocatealpha($processedImage, 255, 255, 255, 127);
                imagefill($processedImage, 0, 0, $transparent);
            }

            // Resize image
            imagecopyresampled(
                $processedImage,
                $sourceImage,
                0,
                0,
                0,
                0,
                $newWidth,
                $newHeight,
                $originalWidth,
                $originalHeight
            );

            // Save processed image
            $processedPath = str_replace('.', '_processed.', $originalPath);
            $destinationPath = storage_path('app/public/' . $processedPath);

            $success = false;
            switch ($imageInfo[2]) {
                case IMAGETYPE_JPEG:
                    $success = imagejpeg($processedImage, $destinationPath, 90);
                    break;
                case IMAGETYPE_PNG:
                    $success = imagepng($processedImage, $destinationPath, 8);
                    break;
                case IMAGETYPE_GIF:
                    $success = imagegif($processedImage, $destinationPath);
                    break;
            }

            // Clean up memory
            imagedestroy($sourceImage);
            imagedestroy($processedImage);

            return $success ? $processedPath : $originalPath;
        } catch (\Exception $e) {
            Log::warning('Basic image processing failed', ['error' => $e->getMessage()]);
            return $originalPath;
        }
    }
}
