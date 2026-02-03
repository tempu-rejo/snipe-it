<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ScannerApiController extends Controller
{
    /**
     * Login untuk scanner app dengan department
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
            'department_id' => 'required|exists:departments,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Verify user belongs to department
        if ($user->department_id != $request->department_id) {
            return response()->json([
                'success' => false,
                'message' => 'User does not belong to selected department'
            ], 403);
        }

        // Generate API token
        $token = $user->createToken('scanner-app')->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'department' => $user->department ? [
                        'id' => $user->department->id,
                        'name' => $user->department->name
                    ] : null
                ],
                'token' => $token
            ]
        ], 200);
    }

    /**
     * Get departments list
     */
    public function getDepartments()
    {
        $departments = Department::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $departments
        ], 200);
    }

    /**
     * Scan asset by QR code (asset tag or id)
     */
    public function scanAsset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $code = $request->code;

        // Try to find asset by asset_tag or id
        $asset = Asset::where('asset_tag', $code)
            ->orWhere('id', $code)
            ->with([
                'model.category',
                'model.manufacturer',
                'location',
                'assignedTo'
            ])
            ->first();

        if (!$asset) {
            return response()->json([
                'success' => false,
                'message' => 'Asset not found'
            ], 404);
        }

        $assignedUser = null;
        if ($asset->assigned_to && $asset->assigned_type === 'App\\Models\\User') {
            $assignedUser = User::find($asset->assigned_to);
        }

        $data = [
            'inv_tag' => $asset->asset_tag,
            'serial' => $asset->serial,
            'name' => $asset->name,
            'category' => $asset->model && $asset->model->category ? $asset->model->category->name : null,
            'type' => $asset->model ? $asset->model->name : null,
            'brand' => $asset->model && $asset->model->manufacturer ? $asset->model->manufacturer->name : null,
            'model' => $asset->model ? $asset->model->model_number : null,
            'warehouse_office' => $asset->location ? $asset->location->name : null,
            'user_name' => $assignedUser ? $assignedUser->getFullNameAttribute() : 'Unassigned',
            'status' => $asset->assetstatus ? $asset->assetstatus->name : null,
            'purchase_date' => $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : null,
            'notes' => $asset->notes,
            'image' => $asset->getImageUrl()
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    /**
     * Get user profile
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'department' => $user->department ? [
                    'id' => $user->department->id,
                    'name' => $user->department->name
                ] : null
            ]
        ], 200);
    }
}
