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
}
