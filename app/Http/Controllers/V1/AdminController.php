<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\RegisterRequest;
use App\Http\Services\V1\UserService;
use Auth;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(protected UserService $userService)
    {
        //
    }


    /**
     * @OA\Post(
     *      path="/api/v1/admin/create",
     *      operationId="createAdmin",
     *      tags={"Admin"},
     *      summary="Create an Admin account",
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
    public function register(RegisterRequest $request)
    {
        $user_resource = $this->userService->registerAdmin($request->all());

        $user_resource->token = $this->userService->adminLogin($request->only('email', 'password'));
        return response()->json(['success' => 1, 'data' => $user_resource]);
    }


    /**
     * @OA\Post(
     *      path="/api/v1/admin/login",
     *      operationId="adminLogin",
     *      tags={"Admin"},
     *      summary="Admin Login",
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
     * Admin login
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function login(LoginRequest $request)
    {
        $token = $this->userService->adminLogin($request->only('email', 'password'));
        if ($token !== null) {
            return response()->json(['success' => 1, 'data' => ['token' => $token]]);
        }

        return response()->json(['success' => 0, 'error' => __('auth.failed')], 422);
    }


    /**
     * @OA\Get(
     *      path="/api/v1/admin/logout",
     *      operationId="adminLogout",
     *      tags={"Admin"},
     *      summary="Admin Logout",
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Entity"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     * Logs current user out
     *
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        if(Auth::logout()) {
            return response()->json(['success' => 1]);
        }

        return response()->json(['success' => 0, 'error' => __('auth.logout_error')], 422);
    }

}
