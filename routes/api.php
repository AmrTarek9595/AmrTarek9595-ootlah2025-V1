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
        Route::put('/updatecountry/{id}', [AdminController::class, 'updateCountry']); // Update Country
        Route::delete('/deletecountry/{id}', [AdminController::class, 'deleteCountry']); // Delete Country by ID

    });


         Route::prefix('admin/countries/provinces')->middleware(['Role:administrator'])->group(function () {
            Route::get('/getcustomprovince/{id}', [AdminController::class, 'getCustomProvince']); // Return Custom Provinces
            Route::get('/getallprovinces', [AdminController::class, 'getAllProvinces']); // Get All Provinces
            Route::post('/addnewprovince', [AdminController::class, 'addNewProvince']); // Add a new Province
            Route::put('/updateprovince/{id}', [AdminController::class, 'updateProvince']); // Update Province
            Route::delete('/deleteprovince/{id}', [AdminController::class, 'deleteProvince']); // Delete Province by ID

    });
});
    // For FAQ

// Route::get('/unserialized', function () {
    
        //     function repairSerialized($str)
        //     {
        //         return preg_replace_callback(
        //             '/s:(\d+):"(.*?)";/s',
        //             fn($m) => 's:' . strlen($m[2]) . ':"' . $m[2] . '";',
        //             $str
        //         );
        //     }

        //     function deepDecode($value)
        //     {
        //         // If it's serialized data, fix and unserialize
        //         if (is_string($value) && preg_match('/^a:\d+:/', $value)) {
        //             $unser = @unserialize(repairSerialized($value));
        //             if ($unser !== false) {
        //                 return deepDecode($unser);
        //             }
        //         }

        //         // If it's base64 encoded, decode and check if result is serialized
        //         if (is_string($value) && ($decoded = base64_decode($value, true)) !== false) {
        //             // If decoded text is serialized
        //             if (preg_match('/^a:\d+:/', $decoded)) {
        //                 $unser = @unserialize(repairSerialized($decoded));
        //                 if ($unser !== false) {
        //                     return deepDecode($unser);
        //                 }
        //             }
        //             return $decoded; // plain base64 text
        //         }

        //         // If it's a language-tagged string like [en]Base64 [ar]Base64
        //         if (is_string($value) && preg_match_all('/\[(\w+)\]([^[]+)/', $value, $matches, PREG_SET_ORDER)) {
        //             $result = [];
        //             foreach ($matches as $m) {
        //                 $lang    = $m[1];
        //                 $decoded = deepDecode($m[2]);
        //                 $result[$lang] = $decoded;
        //             }
        //             return $result;
        //         }

        //         // If it's an array, decode each element
        //         if (is_array($value)) {
        //             foreach ($value as $k => $v) {
        //                 $value[$k] = deepDecode($v);
        //             }
        //             return $value;
        //         }

        //         return $value;
        //     }

        //     // Pull and decode everything dynamically
        //     $raw  = \DB::table('wp_countries')->where('id', 68)->value('faq');
        //     $decodedData = deepDecode($raw);

        //     return $decodedData;

        // });

        // for Cetgories_list in table countries 
