<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AddNewCountry extends FormRequest
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
                'name' => 'required|string|max:255',
                'name_ar' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'title_ar' => 'required|string|max:255',
                'continent' => 'required|in:0,1,2,3,4',//	0 = Asia | 1 = Europe | 2 = Africa | 3 = America | 4 = Oceania	
                'country_code' => 'required|string|max:255',
                'timezone' => 'required|string|max:255',
                'whatsapp_number' => 'sometimes|string|max:11',
                'google_map'=>'sometimes|string|max:11',
                "currency_en"=>"sometimes|string|max:255",
                "currency_ar"=>"sometimes|string|max:255",
                'is_gcc' => 'sometimes|in:0,1', //0 = no | 1 = yes
                'slug' => 'sometimes|string|max:255',
                'search_query' => 'sometimes|string|max:255',
                'search_query_ar' => 'sometimes|string|max:255',
                // 'meta_title' => 'sometimes|string|max:255',
                // 'meta_description'  => 'sometimes|string|max:255',
                // 'meta_title_activities' => 'sometimes|string|max:255',
                // 'meta_description_activities' => 'sometimes|string|max:255',

                // 'meta_title_packages' => 'sometimes|string|max:255',
                // 'meta_description_packages' => 'sometimes|string|max:255',

                // 'meta_title_hotels' =>  'sometimes|string|max:255',
                // 'meta_description_hotels' => 'sometimes|string|max:255',
                'status' => 'sometimes|in:0,1', //0 = no | 1 = yes,

                /**Banner Params */
                "banner_alt_text_en"=>"sometimes|string|max:255",
                "banner_alt_text_ar"=>"sometimes|string|max:255",
                'banner_image' => 'sometimes|file|mimes:jpeg,png,jpg,webp|max:2048',
                /**End Banner Params */
                /**Banner Activities Params */
                "banner_alt_text_en"=>"sometimes|string|max:255",
                "banner_alt_text_ar"=>"sometimes|string|max:255",
                'banner_image' => 'sometimes|file|mimes:jpeg,png,jpg,webp|max:2048',
                /**End Banner Activities Params */

            // 'faq'=>'sometimes|base64',//accept base64 encoded array
                /**
                 * 
                 * Start FAQ
                 */
                "packages_question_text_en"=>"sometimes|array|max:255",
                "packages_question_text_ar"=>"sometimes|array|max:255",
                "packages_answer_text_en"=>"sometimes|array|max:255",
                "packages_answer_text_ar"=>"sometimes|array|max:255",

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
        
                // 'footer_links',
                // 'banners' => 'sometimes|base64',
                //**Bannares Params */
                "banners_activities_main_banner"=>"sometimes|file|mimes:jpeg,png,jpg,webp|max:2048",
                "banners_activities_main_banner_alt_en"=> "sometimes|string|max:255",
                "banners_activities_main_banner_alt_ar"=> "sometimes|string|max:255",



                "banners_restaurants_main_banner"=>"sometimes|file|mimes:jpeg,png,jpg,webp|max:2048",
                "banners_restaurants_main_banner_alt_en"=> "sometimes|string|max:255",
                "banners_restaurants_main_banner_alt_ar"=> "sometimes|string|max:255",


                
                "banners_main_main_banner"=>"sometimes|file|mimes:jpeg,png,jpg,webp|max:2048",
                "banners_main_main_banner_alt_en"=> "sometimes|string|max:255",
                "banners_main_main_banner_alt_ar"=> "sometimes|string|max:255",
     
                /**End Bannares Params */



                /**Start Metas Section  */

                "activities_title_en" => "sometimes|string|max:255",
                "activities_title_ar" => "sometimes|string|max:255",
                "activities_description_en" => "sometimes|string|max:255",
                "activities_description_ar" => "sometimes|string|max:255",
                "activities_keywords_en" => "sometimes|string|max:255",
                "activities_keywords_ar" => "sometimes|string|max:255",



                 "main_title_en" => "sometimes|string|max:255",
                "main_title_ar" => "sometimes|string|max:255",
                "main_description_en" => "sometimes|string|max:255",
                "main_description_ar" => "sometimes|string|max:255",
                "main_keywords_en" => "sometimes|string|max:255",
                "main_keywords_ar" => "sometimes|string|max:255",





                
      

           
       
        ];
    }
}
