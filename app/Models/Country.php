<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'wp_countries';
    protected $fillable = [
        'name',
        'name_ar',
        'title',
        'title_ar',
        'continent', //	0 = Asia | 1 = Europe | 2 = Africa | 3 = America | 4 = Oceania	
        'country_code',
        'timezone', //	used for booking
        'whatsapp_number',
        'banner', //Image
        'banner_alt_text', //accept en and ar and stored  as [en]Tourism in Bahrain[en][ar]السياحة في البحرين[ar] and display above banner
        'banner_activities',
        'banner_activities_alt_text',
        // 'description', //Accept [en][en] [ar][ar]
        'faq',//accept base64 encoded array
        'categories_list',
        'google_map',
        'currency',
        'price_info', //calcuate automatically
        'is_gcc',
        'slug',
        'search_query',
        'search_query_ar',
        // 'meta_title',
        // 'meta_description',
        // 'meta_title_activities',
        // 'meta_description_activities',
        // 'meta_title_packages',
        // 'meta_description_packages',
        // 'meta_title_hotels',
        // 'meta_description_hotels',
        'number_activities', //calculate automatically
        'status',
        'organic_ratio', //calculate automatically
        'booking_count_month', //calculate automatically
        
     
        // 'footer_links',
        'banners',
        'footers'
      



    ];

 const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at'; // If you don’t have this column, also set timestamps = false
public function provinces()
{
    return $this->hasMany(province::class);
}
}
