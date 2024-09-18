<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserService
{
    public function getAllUsers(Request $request)
    {
        $email = $request->query('email');
        $itemsPerPage = $request->query('items_per_page', 15);

        return User::filterByEmail($email)
            ->orderBy('email', 'DESC')
            ->paginate($itemsPerPage);
    }

    public function createUser(array $data)
    {
        return User::create($data);
    }

    public function getUser(User $user)
    {
        return $user;
    }

    public function updateUser(User $user, array $data)
    {
        $currentUser = auth()->user();

        // Hash the password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Remove the password key if it is not set, to prevent updating it with null
            unset($data['password']);
        }

        // Only admin can update the 'role' field
        if (isset($data['role']) && $currentUser->role !== 'admin') {
            unset($data['role']); // Remove 'role' field if the user is not an admin
        }

        // Allow admin or the owner of the record to update other fields
        if ($currentUser->role === 'admin' || $currentUser->id === $user->id) {
            // Perform the update
            $user->update($data);
            return $user;
        }

        // If the user is neither an admin nor the owner, throw an error
        throw new Exception('Unauthorized: You do not have permission to update this user.', 403);
    }

    public function deleteUser(User $user)
    {
        $user->delete();
    }
}
