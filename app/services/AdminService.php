<?php

namespace App\services;
use App\Helper\AdminHelper;
use App\Models\UserRole;
use App\Models\User;
use App\Models\Country;
use App\Models\province;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AdminService
{
   public function addNewRole(array $data)
    {
        try {
            $newSlug=str_replace(' ', '-', strtolower($data['title']));
            $data['slug'] = $newSlug;
            $role = new \App\Models\UserRole();
            $role->fill($data);
            $role->save();
            return response()->json(['message' => 'Role added successfully', 'role' => $role], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!', 'message' => $e->getMessage()], 500);
        }
    }

    public function getAllRoles()
    {
        try {
            $roles = \App\Models\UserRole::all();
            return response()->json(['roles' => $roles], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!', 'message' => $e->getMessage()], 500);
        }
    }
    public function getRoleById($id)
    {
        try {
            $role = \App\Models\UserRole::findOrFail($id);
            return response()->json(['role' => $role], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Role not found', 'message' => $e->getMessage()], 404);
        }
    }
    public function updateRole($id, array $data)
    {
        try {
            $role = \App\Models\UserRole::findOrFail($id);
            if (isset($data['title'])) {
                $data['slug'] = str_replace(' ', '-', strtolower($data['title']));
            }
            $role->update($data);
            return response()->json(['message' => 'Role updated successfully', 'role' => $role], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!', 'message' => $e->getMessage()], 500);
        }
    }
    public function deleteRole($id)
    {
        try {
            $role = \App\Models\UserRole::findOrFail($id);
            $role->delete();
            return response()->json(['message' => 'Role deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 
     * 
     * END SECTION OF ROLES
     */
    public function addNewUser(array $data)
    {
        try {
            $user = new \App\Models\User();
            $user->user_login = $data['user_login'];
            $user->user_email = $data['user_email'];
            $user->user_pass = bcrypt($data['user_pass']);
            $user->save();

            // Assign role to user
            $role = \App\Models\UserRole::where('title', $data['role'])->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }

            return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!', 'message' => $e->getMessage()], 500);
        }
    }
    
    public function getAllUsers()
    {
        try {
            $users = \App\Models\User::with('roles')->paginate(15);
            return response()->json(['users' => $users], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!', 'message' => $e->getMessage()
        ], 500);
        }
    }
    public function updateUser($id, array $data)
    {
        try {
            $user = \App\Models\User::findOrFail($id)->load('roles');

            

            // If the role is being updated, handle it
            if (isset($data['role'])) {
                $role = \App\Models\UserRole::where('title', $data['role'])->first();
                if ($role) {
                   $user->roles()->sync([$role->id]);
                } else {
                    return response()->json(['error' => 'Role not found'], 404);
                }
            }
                        $user->update($data);
            return response()->json(['message' => 'User updated successfully', 'user' => $user->load('roles')], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!', 'message' => $e->getMessage()], 500);
        }
    }
    public function deleteUser($id)
    {
        try {
            $user = \App\Models\User::findOrFail($id);
            $user->roles()->detach();
            $user->delete();
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error'    => 'Something went wrong!', 'message' => $e->getMessage()], 500);
        }
    }

    public function getCustomCountry($id)
    {
        $country = Country::find($id);
        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        $originalText = $country['banner_alt_text'] ?? null;

        if (preg_match('/\[en\](.*?)\[en\]/', $originalText, $matches)) {
            $country['banner_alt_text'] = $matches[1];
        }

        if (preg_match('/\[ar\](.*?)\[ar\]/', $originalText, $matches)) {
            $country['banner_alt_text_ar'] = $matches[1];
        }
        $country['banner_alt_text'] = $country['banner_alt_text'] ?? null;
        $country['banner_alt_text_ar'] = $country['banner_alt_text_ar'] ?? null;
        if (!empty($country->faq)) {
            $faqDecoded = AdminHelper::deepDecodeFAQ($country->faq);
            $faqUtf8 = AdminHelper::utf8ize($faqDecoded);
            $country->setAttribute('faq', $faqUtf8);
            }

        if (!empty($country->categories_list)) {
                $data = AdminHelper::deepUnserializeCategiry_List($country['categories_list']);
                }
       

        $categories_list = [];
        if(!empty($data))
        {
        foreach ($data as $parentId => $childIds) {
            $parent = DB::table('wp_packages_main_category')
                ->where('id', $parentId)
                ->select('id', 'name')
                ->first();

            if (!$parent) continue;

            $children = [];
            if (is_array($childIds) && count($childIds)) {
                $children = DB::table('wp_adventure_type')
                    ->whereIn('id', $childIds)
                    ->select('id', 'name')
                    ->get();
            }

            $categories_list[] = [
                'parent' => $parent,
                'children' => $children
            ];
        }
        }
      

        

        // return $result;
        $country['categories_list'] = $categories_list;
        unset($country['categories']);
        unset($country['categories_list_packages']);
        unset($country['top_sights']);
        unset($country['fetched_categories']);
        unset($country['itineraries_blogs']);
        unset($country['activities_content']);
        unset($country['packages_content']);
        unset($country['thumbnail_name']);
        unset($country['extra_information']);
        unset($country['featured_blogs']); 
        unset($country['order_by']);
        unset($country['destination_type']);
        unset($country['languages']);
        unset($country['area']);
        unset($country['booking_count']);
        unset($country['number_packages']);
        // $country['itineraries_blogs']=unserialize($country['itineraries_blogs']);
        // $country['extra_information']=unserialize($country['extra_information']);    
        // $country['featured_blogs']=unserialize($country['featured_blogs']);
        // $country['order_by']=$this->deepDecodeFooter($country['order_by']);

        $country['google_map'] = !empty($country['google_map'])
        ? unserialize($country['google_map'])
        : '';
        
        $country['price_info']=!empty($country['price_info'])
        ? unserialize($country['price_info'])
        : '';
      
        $country['currency']=!empty($country['currency'])? AdminHelper::decodeLangString($country['currency']):'';
        $country['meta_title']=!empty($country['meta_title']) ? AdminHelper::decodeLangString($country['meta_title']):'';
        $country['meta_description']=!empty($country['meta_description']) ?AdminHelper::decodeLangString($country['meta_description']):'';
        $country['meta_keywords']=!empty($country['meta_keywords']) ?   AdminHelper::decodeLangString($country['meta_keywords']):'';
        $country['meta_title_activities']=!empty($country['meta_title_activities']) ?   AdminHelper::decodeLangString($country['meta_title_activities']): '';
        $country['meta_description_activities']=!empty($country['meta_description_activities']) ?   AdminHelper::decodeLangString($country['meta_description_activities']):'';
        $country['meta_title_packages']=!empty($country['meta_title_packages']) ?   AdminHelper::decodeLangString($country['meta_title_packages']):'';
        $country['meta_title_hotels']=!empty($country['meta_title_hotels']) ?   AdminHelper::decodeLangString($country['meta_title_hotels']) : '';
        $country['meta_description_packages']=!empty($country['meta_description_packages']) ? AdminHelper::decodeLangString($country['meta_description_packages']):'';
        $country['meta_description_hotels']=!empty($country['meta_description_hotels']) ? AdminHelper::decodeLangString($country['meta_description_hotels']):'';
        $country['footer_links']=!empty($country['footer_links'])? AdminHelper::deepUnserializeCategiry_List($country['footer_links']):'    ';
        $country['banners']=!empty($country['banners'])? AdminHelper::deepUnserializeCategiry_List($country['banners']):'';
        $country['footers']=!empty($country['footers'])? AdminHelper::deepDecodeFooter($country['footers']):'';

        return response()->json(['country' => $country], 200);

    }
    public function getAllCountries()
    {
        try {
            $countries = \App\Models\Country::paginate(10);

            $countries->transform(function ($country) {
        if (!empty($country->faq)) {
            $country->faq = AdminHelper::deepDecodeFAQ($country->faq);
        }
        if (!empty($country->banner_alt_text)) {
                    $country->banner_alt_text = AdminHelper::deepDecodeFooter($country->banner_alt_text);
                }

        if (!empty($country->footers)) {
            $country->footers = AdminHelper::deepDecodeFooter($country->footers);
        }
        if (!empty($country->price_info)) {
            $country->price_info = unserialize($country->price_info);
        }
        if (!empty($country->google_map)) {
            $country->google_map = unserialize($country->google_map);
        }
        if (!empty($country->currency)) {
            
            $country->currency = AdminHelper::decodeLangString($country->currency);
        }
        if (!empty($country->meta_title)) {
        $country['meta_title']=AdminHelper::decodeLangString($country['meta_title']);
        }
        if (!empty($country->meta_description)) {
            $country['meta_description']=AdminHelper::decodeLangString($country['meta_description']);
        }
        if (!empty($country->meta_keywords)) {
            $country['meta_keywords']=AdminHelper::decodeLangString($country['meta_keywords']);
        }
        if (!empty($country->meta_title_activities)) {
            $country['meta_title_activities']=AdminHelper::decodeLangString($country['meta_title_activities']);
        }
        if (!empty($country->meta_description_activities)) {
            $country['meta_description_activities']=AdminHelper::decodeLangString($country['meta_description_activities']);
        }
        if (!empty($country->meta_keywords_activities)) {
            $country['meta_keywords_activities']=AdminHelper::decodeLangString($country['meta_keywords_activities']);
        }
        if (!empty($country->meta_title_packages)) {
            $country['meta_title_packages']=AdminHelper::decodeLangString($country['meta_title_packages']);
        }
        if (!empty($country->meta_description_packages)) {
            $country['meta_description_packages']=AdminHelper::decodeLangString($country['meta_description_packages']);
        }
        if (!empty($country->meta_keywords_packages)) {
            $country['meta_keywords_packages']=AdminHelper::decodeLangString($country['meta_keywords_packages']);
        }
        if (!empty($country->meta_title_hotels)) {
            $country['meta_title_hotels']=AdminHelper::decodeLangString($country['meta_title_hotels']);
        }
        if (!empty($country->meta_description_hotels)) {
            $country['meta_description_hotels']=AdminHelper::decodeLangString($country['meta_description_hotels']);

        }    
        if (!empty($country->footer_links)) {
            $country['footer_links']=AdminHelper::deepUnserializeCategiry_List($country['footer_links']);
        }
        if (!empty($country->banners)) {
            $country['banners']=AdminHelper::deepUnserializeCategiry_List($country['banners']);
        }
        if (!empty($country->footers)) {
            $country['footers']=AdminHelper::deepDecodeFooter($country['footers']);
        }

        

        if (!empty($country->categories_list)) {
            $data = AdminHelper::deepUnserializeCategiry_List($country->categories_list);

            $categories_list = [];

            foreach ($data as $parentId => $childIds) {
                $parent = DB::table('wp_packages_main_category')
                    ->where('id', $parentId)
                    ->select('id', 'name')
                    ->first();

                if (!$parent) continue;

                $children = [];
                if (is_array($childIds) && count($childIds)) {
                    $children = DB::table('wp_adventure_type')
                        ->whereIn('id', $childIds)
                        ->select('id', 'name')
                        ->get();
                }

                $categories_list[] = [
                    'parent' => $parent,
                    'children' => $children
                ];
            }

            // هنا تخزن النتيجة جوة الكاونتري
            $country->categories_list = $categories_list;
        }
            unset($country['categories']);
            unset($country['categories_list_packages']);
            unset($country['top_sights']);
            unset($country['fetched_categories']);
            unset($country['itineraries_blogs']);
            unset($country['activities_content']);
            unset($country['packages_content']);
            unset($country['thumbnail_name']);
            unset($country['extra_information']);
            unset($country['featured_blogs']); 
            unset($country['order_by']);
            unset($country['destination_type']);
            unset($country['languages']);
            unset($country['number_packages']);
        return $country;
        });


            return response()->json(['countries' => $countries], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Something went wrong!',
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function addNewCountry(array $data)
    {   
        try 
        {
            /****************************************************************************************************************
             * 
             * 
             * ****************************************************************************************************************
             *
             *1- Banner Image DONE  && Banner_Alt_TEXT banner_alt_text_en+banner_alt_text_ar  [$banner_alt_text,$banner_path]
            *2- FAQ DONE $faq_serialized
            *3- Categories_List Done  $Categories_serialized
            *4- Name english and ar DONE $name and $name_ar
            *5- Title english and ar DONE $title and $title_ar
            *6- continent DONE $continent //	0 = Asia | 1 = Europe | 2 = Africa | 3 = America | 4 = Oceania
            *7- country_code DONE $country_code
            *8- timezone DONE $timezone
            *9- whatsapp_number DONE $whatsapp_number	
            *10- google_map DONE $google_map
            *11- currency DONE $currency

            *13- is_gcc DONE $is_gcc
            *14- slug DONE $slug
            *15- search_query DONE $search_query
            *16- search_query_ar DONE $search_query_ar
            *17- meta_title DONE $meta_title
            *18- meta_description DONE $meta_description
            *19- meta_title_activities DONE $meta_title_activities
            *20- meta_description_activities DONE $meta_description_activities
            *21- meta_title_packages DONE $meta_title_packages
            *22- meta_description_packages DONE $meta_description_packages
            *23- meta_title_hotels DONE $meta_title_hotels
            *24- meta_description_hotels DONE $meta_description_hotels
            *25- status DONE $status
            *26- Footers DONE $Footers_encoded
            *
            ***************************************************************************************************************** 
            * 
            */
            $name=$data['name'] ?? null;
            $name_ar=$data['name_ar']?? null;
            $title=$data['title'] ?? null;
            $title_ar=$data['title_ar'] ?? null;
            $continent=$data['continent'] ?? null;
            $country_code=$data['country_code'] ?? null;
            $timezone=$data['timezone'] ?? null;
            $whatsapp_number=$data['whatsapp_number'] ?? null;
            $google_map = serialize([
                'location'  => $data['location'] ?? null,  
                'latitude'  => $data['latitude'] ?? null,  
                'longitude' => $data['longitude'] ?? null   
            ]);        
            $currency = "[en]" . ($data['currency_en'] ?? '') . "[en]"
            . "[ar]" . ($data['currency_ar'] ?? '') . "[ar]";

            $is_gcc=$data['is_gcc'] ?? null;
            $slug=$data['slug'] ?? null;
            $search_query=$data['search_query'] ?? null;
            $search_query_ar=$data['search_query_ar']     ?? null;
            $meta_title=$data['meta_title'] ?? null;
            $meta_description=$data['meta_description'] ?? null;
            $meta_title_activities=$data['meta_title_activities'] ?? null;
            $meta_description_activities=$data['meta_description_activities'] ?? null;
            $meta_title_packages=$data['meta_title_packages'] ?? null;
            $meta_description_packages=$data['meta_description_packages'] ?? null;
            $meta_title_hotels=$data['meta_title_hotels'] ?? null;
            $meta_description_hotels=$data['meta_description_hotels'] ?? null;

            $status=$data['status'] ?? 0;







            /**
             * Start Section of Banner Image and Alt Text
             */

            if (isset($data['banner_image']) && $data['banner_image'] instanceof \Illuminate\Http\UploadedFile) 
                {

                    $year = date('Y');   
                    $month = date('m'); 

                    $folderPath = public_path("wp-content/uploads/{$year}/{$month}");

                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }

                    $imageName = time().'_'.uniqid().'.'.$data['banner_image']->extension();

                    $data['banner_image']->move($folderPath, $imageName);

                    $banner_path = "wp-content/uploads/{$year}/{$month}/{$imageName}";
                    $banner_alt_text = "[en]".$data['banner_alt_text_en']."[en]"."[ar]".$data['banner_alt_text_ar']. "[ar]";
                


                }

                /**
                 * END Section of Banner Image and Alt Text
                 */
            
                /**
                 * Start Section of Banners 
                 */
                if (isset($data['banners_activities_main_banner']) && $data['banners_activities_main_banner'] instanceof \Illuminate\Http\UploadedFile ) 
                {

                    $year = date('Y');   
                    $month = date('m'); 

                    $folderPath = public_path("wp-content/uploads/{$year}/{$month}");

                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }

                    $imageName = time().'_'.uniqid().'.'.$data['banners_activities_main_banner']->extension();

                    $data['banners_activities_main_banner']->move($folderPath, $imageName);

                    $activities_main_banner_path = "wp-content/uploads/{$year}/{$month}/{$imageName}";
                    $banners_activities_main_banner = $activities_main_banner_path;
                    $banners_activities_carousel_banner = $data['banners_activities_carousel_banner'] ?? null;
                    $banners_activities_video_banner = $data['banners_activities_video_banner'] ?? null;
                    $banners_activities_main_banner_alt_en = $data['banners_activities_main_banner_alt_en'];
                    $banners_activities_main_banner_alt_ar = $data['banners_activities_main_banner_alt_ar'];
                    $banners_activities_carousel_banner_alt_en = $data['banners_activities_carousel_banner_alt_en'] ?? null;
                    $banners_activities_carousel_banner_alt_ar = $data['banners_activities_carousel_banner_alt_ar']?? null;


                }

                if (isset($data['banners_restaurants_main_banner'] ) && $data['banners_restaurants_main_banner'] instanceof \Illuminate\Http\UploadedFile )
                {

                    $year = date('Y');   
                    $month = date('m'); 

                    $folderPath = public_path("wp-content/uploads/{$year}/{$month}");

                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }

                    $imageName = time().'_'.uniqid().'.'.$data['banners_restaurants_main_banner']->extension();

                    $data['banners_restaurants_main_banner']->move($folderPath, $imageName);

                    $banner_restaurants_path = "wp-content/uploads/{$year}/{$month}/{$imageName}";
                    $banners_restaurants_main_banner =  $banner_restaurants_path;
                    $banners_restaurants_carousel_banner = $data['banners_restaurants_carousel_banner'] ?? null;
                    $banners_restaurants_video_banner = $data['banners_restaurants_video_banner'] ?? null;
                    $banners_restaurants_main_banner_alt_en = $data['banners_restaurants_main_banner_alt_en'];
                    $banners_restaurants_main_banner_alt_ar = $data['banners_restaurants_main_banner_alt_ar'];
                    $banners_restaurants_carousel_banner_alt_en = $data['banners_restaurants_carousel_banner_alt_en'] ?? null;
                    $banners_restaurants_carousel_banner_alt_ar = $data['banners_restaurants_carousel_banner_alt_ar'] ?? null;
                }

                if (isset($data['banners_main_main_banner'] ) && $data['banners_main_main_banner'] instanceof \Illuminate\Http\UploadedFile ) 
                {


                    $year = date('Y');   
                    $month = date('m'); 

                    $folderPath = public_path("wp-content/uploads/{$year}/{$month}");

                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }

                    $imageName = time().'_'.uniqid().'.'.$data['banners_main_main_banner']->extension();

                    $data['banners_main_main_banner']->move($folderPath, $imageName);

                    $banner_main_path = "wp-content/uploads/{$year}/{$month}/{$imageName}";
                    $banners_main_main_banner =  $banner_main_path;
                    $banners_main_carousel_banner = $data['banners_main_carousel_banner'] ?? null;
                    $banners_main_video_banner = $data['banners_main_video_banner'] ?? null;
                    $banners_main_main_banner_alt_en = $data['banners_main_main_banner_alt_en'];
                    $banners_main_main_banner_alt_ar = $data['banners_main_main_banner_alt_ar'];
                    $banners_main_carousel_banner_alt_en = $data['banners_main_carousel_banner_alt_en'] ?? null;
                    $banners_main_carousel_banner_alt_ar = $data['banners_main_carousel_banner_alt_ar'] ?? null;
                }







                $banners = [
                    "activities" => [   
                        "main_banner"         => $banners_activities_main_banner ?? null,
                        "carousel_banner"     => $banners_activities_carousel_banner ?? null,
                        "video_banner"        => $banners_activities_video_banner ?? null,
                        "main_banner_alt"     => "[en]".($banners_activities_main_banner_alt_en ?? null)."[en]"."[ar]".($banners_activities_main_banner_alt_ar ?? null)."[ar]",
                        "carousel_banner_alt" => "[en]".($banners_activities_carousel_banner_alt_en ?? null)."[en][ar]".($banners_activities_carousel_banner_alt_ar?? null)."[ar]",
                    ],

                    "restaurants" => [
                        "main_banner"         => $banners_restaurants_main_banner ?? null,
                        "carousel_banner"     => $banners_restaurants_carousel_banner ?? null,
                        "video_banner"        => $banners_restaurants_video_banner ?? null,
                        "main_banner_alt"     => "[en]".($banners_restaurants_main_banner_alt_en ?? null)."[en][ar]".($banners_restaurants_main_banner_alt_ar ?? null)."[ar]",
                        "carousel_banner_alt" => "[en]".($banners_restaurants_carousel_banner_alt_en ?? null)."[en][ar]".($banners_restaurants_carousel_banner_alt_ar ?? null)."[ar]",
                    ],

                    "main" => [
                        "main_banner"         => $banners_main_main_banner ?? null,
                        "carousel_banner"     => $banners_main_carousel_banner ?? null,
                        "video_banner"        => $banners_main_video_banner ?? null,
                        "main_banner_alt"     => "[en]".base64_encode($banners_main_main_banner_alt_en ?? null)."[en][ar]".base64_encode($banners_main_main_banner_alt_ar ?? null)."[ar]",
                        "carousel_banner_alt" => "[en]".base64_encode($banners_main_carousel_banner_alt_en ?? null)."[en][ar]".base64_encode($banners_main_carousel_banner_alt_ar?? null)."[ar]",
                    ]
                ];

                $serializedBanners = serialize($banners);




            /***
                 * 
                 * Start Section OF FAQ 
                 * 
                 */
                $packages_question_text_en = $data['packages_question_text_en'] ?? [];
                $packages_question_text_ar = $data['packages_question_text_ar'] ?? [];
                $packages_answer_text_en   = $data['packages_answer_text_en'] ?? [];
                $packages_answer_text_ar   = $data['packages_answer_text_ar'] ?? [];


                $main_question_text_en = $data['main_question_text_en'] ?? [];
                $main_question_text_ar = $data['main_question_text_ar'] ?? [];
                $main_answer_text_en   = $data['main_answer_text_en'] ?? [];
                $main_answer_text_ar   = $data['main_answer_text_ar'] ?? [];

                $activities_question_text_en = $data['activities_question_text_en'] ?? [];
                $activities_question_text_ar = $data['activities_question_text_ar'] ?? [];
                $activities_answer_text_en   = $data['activities_answer_text_en'] ?? [];
                $activities_answer_text_ar   = $data['activities_answer_text_ar'] ?? [];

                $main_questions_final =    AdminHelper::wrapLang($main_question_text_en, $main_question_text_ar);
                // encode Main answers
                $main_answers_final   = AdminHelper::wrapLang($main_answer_text_en, $main_answer_text_ar);
                // encode packages questions
                $packages_questions_final = AdminHelper::wrapLang($packages_question_text_en, $packages_question_text_ar);

                // encode Packages answers
                $packages_answers_final   = AdminHelper::wrapLang($packages_answer_text_en, $packages_answer_text_ar);
                // encode Activities questions
                $activities_questions_final = AdminHelper::wrapLang($activities_question_text_en, $activities_question_text_ar);
                // encode Activities answers
                $activities_answers_final   =AdminHelper::wrapLang($activities_answer_text_en, $activities_answer_text_ar);
                // final structure to save and serialize
                $faq = [
                    "activities" => [   
                        "questions" => $activities_questions_final,
                        "answers"   => $activities_answers_final,
                    ],

                    "main" => [
                        "questions" => $main_questions_final,
                        "answers"   => $main_answers_final,
                    ],

                    "packages" => [
                        "questions" => $packages_questions_final,
                        "answers"   => $packages_answers_final,
                    ]
                ];

                    // serialize the whole structure
                    $faq_serialized = serialize($faq); // Final serialize the whole structure

                    /***
                     * 
                     * END SECTION OF FAQ
                     */



                        /***
                         * 
                         * Start Section OF Categories LIST 
                         * 
                         */
                $categories_list = [];

                if (isset($data['categories_list'] ) && is_array($data['categories_list'])) 
                    {
                        foreach ($data['categories_list'] as $parentId => $childIds) 
                            {
                            // اتأكد ان الـ parent موجود
                            $parentExists = DB::table('wp_packages_main_category')
                                ->where('id', $parentId)
                                ->exists();

                            if (!$parentExists) {
                                continue;
                            }

                            // هات الـ children اللي فعلاً تبع الـ parent
                            $validChildren = DB::table('wp_adventure_type')
                                ->where('category_id', $parentId)
                                ->whereIn('id', $childIds)
                                ->pluck('id')
                                ->toArray();

                            if (empty($validChildren)) {
                                continue;
                            }

                            // خزنهم بنفس الشكل المطلوب (id => "id")
                            $childrenArray = [];
                            foreach ($validChildren as $childId) {
                                $childrenArray[$childId] = (string) $childId;
                            }

                            $categories_list[$parentId] = $childrenArray;
                            }
                    }

            $Categories_serialized = serialize($categories_list); //Final Serialized to DB of Categories List
        
            /***
             * 
             * END SECTION OF Categories LIST 
             */




            $footers_activities_titles_final = AdminHelper::wrapLang(
                $data['footers_activities_titles_en'] ?? '',
                $data['footers_activities_titles_ar'] ?? ''
            );

            $footers_activities_anchor_texts_final = AdminHelper::wrapLang(
                $data['footers_activities_anchor_texts_en'] ?? [],
                $data['footers_activities_anchor_texts_ar'] ?? []
            );

            $footers_activities_anchor_links_final = AdminHelper::wrapLang(
                $data['footers_activities_anchor_links_en'] ?? [],
                $data['footers_activities_anchor_links_ar'] ?? []
            );
            // For Resturants Footers

            $footers_restaurants_titles_final = AdminHelper::wrapLang(
                $data['footers_restaurants_titles_en'] ?? '',
                $data['footers_restaurants_titles_ar'] ?? ''
            );

            $footers_restaurants_anchor_texts_final = AdminHelper::wrapLang(
                $data['footers_restaurants_anchor_texts_en'] ?? [],
                $data['footers_restaurants_anchor_texts_ar'] ?? []
            );

            $footers_restaurants_anchor_links_final = AdminHelper::wrapLang(
                $data['footers_restaurants_anchor_links_en'] ?? [],
                $data['footers_restaurants_anchor_links_ar'] ?? []
            );

            // For Main Footers

            $footers_main_titles_final = AdminHelper::wrapLang(
                $data['footers_main_titles_en'] ?? '',
                $data['footers_main_titles_ar'] ?? ''
            );

            $footers_main_anchor_texts_final = AdminHelper::wrapLang(
                $data['footers_main_anchor_texts_en'] ?? [],
                $data['footers_main_anchor_texts_ar'] ?? []
            );

            $footers_main_anchor_links_final = AdminHelper::wrapLang(
                $data['footers_main_anchor_links_en'] ?? [],
                $data['footers_main_anchor_links_ar'] ?? []
            );





            $footers = [
                "activities" => [   
                    "footer_titles" => $footers_activities_titles_final,
                    "anchor_texts"  => $footers_activities_anchor_texts_final,
                    "anchor_links"  => $footers_activities_anchor_links_final,
                ],
                "restaurants" => [
                    "footer_titles" => $footers_restaurants_titles_final,
                    "anchor_texts"  => $footers_restaurants_anchor_texts_final,
                    "anchor_links"  => $footers_restaurants_anchor_links_final,
                        ],

                "main" => [
                    "footer_titles" => $footers_main_titles_final,
                    "anchor_texts"  => $footers_main_anchor_texts_final,
                    "anchor_links"  => $footers_main_anchor_links_final,
                        ]
            ];


            $Footers_encoded = serialize($footers);

            $country=new country();
            $country->name = $name;
            $country->name_ar = $name_ar;
            $country->title=$title;
            $country->title_ar=$title_ar;
            $country->continent=$continent;
            $country->country_code=$country_code;
            $country->timezone=$timezone;
            $country->whatsapp_number=$whatsapp_number;
            if(!empty($banner_path)
                )
            {
            $country->banner=$banner_path;

            }
            $country->banner_alt_text=$banner_alt_text??null;
            $country->faq=$faq_serialized;
            $country->categories_list=$Categories_serialized;
            $country->google_map=$google_map;
            $country->currency=$currency;
            $country->is_gcc=$is_gcc;
            $country->slug=$slug;
            $country->search_query=$search_query;$country->search_query_ar=$search_query_ar;
            $country->meta_title=$meta_title;$country->meta_description=$meta_description;
            $country->meta_title_activities=$meta_title_activities;
            $country->meta_description_activities=$meta_description_activities;
            $country->meta_title_packages=$meta_title_packages;
            $country->meta_description_packages=$meta_description_packages;
            $country->meta_title_hotels=$meta_title_hotels;
            $country->meta_description_hotels=$meta_description_hotels;
            $country->status=$status;
            $country->banners=$serializedBanners;
            $country->footers=$Footers_encoded;





            $country->save();

                return response()->json(['message' => 'Country added successfully', 'country' => $country], 201);
        } 
        catch (\Exception $e) {
                return $e;
            }
    }
    public function updateCountry(int $id, array $data)
    {
        try {
            $country = Country::where('id', $id)->first();
            
            if (!$country) {
                throw new \Exception("Country not found");
            }

            $name        = $data['name'] ?? $country->name;
        
            $name_ar     = $data['name_ar'] ?? $country->name_ar;
            $title       = $data['title'] ?? $country->title;
            $title_ar    = $data['title_ar'] ?? $country->title_ar;
            $continent   = $data['continent'] ?? $country->continent;
            $country_code= $data['country_code'] ?? $country->country_code;
            $timezone    = $data['timezone'] ?? $country->timezone;
            $whatsapp_number = $data['whatsapp_number'] ?? $country->whatsapp_number;

            $google_map = serialize([
                'location'  => $data['location'] ?? unserialize($country->google_map)['location'] ?? null,  
                'latitude'  => $data['latitude'] ?? unserialize($country->google_map)['latitude'] ?? null,  
                'longitude' => $data['longitude'] ?? unserialize($country->google_map)['longitude'] ?? null   
            ]);

            $currency = "[en]" . ($data['currency_en'] ?? '') . "[en]"
                    . "[ar]" . ($data['currency_ar'] ?? '') . "[ar]";

            $status = $data['status'] ?? $country->status;

            /**
             * Banner image update (optional)
             */
            $banner_path = $country->banner_image ?? null;
            $banner_alt_text = $country->banner_alt_text ?? null;

            if (isset($data['banner_image']) && $data['banner_image'] instanceof \Illuminate\Http\UploadedFile) 
                {
                $year = date('Y');   
                $month = date('m'); 
                $folderPath = public_path("wp-content/uploads/{$year}/{$month}");

                if (!file_exists($folderPath)) {
                    mkdir($folderPath, 0777, true);
                }

                $imageName = time().'_'.uniqid().'.'.$data['banner_image']->extension();
                $data['banner_image']->move($folderPath, $imageName);

                $banner_path = "wp-content/uploads/{$year}/{$month}/{$imageName}";
                $banner_alt_text = "[en]".($data['banner_alt_text_en'] ?? '')."[en]"
                                ."[ar]".($data['banner_alt_text_ar'] ?? '')."[ar]";
                }


        /**
                     * Start Section of Banners 
                     */
                    if (isset($data['banners_activities_main_banner']) && $data['banners_activities_main_banner'] instanceof \Illuminate\Http\UploadedFile ) 
                    {

                        $year = date('Y');   
                        $month = date('m'); 

                        $folderPath = public_path("wp-content/uploads/{$year}/{$month}");

                        if (!file_exists($folderPath)) {
                            mkdir($folderPath, 0777, true);
                        }

                        $imageName = time().'_'.uniqid().'.'.$data['banners_activities_main_banner']->extension();

                        $data['banners_activities_main_banner']->move($folderPath, $imageName);

                        $activities_main_banner_path = "wp-content/uploads/{$year}/{$month}/{$imageName}";
                        $banners_activities_main_banner = $activities_main_banner_path;
                        $banners_activities_carousel_banner = $data['banners_activities_carousel_banner'] ?? null;
                        $banners_activities_video_banner = $data['banners_activities_video_banner'] ?? null;
                        $banners_activities_main_banner_alt_en = $data['banners_activities_main_banner_alt_en'];
                        $banners_activities_main_banner_alt_ar = $data['banners_activities_main_banner_alt_ar'];
                        $banners_activities_carousel_banner_alt_en = $data['banners_activities_carousel_banner_alt_en'] ?? null;
                        $banners_activities_carousel_banner_alt_ar = $data['banners_activities_carousel_banner_alt_ar']?? null;


                    }

                    if (isset($data['banners_restaurants_main_banner'] ) && $data['banners_restaurants_main_banner'] instanceof \Illuminate\Http\UploadedFile )
                        {

                            $year = date('Y');   
                            $month = date('m'); 

                            $folderPath = public_path("wp-content/uploads/{$year}/{$month}");

                            if (!file_exists($folderPath)) {
                                mkdir($folderPath, 0777, true);
                            }

                            $imageName = time().'_'.uniqid().'.'.$data['banners_restaurants_main_banner']->extension();

                            $data['banners_restaurants_main_banner']->move($folderPath, $imageName);

                            $banner_restaurants_path = "wp-content/uploads/{$year}/{$month}/{$imageName}";
                            $banners_restaurants_main_banner =  $banner_restaurants_path;
                            $banners_restaurants_carousel_banner = $data['banners_restaurants_carousel_banner'] ?? null;
                            $banners_restaurants_video_banner = $data['banners_restaurants_video_banner'] ?? null;
                            $banners_restaurants_main_banner_alt_en = $data['banners_restaurants_main_banner_alt_en'];
                            $banners_restaurants_main_banner_alt_ar = $data['banners_restaurants_main_banner_alt_ar'];
                            $banners_restaurants_carousel_banner_alt_en = $data['banners_restaurants_carousel_banner_alt_en'] ?? null;
                            $banners_restaurants_carousel_banner_alt_ar = $data['banners_restaurants_carousel_banner_alt_ar'] ?? null;
                        }

                    if (isset($data['banners_main_main_banner'] ) && $data['banners_main_main_banner'] instanceof \Illuminate\Http\UploadedFile ) 
                        {


                            $year = date('Y');   
                            $month = date('m'); 

                            $folderPath = public_path("wp-content/uploads/{$year}/{$month}");

                            if (!file_exists($folderPath)) {
                                mkdir($folderPath, 0777, true);
                            }

                            $imageName = time().'_'.uniqid().'.'.$data['banners_main_main_banner']->extension();

                            $data['banners_main_main_banner']->move($folderPath, $imageName);

                            $banner_main_path = "wp-content/uploads/{$year}/{$month}/{$imageName}";
                            $banners_main_main_banner =  $banner_main_path;
                            $banners_main_carousel_banner = $data['banners_main_carousel_banner'] ?? null;
                            $banners_main_video_banner = $data['banners_main_video_banner'] ?? null;
                            $banners_main_main_banner_alt_en = $data['banners_main_main_banner_alt_en'];
                            $banners_main_main_banner_alt_ar = $data['banners_main_main_banner_alt_ar'];
                            $banners_main_carousel_banner_alt_en = $data['banners_main_carousel_banner_alt_en'] ?? null;
                            $banners_main_carousel_banner_alt_ar = $data['banners_main_carousel_banner_alt_ar'] ?? null;
                        }







                    $banners = [
                        "activities" => [   
                            "main_banner"         => $banners_activities_main_banner ?? null,
                            "carousel_banner"     => $banners_activities_carousel_banner ?? null,
                            "video_banner"        => $banners_activities_video_banner ?? null,
                            "main_banner_alt"     => "[en]".($banners_activities_main_banner_alt_en ?? null)."[en]"."[ar]".($banners_activities_main_banner_alt_ar ?? null)."[ar]",
                            "carousel_banner_alt" => "[en]".($banners_activities_carousel_banner_alt_en ?? null)."[en][ar]".($banners_activities_carousel_banner_alt_ar?? null)."[ar]",
                        ],

                        "restaurants" => [
                            "main_banner"         => $banners_restaurants_main_banner ?? null,
                            "carousel_banner"     => $banners_restaurants_carousel_banner ?? null,
                            "video_banner"        => $banners_restaurants_video_banner ?? null,
                            "main_banner_alt"     => "[en]".($banners_restaurants_main_banner_alt_en ?? null)."[en][ar]".($banners_restaurants_main_banner_alt_ar ?? null)."[ar]",
                            "carousel_banner_alt" => "[en]".($banners_restaurants_carousel_banner_alt_en ?? null)."[en][ar]".($banners_restaurants_carousel_banner_alt_ar ?? null)."[ar]",
                        ],

                        "main" => [
                            "main_banner"         => $banners_main_main_banner ?? null,
                            "carousel_banner"     => $banners_main_carousel_banner ?? null,
                            "video_banner"        => $banners_main_video_banner ?? null,
                            "main_banner_alt"     => "[en]".base64_encode($banners_main_main_banner_alt_en ?? null)."[en][ar]".base64_encode($banners_main_main_banner_alt_ar ?? null)."[ar]",
                            "carousel_banner_alt" => "[en]".base64_encode($banners_main_carousel_banner_alt_en ?? null)."[en][ar]".base64_encode($banners_main_carousel_banner_alt_ar?? null)."[ar]",
                        ]
                    ];

                    $serializedBanners = serialize($banners);




                /***
                     * 
                     * Start Section OF FAQ 
                     * 
                     */
                    $packages_question_text_en = $data['packages_question_text_en'] ?? [];
                    $packages_question_text_ar = $data['packages_question_text_ar'] ?? [];
                    $packages_answer_text_en   = $data['packages_answer_text_en'] ?? [];
                    $packages_answer_text_ar   = $data['packages_answer_text_ar'] ?? [];


                    $main_question_text_en = $data['main_question_text_en'] ?? [];
                    $main_question_text_ar = $data['main_question_text_ar'] ?? [];
                    $main_answer_text_en   = $data['main_answer_text_en'] ?? [];
                    $main_answer_text_ar   = $data['main_answer_text_ar'] ?? [];

                    $activities_question_text_en = $data['activities_question_text_en'] ?? [];
                    $activities_question_text_ar = $data['activities_question_text_ar'] ?? [];
                    $activities_answer_text_en   = $data['activities_answer_text_en'] ?? [];
                    $activities_answer_text_ar   = $data['activities_answer_text_ar'] ?? [];

                    $main_questions_final =    AdminHelper::wrapLang($main_question_text_en, $main_question_text_ar);
                    // encode Main answers
                    $main_answers_final   = AdminHelper::wrapLang($main_answer_text_en, $main_answer_text_ar);
                    // encode packages questions
                    $packages_questions_final = AdminHelper::wrapLang($packages_question_text_en, $packages_question_text_ar);

                    // encode Packages answers
                    $packages_answers_final   = AdminHelper::wrapLang($packages_answer_text_en, $packages_answer_text_ar);
                    // encode Activities questions
                    $activities_questions_final = AdminHelper::wrapLang($activities_question_text_en, $activities_question_text_ar);
                    // encode Activities answers
                    $activities_answers_final   =AdminHelper::wrapLang($activities_answer_text_en, $activities_answer_text_ar);
                    // final structure to save and serialize
                    $faq = [
                        "activities" => [   
                            "questions" => $activities_questions_final,
                            "answers"   => $activities_answers_final,
                        ],

                        "main" => [
                            "questions" => $main_questions_final,
                            "answers"   => $main_answers_final,
                        ],

                        "packages" => [
                            "questions" => $packages_questions_final,
                            "answers"   => $packages_answers_final,
                        ]
                    ];

                        // serialize the whole structure
                        $faq_serialized = serialize($faq); // Final serialize the whole structure

                        /***
                         * 
                         * END SECTION OF FAQ
                         */



                            /***
                             * 
                             * Start Section OF Categories LIST 
                             * 
                             */
                    $categories_list = [];

                    if (isset($data['categories_list'] ) && is_array($data['categories_list'])) 
                        {
                            foreach ($data['categories_list'] as $parentId => $childIds) 
                                {
                                // اتأكد ان الـ parent موجود
                                $parentExists = DB::table('wp_packages_main_category')
                                    ->where('id', $parentId)
                                    ->exists();

                                if (!$parentExists) {
                                    continue;
                                }

                                // هات الـ children اللي فعلاً تبع الـ parent
                                $validChildren = DB::table('wp_adventure_type')
                                    ->where('category_id', $parentId)
                                    ->whereIn('id', $childIds)
                                    ->pluck('id')
                                    ->toArray();

                                if (empty($validChildren)) {
                                    continue;
                                }

                                // خزنهم بنفس الشكل المطلوب (id => "id")
                                $childrenArray = [];
                                foreach ($validChildren as $childId) {
                                    $childrenArray[$childId] = (string) $childId;
                                }

                                $categories_list[$parentId] = $childrenArray;
                                }
                        }

                $Categories_serialized = serialize($categories_list); //Final Serialized to DB of Categories List
            
                /***
                 * 
                 * END SECTION OF Categories LIST 
                 */




                $footers_activities_titles_final = AdminHelper::wrapLang(
                    $data['footers_activities_titles_en'] ?? '',
                    $data['footers_activities_titles_ar'] ?? ''
                );

                $footers_activities_anchor_texts_final = AdminHelper::wrapLang(
                    $data['footers_activities_anchor_texts_en'] ?? [],
                    $data['footers_activities_anchor_texts_ar'] ?? []
                );

                $footers_activities_anchor_links_final = AdminHelper::wrapLang(
                    $data['footers_activities_anchor_links_en'] ?? [],
                    $data['footers_activities_anchor_links_ar'] ?? []
                );
                // For Resturants Footers

                $footers_restaurants_titles_final = AdminHelper::wrapLang(
                    $data['footers_restaurants_titles_en'] ?? '',
                    $data['footers_restaurants_titles_ar'] ?? ''
                );

                $footers_restaurants_anchor_texts_final = AdminHelper::wrapLang(
                    $data['footers_restaurants_anchor_texts_en'] ?? [],
                    $data['footers_restaurants_anchor_texts_ar'] ?? []
                );

                $footers_restaurants_anchor_links_final = AdminHelper::wrapLang(
                    $data['footers_restaurants_anchor_links_en'] ?? [],
                    $data['footers_restaurants_anchor_links_ar'] ?? []
                );

                // For Main Footers

                $footers_main_titles_final = AdminHelper::wrapLang(
                    $data['footers_main_titles_en'] ?? '',
                    $data['footers_main_titles_ar'] ?? ''
                );

                $footers_main_anchor_texts_final = AdminHelper::wrapLang(
                    $data['footers_main_anchor_texts_en'] ?? [],
                    $data['footers_main_anchor_texts_ar'] ?? []
                );

                $footers_main_anchor_links_final = AdminHelper::wrapLang(
                    $data['footers_main_anchor_links_en'] ?? [],
                    $data['footers_main_anchor_links_ar'] ?? []
                );





                $footers = [
                    "activities" => [   
                        "footer_titles" => $footers_activities_titles_final,
                        "anchor_texts"  => $footers_activities_anchor_texts_final,
                        "anchor_links"  => $footers_activities_anchor_links_final,
                    ],
                    "restaurants" => [
                        "footer_titles" => $footers_restaurants_titles_final,
                        "anchor_texts"  => $footers_restaurants_anchor_texts_final,
                        "anchor_links"  => $footers_restaurants_anchor_links_final,
                            ],

                    "main" => [
                        "footer_titles" => $footers_main_titles_final,
                        "anchor_texts"  => $footers_main_anchor_texts_final,
                        "anchor_links"  => $footers_main_anchor_links_final,
                            ]
                ];



            country::where('id', $id)->update([
                'name'            => $name,
                'name_ar'         => $name_ar,
                'title'           => $title,
                'title_ar'        => $title_ar,
                'continent'       => $continent,
                'country_code'    => $country_code,
                'timezone'        => $timezone,
                'whatsapp_number' => $whatsapp_number,
                'google_map'      => $google_map,
                'currency'        => $currency,
                'status'          => $status,
                'banner'    => $banner_path,
                'banner_alt_text' => $banner_alt_text,
            // 'banners'         => !empty($serializedBanners) ? $serializedBanners : $country->banners,
                'faq'             => !empty($faq_serialized) ? $faq_serialized : $country->faq,
                'categories_list' => !empty($Categories_serialized) ? $Categories_serialized : $country->categories_list,

                    'footers'         => !empty($footers) ? serialize($footers) : $country->footers,
                            
            
        
            ]);

            return response()->json(['message' => 'Country updated successfully', 'country' => $country], 200);

        } catch (\Exception $e) {
           return response()->json(["error" => $e->getMessage()], 500);
        }
    }
    public function deleteCountry(int $id)
    {    
        try {
            $country = Country::find($id);
            if (!$country) {
                return response()->json(['error' => 'Country not found'], 404);
            }
            $country->delete();
            return response()->json(['message' => 'Country deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!', 'message' => $e->getMessage()], 500);
        }
    }

/**************************************************************************************************************
 * 
 * 
 * END SECTION OF COUNTRIES
 * START SECTION OF PROVINCES
 * **************************************************************************************************************
 */
    public function getCustomProvince($id)
    {
        $province = province::with(['country'=>function($query){
            $query->select('id','name','name_ar');
        }])->find($id);
        if (!$province) {
            return response()->json(['error' => 'province not found'], 404);
        }

  




       
  
        if (!empty($province->faq)) {
            $faqDecoded = AdminHelper::deepDecodeFAQ($province->faq);
            $faqUtf8 = AdminHelper::utf8ize($faqDecoded);
            $province->setAttribute('faq', $faqUtf8);
            }

        if (!empty($province->categories_list)) {
                $data = AdminHelper::deepUnserializeCategiry_List($province['categories_list']);
                }
       

        $categories_list = [];
        if(!empty($data))
        {
        foreach ($data as $parentId => $childIds) {
            $parent = DB::table('wp_packages_main_category')
                ->where('id', $parentId)
                ->select('id', 'name')
                ->first();

            if (!$parent) continue;

            $children = [];
            if (is_array($childIds) && count($childIds)) {
                $children = DB::table('wp_adventure_type')
                    ->whereIn('id', $childIds)
                    ->select('id', 'name')
                    ->get();
            }

            $categories_list[] = [
                'parent' => $parent,
                'children' => $children
            ];
        }
        }
      

        

        // return $result;

        $province['banner_alt_text']=!empty($province['banner_alt_text']) ? AdminHelper::decodeLangString($province['banner_alt_text']):'';   
        $province['banner_activities_alt_text']=!empty($province['banner_activities_alt_text']) ? AdminHelper::decodeLangString($province['banner_activities_alt_text']):'';        
        $province['banner_packages_alt_text']=!empty($province['banner_packages_alt_text']) ? AdminHelper::decodeLangString($province['banner_packages_alt_text']):'';

        $province['categories_list'] = $categories_list;
        unset($province['categories']);
        unset($province['categories_list_packages']);
        unset($province['languages']);
                    unset($province['seo_content_head']);
            unset($province['seo_content_listing']);
            unset($province['seo_listing']);
            unset($province['seo_overview']);
            unset($province['footer_links']);


        $province['google_map'] = !empty($province['google_map'])
        ? unserialize($province['google_map'])
        : '';

        $province['price_info']=!empty($province['price_info'])
        ? unserialize($province['price_info'])
        : '';
        // $province['seo_overview']=!empty($province['seo_overview']) ? AdminHelper::deepDecodeFooter($province['seo_overview']):'';

        // $province['seo_listing']=!empty($province['seo_listing']) ? AdminHelper::deepDecodeFooter($province['seo_listing']):'';

        // $province['seo_content_head']=!empty($province['seo_content_head']) ? AdminHelper::deepDecodeFooter($province['seo_content_head']):'';

        // $province['seo_content_listing']=!empty($province['seo_content_listing']) ? AdminHelper::deepDecodeFooter($province['seo_content_listing']):'';


        $province['currency']=!empty($province['currency'])? AdminHelper::decodeLangString($province['currency']):'';
        $province['meta_title']=!empty($province['meta_title']) ? AdminHelper::decodeLangString($province['meta_title']):'';
        $province['meta_description']=!empty($province['meta_description']) ?AdminHelper::decodeLangString($province['meta_description']):'';
        $province['meta_keywords']=!empty($province['meta_keywords']) ?   AdminHelper::decodeLangString($province['meta_keywords']):'';
        $province['meta_title_activities']=!empty($province['meta_title_activities']) ?   AdminHelper::decodeLangString($province['meta_title_activities']): '';
        $province['meta_description_activities']=!empty($province['meta_description_activities']) ?   AdminHelper::decodeLangString($province['meta_description_activities']):'';    
        $province['meta_keywords_activities']=!empty($province['meta_keywords_activities']) ?   AdminHelper::decodeLangString($province['meta_keywords_activities']):'';


                
        if (!empty($province->meta_keywords_packages)) {
            $province['meta_keywords_packages']=AdminHelper::decodeLangString($province['meta_keywords_packages']);
        }
        $province['meta_title_packages']=!empty($province['meta_title_packages']) ?   AdminHelper::decodeLangString($province['meta_title_packages']):'';

        $province['meta_description_packages']=!empty($province['meta_description_packages']) ? AdminHelper::decodeLangString($province['meta_description_packages']):'';
       

        $province['banners']=!empty($province['banners'])? AdminHelper::deepUnserializeCategiry_List($province['banners']):'';
        $province['footers']=!empty($province['footers'])? AdminHelper::deepDecodeFooter($province['footers']):'';
        $province['metas']=!empty($province['metas'])? AdminHelper::deepUnserializeCategiry_List($province['metas']):'';
        // unset($province['metas']);
        return response()->json(['province' => $province], 200);

    }

    public function getAllProvinces()
    {
        try {
            $provinces = Province::with(['country'=>function($query){
                $query->select('id','name','name_ar');
            }])->paginate(10);

            $provinces->transform(function ($province) {
        if (!empty($province->faq)) {
            $province->faq = AdminHelper::deepDecodeFAQ($province->faq);
        }

        $province['footers']=!empty($province['footers'])? AdminHelper::deepDecodeFooter($province['footers']):'';

        
        $province['banner_alt_text']=!empty($province['banner_alt_text']) ? AdminHelper::decodeLangString($province['banner_alt_text']):'';  
        $province['banner_activities_alt_text']=!empty($province['banner_activities_alt_text']) ? AdminHelper::decodeLangString($province['banner_activities_alt_text']):'';       

        $province['banner_packages_alt_text']=!empty($province['banner_packages_alt_text']) ? AdminHelper::decodeLangString($province['banner_packages_alt_text']):'';


        if (!empty($province->price_info)) {
            $province->price_info = unserialize($province->price_info);
        }
        if (!empty($province->google_map)) {
            $province->google_map = unserialize($province->google_map);
        }
        if (!empty($province->currency)) {

            $province->currency = AdminHelper::decodeLangString($province->currency);
        }
        if (!empty($province->meta_title)) {
            $province['meta_title'] = AdminHelper::decodeLangString($province['meta_title']);
        }
        if (!empty($province->meta_description)) {
            $province['meta_description'] = AdminHelper::decodeLangString($province['meta_description']);
        }
        if (!empty($province->meta_keywords)) {
            $province['meta_keywords'] = AdminHelper::decodeLangString($province['meta_keywords']);
        }
        if (!empty($province->meta_title_activities)) {
            $province['meta_title_activities']=AdminHelper::decodeLangString($province['meta_title_activities']);
        }
        if (!empty($province->meta_description_activities)) {
            $province['meta_description_activities']=AdminHelper::decodeLangString($province['meta_description_activities']);
        }
        if (!empty($province->meta_keywords_activities)) {
            $province['meta_keywords_activities']=AdminHelper::decodeLangString($province['meta_keywords_activities']);
        }
        if (!empty($province->meta_title_packages)) {
            $province['meta_title_packages']=AdminHelper::decodeLangString($province['meta_title_packages']);
        }
        if (!empty($province->meta_description_packages)) {
            $province['meta_description_packages']=AdminHelper::decodeLangString($province['meta_description_packages']);
        }
        if (!empty($province->meta_keywords_packages)) {
            $province['meta_keywords_packages']=AdminHelper::decodeLangString($province['meta_keywords_packages']);
        }
        if (!empty($province->meta_title_hotels)) {
            $province['meta_title_hotels']=AdminHelper::decodeLangString($province['meta_title_hotels']);
        }
        if (!empty($province->meta_description_hotels)) {
            $province['meta_description_hotels']=AdminHelper::decodeLangString($province['meta_description_hotels']);

        }    
        // if (!empty($province->footer_links)) {
        //     $province['footer_links']=AdminHelper::deepUnserializeCategiry_List($province['footer_links']);
        // }
        if (!empty($province->banners)) {
            $province['banners']=AdminHelper::deepUnserializeCategiry_List($province['banners']);
        }
        if (!empty($province->footers)) {
            $province['footers']=AdminHelper::deepDecodeFooter($province['footers']);
        }
        //         $province['seo_content_head']=!empty($province['seo_content_head']) ? AdminHelper::deepDecodeFooter($province['seo_content_head']):'';
        // $province['seo_content_listing']=!empty($province['seo_content_listing']) ? AdminHelper::deepDecodeFooter($province['seo_content_listing']):'';


            //    $province['seo_overview']=!empty($province['seo_overview']) ? AdminHelper::deepDecodeFooter($province['seo_overview']):'';
            //    $province['seo_listing']=!empty($province['seo_listing']) ? AdminHelper::deepDecodeFooter($province['seo_listing']):'';
        if (!empty($province->categories_list)) {
            $data = AdminHelper::deepUnserializeCategiry_List($province->categories_list);
            $categories_list = [];

            foreach ($data as $parentId => $childIds) {
                $parent = DB::table('wp_packages_main_category')
                    ->where('id', $parentId)
                    ->select('id', 'name')
                    ->first();

                if (!$parent) continue;

                $children = [];
                if (is_array($childIds) && count($childIds)) {
                    $children = DB::table('wp_adventure_type')
                        ->whereIn('id', $childIds)
                        ->select('id', 'name')
                        ->get();
                }

                $categories_list[] = [
                    'parent' => $parent,
                    'children' => $children
                ];
            }

            // هنا تخزن النتيجة جوة الكاونتري
            $province->categories_list = $categories_list;
        }
            unset($province['categories']);
            unset($province['categories_list_packages']);
            unset($province['top_sights']);
            unset($province['fetched_categories']);
            unset($province['itineraries_blogs']);
            unset($province['activities_content']);
            unset($province['packages_content']);
            unset($province['thumbnail_name']);
            unset($province['extra_information']);
            unset($province['featured_blogs']);
            unset($province['order_by']);
            unset($province['destination_type']);
            unset($province['languages']);
            unset($province['number_packages']);
            unset($province['seo_content_head']);
            unset($province['seo_content_listing']);
            unset($province['seo_listing']);
            unset($province['seo_overview']);
            unset($province['footer_links']);


            // unset($province['metas']);
        return $province;
        });


            return response()->json(['provinces' => $provinces->items()], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Something went wrong!',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function addNewProvince(array $data)
    {
            try {
                $country=[];
                $country=$data;

                return $country;


            } catch (\Exception $e) {
                return response()->json([
                    'error'   => 'Something went wrong!',
                    'message' => $e->getMessage()
                ], 500);
            }
    }









}
