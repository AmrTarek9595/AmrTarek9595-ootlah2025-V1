<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\SharedController;
use App\Services\UserService;
use App\Services\AdminService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
    use App\Hashing\PasswordHash;
    use App\Helper\AdminHelper;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/user', [UserController::class, 'create']); // Create a new Customer User Only
Route::post('/loginuser', [UserController::class, 'login']); // Login a user For All Users ANd All Roles






Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [SharedController::class, 'me']); // Get current user

           Route::middleware(['Role:customer'])->group(function () {

                    Route::get('/update', [UserController::class, 'update']); // Get user by ID
                
           });
    Route::prefix('admin/roles')->middleware(['Role:administrator'])->group(function () {
    Route::get('/getAllRoles', [AdminController::class, 'getAllRoles']); // Get all roles
    Route::post('/addnewrole', [AdminController::class, 'addNewRole']); // Add a new role
    Route::get('/getRoleById/{id}', [AdminController::class, 'getRoleById']); // Get role by ID
    Route::put('/updateRole/{id}', [AdminController::class, 'updateRole']); // Update role by ID
    Route::delete('/deleteRole/{id}', [AdminController::class, 'deleteRole']); // Delete role by ID
    

    });
    Route::prefix('admin/users')->middleware(['Role:administrator'])->group(function () {
        Route::post('/addnewuser', [AdminController::class, 'addNewUser']); // Add a new user
        Route::get('/getcustomuser/{id}', [AdminController::class, 'getCustomUser']); // Get custom user with roles
        Route::get('/getAllUsers', [AdminController::class, 'getAllUsers']); // Get all users
        Route::put('/updateUser/{id}', [AdminController::class, 'updateUser']); // Update user by ID
        Route::delete('/deleteUser/{id}', [AdminController::class, 'deleteUser']); // Delete user by ID

    });
     Route::prefix('admin/countries')->middleware(['Role:administrator'])->group(function () {
        Route::get('/getcustomcountry/{id}', [AdminController::class, 'getCustomountry']); // Get custom Country    
        Route::get('/getallcountries', [AdminController::class, 'getAllCountries']); // Get all Countries     
        Route::post('/addnewcountry', [AdminController::class, 'addNewCountry']); // Add a new Country
        Route::post('/updatecountry/{id}', [AdminController::class, 'updateCountry']); // Update Country
        Route::delete('/deletecountry/{id}', [AdminController::class, 'deleteCountry']); // Delete Country by ID

    });


         Route::prefix('admin/countries/provinces')->middleware(['Role:administrator'])->group(function () {
            Route::get('/getcustomprovince/{id}', [AdminController::class, 'getCustomProvince']); // Return Custom Provinces
            Route::get('/getallprovinces', [AdminController::class, 'getAllProvinces']); // Get All Provinces
            Route::post('/addnewprovince', [AdminController::class, 'addNewProvince']); // Add a new Province
            Route::post('/updateprovince/{id}', [AdminController::class, 'updateProvince']); // Update Province
            Route::delete('/deleteprovince/{id}', [AdminController::class, 'deleteProvince']); // Delete Province by ID

    });

            Route::prefix('admin/countries/provinces/city')->middleware(['Role:administrator'])->group(function () {
            Route::get('/getcustomcity/{id}', [AdminController::class, 'getCustomCity']); // Return Custom Cities
            Route::get('/getallcities', [AdminController::class, 'GetAllCities']); // Get All Cities
            Route::post('/addnewcity', [AdminController::class, 'addNewCity']); // Add a new City
            Route::post('/updatecity/{id}', [AdminController::class, 'updateCity']); // Update City
            Route::delete('/deletecity/{id}', [AdminController::class, 'deleteCity']); // Delete City by ID

    });

            Route::prefix('admin/packages/main/category')->middleware(['Role:administrator'])->group(function () {
            Route::get('/getcustomcategory/{id}', [AdminController::class, 'getCustomCategory']); // Return Custom Categories
            Route::get('/getallcategories', [AdminController::class, 'GetAllCategories']); // Get All Categories
            Route::post('/addnewcategory', [AdminController::class, 'addNewCategory']); // Add a new Category
            Route::post('/updatecategory/{id}', [AdminController::class, 'updateCategory']); // Update Category
            Route::delete('/deletecategory/{id}', [AdminController::class, 'deleteCategory']); // Delete Category by ID

    });
});
   