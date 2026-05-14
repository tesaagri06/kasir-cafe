<?php
namespace App\Http\Requests\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'username'     => 'required|string|min:3|max:50|unique:users,username',
            'password'     => 'required|string|min:6|confirmed',
            'nama_lengkap' => 'required|string|max:100',
            'email'        => 'nullable|email|max:150|unique:users,email',
            'telepon'      => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required'     => 'Username wajib diisi.',
            'username.unique'       => 'Username sudah digunakan.',
            'password.required'     => 'Password wajib diisi.',
            'password.min'          => 'Password minimal 6 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.unique'          => 'Email sudah digunakan.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
                'data'    => null,
            ], 422)
        );
    }
}