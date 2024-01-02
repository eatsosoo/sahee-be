<?php

namespace App\Exceptions\Business;

use App\Exceptions\ErrorCodeException;
use App\Enums\ErrorCodes;
use App\Helpers\HttpStatusCode;
use Throwable;

class ActionFailException extends ErrorCodeException
{
    public function __construct(string $message = ErrorCodes::ERR_ACTION_FAIL, mixed $code = null, ?Throwable $previous = null, int $responseStatus = null)
    {
        $code = $code ?? ErrorCodes::getKey(ErrorCodes::ERR_ACTION_FAIL);

        if (is_null($responseStatus)) {
            $responseStatus = HttpStatusCode::SERVER_INTERNAL_ERROR;
            if ($previous instanceof ErrorCodeException && isset($previous->responseStatus)) {
                $responseStatus = $previous->responseStatus;
            }
        }

        parent::__construct(message: $message, code: $code, previous: $previous, responseStatus: $responseStatus);
    }
}
