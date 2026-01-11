<?php

namespace App\Http\Middleware;

use App\Services\EditionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ResolveConferenceEdition Middleware
 * 
 * Resolves the conference edition from the year parameter in the URL
 * or defaults to the active edition. Makes the edition available throughout
 * the request lifecycle via the request attributes.
 */
class ResolveConferenceEdition
{
    /**
     * Edition service
     */
    protected EditionService $editionService;

    /**
     * Constructor
     */
    public function __construct(EditionService $editionService)
    {
        $this->editionService = $editionService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if year parameter is in the route
        $year = $request->route('year');

        // Resolve the edition
        if ($year) {
            $edition = $this->editionService->getEditionByYear((int) $year);
            
            if (!$edition) {
                return response()->json([
                    'success' => false,
                    'message' => "Conference edition for year {$year} not found",
                    'status' => 404
                ], 404);
            }
        } else {
            // No year specified, use active edition
            $edition = $this->editionService->getActiveEdition();
            
            if (!$edition) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active conference edition found',
                    'status' => 404
                ], 404);
            }
        }

        // Make edition available in request
        $request->attributes->set('conference_edition', $edition);
        $request->attributes->set('conference_edition_id', $edition->id);
        $request->attributes->set('conference_year', $edition->year);

        return $next($request);
    }
}
