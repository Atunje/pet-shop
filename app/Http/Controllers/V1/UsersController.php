<?php

namespace App\Http\Controllers\V1;

use App\Http\Services\V1\UserService;
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
    public function users(Request $request)
    {
        return response()->json(['success' => 1, "data" => $this->userService->getUsers($request->all())]);
    }
}
