<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AddNewProvince extends FormRequest
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
            'Country_id' => 'required|exists:wp_countries,id',
            'province_code' => 'sometimes|string|max:255',
            'name' => 'sometimes|string|max:255',
            'name_ar' => 'sometimes|string|max:255',
            'title' => 'sometimes|string|max:255',
            'title_ar' => 'sometimes|string|max:255',
            /**Banner Params */
            "banner_alt_text_en"=>"sometimes|string|max:255",
            "banner_alt_text_ar"=>"sometimes|string|max:255",
            'banner_image' => 'sometimes|file|mimes:jpeg,png,jpg,webp|max:2048',
            /**End Banner Params */
            /**Start Section of SEO_OVERVIEW */
            'seo_overview_main_en' => 'sometimes|array|max:255',
            'seo_overview_main_ar' => 'sometimes|array|max:255',
            'seo_overview_activities_en' => 'sometimes|array|max:255',
            'seo_overview_activities_ar' => 'sometimes|array|max:255',
            'seo_overview_packages_en' => 'sometimes|array|max:255',
            'seo_overview_packages_ar' => 'sometimes|array|max:255',
            /**End Section of SEO_OVERVIEW */

            /**Start Section of Seo_listing */
            /**Start Section Of main in Seo_listing */
            'seo_listing_main_pictures'   => 'sometimes|array|max:255',
            'seo_listing_main_pictures.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',


          
            'seo_listing_main_alt_text_en' => 'sometimes|array|max:255',
            'seo_listing_main_alt_text_ar' => 'sometimes|array|max:255',

            'seo_listing_main_names_en' => 'sometimes|array|max:255',
            'seo_listing_main_names_ar' => 'sometimes|array|max:255',


            'seo_listing_main_contents_en' => 'sometimes|array|max:255',
            'seo_listing_main_contents_ar' => 'sometimes|array|max:255',
            /**End Section Of main in Seo_listing */
            /**Start Section Of activities in Seo_listing */
            'seo_listing_activities_pictures'   => 'sometimes|array|max:255',
            'seo_listing_activities_pictures.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',


             'seo_listing_activities_alt_text_en' => 'sometimes|array|max:255',
            'seo_listing_activities_alt_text_ar' => 'sometimes|array|max:255',

            'seo_listing_activities_names_en' => 'sometimes|array|max:255',
            'seo_listing_activities_names_ar' => 'sometimes|array|max:255',


            'seo_listing_activities_contents_en' => 'sometimes|array|max:255',
            'seo_listing_activities_contents_ar' => 'sometimes|array|max:255',
            /**End Section Of activities in Seo_listing */
            /**End Section of Seo_listing */

            /**Start FAQ Section  */
                "faq_main_questions_en"=>"sometimes|array|max:255",
                "faq_main_questions_ar"=>"sometimes|array|max:255",
                "faq_main_answers_en"=>"sometimes|array|max:255",
                "faq_main_answers_ar"=>"sometimes|array|max:255",

                "faq_activities_questions_en"=>"sometimes|array|max:255",
                "faq_activities_questions_ar"=>"sometimes|array|max:255",
                "faq_activities_answers_en"=>"sometimes|array|max:255",
                "faq_activities_answers_ar"=>"sometimes|array|max:255",
            /**End Section of FAQ */

                'categories_list' => 'sometimes|array|max:1000',
                'location'=>'sometimes|string|max:255',
                'latitude'=>'sometimes|string|max:255',
                'longitude'=>'sometimes|string|max:255',
                /**Footer Params */

                
                /**Footer Params */
               "footers_activities_titles_en"=>"sometimes|array|max:255",
               "footers_activities_titles_ar"=>"sometimes|array|max:255",
               "footers_activities_anchor_texts_en"=>"sometimes|array|max:255",
               "footers_activities_anchor_texts_ar"=>"sometimes|array|max:255",    
                "footers_activities_anchor_links_en"=>"sometimes|array|max:255",
                "footers_activities_anchor_links_ar"=>"sometimes|array|max:255",
                "footers_restaurants_titles_en"=>"sometimes|array|max:255",
                "footers_restaurants_titles_ar"=>"sometimes|array|max:255",
                "footers_restaurants_anchor_texts_en"=>"sometimes|array|max:255",
                "footers_restaurants_anchor_texts_ar"=>"sometimes|array|max:255",
                "footers_restaurants_anchor_links_en"=>"sometimes|array|max:255",
                "footers_restaurants_anchor_links_ar"=>"sometimes|array|max:255",
                "footers_main_titles_en"=>"sometimes|array|max:255",
                "footers_main_titles_ar"=>"sometimes|array|max:255",
                "footers_main_anchor_texts_en"=>"sometimes|array|max:255",
                "footers_main_anchor_texts_ar"=>"sometimes|array|max:255",
                "footers_main_anchor_links_en"=>"sometimes|array|max:255",
                "footers_main_anchor_links_ar"=>"sometimes|array|max:255",
                /**End Footer Params */


                
        







        ];
    }
}
