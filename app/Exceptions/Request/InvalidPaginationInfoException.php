<?php

namespace App\Exceptions\Request;

use App\Exceptions\ErrorCodeException;
use App\Enums\ErrorCodes;
use Throwable;

class InvalidPaginationInfoException extends ErrorCodeException
{
    public function __construct(string $message = ErrorCodes::ERR_PAGINATION, mixed $code = null, ?Throwable $previous = null)
    {
        $code = $code ?? ErrorCodes::getKey(ErrorCodes::ERR_PAGINATION);
        parent::__construct(message: $message, code: $code, previous: $previous);
    }
}
