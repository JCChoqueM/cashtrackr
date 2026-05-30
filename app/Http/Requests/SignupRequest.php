<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class SignupRequest extends FormRequest


{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function messages(): array
    {
        return [
            'name.required' => 'El Nombre es obligatorio',
            'email.required' => 'El E-mail es obligatorio',
            'email.email' => 'El E-mail no es válido',
            'email.unique' => 'El E-mail ya está registrado',
            'password.required' => 'La Contraseña es obligatorio',
            'password.confirmed' => 'Las Contraseñas no son iguales',
            'password.min' => 'La Contraseña debe tener al menos :min caracteres',
            'password.letters' => 'La Contraseña debe tener al menos una letra',
            'password.mixed' => 'La Contraseña debe tener al menos una letra mayúscula y una minúscula',
            'password.symbols' => 'La Contraseña debe tener al menos un símbolo',
            'password.numbers' => 'La Contraseña debe tener al menos un número',
            'password.uncompromised' => 'La Contraseña ha aparecido en una fuga de datos',


        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email','unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->symbols()
                    ->numbers()
                    ->uncompromised(),
            ],
        ];
    }
}
