<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateReservationRequest;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationService $reservationService
    ) {}

    public function store(CreateReservationRequest $request): JsonResponse
    {
        try {
            $reservation = $this->reservationService->createReservation($request->validated());
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'NO_TABLE_AVAILABLE') {
                return response()->json([
                    'success' => false,
                    'message' => 'No available table found for the requested party size and date',
                    'data' => null,
                ], 422);
            }

            throw $e;
        }

        $reservation->load(['customer', 'table.restaurant']);

        return response()->json([
            'success' => true,
            'message' => 'Reservation created successfully.',
            'data' => [
                'confirmation_code' => $reservation->confirmation_code,
                'customer_name' => $reservation->customer?->name ?? '',
                'restaurant' => $reservation->table?->restaurant?->name ?? '',
                'table' => 'Table '.($reservation->table?->table_number ?? ''),
                'party_size' => $reservation->party_size,
                'reservation_date' => $reservation->reservation_date->toIso8601String(),
                'status' => $reservation->status,
            ],
        ], 201);
    }

    public function cancel(string $code): JsonResponse
    {
        try {
            $reservation = $this->reservationService->cancelReservation($code);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'NOT_FOUND') {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservation not found.',
                    'data' => null,
                ], 404);
            }

            if ($e->getMessage() === 'ALREADY_CANCELLED') {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservation is already cancelled.',
                    'data' => null,
                ], 422);
            }

            throw $e;
        }

        return response()->json([
            'success' => true,
            'message' => 'Reservation cancelled successfully.',
            'data' => [
                'confirmation_code' => $reservation->confirmation_code,
                'status' => $reservation->status,
            ],
        ], 200);
    }
}
