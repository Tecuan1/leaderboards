<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'score' => ['required', 'integer', 'min:1'],
            'metadata' => ['sometimes', 'nullable', 'array'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $game = $this->route('game');

                if ($game && $game->max_score !== null && $this->input('score') > $game->max_score) {
                    $validator->errors()->add('score', "The score cannot be higher than {$game->max_score}.");
                }
            },
        ];
    }
}
