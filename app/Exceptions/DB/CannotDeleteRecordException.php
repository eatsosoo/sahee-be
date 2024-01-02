<?php

namespace App\Exceptions\DB;

use App\Enums\ErrorCodes;
use App\Exceptions\ErrorCodeException;
use App\Helpers\HttpStatusCode;
use Throwable;

class CannotDeleteRecordException extends ErrorCodeException
{
    public function __construct(
        string $message = ErrorCodes::ERR_CANNOT_DELETE_RECORD,
        mixed $code = null,
        ?Throwable $previous = null,
        int $responseStatus = HttpStatusCode::BAD_REQUEST
    ) {
        $code = $code ?? ErrorCodes::getKey(ErrorCodes::ERR_CANNOT_DELETE_RECORD);
        parent::__construct(message: $message, code: $code, previous: $previous, responseStatus: $responseStatus);
    }
}
