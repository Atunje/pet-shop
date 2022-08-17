<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Services\V1\UserService;
use App\Http\Resources\V1\UserResource;
use App\Http\Requests\V1\UpdateUserRequest;
use App\Http\Requests\V1\UserFilterRequest;
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
     * @param UserFilterRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function index(UserFilterRequest $request)
    {
        $filter_params = $request->filterParams();
        $data = UserResource::collection(User::getUsers($filter_params))->resource;

        return $this->jsonResponse(data: $data);
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
