<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

use Illuminate\Support\Arr;

class PembiayaanStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'plafond' => 'required|integer',
            'mpt' => 'required|integer',
            'tenor' => 'required|integer',
            'pi_pokok' => 'required|integer',
            'pi_margin' => 'required|integer',
            'start_date' => 'required|date:YYYY-mm-dd'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $message = implode(", ", Arr::flatten($validator->errors()->toArray()));
        throw new HttpResponseException(response()->json([
            'code' => 422,
            'message' => $message
        ], 422));
    }
}
