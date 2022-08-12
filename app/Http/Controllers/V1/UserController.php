<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\RegisterRequest;
use App\Http\Requests\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Http\Services\V1\UserService;
use Auth;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserController extends Controller
{
    public function __construct(protected UserService $userService)
    {
        //
    }


    /**
     * @OA\Post(
     *      path="/api/v1/user/create",
     *      operationId="createUser",
     *      tags={"User"},
     *      summary="Create an User account",
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
     * Register new user and return user data
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function create(RegisterRequest $request)
    {
        $user_resource = $this->userService->registerUser($request->all());

        $user_resource->token = $this->userService->userLogin($request->only('email', 'password'));
        return response()->json(['success' => 1, 'data' => $user_resource]);
    }


    /**
     * @OA\Post(
     *      path="/api/v1/user/login",
     *      operationId="userLogin",
     *      tags={"User"},
     *      summary="User Login",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={
     *                      "email",
     *                      "password",
     *                  },
     *                  @OA\Property(property="email", type="email"),
     *                  @OA\Property(property="password", type="string")
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
     * User login
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function login(LoginRequest $request)
    {
        $token = $this->userService->userLogin($request->only('email', 'password'));
        if($token !== null) {
            return response()->json(['success' => 1, 'data' => ['token' => $token]]);
        }

        return response()->json(['success' => 0, 'error' => __('auth.failed')], Response::HTTP_UNAUTHORIZED);
    }


    /**
     * @OA\Get(
     *      path="/api/v1/user/logout",
     *      operationId="userLogout",
     *      tags={"User"},
     *      summary="User Logout",
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Entity"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     *
     * Logs current user out
     *
     * @return JsonResponse
     */
    public function logout()
    {
        if(Auth::logout()) {
            return response()->json(['success' => 1]);
        }

        return response()->json(['success' => 0, 'error' => __('auth.logout_error')], Response::HTTP_UNAUTHORIZED);
    }


    /**
     * @OA\Get(
     *      path="/api/v1/user",
     *      operationId="viewUserAccount",
     *      tags={"User"},
     *      summary="View a User Account",
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
     * @OA\Get(
     *      path="/api/v1/user/edit",
     *      operationId="editUserAccount",
     *      tags={"User"},
     *      summary="Edit a User Account",
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
        } catch (AuthorizationException $e) {
            return response()->json(['success' => 0, 'error' => __('profile.admin_edit_disallowed')]);
        }
    }

}
