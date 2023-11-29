<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductRequest extends FormRequest
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
            'type'  => ['required' , 'integer', 'exists:types,id'],
			'brand' => ['required' , 'integer', 'exists:brands,id'],
			'description'  => ['nullable' , 'string', 'max:50'],
            'price' => ['required' , 'numeric', 'regex:/^\d{1,5}(?:\.\d{1,2})?$/'],
            'stock' => ['nullable' , 'integer'],
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
    //         'SHOW FULL COLUMNS FROM products'
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
            'type.required' => 'type is required',
            'type.integer' => 'type must be a integer',
            'type.exists' => 'type doesnt exist',

            'brand.required' => 'brand is required',
            'brand.integer' => 'brand must be a integer',
            'brand.exists' => 'brand doesnt exist',

            'description.string' => 'description must be a string',
            'description.max' => 'description must have a max of 50 characters',

            'price.required' => 'price is required',
            'price.numeric' => 'price must be a numeric',
            'price.regex' => 'price must have a max of 5 numbers and a max of 2 decimals',

            'stock.integer' => 'stock is required',
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