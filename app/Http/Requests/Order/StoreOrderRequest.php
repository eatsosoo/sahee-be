<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'user_id' => ['required'],
            'status' => ['required'],
            'total_amount' => ['required'],
            'payment_method' => ['required'],
            'customer_name' => ['required'],
            'customer_phone' => ['required'],
            'shipping_address' => ['required'],
            'shipping_cost' => ['required'],
            'items' => ['required']
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
            'user_id',
            'status',
            'total_amount',
            'payment_method',
            'customer_name',
            'customer_phone',
            'shipping_address',
            'shipping_cost',
            'items'
        );

        $this->replace($input);
    }
}
