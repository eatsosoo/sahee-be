<?php

namespace App\Exceptions\DB;

use App\Exceptions\ErrorCodeException;
use App\Enums\ErrorCodes;
use App\Helpers\HttpStatusCode;
use Throwable;

class IdIsNotProvidedException extends ErrorCodeException
{
    public function __construct(
        string $message = ErrorCodes::ERR_ID_IS_NOT_PROVIDED,
        mixed $code = null,
        ?Throwable $previous = null,
        int $responseStatus = HttpStatusCode::INVALID_FORM_REQUEST
    ) {
        $code = $code ?? ErrorCodes::getKey(ErrorCodes::ERR_ID_IS_NOT_PROVIDED);
        parent::__construct(message: $message, code: $code, previous: $previous, responseStatus: $responseStatus);
    }
}
