<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'id' => ['required'],
            'name' => ['required'],
            'author' => ['required'], // 'author' => 'required
            'book_cover_url' => ['required'],
            'price' => ['required'],
            'stock' => ['required'],
            'user_id' => ['required'],
            'category_id' => ['required'],
            'description' => ['required']
        ];

        return $rules;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        $input = $this->only(
            'id',
            'name',
            'author',
            'description',
            'book_cover_url',
            'price',
            'stock',
            'user_id',
            'category_id',
            'description'
        );

        $this->replace($input);
    }
}
