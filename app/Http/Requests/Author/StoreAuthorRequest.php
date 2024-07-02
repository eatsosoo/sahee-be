<?php

namespace App\Http\Requests\Author;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuthorRequest extends FormRequest
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
            'name' => ['required','unique:authors'],
            'nationality' => ['required'],
            'dob' => ['required','date'],
            'pseudonym' => ['required'],
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'name.unique' => 'Tên tác giả đã tồn tại trong hệ thống. Vui lòng chọn tên khác.',
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
            'name',
            'nationality',
            'dob',
            'pseudonym'
        );

        $this->replace($input);
    }
}
