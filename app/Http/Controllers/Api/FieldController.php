<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fields = Field::all();
        return response()->json([
            'success' => true,
            'data' => $fields
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Permission check
        if (!$request->user()?->hasRole('admin') && !$request->user()?->can('manage fields')) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_hour' => 'required|numeric|min:0',
            'is_available' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $field = Field::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Field created successfully.',
            'data' => $field
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Field $field)
    {
        return response()->json([
            'success' => true,
            'data' => $field
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Field $field)
    {
        // Permission check
        if (!$request->user()?->hasRole('admin') && !$request->user()?->can('manage fields')) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'type' => 'string|max:255',
            'description' => 'nullable|string',
            'price_per_hour' => 'numeric|min:0',
            'is_available' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $field->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Field updated successfully.',
            'data' => $field
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Field $field)
    {
        // Permission check
        $user = request()->user();

        if (!$user?->hasRole('admin') && !$user?->can('manage fields')) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }

        $field->delete();

        return response()->json([
            'success' => true,
            'message' => 'Field deleted successfully.'
        ]);
    }
}
