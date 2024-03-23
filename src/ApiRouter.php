<?php
namespace Arostech\Api;  

use Illuminate\Support\Facades\Route;
use Arostech\Api\ApiController;

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
        // ------------------ USERS
        Route::post('/v1/users',[ApiController::class, 'usersPost']);
        Route::post('/v1/authenticate',[ApiController::class,'authenticate']);

        // ------------------ CONTENT
        Route::get('/v1/content', [ApiController::class,'contentGet']);


        // ------------------------- Protected routes ----------------------
        // ------------------------- Protected routes ----------------------
        // ------------------------- Protected routes ----------------------
        Route::group(['middleware' => ['auth:sanctum']], function (){
            // Users
            Route::post('/v1/logout',[ApiController::class,'logout']);

            // Content
            Route::get('/v1/content/{id}',[ApiController::class, 'contentGetId']);
            Route::post('/v1/content',[ApiController::class,'contentPost']);
            Route::put('/v1/content/{id}',[ApiController::class, 'contentPut']);    
            Route::delete('/v1/content/{id}',[ApiController::class,'contentDelete']);


            // Messages
            Route::get('/v1/messages',[ApiController::class, 'messagesGet']);
            Route::put('/v1/messages/{message}',[ApiController::class, 'messagesPut']);

            // Testimonials
            Route::get('/v1/testimonials',[ApiController::class,'testimonialsGet']);
            Route::post('/v1/testimonials', [ApiController::class,'testimonialsPost']);
            Route::get('/v1/testimonials/{testimonial}',[ApiController::class,'testimonialsGetId']);
            Route::put('/v1/testimonials/{testimonial}',  [ApiController::class,'testimonialsPut']);

            // Emails
            Route::get('/v1/emails',[ApiController::class,'emailsGet']);

            // Images
            Route::get('/v1/images',[ApiController::class,'imagesGet']);
            Route::post('/v1/images',[ApiController::class,'imagesPost']);
            Route::get('/v1/images/{image}',[ApiController::class,'imagesShow']);
            Route::put('/v1/images/{image}',[ApiController::class,'imagesPut']);
            Route::delete('/v1/images/{image}',[ApiController::class,'imagesDelete']);

            // Analytics
            Route::get('/v1/analytics',[ApiController::class,'analyticsGet']);

            // Processed analytics
            Route::get('/v1/processed-analytics',[ApiController::class,'processedAnalyticsGet']);

        });

    }
}