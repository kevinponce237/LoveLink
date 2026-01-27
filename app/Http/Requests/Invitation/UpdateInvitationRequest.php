<?php

namespace App\Http\Requests\Invitation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvitationRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta solicitud
     */
    public function authorize(): bool
    {
        $invitation = $this->route('invitation');

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
            'title' => 'sometimes|string|max:200',
            'slug' => 'sometimes|string|max:50|regex:/^[a-z0-9\-]+$/',
            'yes_message' => 'sometimes|string|max:100',
            'no_messages' => 'sometimes|array',
            'no_messages.*' => 'string|max:100',
            'is_published' => 'sometimes|boolean',
        ];
    }

    /**
     * Obtiene los mensajes de error personalizados
     */
    public function messages(): array
    {
        return [
            'title.max' => 'El título no puede exceder 200 caracteres.',
            'slug.regex' => 'El slug solo puede contener letras minúsculas, números y guiones.',
            'slug.max' => 'El slug no puede exceder 50 caracteres.',
            'yes_message.max' => 'El mensaje de "sí" no puede exceder 100 caracteres.',
            'no_messages.array' => 'Los mensajes de "no" deben ser un array.',
            'no_messages.*.string' => 'Cada mensaje de "no" debe ser una cadena de texto.',
            'no_messages.*.max' => 'Cada mensaje de "no" no puede exceder 100 caracteres.',
            'is_published.boolean' => 'El estado de publicación debe ser verdadero o falso.',
        ];
    }
}
