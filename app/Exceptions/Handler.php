<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
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
        $this->reportable(function (Throwable $e) {
            if($e instanceof ModelNotFoundException) {
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

                dd($e->errors());
                foreach($e->errors() as $error) {

                }

                return response()->json([
                    'error' => [
                        'code' => 422,
                        'message' => 'Validation error',
                        'errors' => []
                    ]
                ], 422);
            }
        });
    }
}
