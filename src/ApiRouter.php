<?php
namespace Arostech\Api;  

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArosTechApiController;

class ApiRouter{
    public static function run(){
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################
        ############ DETTE ER DEN MEST UPDATEREDE API ################


        // ------------------------- Public routes ----------------------
        // ------------------------- Public routes ----------------------
        // ------------------------- Public routes ----------------------
        // ------------------ USERS -- API
        Route::post('/v1/users',[ArosTechApiController::class, 'usersPost']);
        Route::post('/v1/authenticate',[ArosTechApiController::class,'authenticate']);

        // ------------------------- Protected routes ----------------------
        // ------------------------- Protected routes ----------------------
        // ------------------------- Protected routes ----------------------
        Route::group(['middleware' => ['auth:sanctum']], function (){
            // Users
            Route::post('/v1/logout',[ArosTechApiController::class,'logout']);

            // Content
            Route::get('/v1/content', [ArosTechApiController::class,'contentGet']);
            Route::get('/v1/content/{id}',[ArosTechApiController::class, 'contentGetId']);
            Route::post('/v1/content',[ArosTechApiController::class,'contentPost']);
            Route::put('/v1/content/{id}',[ArosTechApiController::class, 'contentPut']);    
            Route::delete('/v1/content/{id}',[ArosTechApiController::class,'contentDelete']);


            // Messages
            Route::get('/v1/messages',[ArosTechApiController::class, 'messagesGet']);
            Route::put('/v1/messages/{message}',[ArosTechApiController::class, 'messagesPut']);

            // Testimonials
            Route::get('/v1/testimonials',[ArosTechApiController::class,'testimonialsGet']);
            Route::post('/v1/testimonials', [ArosTechApiController::class,'testimonialsPost']);
            Route::get('/v1/testimonials/{testimonial}',[ArosTechApiController::class,'testimonialsGetId']);
            Route::put('/v1/testimonials/{testimonial}',  [ArosTechApiController::class,'testimonialsPut']);

            // Emails
            Route::get('/v1/emails',[ArosTechApiController::class,'emailsGet']);

            // Images
            Route::get('/v1/images',[ArosTechApiController::class,'imagesGet']);
            Route::post('/v1/images',[ArosTechApiController::class,'imagesPost']);
            Route::get('/v1/images/{image}',[ArosTechApiController::class,'imagesShow']);
            Route::put('/v1/images/{image}',[ArosTechApiController::class,'imagesPut']);
            Route::delete('/v1/images/{image}',[ArosTechApiController::class,'imagesDelete']);

            // Analytics
            Route::get('/v1/analytics',[ArosTechApiController::class,'analyticsGet']);

            // Processed analytics
            Route::get('/v1/processed-analytics',[ArosTechApiController::class,'processedAnalyticsGet']);

        });

    }
}