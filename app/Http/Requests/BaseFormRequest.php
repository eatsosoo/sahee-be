<?php

namespace App\Http\Requests;

use App\DataResources\Extras\PaginationInfo;
use App\Exceptions\Request\InvalidPaginationInfoException;
use Illuminate\Foundation\Http\FormRequest;

class BaseFormRequest extends FormRequest
{
    /**
     * overwrite this function to prepare or convert data before validating
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->isJson() && $this->json) {
            $this->replace($this->json->all());
        }

        $input = $this->input();
        $pagingParam = $this->parsePaginationParam();
        $input = array_merge($input, $pagingParam);
        $this->replace($input);
    }

    /**
     * correct raw pagination parameter to support
     * 1. URL query: using json string (disadvantage: ugly url)
     * 2. Send json object in request body (disadvantage: not supported by some web server)
     *
     * @return array<string, mixed>
     */
    private function parsePaginationParam(): array
    {
        if ($this->has('pagination')) {
            $paging = $this['pagination'];
            if (is_string($this['pagination'])) {
                $paging = json_decode($this['pagination'], true);
            }

            return ['pagination' => $paging];
        }
        return [];
    }

    /**
     * @return PaginationInfo|null
     * @throws InvalidPaginationInfoException
     */
    public function getPaginationInfo(): ?PaginationInfo
    {
        return $this->has('pagination') ? PaginationInfo::parse($this['pagination']) : new PaginationInfo();
    }
}
