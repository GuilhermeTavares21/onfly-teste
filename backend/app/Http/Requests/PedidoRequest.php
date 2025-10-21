<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class PedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destino' => 'required|string',
            'data_ida' => 'required|date',
            'data_volta' => 'required|date|after_or_equal:data_ida',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        Log::error('Erro de validação no PedidoRequest', $validator->errors()->toArray());

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Erro de validação',
            'errors' => $validator->errors(),
        ], 422));
    }
}
