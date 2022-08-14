<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\UpdateUserRequest;
use App\Http\Services\V1\UserService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
        //
    }


    /**
     * Get a paginated list of users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return response()->json(['success' => 1, "data" => $this->userService->getUsers($request->all())]);
    }


    public function edit(User $user, UpdateUserRequest $request)
    {
        if ($this->userService->update($user, $request->all())) {
            return response()->json(['success' => 1]);
        }

        return response()->json(['success' => 0, 'error' => __('profile.edit_failed')]);
    }


    public function destroy(User $user)
    {
        if ($this->userService->delete($user)) {
            return response()->json(['success' => 1]);
        }

        return response()->json(['success' => 0, 'error' => __('profile.delete_failed')]);
    }
}
