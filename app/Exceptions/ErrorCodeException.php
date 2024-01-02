<?php

namespace App\Exceptions;

use App\Enums\ErrorCodes;
use App\Helpers\HttpStatusCode;
use Exception;
use Throwable;

class ErrorCodeException extends Exception
{
    public int $responseStatus;
    /**
     * @var mixed
     */
    protected $code;

    /**
     * __construct
     *
     * @param  string $message
     * @param  mixed $code
     * @param  ?Throwable $previous
     * @param  int $responseStatus
     * @return void
     */
    public function __construct(string $message = '', mixed $code = null, ?Throwable $previous = null, $responseStatus = HttpStatusCode::SERVER_INTERNAL_ERROR)
    {
        parent::__construct(message: $message, code: 0, previous: $previous);
        $this->code = $code;
        $this->responseStatus = $responseStatus;
        if (is_null($code)) {
            try {
                $this->code = ErrorCodes::getKey($message);
            } catch (Exception $ex) {
                // pass
            }
        }
    }
}
