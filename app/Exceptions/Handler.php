<?php

namespace App\Exceptions;

use App\Core\Traits\ApiResponse;
use App\Helpers\Enum\Message;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|JsonResponse
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof CustomErrorException) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
        if ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }
        if ($e instanceof ModelNotFoundException) {
            return $this->errorResponse(Message::MODEL_NOT_FOUND_EXCEPTION, Response::HTTP_NOT_FOUND);
        }
        if ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        }
        if ($e instanceof AuthorizationException) {
            return $this->errorResponse(Message::AUTHORIZATION_EXCEPTION, Response::HTTP_FORBIDDEN);
        }
        if ($e instanceof NotFoundHttpException) {
            return $this->errorResponse(Message::NOT_FOUND_HTTP_EXCEPTION, Response::HTTP_NOT_FOUND);
        }
        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse(Message::METHOD_NOT_ALLOWED_HTTP_EXCEPTION, Response::HTTP_METHOD_NOT_ALLOWED);
        }
        if ($e instanceof QueryException) {
            if ($e->errorInfo == null) {
                return $this->errorResponse(Message::INTERNAL_SERVER_ERROR, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $codigo = $e->errorInfo[1];
            if ($codigo == 1451 || $codigo == 547) {
                return $this->errorResponse(Message::QUERY_EXCEPTION_1451, Response::HTTP_CONFLICT);
            }
        }
        if ($e instanceof HttpException) {
            if ($e->getCode() === Response::HTTP_TOO_MANY_REQUESTS) {
                return $this->errorResponse(Message::THROTTLE_REQUESTS_EXCEPTION, Response::HTTP_TOO_MANY_REQUESTS)
                    ->withHeaders($e->getHeaders());
            }
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }
        if (config('app.debug')) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->errorResponse(Message::INTERNAL_SERVER_ERROR, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse|Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->errorResponse(Message::AUTHENTICATION_EXCEPTION, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param ValidationException $e
     * @param Request $request
     * @return JsonResponse|\Illuminate\Http\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->toArray();
        return $this->errorResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
