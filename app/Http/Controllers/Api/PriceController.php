<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PriceController extends Controller
{
    /**
     * Display all pricing information
     */
    public function index()
    {
        $prices = Price::where('is_active', true)
            ->with('field')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $prices
        ]);
    }

    /**
     * Store a newly created pricing
     */
    public function store(Request $request)
    {
        // Permission check - only admin
        if (!$request->user()?->hasRole('admin') && !$request->user()?->can('manage prices')) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'field_id' => 'nullable|exists:fields,id',
            'time_period' => 'required|string|max:50',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'price_per_hour' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $price = Price::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Price created successfully.',
            'data' => $price
        ], 201);
    }

    /**
     * Display the specified pricing
     */
    public function show(Price $price)
    {
        return response()->json([
            'success' => true,
            'data' => $price
        ]);
    }

    /**
     * Update the specified pricing
     */
    public function update(Request $request, Price $price)
    {
        // Permission check
        if (!$request->user()?->hasRole('admin') && !$request->user()?->can('manage prices')) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'field_id' => 'nullable|exists:fields,id',
            'time_period' => 'string|max:50',
            'start_time' => 'date_format:H:i:s',
            'end_time' => 'date_format:H:i:s',
            'price_per_hour' => 'numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $price->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Price updated successfully.',
            'data' => $price
        ]);
    }

    /**
     * Remove the specified pricing
     */
    public function destroy(Price $price)
    {
        // Permission check
        $user = request()->user();

        if (!$user?->hasRole('admin') && !$user?->can('manage prices')) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }

        $price->delete();

        return response()->json([
            'success' => true,
            'message' => 'Price deleted successfully.'
        ]);
    }
}
