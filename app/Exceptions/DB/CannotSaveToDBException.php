<?php

namespace App\Exceptions\DB;

use App\Exceptions\ErrorCodeException;
use App\Enums\ErrorCodes;
use App\Helpers\HttpStatusCode;
use Throwable;

class CannotSaveToDBException extends ErrorCodeException
{
    public function __construct(
        string $message = ErrorCodes::ERR_CANNOT_SAVE_TO_DB,
        mixed $code = null,
        ?Throwable $previous = null,
        int $responseStatus = HttpStatusCode::BAD_REQUEST
    ) {
        $code = $code ?? ErrorCodes::getKey(ErrorCodes::ERR_CANNOT_SAVE_TO_DB);
        parent::__construct(message: $message, code: $code, previous: $previous, responseStatus: $responseStatus);
    }
}
