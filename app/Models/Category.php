<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table="wp_packages_main_category";

          protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'banner',
        'status', //0 for published and 1 for unpublished

    ];
}
