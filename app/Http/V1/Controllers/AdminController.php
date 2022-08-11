<?php

namespace App\Http\V1\Controllers;

use App\Http\V1\Requests\RegisterRequest;
use App\Http\V1\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;

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

}
