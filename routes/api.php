<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConferenceController;
use App\Http\Controllers\Api\ContactPersonController;
use App\Http\Controllers\Api\EventLocationController;
use App\Http\Controllers\Api\PaymentInformationController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\RegistrationFeeController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public API routes (no authentication required)
Route::prefix('v1')->group(function () {
    
    // Authentication routes
    Route::post('/admin/login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/admin/logout', [AuthController::class, 'logout']);
        Route::get('/admin/me', [AuthController::class, 'me']);
    });
    
    // Conference routes
    Route::get('/conferences', [ConferenceController::class, 'index']);
    Route::get('/conferences/{year}', [ConferenceController::class, 'show']);
    Route::get('/conferences/{year}/speakers', [ConferenceController::class, 'speakers']);
    Route::get('/conferences/{year}/important-dates', [ConferenceController::class, 'importantDates']);
    Route::get('/conferences/{year}/committees', [ConferenceController::class, 'committees']);
    Route::get('/conferences/{year}/contacts', [ConferenceController::class, 'contacts']);
    Route::get('/conferences/{year}/documents', [ConferenceController::class, 'documents']);
    Route::get('/conferences/{year}/research-areas', [ConferenceController::class, 'researchAreas']);
    Route::get('/conferences/{year}/location', [ConferenceController::class, 'location']);
    Route::get('/conferences/{year}/author-instructions', [ConferenceController::class, 'authorInstructions']);
    Route::get('/conferences/{year}/assets', [ConferenceController::class, 'assets']);
    Route::get('/conferences/{year}/social-media', [ConferenceController::class, 'socialMediaLinks']);
    
    // Event locations (separate management)
    Route::get('/event-locations/{year}', [EventLocationController::class, 'show']);
    
    // Registration fees
    Route::get('/registration-fees/{year}', [RegistrationFeeController::class, 'index']);
    
    // Contact persons
    Route::get('/contact-persons/{year}', [ContactPersonController::class, 'index']);
    
    // Registration and Payment routes
    Route::get('/registration', [RegistrationController::class, 'index']);
    Route::get('/registration/fees', [RegistrationController::class, 'fees']);
    Route::get('/registration/policies', [RegistrationController::class, 'policies']);
    Route::get('/payment-information', [PaymentInformationController::class, 'index']);

    // Admin routes (protected by Sanctum authentication)
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        
        // Edition management
        Route::post('/editions', [ConferenceController::class, 'storeEdition']);
        Route::put('/editions/{id}', [ConferenceController::class, 'updateEdition']);
        Route::delete('/editions/{id}', [ConferenceController::class, 'deleteEdition']);
        Route::post('/editions/{id}/activate', [ConferenceController::class, 'activateEdition']);
        Route::post('/editions/{id}/publish', [ConferenceController::class, 'publishEdition']);
        Route::post('/editions/{id}/archive', [ConferenceController::class, 'archiveEdition']);
        
        // Speakers management (edition-based)
        Route::get('/editions/{editionId}/speakers', [ConferenceController::class, 'getEditionSpeakers']);
        Route::post('/editions/{editionId}/speakers', [ConferenceController::class, 'createSpeaker']);
        Route::put('/speakers/{id}', [ConferenceController::class, 'updateSpeaker']);
        Route::delete('/speakers/{id}', [ConferenceController::class, 'deleteSpeaker']);
        Route::post('/speakers/{id}/photo', [ConferenceController::class, 'uploadSpeakerPhoto']);
        Route::delete('/speakers/{id}/photo', [ConferenceController::class, 'deleteSpeakerPhoto']);
        
        // Conference management
        Route::post('/conferences', [ConferenceController::class, 'store']);
        Route::put('/conferences/{year}', [ConferenceController::class, 'update']);
        Route::delete('/conferences/{year}', [ConferenceController::class, 'destroy']);
        
        // Speakers management
        Route::post('/conferences/{year}/speakers', [ConferenceController::class, 'addSpeaker']);
        Route::put('/speakers/{id}', [ConferenceController::class, 'updateSpeaker']);
        Route::delete('/speakers/{id}', [ConferenceController::class, 'deleteSpeaker']);
        Route::post('/speakers/{id}/photo', [ConferenceController::class, 'uploadSpeakerPhoto']);
        
        // Documents management
        Route::post('/conferences/{year}/documents', [ConferenceController::class, 'uploadDocument']);
        Route::delete('/documents/{id}', [ConferenceController::class, 'deleteDocument']);
        
        // Important dates management
        Route::post('/conferences/{year}/important-dates', [ConferenceController::class, 'addImportantDate']);
        Route::put('/important-dates/{id}', [ConferenceController::class, 'updateImportantDate']);
        Route::delete('/important-dates/{id}', [ConferenceController::class, 'deleteImportantDate']);
        
        // Committee management
        Route::post('/conferences/{year}/committees/import', [ConferenceController::class, 'importCommitteeMembers']);
        Route::post('/conferences/{year}/committee-members', [ConferenceController::class, 'addCommitteeMember']);
        Route::put('/committee-members/{id}', [ConferenceController::class, 'updateCommitteeMember']);
        Route::delete('/committee-members/{id}', [ConferenceController::class, 'deleteCommitteeMember']);
        
        // Research areas management
        Route::post('/conferences/{year}/research-categories', [ConferenceController::class, 'addResearchCategory']);
        Route::post('/research-categories/{id}/areas', [ConferenceController::class, 'addResearchArea']);
        Route::put('/research-areas/{id}', [ConferenceController::class, 'updateResearchArea']);
        Route::delete('/research-areas/{id}', [ConferenceController::class, 'deleteResearchArea']);
        
        // Assets management
        Route::post('/conferences/{year}/assets', [ConferenceController::class, 'uploadAsset']);
        Route::put('/assets/{id}', [ConferenceController::class, 'updateAsset']);
        Route::delete('/assets/{id}', [ConferenceController::class, 'deleteAsset']);
        
        // Social media management
        Route::post('/conferences/{year}/social-media', [ConferenceController::class, 'addSocialMediaLink']);
        Route::put('/social-media/{id}', [ConferenceController::class, 'updateSocialMediaLink']);
        Route::delete('/social-media/{id}', [ConferenceController::class, 'deleteSocialMediaLink']);
        
        // Author instructions management
        Route::put('/conferences/{year}/author-config', [ConferenceController::class, 'updateAuthorConfig']);
        Route::post('/conferences/{year}/submission-methods', [ConferenceController::class, 'addSubmissionMethod']);
        Route::put('/submission-methods/{id}', [ConferenceController::class, 'updateSubmissionMethod']);
        Route::delete('/submission-methods/{id}', [ConferenceController::class, 'deleteSubmissionMethod']);
        Route::post('/conferences/{year}/presentation-guidelines', [ConferenceController::class, 'addPresentationGuideline']);
        Route::put('/presentation-guidelines/{id}', [ConferenceController::class, 'updatePresentationGuideline']);
        Route::delete('/presentation-guidelines/{id}', [ConferenceController::class, 'deletePresentationGuideline']);
        
        // Event location management
        Route::put('/event-locations/{year}', [EventLocationController::class, 'upsert']);
        Route::delete('/event-locations/{year}', [EventLocationController::class, 'destroy']);
        
        // Registration fees management
        Route::post('/registration-fees/{year}', [RegistrationFeeController::class, 'store']);
        Route::put('/registration-fees/{id}', [RegistrationFeeController::class, 'update']);
        Route::delete('/registration-fees/{id}', [RegistrationFeeController::class, 'destroy']);
        
        // Contact persons management
        Route::post('/contact-persons/{year}', [ContactPersonController::class, 'store']);
        Route::put('/contact-persons/{id}', [ContactPersonController::class, 'update']);
        Route::delete('/contact-persons/{id}', [ContactPersonController::class, 'destroy']);
        
        // User management
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });
});
