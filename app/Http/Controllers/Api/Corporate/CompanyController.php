<?php

namespace App\Http\Controllers\Api\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    /**
     * Update existing company profile (auto-create if not exists).
     * Only accessible by corporate (role_id = 2)
     */
    public function update(Request $request)
    {
        $user = $request->user();


        if (!$user) {
            return response()->json(['message' => 'Unauthenticated. Please login again.'], 401);
        }


        if ($user->role_id !== 2) {
            return response()->json([
                'message' => 'Only corporate users can update company profiles.'
            ], 403);
        }

        // ✅ Validasi hanya untuk field editable
        $validated = $request->validate([
            'logo' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $company = Company::firstOrNew(['user_id' => $user->id]);

        $company->fill([
            'name' => $user->name, 
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'logo' => $validated['logo'] ?? $company->logo,
            'website' => $validated['website'] ?? $company->website,
            'address' => $validated['address'] ?? $company->address,
            'description' => $validated['description'] ?? $company->description,
        ]);

        $company->save();

        return response()->json([
            'message' => 'Company profile updated successfully.',
            'data' => $company
        ], 200);
    }

    /**
     * Get the current corporate user's company profile.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($user->role_id !== 2) {
            return response()->json([
                'message' => 'Only corporate users can view company profiles.'
            ], 403);
        }

        $company = Company::where('user_id', $user->id)->first();

        if (!$company) {
            return response()->json(['message' => 'Company profile not found.'], 404);
        }

        return response()->json([
            'message' => 'Company profile retrieved successfully.',
            'data' => $company
        ], 200);
    }
}
