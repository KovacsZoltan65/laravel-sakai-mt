<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
        $this->renderable(function(HttpExceptionInterface $e, Request $request) {
            if( $e->getStatusCode() === 503 && $request->expectsJson() === false ) {
                return \Inertia\Inertia::render('Errors/Maintenance')
                    ->toResponse($request)
                    ->setStatusCode(503);
            }
        });
    }
}
