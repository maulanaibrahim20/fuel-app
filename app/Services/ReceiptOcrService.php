<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Log;

class ReceiptOcrService
{
    private array $fuelProducts = [
        'pertamina' => ['pertalite', 'pertamax', 'pertamax turbo', 'premium', 'solar', 'dexlite', 'pertamax green'],
        'shell' => ['super', 'v-power', 'v-power diesel', 'regular'],
        'bp' => ['bp 92', 'bp 95', 'bp diesel'],
        'total' => ['total 92', 'total 95', 'total diesel'],
        'vivo' => ['revvo 90', 'revvo 92', 'revvo 95', 'revvo diesel']
    ];

    public function extractText(string $imagePath): string
    {
        try {
            // Preprocessing untuk meningkatkan akurasi OCR
            $ocr = new TesseractOCR($imagePath);
            $ocr->lang('eng', 'ind')
                ->psm(6) // Assume uniform block of text
                ->oem(3) // Default OCR Engine Mode
                ->dpi(300)
                ->allowlist('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz.,/:-_()@ ');

            return $ocr->run();
        } catch (\Exception $e) {
            Log::error('OCR extraction failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function parseReceipt(string $text): array
    {
        $text = $this->cleanText($text);

        $extractedData = [
            'spbu_info' => $this->extractSpbuInfo($text),
            'transaction_info' => $this->extractTransactionInfo($text),
            'fuel_info' => $this->extractFuelInfo($text),
            'payment_info' => $this->extractPaymentInfo($text),
            'operator_info' => $this->extractOperatorInfo($text),
            'additional_info' => $this->extractAdditionalInfo($text),
        ];

        // Calculate confidence score
        $confidence = $this->calculateConfidenceScore($extractedData);

        return [
            'extracted_data' => $extractedData,
            'confidence_score' => $confidence,
            'scan_type' => $this->determineScanType($extractedData),
            'raw_text' => $text,
        ];
    }

    private function cleanText(string $text): string
    {
        // Remove unwanted characters but keep structure
        $text = preg_replace('/[^\w\s\d.,\/:\-()@]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    private function extractSpbuInfo(string $text): array
    {
        $spbuInfo = [];

        // SPBU Number/Code
        $patterns = [
            '/(\d{7,8})\s*SPBU/i',
            '/SPBU\s*([A-Z\s]+)/i',
            '/Station\s*(\d+[\d.]*)/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $spbuInfo['code'] = trim($matches[1]);
                break;
            }
        }

        // SPBU Name/Location
        if (preg_match('/SPBU\s+([A-Z\s]+)/i', $text, $matches)) {
            $spbuInfo['name'] = trim($matches[1]);
        }

        // Address
        if (preg_match('/Jl\.?\s*([^0-9\n]+(?:\s+No\.?\s*\d+)?)/i', $text, $matches)) {
            $spbuInfo['address'] = trim($matches[1]);
        }

        return $spbuInfo;
    }

    private function extractTransactionInfo(string $text): array
    {
        $transactionInfo = [];

        // Transaction Number
        $patterns = [
            '/No\.?\s*Trans[:\s]*(\d+)/i',
            '/Trans[:\s]*(\d+)/i',
            '/Transaction[:\s]*(\d+)/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $transactionInfo['transaction_number'] = $matches[1];
                break;
            }
        }

        // Shift Information
        if (preg_match('/Shift[:\s]*(\d+)/i', $text, $matches)) {
            $transactionInfo['shift'] = $matches[1];
        }

        // Pump Number
        $pumpPatterns = [
            '/Pulau[\/\s]*Pompa[:\s]*(\d+)/i',
            '/Pump[:\s]*(\d+)/i',
            '/Pompa[:\s]*(\d+)/i'
        ];

        foreach ($pumpPatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $transactionInfo['pump_number'] = $matches[1];
                break;
            }
        }

        // Date and Time
        $dateTimePatterns = [
            '/Waktu[:\s]*([\d\/\-\s:]+)/i',
            '/(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}\s+\d{1,2}:\d{2}(?::\d{2})?)/i',
            '/(\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}\s+\d{1,2}:\d{2}(?::\d{2})?)/i'
        ];

        foreach ($dateTimePatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $transactionInfo['datetime'] = trim($matches[1]);
                break;
            }
        }

        return $transactionInfo;
    }

    private function extractFuelInfo(string $text): array
    {
        $fuelInfo = [];

        // Product Name - More flexible patterns
        $productPatterns = [
            '/(?:Nama\s*Produk|Product)[:\s]*([A-Za-z0-9\s]+)/i',
            '/\b(' . $this->getFuelProductsRegex() . ')\b/i',
            '/BBM[:\s]*([A-Za-z0-9\s]+)/i'
        ];

        foreach ($productPatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $fuelInfo['product_name'] = trim($matches[1]);
                break;
            }
        }

        // Price per Liter - Multiple formats
        $pricePatterns = [
            '/Harga[:\s]*Rp\.?\s*([\d,.]+)/i',
            '/(?:Price|Harga)[\/\s]*(?:L|Liter)[:\s]*Rp\.?\s*([\d,.]+)/i',
            '/Rp\.?\s*([\d,.]+)[\/\s]*(?:L|Liter)/i',
            '/(?:@|at)\s*Rp\.?\s*([\d,.]+)/i'
        ];

        foreach ($pricePatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $fuelInfo['price_per_liter'] = $this->normalizeNumber($matches[1]);
                break;
            }
        }

        // Volume - Multiple formats
        $volumePatterns = [
            '/Volume[:\s]*\([L]\)\s*([\d,.]+)/i',
            '/Volume[:\s]*([\d,.]+)/i',
            '/(?:Vol|Qty)[:\s]*([\d,.]+)\s*L?/i',
            '/([\d,.]+)\s*(?:L|Liter)/i'
        ];

        foreach ($volumePatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $fuelInfo['volume'] = $this->normalizeNumber($matches[1]);
                break;
            }
        }

        return $fuelInfo;
    }

    private function extractPaymentInfo(string $text): array
    {
        $paymentInfo = [];

        // Total Amount
        $totalPatterns = [
            '/Total\s*Harga[:\s]*Rp\.?\s*([\d,.]+)/i',
            '/Total[:\s]*Rp\.?\s*([\d,.]+)/i',
            '/Amount[:\s]*Rp\.?\s*([\d,.]+)/i'
        ];

        foreach ($totalPatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $paymentInfo['total_amount'] = $this->normalizeNumber($matches[1]);
                break;
            }
        }

        // Payment Method
        if (preg_match('/\b(CASH|CREDIT|DEBIT|EDC|QRIS|GOPAY|OVO|DANA)\b/i', $text, $matches)) {
            $paymentInfo['payment_method'] = strtoupper($matches[1]);
        }

        // Cash Amount (if different from total)
        if (preg_match('/(?:CASH|Tunai)[:\s]*Rp\.?\s*([\d,.]+)/i', $text, $matches)) {
            $paymentInfo['cash_amount'] = $this->normalizeNumber($matches[1]);
        }

        return $paymentInfo;
    }

    private function extractOperatorInfo(string $text): array
    {
        $operatorInfo = [];

        if (preg_match('/Operator[:\s]*([A-Z\s]+)/i', $text, $matches)) {
            $operatorInfo['name'] = trim($matches[1]);
        }

        return $operatorInfo;
    }

    private function extractAdditionalInfo(string $text): array
    {
        $additionalInfo = [];

        // Subsidy Information
        if (preg_match('/Subsidi\s+BBM/i', $text)) {
            $additionalInfo['has_subsidy_info'] = true;

            // Extract subsidy amounts
            if (preg_match_all('/Rp\.?\s*([\d,.]+)[\/\s]*liter/i', $text, $matches)) {
                $additionalInfo['subsidy_rates'] = array_map([$this, 'normalizeNumber'], $matches[1]);
            }
        }

        // Brand Detection
        $brands = ['pertamina', 'shell', 'bp', 'total', 'vivo'];
        foreach ($brands as $brand) {
            if (stripos($text, $brand) !== false) {
                $additionalInfo['brand'] = ucfirst($brand);
                break;
            }
        }

        return $additionalInfo;
    }

    private function getFuelProductsRegex(): string
    {
        $allProducts = [];
        foreach ($this->fuelProducts as $brand => $products) {
            $allProducts = array_merge($allProducts, $products);
        }
        return implode('|', array_map('preg_quote', $allProducts));
    }

    private function normalizeNumber(string $number): string
    {
        // Handle Indonesian number format
        $number = trim($number);

        // Remove spaces
        $number = str_replace(' ', '', $number);

        // If contains both comma and dot, determine which is decimal separator
        if (strpos($number, ',') !== false && strpos($number, '.') !== false) {
            // If comma comes after dot, comma is decimal
            if (strrpos($number, ',') > strrpos($number, '.')) {
                $number = str_replace('.', '', $number);
                $number = str_replace(',', '.', $number);
            } else {
                // Dot is decimal separator
                $number = str_replace(',', '', $number);
            }
        } elseif (strpos($number, ',') !== false) {
            // Only comma - could be thousands separator or decimal
            $commaPos = strrpos($number, ',');
            $afterComma = substr($number, $commaPos + 1);

            // If 3 digits after comma, it's thousands separator
            if (strlen($afterComma) === 3 && is_numeric($afterComma)) {
                $number = str_replace(',', '', $number);
            } else {
                // It's decimal separator
                $number = str_replace(',', '.', $number);
            }
        }

        return $number;
    }

    private function calculateConfidenceScore(array $extractedData): float
    {
        $score = 0;
        $maxScore = 0;

        $weights = [
            'spbu_info' => 15,
            'transaction_info' => 20,
            'fuel_info' => 30,
            'payment_info' => 25,
            'operator_info' => 10
        ];

        foreach ($weights as $section => $weight) {
            $maxScore += $weight;

            if (isset($extractedData[$section]) && !empty($extractedData[$section])) {
                $sectionScore = 0;
                $maxSectionScore = count($extractedData[$section]);

                foreach ($extractedData[$section] as $value) {
                    if (!empty($value)) {
                        $sectionScore++;
                    }
                }

                if ($maxSectionScore > 0) {
                    $score += ($sectionScore / $maxSectionScore) * $weight;
                }
            }
        }

        return $maxScore > 0 ? round(($score / $maxScore) * 100, 2) : 0;
    }

    private function determineScanType(array $extractedData): string
    {
        // Check if it's fuel receipt
        if (
            !empty($extractedData['fuel_info']['product_name']) ||
            !empty($extractedData['fuel_info']['volume']) ||
            !empty($extractedData['spbu_info'])
        ) {
            return 'fuel_receipt';
        }

        // Check for maintenance keywords
        $maintenanceKeywords = ['service', 'ganti', 'oli', 'tune up', 'maintenance'];
        $text = strtolower(json_encode($extractedData));

        foreach ($maintenanceKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return 'maintenance_receipt';
            }
        }

        return 'other';
    }

    public function validateReceiptData(array $extractedData): array
    {
        $errors = [];
        $warnings = [];

        // Validate fuel info if it's a fuel receipt
        if (isset($extractedData['fuel_info'])) {
            $fuelInfo = $extractedData['fuel_info'];

            // Check for mathematical consistency
            if (
                !empty($fuelInfo['price_per_liter']) &&
                !empty($fuelInfo['volume']) &&
                !empty($extractedData['payment_info']['total_amount'])
            ) {

                $calculatedTotal = floatval($fuelInfo['price_per_liter']) * floatval($fuelInfo['volume']);
                $actualTotal = floatval($extractedData['payment_info']['total_amount']);
                $difference = abs($calculatedTotal - $actualTotal);

                if ($difference > 100) { // Allow small rounding differences
                    $warnings[] = 'Perhitungan total tidak sesuai dengan harga per liter × volume';
                }
            }
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }
}
