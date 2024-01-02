<?php

namespace App\Exceptions\Business;

use App\Exceptions\ErrorCodeException;
use App\Enums\ErrorCodes;
use App\Helpers\HttpStatusCode;
use Throwable;

class InvalidModelInstanceException extends ErrorCodeException
{
    public function __construct(
        string $message = ErrorCodes::ERR_MODEL_CLASS_NOT_EXISTS,
        mixed $code = null,
        ?Throwable $previous = null,
        int $responseStatus = HttpStatusCode::NOT_FOUND
    ) {
        $code = $code ?? ErrorCodes::getKey(ErrorCodes::ERR_MODEL_CLASS_NOT_EXISTS);
        parent::__construct(message: $message, code: $code, previous: $previous, responseStatus: $responseStatus);
    }
}
