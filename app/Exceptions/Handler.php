<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e) {
            if($e instanceof NotFoundHttpException) {
                return response()->json([
                    'error' => [
                        'code' => 404,
                        'message' => 'Not found',
                    ]
                ], 404);
            }

            if($e instanceof UnauthorizedException) {
                return response()->json([
                    'error' => [
                        'code' => 401,
                        'message' => 'Unauthorized',
                    ]
                ], 401);
            }

            if($e instanceof ValidationException) {
                $messages = [];

                // NOTE: $errors inside foreach loop is array of messages
                foreach($e->errors() as $key=>$errors) {
                    $errorMessages = [];

                    // NOTE: specification requires custom messages for validation
                    foreach($errors as $error) {
                        $message = $error;

                        if(str_contains($error, 'is required')) {
                            $message = "field ${key} can not be blank";
                        }

                        $errorMessages[] = $message;
                    }

                    $messages[$key] = $errorMessages;
                }

                return response()->json([
                    'error' => [
                        'code' => 422,
                        'message' => 'Validation error',
                        'errors' => $messages
                    ]
                ], 422);
            }
        });
    }
}
