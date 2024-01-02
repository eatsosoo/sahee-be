<?php

namespace App\Helpers;

class HttpStatusCode
{
    // Http status code
    public const SUCCESS = 200;
    public const BAD_REQUEST = 400;
    public const UNAUTHORIZED = 401;
    public const NOT_FOUND = 404;
    public const INVALID_FORM_REQUEST = 422;
    public const TOO_MANY_REQUEST = 429;
    public const SERVER_INTERNAL_ERROR = 500;
}
