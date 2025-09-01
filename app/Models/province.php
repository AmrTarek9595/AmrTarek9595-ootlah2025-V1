<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class province extends Model
{
 protected $table = 'wp_provinces';
    protected $fillable = [
        'country_id',
        'province_code',

        'name',
        'name_ar',
        'title',
        'title_ar',

        'banner', //Image For page Resturant restaurants-offers Example Restaurants in Jazan Province and Restaurants in tabuk
        'banner_alt_text', //accept en and ar and stored  as [en]Tourism in Bahrain[en][ar]السياحة في البحرين[ar] and display above banner
        'banner_activities',
        'banner_activities_alt_text',
        // 'banner_packages',
        // 'banner_packages_alt_text',
        // 'video_activities',
        // 'video_packages',
        // 'description',
        // 'seo_overview',
        // 'seo_listing',
        // 'seo_content_head',
        // 'seo_content_listing',
        'faq',
        // 'categories',
        'categories_list',
        // 'categories_list_packages',
        // 'top_sights',
        // 'itineraries_blogs',
        // 'fetched_categories',
        // 'activities_content',
        // 'packages_content',
        // 'thumbnail_name',
        // 'extra_information',
        'google_map',
        // 'languages',
        // 'currency',
        'price_info',
        // 'area',
        // 'featured', //i will ASK 
        // 'featured_date',
        // 'featured_blogs',
        'slug',
        'search_query',
        'search_query_ar',
        // 'breadcrumbs_main',
        // 'breadcrumbs_activities',
        // 'breadcrumbs_packages',
        // 'breadcrumbs_hotels',
        // 'breadcrumbs_flights',
        // 'meta_title',
        // 'meta_description',
        // 'meta_keywords',
        // 'meta_title_activities',
        // 'meta_description_activities',
        // 'meta_keywords_activities',
        // 'meta_title_activites_show_count',
        // 'meta_title_packages',
        // 'meta_description_packages',
        // 'meta_keywords_packages',
        // 'meta_title_packages_show_count',
        // 'meta_title_hotels',
        // 'meta_description_hotels',
        // 'meta_keywords_hotels',
        // 'meta_title_flights',
        // 'meta_description_flights',
        // 'meta_keywords_flights',
        // 'footer_links',
        // 'order_by',
        'status',
        'banners',
        'footers',
        // 'metas',



    ];

 const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at'; // If you don’t have this column, also set timestamps = false
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class, 'province_id');
    }
}
