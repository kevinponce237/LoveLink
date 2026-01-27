<?php

namespace App\Http\Requests\Invitation;

use Illuminate\Foundation\Http\FormRequest;

class AttachInvitationMediaRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta solicitud
     */
    public function authorize(): bool
    {
        // Obtener la invitation desde la ruta
        $invitation = $this->route('invitation');

        // Asegurarse de que sea una instancia del modelo Invitation
        if (is_string($invitation)) {
            $invitation = \App\Models\Invitation::find($invitation);
        }

        return auth()->check() &&
               $invitation &&
               $invitation->user_id === auth()->id();
    }

    /**
     * Obtiene las reglas de validación que aplican a la solicitud
     */
    public function rules(): array
    {
        return [
            'media_id' => [
                'required',
                'integer',
                'exists:media,id',
                function ($attribute, $value, $fail) {
                    // Verificar que el media pertenezca al usuario autenticado
                    $media = \App\Models\Media::find($value);
                    if (! $media || $media->user_id !== auth()->id()) {
                        $fail('El archivo multimedia seleccionado no te pertenece.');
                    }
                },
            ],
        ];
    }

    /**
     * Obtiene los mensajes de error personalizados
     */
    public function messages(): array
    {
        return [
            'media_id.required' => 'El archivo multimedia es obligatorio.',
            'media_id.integer' => 'El ID del archivo multimedia debe ser un número.',
            'media_id.exists' => 'El archivo multimedia seleccionado no existe.',
        ];
    }
}
