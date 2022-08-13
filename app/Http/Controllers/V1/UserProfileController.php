<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Http\Services\V1\UserService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class UserProfileController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
        //
    }


    /**
     * @OA\Get(
     *      path="/api/v1/user",
     *      operationId="viewUserAccount",
     *      tags={"User"},
     *      summary="View a User Account",
     *      security = {"bearerAuth"},
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Entity"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     *
     * Returns user info
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        return response()->json([
            'success' => 1,
            'data' => new UserResource($request->user())
        ]);
    }


    /**
     * @OA\Put(
     *      path="/api/v1/user/edit",
     *      operationId="editUser",
     *      tags={"User"},
     *      security = {"bearerAuth"},
     *      summary="Edit User account",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={
     *                      "first_name",
     *                      "last_name",
     *                      "email",
     *                      "phone_number",
     *                      "address",
     *                      "password",
     *                      "password_confirmation",
     *                      "avatar"
     *                  },
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="last_name", type="string"),
     *                  @OA\Property(property="email", type="email"),
     *                  @OA\Property(property="phone_number", type="string"),
     *                  @OA\Property(property="address", type="string"),
     *                  @OA\Property(property="password", type="string"),
     *                  @OA\Property(property="password_confirmation", type="string"),
     *                  @OA\Property(property="avatar", type="string"),
     *                  @OA\Property(property="is_marketing", type="string"),
     *              )
     *          )
     *      ),
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Entity"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     *
     * Updates user record
     *
     * @param UpdateUserRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateUserRequest $request)
    {
        $user = $request->user();

        try {
            Gate::denyIf(fn($user) => $user->isAdmin());

            if ($user !== null && $this->userService->update($user, $request->all())) {
                return response()->json(['success' => 1]);
            }

            return response()->json(['success' => 0, 'error' => __('profile.edit_failed')]);
        } catch (AuthorizationException) {
            return response()->json([
                'success' => 0,
                'error' => __('profile.admin_edit_disallowed')
            ], Response::HTTP_UNAUTHORIZED);
        }
    }


    /**
     * @OA\Delete(
     *      path="/api/v1/user",
     *      operationId="deleteUserAccount",
     *      tags={"User"},
     *      summary="Delete a User Account",
     *      security = {"bearerAuth"},
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Entity"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     *
     * Delete user record
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function delete(Request $request)
    {
        $user = $request->user();

        try {
            Gate::denyIf(fn($user) => $user->isAdmin());

            if ($user !== null && $this->userService->delete($user)) {
                return response()->json(['success' => 1]);
            }

            return response()->json(['success' => 0, 'error' => __('profile.delete_failed')]);
        } catch(AuthorizationException) {
            return response()->json([
                'success' => 0,
                'error' => __('profile.admin_delete_disallowed')
            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}
