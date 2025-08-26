<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService as UserService;
use App\Http\Requests\UserValidation as UserRequest;
use App\Http\Requests\Customer\CheckLogin as UserRequestLogin;
use App\Http\Requests\Customer\UpdateValidation as UpdateRequest;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function create(UserRequest $request)
    {
        try 
        {
            $validatedData = $request->validated();

            $validatedData['role'] = 'customer';


            return $this->userService->createUser($validatedData);
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => 'Something went wrong!', 'message' => $e->getMessage()], 500);
        }

    }

    public function login(UserRequestLogin $request)
    {
        
    return $this->userService->loginUser($request->validated());
    }
    public function update(UpdateRequest $request)
    {
        return $this->userService->updateUser($request->validated());
    }

}
