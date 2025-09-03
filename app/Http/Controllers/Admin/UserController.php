<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 1); // Only show regular users, not admins

        // Apply filters if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Order by created date and paginate
        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Keep filters in pagination links
        $users->appends($request->all());

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(UserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => $request->status,
            ]);

            ActivityLogService::log('created', $user, "Created user: {$user->name}");

            return response()->json([
                'success' => true,
                'message' => 'User created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating user: ' . $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        // Return JSON for AJAX modal requests
        if (request()->ajax()) {
            return response()->json($user);
        }
        
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $user = User::findOrFail($id);
            $oldData = $user->toArray();

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'status' => $request->status,
                // Keep existing role - don't allow changing from user to admin
                'role' => $user->role,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            $changes = array_diff_assoc($user->fresh()->toArray(), $oldData);
            ActivityLogService::log('updated', $user, "Updated user: {$user->name}", $changes);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating user: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent deleting self
            if ($user->id == auth()->id()) {
                return back()->with('error', 'You cannot delete yourself!');
            }

            $name = $user->name;
            ActivityLogService::log('deleted', $user, "Deleted user: {$name}");
            $user->delete();

            return back()->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }
}
