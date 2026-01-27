<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente
     */
    protected $fillable = [
        'user_id',
        'filename',
        'path',
        'mime_type',
        'size',
        'url',
    ];

    /**
     * Los atributos que deben ser casteados
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relación: Media pertenece a un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Media pertenece a múltiples landings via pivot
     */
    public function landings(): BelongsToMany
    {
        return $this->belongsToMany(Landing::class, 'landing_media')
            ->withPivot('sort_order')
            ->orderBy('landing_media.sort_order');
    }

    /**
     * Relación: Media pertenece a múltiples invitations via pivot
     */
    public function invitations(): BelongsToMany
    {
        return $this->belongsToMany(Invitation::class, 'invitation_media');
    }

    /**
     * Relación: Media puede ser usado como imagen de fondo de temas
     */
    public function themes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Theme::class, 'bg_image_media_id');
    }

    /**
     * Verifica si el archivo es una imagen válida
     */
    public function isValidImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Obtiene el tamaño del archivo en MB
     */
    public function getFileSizeInMb(): float
    {
        return round($this->size / 1024 / 1024, 2);
    }

    /**
     * Scope para filtrar por tipo MIME
     */
    public function scopeByMimeType($query, string $mimeType)
    {
        return $query->where('mime_type', $mimeType);
    }

    /**
     * Scope para filtrar por usuario
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para imágenes solamente
     */
    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }
}
