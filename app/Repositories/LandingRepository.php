<?php

namespace App\Repositories;

use App\Models\Landing;
use Illuminate\Database\Eloquent\Collection;

class LandingRepository
{
    /**
     * Obtiene las landings de un usuario
     */
    public function findByUser(int $userId): Collection
    {
        return Landing::where('user_id', $userId)
            ->with(['theme', 'media'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Busca una landing por slug
     */
    public function findBySlug(string $slug): ?Landing
    {
        return Landing::where('slug', $slug)
            ->with(['theme', 'media', 'user'])
            ->first();
    }

    /**
     * Busca una landing por ID
     */
    public function findById(int $id): ?Landing
    {
        return Landing::with(['theme', 'media', 'user'])->find($id);
    }

    /**
     * Crea una nueva landing
     */
    public function create(array $data): Landing
    {
        return Landing::create($data);
    }

    /**
     * Actualiza una landing existente
     */
    public function update(int $id, array $data): Landing
    {
        $landing = Landing::findOrFail($id);
        $landing->update($data);
        return $landing->fresh(['theme', 'media']);
    }

    /**
     * Elimina una landing
     */
    public function delete(int $id): bool
    {
        $landing = Landing::find($id);
        return $landing ? $landing->delete() : false;
    }

    /**
     * Vincula media a una landing con orden
     */
    public function attachMedia(int $landingId, int $mediaId, int $order): void
    {
        $landing = Landing::findOrFail($landingId);
        
        // Si ya existe el media, actualizar solo el orden
        if ($landing->media->contains($mediaId)) {
            $landing->media()->updateExistingPivot($mediaId, ['sort_order' => $order]);
        } else {
            // Si no existe, adjuntar con el orden
            $landing->media()->attach($mediaId, ['sort_order' => $order]);
        }
    }

    /**
     * Desvincula media de una landing
     */
    public function detachMedia(int $landingId, int $mediaId): void
    {
        $landing = Landing::findOrFail($landingId);
        $landing->media()->detach($mediaId);
    }

    /**
     * Actualiza el orden de los media en una landing
     */
    public function updateMediaOrder(int $landingId, array $orders): void
    {
        $landing = Landing::findOrFail($landingId);
        
        foreach ($orders as $item) {
            if (isset($item['media_id']) && isset($item['sort_order'])) {
                $landing->media()->updateExistingPivot(
                    $item['media_id'], 
                    ['sort_order' => $item['sort_order']]
                );
            }
        }
    }

    /**
     * Obtiene el próximo número de orden para media en una landing
     */
    public function getNextSortOrder(int $landingId): int
    {
        $landing = Landing::findOrFail($landingId);
        $maxOrder = $landing->media()->max('landing_media.sort_order') ?? 0;
        return $maxOrder + 1;
    }

    /**
     * Verifica si un slug es único para un usuario
     */
    public function isSlugUniqueForUser(string $slug, int $userId, ?int $excludeId = null): bool
    {
        $query = Landing::where('slug', $slug)
            ->where('user_id', $userId);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->doesntExist();
    }

    /**
     * Cuenta el total de media vinculado a una landing
     */
    public function countMediaForLanding(int $landingId): int
    {
        $landing = Landing::find($landingId);
        return $landing ? $landing->media()->count() : 0;
    }

    /**
     * Obtiene landing con eager loading optimizado para vista pública
     */
    public function findBySlugPublic(string $slug): ?Landing
    {
        return Landing::where('slug', $slug)
            ->with([
                'theme:id,name,primary_color,secondary_color,bg_color,bg_image_url,css_class',
                'media' => function($query) {
                    $query->select('media.id', 'media.filename', 'media.url', 'media.mime_type')
                        ->orderBy('landing_media.sort_order');
                }
            ])
            ->select('id', 'theme_id', 'slug', 'couple_names', 'anniversary_date', 'bio_text')
            ->first();
    }
}