<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Services\V1\UserService;
use App\Http\Requests\V1\UpdateUserRequest;
use Symfony\Component\HttpFoundation\Response;

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
        return $this->jsonResponse(data: $this->userService->getUsers($request->all()));
    }

    /**
     * Edit user account
     *
     * @param User $user
     * @param UpdateUserRequest $request
     * @return JsonResponse
     */
    public function edit(User $user, UpdateUserRequest $request)
    {
        if ($this->userService->update($user, $request->all())) {
            return response()->json(['success' => 1]);
        }

        return $this->jsonResponse(
            status_code: Response::HTTP_UNPROCESSABLE_ENTITY,
            error: __('profile.edit_failed')
        );
    }

    /**
     * Delete user account
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user)
    {
        if ($this->userService->delete($user)) {
            return $this->jsonResponse();
        }

        return $this->jsonResponse(status_code: Response::HTTP_UNPROCESSABLE_ENTITY, error: 'profile.delete_failed');
    }
}
