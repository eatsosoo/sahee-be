<?php

namespace App\Helpers\Responses;

use App\DataResources\BaseDataResource;
use App\DataResources\IDataResource;
use App\Enums\ApiResponseResults;
use Illuminate\Contracts\Translation\Translator;

class ApiResponse
{
    private string $version;
    private ?int $statusCode = null;
    private mixed $headers = ['Content-Type' => 'application/json'];
    private string $dataKey = 'message';
    private mixed $resultState = ApiResponseResults::SUCCESS;
    private mixed $pagination = null;
    private mixed $message = '';
    private mixed $item = [];

    public function __construct(string $version = '1.0')
    {
        $this->version = $version;
    }

    /**
     * setup http status code
     *
     * @param int $statusCode
     * @return $this
     */
    public function withStatusCode(int $statusCode = HttpStatuses::HTTP_OK): ApiResponse
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * setup data key
     *
     * @param string $dataKey
     * @return $this
     */
    public function withDataKey(string $dataKey = 'message')
    {
        $this->dataKey = $dataKey;
        return $this;
    }

    /**
     * setup result
     *
     * @param bool $state
     * @return $this
     */
    public function withResultState($state = ApiResponseResults::SUCCESS)
    {
        $this->resultState = $state;
        return $this;
    }

    /**
     * setup header
     *
     * @param mixed $headers
     * @return $this
     */
    public function withHeaders(mixed $headers)
    {
        $this->headers = $headers;
        $this->headers['Content-Type'] = 'application/json';
        return $this;
    }

    /**
     * setup pagination
     *
     * @param int $totalPages
     * @param int $total
     * @return $this
     */
    public function withTotalPages(int $totalPages, int $total)
    {
        $this->pagination = ['total_pages' => $totalPages, 'total' => $total];
        return $this;
    }

    /**
     * @param mixed $data
     * @param string|null $dataKey
     * @return mixed
     */
    public function send(mixed $data, string $dataKey = null): mixed
    {
        $dataKey = $dataKey ?? $this->dataKey;
        $data = ($data instanceof IDataResource) ?
        $data->toArray() :
        BaseDataResource::objectToArray($data);

        $content = [
            'version' => $this->version,
            'result' => $this->resultState,
            'data' => [
                $dataKey => $data,
            ],
        ];
        if (!is_null($this->pagination)) {
            $content['data']['pagination'] = $this->pagination;
        }

        if ($this->message) {
            $content['data']['message'] = $this->message;
        }

        if ($this->item) {
            foreach ($this->item as $key => $item) {
                $content[$key] = $item;
            }
        }

        $status = $this->statusCode ?? HttpStatuses::HTTP_OK;
        $headers = $this->headers;
        return response($content, $status, $headers);
    }

    /**
     * @param mixed $data
     * @param string $dataKey
     * @return mixed
     */
    public function success(mixed $data = [], string $dataKey = 'message'): mixed
    {
        $status = $this->statusCode ?? HttpStatuses::HTTP_OK;
        return $this->withDataKey($dataKey)
            ->withStatusCode($status)
            ->withResultState(ApiResponseResults::SUCCESS)
            ->send($data);
    }

    /**
     * @param mixed $data
     * @param string $dataKey
     * @return mixed
     */
    public function fail(mixed $data = [], string $dataKey = 'errors'): mixed
    {
        $status = $this->statusCode ?? HttpStatuses::HTTP_INTERNAL_SERVER_ERROR;
        return $this->withDataKey($dataKey)
            ->withStatusCode($status)
            ->withResultState(ApiResponseResults::FAIL)
        // ->withMessage(__('message.failed'))
            ->send($data);
    }

    /**
     * @param bool $result
     * @param mixed|null $data
     * @param string $dataKey
     * @return mixed
     */
    public function report(bool $result, mixed $data = [], string $dataKey = 'message'): mixed
    {
        return $result ? $this->success($data, dataKey : $dataKey): $this->fail($data, dataKey: $dataKey);
    }

    /**
     * @param  Translator|string|array<mixed>|null $message
     * @return $this
     */
    public function withMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param  array<string,mixed> $item
     * @return $this
     */
    public function withObject($item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * @return ApiResponse
     */
    public static function v1(): ApiResponse
    {
        return new ApiResponse('1.0');
    }
}
