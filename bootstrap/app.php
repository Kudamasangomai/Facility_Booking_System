<?php

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
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (NotFoundHttpException $e) {
            // return response()->json([
            //     'message' => 'Record not found.'
            // ], 404);

            // if (request()->is('api/*') && ($e->getPrevious() instanceof  ModelNotFoundException)) {
            //     $message = match ($e->getPrevious()->getModel()) {
            //         User::class => 'User Not Found.',
            //         Facility::class => 'Facility Not Found.',
            //         Booking::class => 'Booking Record Not Found'
            //     };
            //     return response()->json(['message' => $message],404);
            // }

            if (request()->is('api/*') && ($e->getPrevious() instanceof  ModelNotFoundException)) {
                $model = Str::afterLast($e->getPrevious()->getModel(), '\\'); //extract Model name
                return response()->json(['message' => $model.' Record not found',],Response::HTTP_NOT_FOUND);
              }
        });

        $exceptions->renderable(function(AuthorizationException $e){
            if ($e instanceof AuthorizationException) {
                return response()->json([
                    'message' => 'benzi iwe'
                ], Response::HTTP_FORBIDDEN);
            }
        });
    })->create();

    // if ($exception instanceof AuthorizationException) {
    //     // Return a custom response or view for authorization exceptions
    //     if ($request->expectsJson()) {
    //         return response()->json([
    //             'error' => 'You are not authorized to perform this action.',
    //         ], Response::HTTP_FORBIDDEN);
    //     }

    //     return response()->view('errors.403', [], Response::HTTP_FORBIDDEN);
    // }