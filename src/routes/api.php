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

// ------------------ EMAILS
Route::post('/api/v1/emails',[ApiController::class,'emailsPost']);

// ------------------ POSTS
Route::get('/api/v1/posts',[ApiController::class, 'postsGet']);
Route::get('/api/v1/posts/{id}',[ApiController::class, 'postsGetId']);

// ------------------ CATEGORIES
Route::get('/api/v1/categories',[ApiController::class,'categoriesGet']);
Route::get('/api/v1/categories/{id}',[ApiController::class,'categoriesGetId']);





// ------------------------- Protected routes ----------------------
// ------------------------- Protected routes ----------------------
// ------------------------- Protected routes ----------------------
Route::group(['middleware' => ['auth:sanctum']], function (){
    // Users
    Route::get('/api/v1/users',[ApiController::class,'usersGet']);
    Route::get('/api/v1/users/authenticated-user',[ApiController::class,'usersGetAuthenticated']);
    Route::get('/api/v1/users/{id}',[ApiController::class,'usersGetSingle']);
    Route::post('/api/v1/logout',[ApiController::class,'logout']);
    Route::delete('/api/v1/users/{id}',[ApiController::class,'usersDelete']);

    // Content
    Route::get('/api/v1/content/{id}',[ApiController::class, 'contentGetId']);
    Route::post('/api/v1/content',[ApiController::class,'contentPost']);
    Route::put('/api/v1/content/{id}',[ApiController::class, 'contentPut']);    
    Route::delete('/api/v1/content/{id}',[ApiController::class,'contentDelete']);

    // Posts
    Route::post('/api/v1/posts',[ApiController::class,'postsPost']);
    Route::put('/api/v1/posts/{id}',[ApiController::class, 'postsPut']);    
    Route::delete('/api/v1/posts/{id}',[ApiController::class,'postsDelete']);
    Route::post('/api/v1/posts/{id}/sync-categories',[ApiController::class,'postsSyncCategories']);
    Route::post('/api/v1/posts/{id}/attach-category',[ApiController::class,'postsAttachCategory']);
    Route::post('/api/v1/posts/{id}/detach-categories',[ApiController::class,'postsDetachCategories']);
    Route::post('/api/v1/posts/{id}/clone',[ApiController::class,'postsClone']);
    Route::get('/api/v1/posts/all/also-unpublished',[ApiController::class,'postsGetAllAlsoUnpublished']);


    

    // Categories
    Route::post('/api/v1/categories',[ApiController::class,'categoriesPost']);
    Route::put('/api/v1/categories/{id}',[ApiController::class, 'categoriesPut']);    
    Route::delete('/api/v1/categories/{id}',[ApiController::class,'categoriesDelete']);


    // Messages
    Route::get('/api/v1/messages',[ApiController::class, 'messagesGet']);
    Route::get('/api/v1/messages/{id}',[ApiController::class, 'messagesGetSingle']);
    Route::put('/api/v1/messages/{message}',[ApiController::class, 'messagesPut']);
    Route::delete('/api/v1/messages/{message}',[ApiController::class, 'messagesDelete']);

    // Testimonials
    Route::get('/api/v1/testimonials',[ApiController::class,'testimonialsGet']);
    Route::post('/api/v1/testimonials', [ApiController::class,'testimonialsPost']);
    Route::get('/api/v1/testimonials/{id}',[ApiController::class,'testimonialsGetId']);
    Route::put('/api/v1/testimonials/{testimonial}',  [ApiController::class,'testimonialsPut']);
    Route::delete('/api/v1/testimonials/{testimonial}',  [ApiController::class,'testimonialsDelete']);

    // Emails
    Route::get('/api/v1/emails',[ApiController::class,'emailsGet']);
    Route::get('/api/v1/emails/{id}',[ApiController::class,'emailsGetSingle']);
    Route::delete('/api/v1/emails/{id}',[ApiController::class,'emailsDelete']);

    // Images
    Route::get('/api/v1/images',[ApiController::class,'imagesGet']);
    Route::post('/api/v1/images',[ApiController::class,'imagesPost']);
    Route::get('/api/v1/images/{id}',[ApiController::class,'imagesShow']);
    Route::put('/api/v1/images/{id}',[ApiController::class,'imagesPut']);
    Route::delete('/api/v1/images/{id}',[ApiController::class,'imagesDelete']);

    // Analytics
    Route::get('/api/v1/analytics',[ApiController::class,'analyticsGet']);

    // Processed analytics
    Route::get('/api/v1/processed-analytics',[ApiController::class,'processedAnalyticsGet']);


    // Pages
    Route::get('/api/v1/pages',[ApiController::class, 'pagesGet']);
    Route::get('/api/v1/pages/{id}',[ApiController::class, 'pagesGetSingle']);
    Route::post('/api/v1/pages',[ApiController::class, 'pagesPost']);
    Route::put('/api/v1/pages/{id}',[ApiController::class, 'pagesPut']);
    Route::delete('/api/v1/pages/{id}',[ApiController::class, 'pagesDelete']);
    Route::post('/api/v1/pages/add-relationship',[ApiController::class, 'pagesAddRelationship']);
    Route::post('/api/v1/pages/remove-relationship',[ApiController::class, 'pagesRemoveRelationship']);
    Route::post('/api/v1/pages/sync-relationship',[ApiController::class, 'pagesSyncRelationship']);

});