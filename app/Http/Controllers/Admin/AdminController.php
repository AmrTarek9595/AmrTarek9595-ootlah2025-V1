<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\AddNewRole ;
use App\Http\Requests\Admin\UpdateSingleRole;
use App\Services\AdminService;
use App\Http\Requests\Admin\AddNewUser;
use App\Http\Requests\Admin\UpdateUserData;
use App\Http\Requests\Admin\AddNewCountry;
use App\Http\Requests\Admin\AddNewProvince;
use App\Helper\AdminHelper;
use App\Models\Country;
class AdminController extends Controller
{
    protected $adminService;
    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function AddNewRole(AddNewRole $request)
    {
        return $this->adminService->addNewRole($request->validated());
    }
    public function getAllRoles()
    {
        return $this->adminService->getAllRoles();
    }
    public function getRoleById($id)
    {
        return $this->adminService->getRoleById($id);
    }
    public function updateRole($id, UpdateSingleRole $request)
    {
        $data = $request->validated();
        return $this->adminService->updateRole($id, $data);
    }
    public function deleteRole($id)
    {
        return $this->adminService->deleteRole($id);
    }
    /*
    * Get custom user with roles
    */
    public function getCustomUser($id)
    {
        $user = \App\Models\User::with('roles')->find($id);
        // Ensure unique roles
        return response()->json([
            'user' => $user->setRelation('roles', $user->roles->unique('id'))
        ]);
    }
    public function addNewUser(AddNewUser $request)
    {
        // Validate the request data
        $validatedData = $request->validated();

        // Call the service to add a new user
        return $this->adminService->addNewUser($validatedData);
    }
    public function getAllUsers()
    {
        return $this->adminService->getAllUsers();
    }
    public function updateUser($id, UpdateUserData $request)
    {
        $validatedData = $request->validated();

        // Call the service to update the user
        return $this->adminService->updateUser($id, $validatedData);
    }
    public function deleteUser($id)
    {
        return $this->adminService->deleteUser($id);
    }
    /**
     * End section of users
     */

    /**
     * Start section of countries
     */ 

    public function getCustomountry($id)
    {
        return $this->adminService->getCustomCountry($id);
    }

    public function getAllCountries()
    {
        return $this->adminService->getAllCountries();
    }

    
    public function addNewCountry(AddNewCountry $request)
    {   
        return $this->adminService->addNewCountry($request->validated());
    }
    public function updateCountry($id, AddNewCountry $request)
    {
        return $this->adminService->updateCountry($id, $request->validated());
    }
    public function deleteCountry($id)
    {
        return $this->adminService->deleteCountry($id);
    }

    /***
     * 
     * 
     * 
     * END SECTION OF COUNTRY
     * 
     * 
     * START SECTION OF PROVINCES
     */

    public function getCustomProvince($id)
    {
        return $this->adminService->getCustomProvince($id);
    }

    public function getAllProvinces()
    {
        return $this->adminService->getAllProvinces();
    }

    public function addNewProvince(AddNewProvince $request)
    {
        
        return $this->adminService->addNewProvince($request->validated());
    }




    
}