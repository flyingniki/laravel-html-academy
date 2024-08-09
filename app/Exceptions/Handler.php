<?php

namespace App\Exceptions;

use App\Http\Responses\ExceptionResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
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


    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            $exception = new NotFoundHttpException('Страница не найдена', previous:$exception);
        }

        if ($exception instanceof ModelNotFoundException) {
            $exception = new ModelNotFoundException('Страница не найдена', previous:$exception);
        }

        if ($exception instanceof AuthenticationException) {
            $exception = new AuthenticationException(
                'Запрос требует аутентификации.',
                $exception->guards(),
                $exception->redirectTo()
            );
        }

        if ($exception instanceof AuthorizationException) {
            $exception = new AuthorizationException(trans($exception->getMessage()), previous:$exception);
        }

        if ($exception instanceof AccessDeniedHttpException) {
            $exception = new AccessDeniedHttpException(trans($exception->getMessage()), previous:$exception);
        }

        return parent::render($request, $exception);
    }

    protected function prepareJsonResponse($request, Throwable $e)
    {
        return (new ExceptionResponse($e))->toResponse($request);
    }
}
