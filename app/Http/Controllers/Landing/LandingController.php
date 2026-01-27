<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Landing\StoreLandingRequest;
use App\Http\Requests\Landing\UpdateLandingRequest;
use App\Services\LandingService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function __construct(
        protected LandingService $landingService
    ) {}

    /**
     * Lista las landings del usuario autenticado
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $landings = $this->landingService->getUserLandings($request->user()->id);

            return response()->json([
                'success' => true,
                'data' => $landings,
                'message' => 'Landings obtenidas exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las landings: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Crea una nueva landing
     */
    public function store(StoreLandingRequest $request): JsonResponse
    {
        try {
            $landing = $this->landingService->createLanding(
                $request->user(),
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'data' => $landing->load(['theme', 'media']),
                'message' => 'Landing creada exitosamente.',
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la landing: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Muestra una landing específica
     * Si recibe un ID numérico, lo trata como ID de la landing (PÚBLICO)
     * Si recibe una string, lo trata como slug (PÚBLICO)
     */
    public function show(Request $request, string $identifier): JsonResponse
    {
        try {
            // Determinar si es ID o slug
            if (is_numeric($identifier)) {
                $landing = $this->landingService->getLandingByIdPublic((int) $identifier);
            } else {
                $landing = $this->landingService->getLandingBySlugPublic($identifier);
            }

            return response()->json([
                'success' => true,
                'data' => $landing,
                'message' => 'Landing obtenida exitosamente.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Landing no encontrada.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la landing: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualiza una landing existente
     */
    public function update(UpdateLandingRequest $request, int $id): JsonResponse
    {
        try {
            $landing = $this->landingService->updateLanding(
                $id,
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'success' => true,
                'data' => $landing,
                'message' => 'Landing actualizada exitosamente.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Landing no encontrada.',
            ], 404);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            if ($e->getCode() == 403) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 403);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la landing: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Elimina una landing
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $success = $this->landingService->deleteLanding($id, $request->user());

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Landing eliminada exitosamente.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la landing.',
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Landing no encontrada.',
            ], 404);
        } catch (\Exception $e) {
            if ($e->getCode() == 403) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 403);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la landing: '.$e->getMessage(),
            ], 500);
        }
    }
}
