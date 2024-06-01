<?php

namespace Arostech\Api;

use App\Http\Controllers\Controller;
use Arostech\Mail\MessageCustomer;
use Arostech\Mail\MessageOwner;

use Arostech\Models\Content;
use Arostech\Models\Emailsubscriber;
use Arostech\Models\Image;
use Arostech\Models\Message;
use Arostech\Models\Processedanalytic;
use Arostech\Models\Request as ModelsRequest;
use Arostech\Models\Testimonial;
use Arostech\Models\User;
use Arostech\Models\Post;
use Arostech\Models\Category;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;


class ApiController extends Controller
{



    // -------------------------------------------- CONTENT ---------------------------------------------
    // -------------------------------------------- CONTENT ---------------------------------------------
    // -------------------------------------------- CONTENT ---------------------------------------------
    // -------------------------------------------- CONTENT ---------------------------------------------

    // Show all contents
    public function contentGet(){
        return Content::all();
    }

    // Show content with ID
    public function contentGetId($id){
        $response['images'] = Image::where('is_deleted','0')->get();
        $response['content'] = Content::find($id);
        return $response;
    }

    // Create new piece of content
    public function contentPost(Request $request){
        $formFields = $request->validate([
            'img' => 'required',
            'data' => 'required',
            'page' => 'required',
            'title' => 'required',
            'img_extension' => 'required',
            'password' => 'required',
        ]);
        
        // Tjekker password
        $password = array_pop($formFields);
        if($password != 'Arostech'){
            return response('Error password wrong.',400);
        }

        // Filtering the inputted data -- I only need some of it.
        $dataToInput = [
            'title' => $formFields['title'],
            'page' => $formFields['page'],
            'data' => $formFields['data'],
            'inputtype' => '_deprecated::_msg_from_developer'
        ];

        // Creating the content
        $content = Content::create($dataToInput);

        // If ok, then returning the content
        if($content){
            return $content;
        }
        else{
            return response('Error creating new content',400);
        }


    }

    // Update content with ID
    public function contentPut($id, Request $request){
        $id = Content::find($id);
        // Få  _post fields
        $formFields = $request->validate([
            'img' => 'required',
            'data' => 'required',
            'page' => 'required',
            'title' => 'required',
            'img_extension' => 'required',
            'password' => 'required',
        ]);

        // Tjekker password
        $password = array_pop($formFields);
        if($password != 'Arostech'){
            return response('Error password wrong.',400);
        }


        // Tjek om der er et billede medsendt.
        if($formFields['img'] !== 'none') {
            
            // Set path til Storage facade -- ikke den samme som path i databasen!
            $path = 'public/'.$id['id'].'.'. $formFields['img_extension'];

            // Decode tilbage til ægte image data.
            $imgData = base64_decode($formFields['img']);
            if(Storage::fileExists($path)){
                // Deleting original file
                Storage::delete($path);
                // Reseting path
                $id->img = '';
                $id->save();
            }

            // Hvis det lykkes at printe/store filen
            if(Storage::put($path,$imgData)){
                // Ændr img til at være database-kolonnens path
                $formFields['img'] = $id['id'].'.'.$formFields['img_extension'];
                // Opdater databasen med den nye img-path og ændringer i content/data.
                $id->data = $formFields['data'];
                $id->img = $formFields['img'];
                $id->page = $formFields['page'];
                $id->title = $formFields['title'];
                $id->save();
                return response($id,200);
            }
        }
        // Hvis intet billede er fundet så bare opdater content/data
        else{
            $id->data = $formFields['data'];
            $id->title = $formFields['title'];
            $id->page = $formFields['page'];
            if($id->save()){
                return response($id,200);
            }
            
        }
        return response('Server error at end of method',500);
    }


    public function contentBatchPut(Request $request){
        $formFields = $request->validate([
            'finalChanges' => 'required',
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check that username and password match
        if($formFields['username'] != 'Arostech'){
            return response('Unauthorized in controller',401);
        }
        if($formFields['password'] != 'Arostech'){
            return response('Unauthorized in controller',401);
        }


        // For each of the final changes, update the ID-associated row.
        $finalChanges = $formFields['finalChanges'];



        // return $finalChanges;
        // for each
        foreach ($finalChanges as $change) {
            $content = Content::find($change['id']);

            if(!$content){
                return response('Content not found',404);
            }
            $content->data = $change['content'];
            if(!$content->save()){
                return response('Error saving ID: '.$content->id,404);
            }
        }


        // Creating new deployment in Vercel!
        // Removed this: withOptions(['verify',false])->
        if(env('IS_NEXT_JS_APP')){
            $res = Http::withHeaders([
                'Authorization' => 'Bearer '. env('VERCEL_FRONTEND_TOKEN'),
                'Content-Type' => 'application/json'
            ])->post('https://api.vercel.com/v13/deployments',[
                'name' => env('VERCEL_FRONTEND_NAME'),
                'deploymentId' => env('VERCEL_FRONTEND_DEPLOYMENT_ID'),
                'target' => 'production'
            ]);
            if($res->successful()){
                return response('we did it wow',200);
            }
            else{
                // echo $res->body();
                return response('Error trying to create new deployment on Vercel',500);
            }
        } else {
            return response('Not Next.js app, so all good',200);
        }
    }

    // Delete content with ID
    public function contentDelete(Request $request, $id){ 
        $id = Content::find($id);
        $formFields = $request->validate([
            'password' => 'required'
        ]);

        // Tjekker password
        $password = array_pop($formFields);
        if($password != 'Arostech'){
            return response('Error password wrong.',400);
        }

        // Trying to delete
        if($id->delete()){
            return response('Content '. $id->id .' deleted successfully',200);
        }
        else{
            return response('Error, something went wrong deleting, maybe the content doesnt exist', 500);
        }



    }

    // -------------------------------------------- POSTS ---------------------------------------------
    // -------------------------------------------- POSTS ---------------------------------------------
    // -------------------------------------------- POSTS ---------------------------------------------
    // -------------------------------------------- POSTS ---------------------------------------------
    public function postsGet(){
        return response(Post::with('categories','featuredimage')->get());
    }

    public function postsGetId($id){
        $post = Post::find($id);
        $post->categories;
        // $post->image = Image::find($post->featured_image_id);
        $post->featuredimage;
        $images = Image::where('is_deleted',0)->get();
        $categories = Category::all();
        $res = [
            'post' => $post,
            'images' => $images,
            'categories' => $categories
        ];
        return response($res);
    }

    public function postsPost(Request $request){
        $fields = $request->validate([
            'user_id' => 'integer|required',
            'author' => 'string',
            'title' => 'string|required',
            'subtitle' => 'string|required',
            'summary' => 'string|required',
            'content' => 'string|required',
            'alt_field1' => 'string|required', //so far not used - default defined in PostController
            'metadata' => 'json', //so far not used - default defined in PostController
            'view_count' => 'integer', 
            'featured_image_id' => 'integer', 
            'slug' => 'string|required',
            'published_at' => 'required|date'
        ]);

        $postLatest = Post::latest()->first();
        if(!$postLatest){
            $fields['slug'] = 1 . $fields['slug'];
            
        } else{
            $fields['slug'] = $postLatest->id + 1 . $fields['slug'];
        }

        $post = Post::create($fields);

        if($post){
            return response($post,200);
        } else {
            return response('Error making post',500);
        }
    }

    public function postsPut($id, Request $request){
        $fields = $request->validate([
            'author' => 'string',
            'title' => 'string|required',
            'subtitle' => 'string|required',
            'summary' => 'string|required',
            'content' => 'string|required',
            'alt_field1' => 'string|required', //so far not used - default defined in PostController
            'metadata' => 'json|required', //so far not used - default defined in PostController
            'featured_image_id' => 'integer',
            'slug' => 'string',
            'published_at' => 'required|date'
        ]);

        $post = Post::find($id);

        $post->author = $fields['author'] ? $fields['author'] : '';
        $post->title = $fields['title'];
        $post->subtitle = $fields['subtitle'];
        $post->summary = $fields['summary'];
        $post->content = $fields['content'];
        $post->alt_field1 = $fields['alt_field1'];
        $post->metadata = $fields['metadata'];
        if(key_exists('featured_image_id',$fields)){
            $post->featured_image_id = $fields['featured_image_id'];
        } else {
            $post->featured_image_id = null;
        }
        $post->slug = $fields['slug'];
        $post->published_at = $fields['published_at'];


        if($post->save()){
            return response($post,200);
        } else {
            return response('Error updating post',500);
        }
    }

    public function postsDelete($id){
        $post = Post::find($id);
        if($post->delete()){
            return response('Post successfully deleted',200);
        }
        else {
            return response('Error deleting post',500);
        }
    }

    public function postsSyncCategories($postId, Request $request){
        $fields = $request->validate([
            'categories' => 'array'
        ]);

        $post = Post::find($postId);

        if(!key_exists('categories',$fields)){
            return response('Bad request: No category specified in controller',403);
        }
        
        if($post->categories()->sync($fields['categories'])){
            $post->categories;
            return response($post,200);
        } else{
            return response('Error adding category in controller',500);
        }
    }

    public function postsAttachCategory($postId, Request $request){
        $fields = $request->validate([
            'category' => 'integer|required'
        ]);

        $post = Post::find($postId);

        try {
            if($post->categories()->attach($fields['category'])){
                $post->categories;
                return response($post,200);
            } 
        } catch (\Throwable $th) {
            return response('Error adding category in controller. Error: '.$th,500);
        }
    }

    public function postsDetachCategories($postId, Request $request){
        $fields = $request->validate([
            'categories' => 'array|required'
        ]);

        $post = Post::find($postId);

        if($post->categories()->detach($fields['categories'])){
            $post->categories;
            return response($post,200);
        } else{
            return response('Error adding category in controller',500);
        }
    }

    // -------------------------------------------- CATEGORIES ---------------------------------------------
    // -------------------------------------------- CATEGORIES ---------------------------------------------
    // -------------------------------------------- CATEGORIES ---------------------------------------------
    // -------------------------------------------- CATEGORIES ---------------------------------------------

    public function categoriesGet(){
        $categories = Category::tree()->get()->toTree();
        // return response(Category::with('posts')->get());
        return response($categories);
    }

    public function categoriesGetId($id){
        $category = Category::find($id);
        if(!$category){
            return response('Category not found',404);
        }
        $category->posts;
        return response($category,200);
    }

    public function categoriesPost(Request $request){
        $fields = $request->validate([
            'name' => 'required|string',
            'parent_id' => 'integer',
            'hexcolor' => 'string'
        ]);

        $category = Category::create($fields);

        if($category){
            return response($category,201);
        } else {
            return response('Error making category in controller',500);
        }
    }

    public function categoriesPut($id, Request $request){

        $fields = $request->validate([
            'name' => 'required|string',
            'parent_id' => 'integer',
            'hexcolor' => 'string'
        ]);

        $category = Category::find($id);
        if(!$category){
            return response('Bad request. Could not find category',404);
        }

        $category->name = $fields['name'];
        if(array_key_exists('parent_id',$fields)){
            $category->parent_id = $fields['parent_id'];
        }
        $category->hexcolor = $fields['hexcolor'];
        

        if($category->save()){
            return response($category,201);
        } else {
            return response('Error updating category in controller',500);
        }
    }

    public function categoriesDelete($id){
        $category = Category::find($id);

        if(!$category){
            return response('Bad request. Could not find category',404);
        }

        if($category->delete()){
            return response('Category deleted successfully',200);
        } else {
            return response('Error deleting category in controller',500);
        }
        

    }




    // -------------------------------------------- EMAILS ---------------------------------------------
    // -------------------------------------------- EMAILS ---------------------------------------------
    // -------------------------------------------- EMAILS ---------------------------------------------
    // -------------------------------------------- EMAILS ---------------------------------------------
    public function emailsGet(){
        return response(Emailsubscriber::all());
    }

    public function emailsGetSingle($id){
        return response(Emailsubscriber::find($id));
    }

    public function emailsPost(Request $request){
        $formFields = $request->validate([
            'consent' => 'required|boolean',
            'email' => 'required|email|string'
        ]);

        $emailSubscriber = Emailsubscriber::create($formFields);

        return response($emailSubscriber,201);
    }

    public function emailsDelete($id){
        $emailSubscriber = Emailsubscriber::find($id);
        $emailSubscriber->delete();
        return response('Emailsubscriber deleted',200);
    }
    //
    // -------------------------------------------- MESSAGES ---------------------------------------------
    // -------------------------------------------- MESSAGES ---------------------------------------------
    // -------------------------------------------- MESSAGES ---------------------------------------------
    // -------------------------------------------- MESSAGES ---------------------------------------------

    // Get all messages
    public function messagesGet(){
        return response(Message::all(),200);
    }

    public function messagesGetSingle($id){
        return response(Message::find($id),200);
    }

    public function messagesPost(Request $request){
        $formFields = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'msg' => 'required|string',
            'username' => 'required|string',
            'password'=>'required|string'
        ]);

        if($formFields['username'] != config('arostech-mail.arostech_username')){
            return response('Unauthorized in controller',500);
        }
        if($formFields['password'] != config('arostech-mail.arostech_password')){
            return response('Unauthorized in controller',500);
        }

        // Indsætter status på besked 
        $message = Message::create($formFields);

        $data = [
            'email' => $formFields['email'],
            'name' => $formFields['name'],
            'msg' => $formFields['msg']
        ];


        $mailToOwner = Mail::to(config('arostech-mail.app_owners_email'))->send(new MessageOwner($data['email'],$data['name'],$data['msg']));
        if(!$mailToOwner){
            return response('Error: Email to owner was not sent. Please contact your Aros Tech administrator',500);
        }

        $mailToCustomer = Mail::to($data['email'])->send(new MessageCustomer($data['email'],$data['name'],$data['msg']));
        if(!$mailToCustomer){
            return response('Error: Email to customer was not sent. Please contact your Aros Tech administrator',500);
        }

        return response($message,200);

    }

    // Update specific message
    public function messagesPut($id){
        $message = Message::find($id);


        switch ($message->status) {
            case '1':
                $message->status = 2;
                break;
            
            case '2':
                $message->status = 1;
                break;
        }
        $message->save();
        

        return Message::all();

    }


    public function messagesDelete($id){
        $message = Message::find($id);
        if($message->delete()){
            return response('Message deleted',200);
        }
        else{
            return response('Error deleting message',500);
        }

    }


    // -------------------------------------------- TESTIMONIALS ---------------------------------------------
    // -------------------------------------------- TESTIMONIALS ---------------------------------------------
    // -------------------------------------------- TESTIMONIALS ---------------------------------------------
    // -------------------------------------------- TESTIMONIALS ---------------------------------------------

    // Get all testimonials
    public function testimonialsGet(){
        return response(Testimonial::all(),200);
    }

    // Get specific testimonial
    public function testimonialsGetId($id){
        return response(Testimonial::find($id),200);
    }

    // POST new testimonial to the DB
    public function testimonialsPost(Request $request){
        // Give non-empty requirements 
        $formFields = $request->validate([
            'name' => 'required',
            'age' => 'required',
            'date' => 'required',
            'company' => '',
            'review' => 'required'
        ]);

        // Clean tags and backslashes and trim
        $formFields = array_map('self::stripTags',$formFields);

        // Insert into DB
        $testimonial = Testimonial::create($formFields);

        return $testimonial;
    }

    // Update content with ID
    public function testimonialsPut($id, Request $request){

        // Find testimonial to update
        $item = Testimonial::find($id);

        // Få alle _post fields
        $formFields = $request->validate([
            'name' => 'required',
            'age' => 'required',
            'date' => 'required',
            'company' => '',
            'review' => 'required'
        ]);


        // Clean tags and backslashes and trim
        $formFields = array_map('self::stripTags',$formFields);


        // Assign values to dedicated fields
        $item->name = $formFields['name'];
        $item->age = $formFields['age'];
        $item->date = $formFields['date'];
        $item->company = $formFields['company'];
        $item->review = $formFields['review'];

        // Save the item
        $item->save();

        // Return 
        return $item;
    }

    public function testimonialsDelete($id){
        $testimonial = Testimonial::find($id);
        if($testimonial->delete()){
            return response('Testimonial deleted',200);
        } else{
            return response('Error deleting testimonial');
        }
    }

    // -------------------------------------------- USERS ---------------------------------------------
    // -------------------------------------------- USERS ---------------------------------------------
    // -------------------------------------------- USERS ---------------------------------------------
    // -------------------------------------------- USERS ---------------------------------------------


    public function usersGet(){
        return response(User::all());
    } 

    public function usersGetSingle($id){
        return response(User::find($id));
    }

    public function usersGetAuthenticated(){
        return response(Auth::user());
    }

    // Register new user in users table
    public function usersPost(Request $request){
        $formFields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string',
            'organisation' => 'required|string',
            'organisation_url' => 'required|string',
            'authorization' => '',
            'apiEmail' => 'required|string|email',
            'apiPassword' => 'required|string',
        ]);

        $apiPassword = array_pop($formFields);
        $apiEmail = array_pop($formFields);

        if($apiEmail != 'createuser@arostech.dk' || $apiPassword != 'CreateUser!123'){
            return response('No access to post user', 401);
        }

        // Skaber user
        $user = User::create($formFields);

        return response($user,200);


    }

    public function usersDelete($id){
        $user = User::find($id);
        if($user->delete()){
            return response('User with id: '.$id.' was deleted succesfully',200);
        } else {
            return response('Error. User was not deleted.',500);
        }
    }

    public function authenticate(Request $request){
        $formFields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if(!Auth::attempt($formFields)){
            return response('No credentials match',401);
        }
        $user = Auth::user();
        $apiToken = $user->createToken($user->name);

        return $apiToken->plainTextToken;

    }

    public function logout(){
        $user = Auth::user();
 
        $user->tokens()->delete();

        return response('User logged out and all tokens associated has been deleted',201);

    }


    // -------------------------------------------- IMAGES ---------------------------------------------
    // -------------------------------------------- IMAGES ---------------------------------------------
    // -------------------------------------------- IMAGES ---------------------------------------------
    // -------------------------------------------- IMAGES ---------------------------------------------

    public function imagesGet(){
        return Image::where('is_deleted','0')->get();

    }

    public function imagesPost(Request $request){
        $formFields = $request->validate([
            'imageData' => 'required|string', //husk at validere image type hos Aros Tech
            'name' => 'required|string|max:255',
            'extension' => 'required|string|max:255',
            'name_extension' => 'required|string|max:510',
            'filesize' => 'required|string|max:255',
            'dimensions' => 'required|string|max:500',
            'title' => 'required|string|max:500',
            'caption' => 'required|string',
            'alt_text' => 'required|string',
        ]);
        // Getting base64 data
        $imageData = array_shift($formFields);
        $imageData = base64_decode($imageData);

        // getting and setting user id
        $user = Auth::user();
        $formFields['user_id'] = $user->id;
        $formFields['last_updated_by_user'] = $user->id;

        // cleaning formfields
        $formFields = array_map('self::stripTags',$formFields);

        // creating model in DB
        $image = Image::create($formFields);

        // Set path til Storage facade -- ikke den samme som path i databasen!
        $path = 'public/images/'.$image->name_extension;

        // Decode tilbage til ægte image data.
        if(Storage::fileExists($path)){
            // Deleting original file
            Storage::delete($path);
        }

        // Hvis det lykkes at printe/store filen
        if(Storage::put($path,$imageData,'public')){
            return response($image,200);
        }
        else{
            return response('Error on storing file: Status 500',500);
        }

    }

    public function imagesShow($id){
        $image = Image::find($id);
        if($image->is_deleted == 0){
            return $image;
        }
        return response('Error: image has been deleted',404);

    }

    public function imagesPut($id, Request $request){
        $image = Image::find($id);
        if($image->is_deleted != 0){
            return response('Error: image has been deleted',404);
        }
        $formFields = $request->validate([
            'title' => 'required|string|max:500',
            'caption' => 'required|string',
            'alt_text' => 'required|string',
        ]);
        if(!$image){
            return response('Image not found',404);
        }
        // getting and setting user id
        $user = Auth::user();
        $formFields['last_updated_by_user'] = $user->id;

        // cleaning formfields
        $formFields = array_map('self::stripTags',$formFields);

        // updating model in DB
        $image->title = $formFields['title'];
        $image->caption = $formFields['caption'];
        $image->alt_text = $formFields['alt_text'];
        $image->last_updated_by_user = $formFields['last_updated_by_user'];


        if($image->update()){
            return response($image,200);
        }
        else{
            return response('error updating image',500);
        }


    }

    public function imagesDelete($id, Request $request){
        $user = Auth::user();

        $image = Image::find($id);

        if($image->is_deleted != 0){
            return response('Error: image has been deleted',404);
        }
        $formFields = $request->validate([
            'password' => 'string|required',
        ]);

        if($formFields['password'] != 'Arostech'){
            return response('Delete not allowed', 401);
        }
        // setting from path for file rename later
        $pathFrom = 'public/images/'.$image->name_extension;

        // Soft deletion
        $image->is_deleted = 1;
        $image->last_updated_by_user = $user->id;
        $image->name = 'del_' . time() . '_'. hash('sha256',$image->name);
        $image->name_extension = $image->name . '.' . $image->extension;
        $image->update();

        // renaming the file
        $pathTo = 'public/images/'.$image->name_extension;
        Storage::move($pathFrom,$pathTo);


        return $image;

        
    }

    // -------------------------------------------- ANALYTICS  ---------------------------------------------
    // -------------------------------------------- ANALYTICS  ---------------------------------------------
    // -------------------------------------------- ANALYTICS  ---------------------------------------------
    // -------------------------------------------- ANALYTICS  ---------------------------------------------
    public function analyticsGet(){
        return ModelsRequest::all();
    }


    // Processed analytics get
    public function processedAnalyticsGet(){
        return Processedanalytic::latest()->first();
    }



    // ------------------------------ HELPERS ----------------------------------------
    public function stripTags($data){
        $data = strip_tags($data);
        $data = stripslashes($data);
        $data = trim($data);
        return $data;
    }


}
