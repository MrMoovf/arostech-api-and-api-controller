<?php

use Illuminate\Support\Facades\Route;
use Arostech\Api\ApiController;
 



// ------------------------- Public routes ----------------------
// ------------------------- Public routes ----------------------
// ------------------------- Public routes ----------------------
// ------------------ USERS
Route::post('/api/v1/users',[ApiController::class, 'usersPost']);
Route::post('/api/v1/authenticate',[ApiController::class,'authenticate']);

// ------------------ CONTENT
Route::get('/api/v1/content', [ApiController::class,'contentGet']);
Route::put('/api/v1/content/batch',[ApiController::class, 'contentBatchPut']);    

// ------------------ MESSAGES
Route::post('/api/v1/messages',[ApiController::class,'messagesPost']);





// ------------------------- Protected routes ----------------------
// ------------------------- Protected routes ----------------------
// ------------------------- Protected routes ----------------------
Route::group(['middleware' => ['auth:sanctum']], function (){
    // Users
    Route::get('/api/v1/users',[ApiController::class,'usersGet']);
    Route::get('/api/v1/users/{id}',[ApiController::class,'usersGetSingle']);
    Route::post('/api/v1/logout',[ApiController::class,'logout']);

    // Content
    Route::get('/api/v1/content/{id}',[ApiController::class, 'contentGetId']);
    Route::post('/api/v1/content',[ApiController::class,'contentPost']);
    Route::put('/api/v1/content/{id}',[ApiController::class, 'contentPut']);    
    Route::delete('/api/v1/content/{id}',[ApiController::class,'contentDelete']);

    // Posts
    Route::get('/api/v1/posts',[ApiController::class, 'postsGet']);
    Route::get('/api/v1/posts/{id}',[ApiController::class, 'postsGetId']);
    Route::post('/api/v1/posts',[ApiController::class,'postsPost']);
    Route::put('/api/v1/posts/{id}',[ApiController::class, 'postsPut']);    
    Route::delete('/api/v1/posts/{id}',[ApiController::class,'postsDelete']);


    // Messages
    Route::get('/api/v1/messages',[ApiController::class, 'messagesGet']);
    Route::get('/api/v1/messages/{id}',[ApiController::class, 'messagesGetSingle']);
    Route::put('/api/v1/messages/{message}',[ApiController::class, 'messagesPut']);
    Route::delete('/api/v1/messages/{message}',[ApiController::class, 'messagesDelete']);

    // Testimonials
    Route::get('/api/v1/testimonials',[ApiController::class,'testimonialsGet']);
    Route::post('/api/v1/testimonials', [ApiController::class,'testimonialsPost']);
    Route::get('/api/v1/testimonials/{testimonial}',[ApiController::class,'testimonialsGetId']);
    Route::put('/api/v1/testimonials/{testimonial}',  [ApiController::class,'testimonialsPut']);
    Route::delete('/api/v1/testimonials/{testimonial}',  [ApiController::class,'testimonialsDelete']);

    // Emails
    Route::get('/api/v1/emails',[ApiController::class,'emailsGet']);
    Route::get('/api/v1/emails/{id}',[ApiController::class,'emailsGetSingle']);
    Route::post('/api/v1/emails',[ApiController::class,'emailsPost']);
    Route::delete('/api/v1/emails/{id}',[ApiController::class,'emailsDelete']);

    // Images
    Route::get('/api/v1/images',[ApiController::class,'imagesGet']);
    Route::post('/api/v1/images',[ApiController::class,'imagesPost']);
    Route::get('/api/v1/images/{image}',[ApiController::class,'imagesShow']);
    Route::put('/api/v1/images/{image}',[ApiController::class,'imagesPut']);
    Route::delete('/api/v1/images/{image}',[ApiController::class,'imagesDelete']);

    // Analytics
    Route::get('/api/v1/analytics',[ApiController::class,'analyticsGet']);

    // Processed analytics
    Route::get('/api/v1/processed-analytics',[ApiController::class,'processedAnalyticsGet']);

});