<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Event::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'team' => ['nullable', 'in:arabic,english,french'],
            'filiere' => ['nullable', 'string', 'max:100'],
            // The status is chosen by the submit button ("Publish event" vs
            // "Save as draft"). Anything other than an authorized manager's
            // explicit choice falls back to draft server-side.
            'status' => ['nullable', 'in:draft,published'],
        ];
    }

    public function withValidator($validator): void
    {
        // Events are scoped to a team OR a filière — never both.
        $validator->after(function ($v) {
            if ($this->input('team') && $this->input('filiere')) {
                $v->errors()->add('filiere', 'Pick either a team or a filière, not both.');
            }
        });
    }

    /**
     * Normalized creation payload: default to DRAFT unless a manager
     * explicitly requested publishing. Never trust raw client input.
     */
    public function creationData(): array
    {
        $data = $this->validated();
        $requested = $data['status'] ?? $this->input('submit_action');

        $publish = in_array($requested, ['published', 'publish'], true)
            && $this->user()?->can('publish', \App\Models\Event::class);

        unset($data['status']);

        return [
            ...$data,
            'status' => $publish ? 'published' : 'draft',
            'published_at' => $publish ? now() : null,
        ];
    }
}
