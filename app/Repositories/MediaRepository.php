<?php

namespace App\Repositories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Collection;

class MediaRepository
{
    /**
     * Encuentra media accesible por el usuario
     */
    public function findUserAccessible(int $userId): Collection
    {
        return Media::where('user_id', $userId)->get();
    }

    /**
     * Crea un nuevo media
     */
    public function create(array $data): Media
    {
        return Media::create($data);
    }

    /**
     * Encuentra media por ID
     */
    public function findById(int $id): ?Media
    {
        return Media::find($id);
    }

    /**
     * Elimina media
     */
    public function delete(int $id): bool
    {
        return Media::destroy($id) > 0;
    }

    /**
     * Verifica si el media está vinculado a alguna entidad
     */
    public function isLinkedToAnyEntity(int $mediaId): bool
    {
        $media = Media::find($mediaId);
        
        if (!$media) {
            return false;
        }

        // Verificar si está vinculado a themes como imagen de fondo
        $linkedToTheme = $media->themes()->exists();

        if ($linkedToTheme) {
            return true;
        }

        // Verificar si está vinculado a landings
        $linkedToLanding = $media->landings()->exists();

        if ($linkedToLanding) {
            return true;
        }

        // Verificar si está vinculado a invitations
        $linkedToInvitation = $media->invitations()->exists();

        return $linkedToInvitation;
    }

    /**
     * Obtiene media por usuario con relaciones
     */
    public function findUserAccessibleWithRelations(int $userId): Collection
    {
        return Media::where('user_id', $userId)
            ->with(['themes', 'landings', 'invitations'])
            ->get();
    }

    /**
     * Cuenta el total de media por usuario
     */
    public function countByUser(int $userId): int
    {
        return Media::where('user_id', $userId)->count();
    }

    /**
     * Encuentra media por tipo MIME
     */
    public function findByMimeType(string $mimeType, int $userId): Collection
    {
        return Media::where('user_id', $userId)
            ->where('mime_type', $mimeType)
            ->get();
    }

    /**
     * Encuentra solo imágenes del usuario
     */
    public function findUserImages(int $userId): Collection
    {
        return Media::where('user_id', $userId)
            ->where('mime_type', 'like', 'image/%')
            ->get();
    }
}