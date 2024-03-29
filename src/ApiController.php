<?php

namespace Arostech\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Emailsubscriber;
use App\Models\Image;
use App\Models\Message;
use App\Models\Processedanalytic;
use App\Models\Request as ModelsRequest;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;



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
    public function contentPut(Content $id, Request $request){
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
                return response('Img found and data updated',200);
            }
        }
        // Hvis intet billede er fundet så bare opdater content/data
        else{
            $id->data = $formFields['data'];
            $id->title = $formFields['title'];
            $id->page = $formFields['page'];
            if($id->save()){
                return response('No picture found - successfully updated',200);
            }
            
        }
        return 'yesyes';
    }

    public function contentDelete(Request $request, Content $id){
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
            return response('Error, something went wrong deleting, maybe the content doesnt exist', 400);
        }



    }

    // -------------------------------------------- EMAILS ---------------------------------------------
    // -------------------------------------------- EMAILS ---------------------------------------------
    // -------------------------------------------- EMAILS ---------------------------------------------
    // -------------------------------------------- EMAILS ---------------------------------------------
    public function emailsGet(){
        return Emailsubscriber::all();
    }
    //
    // -------------------------------------------- MESSAGES ---------------------------------------------
    // -------------------------------------------- MESSAGES ---------------------------------------------
    // -------------------------------------------- MESSAGES ---------------------------------------------
    // -------------------------------------------- MESSAGES ---------------------------------------------

    // Get all messages
    public function messagesGet(){
        return Message::all();
    }

    // Update specific message
    public function messagesPut(Message $message){
        switch ($message->status) {
            case '1':
                $data['status'] = '2';
                break;
            
            case '2':
                $data['status'] = '1';
                break;
        }
        
        $message->update($data);

        return Message::all();

    }
    // -------------------------------------------- TESTIMONIALS ---------------------------------------------
    // -------------------------------------------- TESTIMONIALS ---------------------------------------------
    // -------------------------------------------- TESTIMONIALS ---------------------------------------------
    // -------------------------------------------- TESTIMONIALS ---------------------------------------------

    // Get all testimonials
    public function testimonialsGet(){
        return Testimonial::all();
    }

    // Get specific testimonial
    public function testimonialsGetId($id){
        return Testimonial::find($id);
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

    // -------------------------------------------- USERS ---------------------------------------------
    // -------------------------------------------- USERS ---------------------------------------------
    // -------------------------------------------- USERS ---------------------------------------------
    // -------------------------------------------- USERS ---------------------------------------------

    // Register new user in users table
    public function usersPost(Request $request){
        $formFields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string',
            'organisation' => 'required|string',
            'organisation_url' => 'required|string',
            'apiEmail' => 'required|string|email',
            'apiPassword' => 'required|string'
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

    public function imagesShow(Image $image){
        if($image->is_deleted == 0){
            return $image;
        }
        return response('Error: image has been deleted',404);

    }

    public function imagesPut(Image $image, Request $request){
        if($image->is_deleted != 0){
            return response('Error: image has been deleted',404);
        }
        $formFields = $request->validate([
            'title' => 'required|string|max:500',
            'caption' => 'required|string',
            'alt_text' => 'required|string',
        ]);
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

    public function imagesDelete(Image $image, Request $request){
        $user = Auth::user();
        if($image->is_deleted != 0){
            return response('Error: image has been deleted',404);
        }
        $formFields = $request->validate([
            'name' => 'string|required',
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

        // CODE FOR HARD DELETING
        // // Setting path to image in the public images folder
        // $path = 'public/images/'.$image->name_extension;

        // if(Storage::delete($path)){
        //     if($image->delete()){
        //         return response('Model deleted successfully',200);
        //     }
        //     else{
        //         return response('Error: Image deleted but model still in database',500);
        //     }
        // }
        // else{
        //     return response('Error deleting image; was probably not found',500);
        // }
        
    }

    // -------------------------------------------- ANALYTICS  ---------------------------------------------
    // -------------------------------------------- ANALYTICS  ---------------------------------------------
    // -------------------------------------------- ANALYTICS  ---------------------------------------------
    // -------------------------------------------- ANALYTICS  ---------------------------------------------
    public function analyticsGet(){
        return ModelsRequest::all();
    }

    // Log exit requests -- not in use currently
    public function logRequest(Request $request){
        try {
            ModelsRequest::create([
                'log_type' => 'pageview',
                'route' => $request->path(),
                'useragent' => $request->userAgent(),
                'visitor_id' => crypt($request->userAgent() . $request->ip(),'123'),
                'action_type' => 'exit',
                'referer' => request()->header('referer') ?? 'wasNull'
            ]);
        } catch (\Throwable $th) {
            // throw $th;
        }
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
