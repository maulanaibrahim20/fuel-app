<?php

namespace App\Http\Controllers\User;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\Vehicles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicles::where('user_id', Auth::id())->paginate(6);
        return view('pages.user.vechile.index', compact('vehicles'));
    }

    public function create()
    {
        return view('pages.user.vechile.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:255',
            'brand'            => 'required|string|max:255',
            'model'            => 'required|string|max:255',
            'year'             => 'required|digits:4|integer',
            'fuel_type'        => 'required|in:gasoline,diesel,hybrid,electric,lpg,pertamax,pertamax_plus',
            'transmission'     => 'required|in:manual,automatic,cvt',
            'engine_capacity'  => 'nullable|numeric|min:0',
            'license_plate'    => 'required|string|max:50',
            'tank_capacity'    => 'nullable|numeric|min:0',
            'color'            => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
            'is_active'        => 'boolean',
            'image'            => 'nullable|image|max:2048',
            'initial_odometer' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return Message::validator("Validation failed.", $validator->errors());
        }

        DB::beginTransaction();

        try {
            $imagePath = null;

            if ($request->hasFile('image')) {
                $filename = Str::uuid()->toString() . '.' . $request->file('image')->getClientOriginalExtension();
                $imagePath = $request->file('image')->storeAs('vehicles', $filename, 'public');
            }

            Vehicles::create([
                'user_id'           => Auth::id(),
                'name'              => $request->name,
                'brand'             => $request->brand,
                'model'             => $request->model,
                'year'              => $request->year,
                'fuel_type'         => $request->fuel_type,
                'transmission'      => $request->transmission,
                'engine_capacity'   => $request->engine_capacity,
                'license_plate'     => $request->license_plate,
                'tank_capacity'     => $request->tank_capacity,
                'color'             => $request->color,
                'notes'             => $request->notes,
                'is_active'         => $request->is_active ?? true,
                'initial_odometer'  => $request->initial_odometer,
                'image'             => $imagePath, // simpan path gambar
            ]);

            DB::commit();

            return Message::create('Vehicle has been created.');
        } catch (\Exception $e) {
            DB::rollBack();
            return Message::error('Failed to create vehicle.', $e->getMessage());
        }
    }

    public function edit(Request $request, $id)
    {
        $data['vehicles'] = Vehicles::find($id);
        return view('pages.user.vechile.edit', $data);
    }
}
