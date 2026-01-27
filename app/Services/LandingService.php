<?php

namespace App\Services;

use App\Models\Landing;
use App\Models\User;
use App\Repositories\LandingRepository;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class LandingService
{
    public function __construct(
        protected LandingRepository $landingRepository
    ) {}

    /**
     * Obtiene las landings de un usuario autenticado
     */
    public function getUserLandings(int $userId): Collection
    {
        return $this->landingRepository->findByUser($userId);
    }

    /**
     * Crea una nueva landing con slug generado si no se proporciona
     */
    public function createLanding(User $user, array $data): Landing
    {
        // Generar slug si no se proporciona
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['couple_names'], $user->id);
        } else {
            // Validar que el slug sea único para el usuario
            if (!$this->validateSlugUniqueness($data['slug'], $user->id)) {
                throw new \InvalidArgumentException('El slug ya existe para este usuario');
            }
        }

        $data['user_id'] = $user->id;

        return $this->landingRepository->create($data);
    }

    /**
     * Actualiza una landing existente
     */
    public function updateLanding(int $id, array $data, User $user): Landing
    {
        $landing = $this->landingRepository->findById($id);
        
        if (!$landing) {
            throw new \ModelNotFoundException('Landing no encontrada');
        }

        if ($landing->user_id !== $user->id) {
            throw new \UnauthorizedHttpException('', 'No tienes permisos para actualizar esta landing');
        }

        // Si se actualiza el slug, verificar unicidad
        if (isset($data['slug']) && $data['slug'] !== $landing->slug) {
            if (!$this->validateSlugUniqueness($data['slug'], $user->id, $id)) {
                throw new \InvalidArgumentException('El slug ya existe para este usuario');
            }
        }

        return $this->landingRepository->update($id, $data);
    }

    /**
     * Elimina una landing del usuario
     */
    public function deleteLanding(int $id, User $user): bool
    {
        $landing = $this->landingRepository->findById($id);
        
        if (!$landing) {
            throw new \ModelNotFoundException('Landing no encontrada');
        }

        if ($landing->user_id !== $user->id) {
            throw new \UnauthorizedHttpException('', 'No tienes permisos para eliminar esta landing');
        }

        return $this->landingRepository->delete($id);
    }

    /**
     * Obtiene una landing por ID (para propietario)
     */
    public function getLandingById(int $id, User $user): Landing
    {
        $landing = $this->landingRepository->findById($id);
        
        if (!$landing) {
            throw new \ModelNotFoundException('Landing no encontrada');
        }

        if ($landing->user_id !== $user->id) {
            throw new \UnauthorizedHttpException('', 'No tienes permisos para ver esta landing');
        }

        return $landing;
    }

    /**
     * Obtiene una landing por slug (público)
     */
    public function getLandingBySlugPublic(string $slug): Landing
    {
        $landing = $this->landingRepository->findBySlugPublic($slug);
        
        if (!$landing) {
            throw new \ModelNotFoundException('Landing no encontrada');
        }

        return $landing;
    }

    /**
     * Obtiene una landing por ID (público)
     */
    public function getLandingByIdPublic(int $id): Landing
    {
        $landing = $this->landingRepository->findById($id);
        
        if (!$landing) {
            throw new \ModelNotFoundException('Landing no encontrada');
        }

        return $landing;
    }

    /**
     * Genera un slug único basado en los nombres de la pareja
     */
    public function generateUniqueSlug(string $coupleNames, int $userId): string
    {
        // Convertir nombres a slug base
        $baseSlug = Str::slug($coupleNames, '-');
        
        // Asegurar que no sea demasiado largo
        $baseSlug = Str::limit($baseSlug, 40, '');
        
        $slug = $baseSlug;
        $counter = 1;

        // Verificar unicidad y agregar contador si es necesario
        while (!$this->validateSlugUniqueness($slug, $userId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Valida que un slug sea único para un usuario
     */
    public function validateSlugUniqueness(string $slug, int $userId, ?int $excludeId = null): bool
    {
        return $this->landingRepository->isSlugUniqueForUser($slug, $userId, $excludeId);
    }

    /**
     * Valida que el usuario puede modificar la landing
     */
    public function canUserModifyLanding(int $landingId, int $userId): bool
    {
        $landing = $this->landingRepository->findById($landingId);
        return $landing && $landing->user_id === $userId;
    }

    /**
     * Obtiene el próximo orden para media en una landing
     */
    public function getNextMediaSortOrder(int $landingId): int
    {
        return $this->landingRepository->getNextSortOrder($landingId);
    }

    /**
     * Valida límites de media para la landing
     */
    public function validateMediaLimit(int $landingId): bool
    {
        $currentCount = $this->landingRepository->countMediaForLanding($landingId);
        return $currentCount < 20; // Límite según especificación
    }

    /**
     * Vincula media a una landing
     */
    public function attachMediaToLanding(int $landingId, int $mediaId, int $userId, ?int $sortOrder = null): void
    {
        // Validar permisos del usuario sobre la landing
        if (!$this->canUserModifyLanding($landingId, $userId)) {
            throw new \UnauthorizedHttpException('', 'No tienes permisos sobre esta landing');
        }

        // Validar límite de media
        if (!$this->validateMediaLimit($landingId)) {
            throw new \InvalidArgumentException('Se ha alcanzado el límite máximo de media (20) para esta landing');
        }

        // Si no se proporciona orden, obtener el siguiente disponible
        if (is_null($sortOrder)) {
            $sortOrder = $this->getNextMediaSortOrder($landingId);
        }

        $this->landingRepository->attachMedia($landingId, $mediaId, $sortOrder);
    }

    /**
     * Desvincula media de una landing
     */
    public function detachMediaFromLanding(int $landingId, int $mediaId, int $userId): void
    {
        // Validar permisos del usuario sobre la landing
        if (!$this->canUserModifyLanding($landingId, $userId)) {
            throw new \UnauthorizedHttpException('', 'No tienes permisos sobre esta landing');
        }

        $this->landingRepository->detachMedia($landingId, $mediaId);
    }

    /**
     * Reordena los media de una landing
     */
    public function reorderLandingMedia(int $landingId, array $mediaOrder, int $userId): void
    {
        // Validar permisos del usuario sobre la landing
        if (!$this->canUserModifyLanding($landingId, $userId)) {
            throw new \UnauthorizedHttpException('', 'No tienes permisos sobre esta landing');
        }

        // Validar que todos los media pertenezcan a la landing
        $landing = $this->landingRepository->findById($landingId);
        $landingMediaIds = $landing->media->pluck('id')->toArray();
        
        foreach ($mediaOrder as $item) {
            if (!in_array($item['media_id'], $landingMediaIds)) {
                throw new \InvalidArgumentException('El media ' . $item['media_id'] . ' no pertenece a esta landing');
            }
        }

        $this->landingRepository->updateMediaOrder($landingId, $mediaOrder);
    }
}