<?php

namespace App\services;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;



class UserService
{
    public function getUserById($id)
    {

//         try {
//     $user = \App\Models\User::findOrFail($id);
//     return response()->json([
//         'user' => $user,
//         'roles' => $user->assignedRoles // Optional: include roles separately
//     ], 200);
// } catch (\Exception $e) {
//     return response()->json([
//         'error' => 'User not found',
//         'message' => $e->getMessage()
//     ], 404);
// }
    }

    public function createUser(array $data)
    {
        try {
            $user = new \App\Models\User();
            $user->user_nicename  = str_replace(' ', '-', strtolower($data['user_login']));
          
            $user->fill($data);
            $user->save();
            $user->roles()->attach(6); // Assuming 'role' is an array of role IDs 6 for Customer Role
            return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!', 'message' => $e->getMessage()], 500);
        }
        catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!', 'message' => $e->getMessage()], 500);
        }
    }

    public function loginUser(array $data)
    {
            $credentials = [
                'user_email' => $data['user_email'],
                'user_pass' => $data['user_pass'],
            ];

        try {
            $user = User::where('user_email', $credentials['user_email'])->first();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            if (!hash_equals($user->user_pass, $credentials['user_pass'])) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;


            return response()->json(['token' => $token, 'user' => $user->load('roles')], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!', 'message' => $e->getMessage()], 500);
        }
    }
    public function updateUser(array $data)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $user->update($data);
            return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!', 'message' => $e->getMessage()], 500);
        }
    }

}
