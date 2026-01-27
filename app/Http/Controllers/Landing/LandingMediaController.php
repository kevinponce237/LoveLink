<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Landing\AttachMediaRequest;
use App\Http\Requests\Landing\ReorderMediaRequest;
use App\Services\LandingService;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LandingMediaController extends Controller
{
    public function __construct(
        protected LandingService $landingService,
        protected MediaService $mediaService
    ) {}

    /**
     * Vincula media a una landing
     */
    public function store(AttachMediaRequest $request, int $landingId): JsonResponse
    {
        try {
            // Verificar que el usuario es propietario del media
            if (! $this->mediaService->validateUserOwnership($request->media_id, $request->user()->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos sobre este media.',
                ], 403);
            }

            $this->landingService->attachMediaToLanding(
                $landingId,
                $request->media_id,
                $request->user()->id,
                $request->sort_order
            );

            return response()->json([
                'success' => true,
                'message' => 'Media vinculado a la landing exitosamente.',
            ], 201);
        } catch (\Exception $e) {
            if ($e->getCode() === 403) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos sobre esta landing.',
                ], 403);
            }

            if ($e instanceof \InvalidArgumentException) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al vincular media: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Desvincula media de una landing
     */
    public function destroy(Request $request, int $landingId, int $mediaId): JsonResponse
    {
        try {
            $this->landingService->detachMediaFromLanding(
                $landingId,
                $mediaId,
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Media desvinculado de la landing exitosamente.',
            ]);
        } catch (\Exception $e) {
            if ($e->getCode() === 403) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos sobre esta landing.',
                ], 403);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al desvincular media: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reordena los media de una landing
     */
    public function reorder(ReorderMediaRequest $request, int $landingId): JsonResponse
    {
        try {
            $this->landingService->reorderLandingMedia(
                $landingId,
                $request->media_order,
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Media reordenado exitosamente.',
            ]);
        } catch (\Exception $e) {
            if ($e->getCode() === 403) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos sobre esta landing.',
                ], 403);
            }

            if ($e instanceof \InvalidArgumentException) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al reordenar media: '.$e->getMessage(),
            ], 500);
        }
    }
}
