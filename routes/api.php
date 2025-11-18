<?php

use App\Http\Controllers\Api\ConferenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public API routes (no authentication required)
Route::prefix('v1')->group(function () {
    
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

    // Admin routes (protected by Sanctum authentication)
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        
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
        Route::delete('/assets/{id}', [ConferenceController::class, 'deleteAsset']);
    });
});
