<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * API JSON envelopes for exceptions. Registered via bootstrap/app.php → withExceptions().
 */
final class Handler
{
    public static function renderApiValidation(ValidationException $e, Request $request): ?Response
    {
        if (! $request->is('api/*')) {
            return null;
        }

        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'data' => $e->errors(),
        ], 422);
    }

    public static function renderApiModelNotFound(ModelNotFoundException $e, Request $request): ?Response
    {
        if (! $request->is('api/*')) {
            return null;
        }

        return response()->json([
            'success' => false,
            'message' => 'Resource not found.',
            'data' => null,
        ], 404);
    }
}
