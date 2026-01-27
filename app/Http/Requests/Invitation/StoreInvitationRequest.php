<?php

namespace App\Http\Requests\Invitation;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvitationRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta solicitud
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Obtiene las reglas de validación que aplican a la solicitud
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:50|regex:/^[a-z0-9\-]+$/',
            'yes_message' => 'nullable|string|max:100',
            'no_messages' => 'nullable|array',
            'no_messages.*' => 'string|max:100',
        ];
    }

    /**
     * Obtiene los mensajes de error personalizados
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede exceder 200 caracteres.',
            'slug.regex' => 'El slug solo puede contener letras minúsculas, números y guiones.',
            'slug.max' => 'El slug no puede exceder 50 caracteres.',
            'yes_message.max' => 'El mensaje de "sí" no puede exceder 100 caracteres.',
            'no_messages.array' => 'Los mensajes de "no" deben ser un array.',
            'no_messages.*.string' => 'Cada mensaje de "no" debe ser una cadena de texto.',
            'no_messages.*.max' => 'Cada mensaje de "no" no puede exceder 100 caracteres.',
        ];
    }

    /**
     * Prepara los datos para validación
     */
    protected function prepareForValidation(): void
    {
        // Si no se proporciona yes_message, usar el default
        if (! $this->has('yes_message')) {
            $this->merge(['yes_message' => 'Sí']);
        }

        // Si no se proporciona no_messages, usar los defaults
        if (! $this->has('no_messages') || empty($this->no_messages)) {
            $this->merge(['no_messages' => ['No', 'Tal vez', 'No te arrepentirás', 'Piénsalo mejor']]);
        }
    }
}
