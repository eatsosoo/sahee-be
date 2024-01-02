<?php

namespace App\Exceptions;

use App\Exceptions\Business\ActionFailException;
use App\Helpers\Responses\ApiResponse;
use App\Helpers\Responses\HttpStatuses;
use BenSampo\Enum\Exceptions\InvalidEnumKeyException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render exception response
     * @param $request
     * @param Throwable $exception
     * @return mixed
     * @throws InvalidEnumKeyException
     * @throws Throwable
     */
    public function render($request, Throwable $exception): mixed
    {
        # if not api call render as normal
        if (!$this->shouldReturnJson($request, $exception)) {
            return parent::render($request, $exception);
        }

        return $this->renderForApi($request, $exception);
    }

    /**
     * Return error response for validation exception
     * @param mixed $exception
     * @return Response
     */
    private function apiResponseForValidationError(mixed $exception): Response
    {
        return ApiResponse::v1()
            ->withStatusCode(HttpStatuses::HTTP_BAD_REQUEST)
            ->fail(data: $exception->errors(), dataKey: "errors");
    }

    /**
     * Return custom response for well-known business exception
     * @throws InvalidEnumKeyException
     * @return Response
     */
    private function apiResponseForActionFail(ActionFailException $exception): Response
    {
        $previous = $exception->getPrevious();

        $data = [
            'message' => is_null($previous) ? $exception->getMessage() : $previous->getMessage(),
        ];

        $httpStatus = is_null($previous) ? HttpStatuses::HTTP_INTERNAL_SERVER_ERROR : $previous->responseStatus;

        if (config('app.debug')) {
            $data['trace'] = $exception->getTrace();
        }

        return ApiResponse::v1()
            ->withStatusCode($httpStatus)
            ->fail(data: $data, dataKey: "errors");
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  Request  $request
     * @param AuthenticationException $exception
     * @return Response
     */
    protected function unauthenticated($request, AuthenticationException $exception): Response
    {
        return $this->shouldReturnJson($request, $exception)
        ? ApiResponse::v1()
            ->withStatusCode(HttpStatuses::HTTP_UNAUTHORIZED)
            ->fail(data : [__("auth.unauthenticated")])
        : redirect()->guest($exception->redirectTo() ?? route('unauthenticated'));
    }

    /**
     * Render exception for API call
     * @param Request $request
     * @param Throwable $exception
     * @return mixed|Response
     * @throws InvalidEnumKeyException
     */
    private function renderForApi(Request $request, Throwable $exception): mixed
    {
        # invalid inputs caught by FormRequest
        if ($exception instanceof ValidationException) {
            return $this->apiResponseForValidationError($exception);
        }

        if ($exception instanceof ActionFailException) {
            return $this->apiResponseForActionFail($exception);
        }

        if ($exception instanceof AuthenticationException) {
            return ApiResponse::v1()
                ->withStatusCode(HttpStatuses::HTTP_UNAUTHORIZED)
                ->fail(
                    [
                        'code' => HttpStatuses::HTTP_UNAUTHORIZED,
                        'message' => 'Unauthorized',
                    ],
                    'errors'
                );
        }

        return $this->apiResponseForOtherException($request, $exception);
    }

    /**
     * Return exception response for type of exception
     * @param Request $request
     * @param Throwable $exception
     * @return mixed
     */
    private function apiResponseForOtherException(Request $request, Throwable $exception): mixed
    {
        $data = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ];

        if (config('app.debug')) {
            $data['trace'] = $exception->getTrace();
        }

        return ApiResponse::v1()
            ->withStatusCode(HttpStatuses::HTTP_INTERNAL_SERVER_ERROR)
            ->fail(data: $data, dataKey: "errors");
    }
}
