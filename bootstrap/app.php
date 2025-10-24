<?php

use App\Exceptions\TransactionException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $errResponseFn = fn (Request $request, Throwable $error, int $code) => $request->expectsJson()
            ? response()->json(
                ['message' => $error->getMessage(), 'errors' => [$error->getMessage()]],
                $code
            )
            : response()->noContent($code);

        $exceptions
            ->render(fn (NotFoundHttpException $e, Request $r) => $errResponseFn($r, $e, 404))
            ->render(fn (TransactionException $e, Request $r) => $errResponseFn($r, $e, 419));
    })->create();
