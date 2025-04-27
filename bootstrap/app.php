<?php

use App\Http\Middleware\isAdmin;
use App\Models\User;
use App\Models\Booking;
use App\Models\Facility;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Foundation\Application;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'isAdmin' => isAdmin::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (NotFoundHttpException $e) {

            if (request()->is('api/*') && ($e->getPrevious() instanceof  ModelNotFoundException)) {
                //extract Model name
                $model = Str::afterLast($e->getPrevious()->getModel(), '\\');
                return response()->json(
                    ['message' => $model . ' Record not found'],
                    Response::HTTP_NOT_FOUND
                );
            }
        });

        $exceptions->renderable(function (AuthorizationException $e) {
            if ($e instanceof AuthorizationException) {
                return response()->json([
                    'message' => 'You Are Not Authorize To Perform This Action'
                ], Response::HTTP_UNAUTHORIZED);
            }
        });
    })->create();
