<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\BaseFormRequest;

class SearchOrdersRequest extends BaseFormRequest
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
     * @return array<string,mixed>
     */
    public function rules()
    {
        return [
            'sort' => ['array'],
        ];
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
            'order_code',
            'customer_name',
            'customer_phone',
            'status',
            'from',
            'to',
            'pagination',
            'sort',
        );

        if (isset($input['sort'])) {
            $input['sort'] = json_decode($input['sort'], true);
        }

        $this->replace($input);
    }
}
