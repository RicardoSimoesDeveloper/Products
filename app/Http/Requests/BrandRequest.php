<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BrandRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'brand' => ['required' , 'string', 'max:50','unique:brands'],
        ];

        if($this->method() == "PUT") {
            $adaptativeRules = [];
            foreach ($rules as $property => $propertyRules) {
                foreach ($propertyRules as $rule) {
                    if ($rule !== 'required')
                        $adaptativeRules[$property][] = $rule;
                }
            }
            $rules = $adaptativeRules;
        }

        return $rules;
    }

    /**
     * ATENÇÃO:
     * Em caso de uso de PostgreSQL como SGBD, comentar esta função.
     * Funcionamento desta é exclusivamente para MySQL.
     */
    // public function attributes()
    // {
    //     $columns = DB::select(
    //         'SHOW FULL COLUMNS FROM brands'
    //     );
    //     $attributes = [];
    //     foreach ($columns as $column) {
    //         $attributes[$column->Field] = (!empty($column->Comment)) ? $column->Comment : $column->Field;
    //     }

    //     return $attributes;
    // }

    public function messages()
    {
        return [
            'brand.required' => 'Brand is required',
            'brand.string' => 'Brand must be a string',
            'brand.max' => 'Brand must have a max of 50 characters',
            'brand.unique' => 'This brand already exists',
        ];
    }

    protected function failedValidation(ValidationValidator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }

    public function setValidation(array $data)
    {
        $validator = Validator::make($data, $this->rules());
        $this->setValidator($validator);
    }
}