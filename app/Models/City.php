<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
        protected $table="wp_cities";

              protected $fillable = [
                'city_code',
                'country_id',
                'province_id',
        'name',
        'name_ar',
        'title',
        'title_ar',

        'banner', //Image
        'banner_alt_text', //accept en and ar and stored  as [en]Tourism in Bahrain[en][ar]السياحة في البحرين[ar] and display above banner
        'banner_activities',
        'banner_activities_alt_text',
        'banner_restaurants',
        'banner_restaurants_alt_text',
        'video_activities',
        // 'video_packages',
        'seo_overview',
        'seo_listing',
        'seo_content_head',
        'seo_content_listing',
        'faq',
        // 'categories',
        'categories_list',
        // 'categories_list_packages',
        // 'activities_content',
        // 'packages_content',
        'google_map',
        'currency',
        'price_info',
        'slug',
        'slug_province_city',
        'search_query',
        'search_query_ar',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_title_activities',
        'meta_description_activities',
        'meta_keywords_activities',
        // 'meta_title_activites_show_count',
        'footer_links',
        'status',
        'number_activities',
        'number_restaurants',
        'booking_count_month',
        // 'footers',
        // 'metas'








    ];


    public function provinces()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

}
