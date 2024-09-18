<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
// use Illuminate\Support\Facades\Hash;
use Exception;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware('auth:api', ['except' => ['store']]);
        $this->middleware('admin_or_owner', ['except' => ['store']]);

        $this->userService = $userService;
    }

    /**
     * Show list all users.
     * Only admin can show all users.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $users = $this->userService->getAllUsers($request);
            return response()->json(UserResource::collection($users), 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Create new user.
     *
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = $this->userService->createUser($validated);
            return response()->json(new UserResource($user), 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Show user info.
     * Only this user and admin can show this info.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        try {
            $user = $this->userService->getUser($user);
            return response()->json(new UserResource($user), 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Update user info.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $validated = $request->validated();
            $user = $this->userService->updateUser($user, $validated);
            return response()->json(new UserResource($user), 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete user.
     * Only this user and admin can delete record.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        try {
            $this->userService->deleteUser($user);
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json(['message' => 'user not found']);
        }
    }
}
