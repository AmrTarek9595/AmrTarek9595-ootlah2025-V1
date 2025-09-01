<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AddNewCity extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
                "province_id"=>"required|integer|exists:wp_provinces,id",
                "city_code"=>'required|string|max:255',
                'name' => 'required|string|max:255',
                'name_ar' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'title_ar' => 'required|string|max:255',       
             /**Banner Params */
                "banner_alt_text_en"=>"sometimes|string|max:255",
                "banner_alt_text_ar"=>"sometimes|string|max:255",
                'banner_image' => 'sometimes|file|mimes:jpeg,png,jpg,webp|max:2048',
                /**End Banner Params */
                /**Activities Banner Params */
                "banner_activities_alt_text_en"=>"sometimes|string|max:255",
                "banner_activities_alt_text_ar"=>"sometimes|string|max:255",
                'banner_activities_image' => 'sometimes|file|mimes:jpeg,png,jpg,webp|max:2048',
                /**End Activities Banner Params */

                /**Resturants Banner Params */
                "banner_restaurants_alt_text_en"=>"sometimes|string|max:255",
                "banner_restaurants_alt_text_ar"=>"sometimes|string|max:255",
                'banner_restaurants_image' => 'sometimes|file|mimes:jpeg,png,jpg,webp|max:2048',
                /**End Resturants Banner Params */

                /**Video Activities Params */
               
                'video_activities' => 'sometimes|file|mimes:mp4,avi,mov|max:15360',
                /**End Video Activities Params */

                // /**Video Activities Params */
               
                // 'video_activities' => 'sometimes|file|mimes:mp4,avi,mov|max:15360',
                // /**End Video Activities Params */

            
                'seo_overview_main_en' => 'sometimes|string|max:255',
                'seo_overview_main_ar' => 'sometimes|string|max:255',

                'seo_overview_activities_en' => 'sometimes|string|max:255',
                'seo_overview_activities_ar' => 'sometimes|string|max:255',

                
                /** Start SEO Listing Params */
                'seo_listing_main_pictures'=>'sometimes|file|mimes:jpeg,png,jpg,webp|max:2048',
                // 'seo_listing_main_pictures.*'=>'file|mimes:jpeg,png,jpg,webp|max:2048',

                'seo_listing_main_alt_text_en'=>'sometimes|string|max:255',
                'seo_listing_main_alt_text_ar'=>'sometimes|string|max:255',

                'seo_listing_main_names_en'=>'sometimes|string|max:255',
                'seo_listing_main_names_ar'=>'sometimes|string|max:255',

                'seo_listing_main_contents_en'=>'sometimes|string|max:10000',
                'seo_listing_main_contents_ar'=>'sometimes|string|max:10000',

                
                'seo_listing_activities_pictures'=>'sometimes|file|mimes:jpeg,png,jpg,webp|max:2048',
                // 'seo_listing_activities_pictures.*'=>'file|mimes:jpeg,png,jpg,webp|max:2048',

                'seo_listing_activities_alt_text_en'=>'sometimes|string|max:255',
                'seo_listing_activities_alt_text_ar'=>'sometimes|string|max:255',

                'seo_listing_activities_names_en'=>'sometimes|string|max:255',
                'seo_listing_activities_names_ar'=>'sometimes|string|max:255',

                'seo_listing_activities_contents_en'=>'sometimes|string|max:255',
                'seo_listing_activities_contents_ar'=>'sometimes|string|max:255',
                

                /** END SEO Listing Params */


                'seo_content_head_main_en'=>'sometimes|string|20000',
                'seo_content_head_main_ar'=>'sometimes|string|20000',

                'seo_content_head_activities_en'=>'sometimes|string|1000',
                'seo_content_head_activities_ar'=>'sometimes|string|1000',

                /**Start Section of SEo content listing  */

                'seo_content_listing_main_pictures'=>'sometimes|array',
                'seo_content_listing_main_pictures.*'=>'file|mimes:jpeg,png,jpg,webp|max:2048',
                'seo_content_listing_main_alt_text_en'=>'sometimes|array|max:255',
                'seo_content_listing_main_alt_text_ar'=>'sometimes|array',

                'seo_content_listing_main_names_en'=>'sometimes|array',
                'seo_content_listing_main_names_ar'=>'sometimes|array',

                'seo_content_listing_main_contents_en'=>'sometimes|array|max:1000',
                'seo_content_listing_main_contents_ar'=>'sometimes|array|max:1000',


                'seo_content_listing_activities_pictures'=>'sometimes|array',
                'seo_content_listing_activities_pictures.*'=>'file|mimes:jpeg,png,jpg,webp|max:2048',
                'seo_content_listing_activities_alt_text_en'=>'sometimes|array|max:255',
                'seo_content_listing_activities_alt_text_ar'=>'sometimes|array',

                'seo_content_listing_activities_names_en'=>'sometimes|array',
                'seo_content_listing_activities_names_ar'=>'sometimes|array',

                'seo_content_listing_activities_contents_en'=>'sometimes|array|max:1000',
                'seo_content_listing_activities_contents_ar'=>'sometimes|array|max:1000',

                                /**
                 * 
                 * Start FAQ
                 */
                
                "main_question_text_en"=>"sometimes|array|max:255",
                "main_question_text_ar"=>"sometimes|array|max:255",
                "main_answer_text_en"=>"sometimes|array|max:255",
                "main_answer_text_ar"=>"sometimes|array|max:255",

                "activities_question_text_en"=>"sometimes|array|max:255",
                "activities_question_text_ar"=>"sometimes|array|max:255",
                "activities_answer_text_en"=>"sometimes|array|max:255",
                "activities_answer_text_ar"=>"sometimes|array|max:255",
                /*
                 * End FAQ
                 */
                'categories_list' => 'sometimes|array|max:1000',
                'location'=>'sometimes|string|max:255',
                'latitude'=>'sometimes|string|max:255',
                'longitude'=>'sometimes|string|max:255',
                
                "currency_en"=>"sometimes|string|max:25",
                "currency_ar"=>"sometimes|string|max:25",
                'slug' => 'sometimes|string|max:255',
                'slug_province_city' => 'sometimes|string|max:255',
                'search_query' => 'sometimes|string|max:255',
                'search_query_ar' => 'sometimes|string|max:255',




                'meta_title_en' => 'sometimes|string|max:255',
                'meta_title_ar' => 'sometimes|string|max:255',
                'meta_description_en'  => 'sometimes|string|max:255',
                'meta_description_ar'  => 'sometimes|string|max:255',

                'meta_title_activities_en' => 'sometimes|string|max:255',
                'meta_title_activities_ar' => 'sometimes|string|max:255',
                'meta_description_activities_en' => 'sometimes|string|max:255',
                'meta_description_activities_ar' => 'sometimes|string|max:255',

                  /**Footer Params */
                "footers_main_titles_en"=>"sometimes|string|max:255",
                "footers_main_titles_ar"=>"sometimes|string|max:255",
                "footers_main_anchor_texts_en"=>"sometimes|array|max:255",
                "footers_main_anchor_texts_ar"=>"sometimes|array|max:255",    
                "footers_main_anchor_links_en"=>"sometimes|array|max:255",
                "footers_main_anchor_links_ar"=>"sometimes|array|max:255",
                /**End Footer Params */

                'status' => 'sometimes|in:0,1', //0 = no | 1 = yes,


           
             



            ];
    }
}
