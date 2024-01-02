<?php

namespace App\Exceptions\DB;

use App\Exceptions\ErrorCodeException;
use App\Enums\ErrorCodes;
use App\Helpers\HttpStatusCode;
use Throwable;

class RecordIsNotFoundException extends ErrorCodeException
{
    public function __construct(
        string $message = ErrorCodes::ERR_RECORD_NOT_FOUND,
        mixed $code = null,
        ?Throwable $previous = null,
        int $responseStatus = HttpStatusCode::NOT_FOUND
    ) {
        $code = $code ?? ErrorCodes::getKey(ErrorCodes::ERR_RECORD_NOT_FOUND);
        parent::__construct(message: $message, code: $code, previous: $previous, responseStatus: $responseStatus);
    }
}
