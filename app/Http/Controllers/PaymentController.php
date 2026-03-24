<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
    }

    #[OA\Post(
        path: '/api/payments/stripe',
        summary: 'Pay a reservation using Stripe',
        tags: ['Payments'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['reservation_id', 'payment_method_id'],
            properties: [
                new OA\Property(property: 'reservation_id', type: 'integer', example: 1),
                new OA\Property(property: 'payment_method_id', type: 'string', example: 'pm_card_visa')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Payment successful'
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid request or reservation issue'
    )]
    #[OA\Response(
        response: 500,
        description: 'Payment failed'
    )]
    public function payWithStripe(Request $request)
    {
        // 1. Validation
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'payment_method_id' => 'required|string'
        ]);

        // 2. Récupération réservation
        $reservation = Reservation::with('seance', 'seats')
            ->where('id', $request->reservation_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // 3. Vérification statut
        if ($reservation->status === 'expired') {
            return response()->json([
                'error' => 'Cette réservation a expiré. Veuillez recommencer.'
            ], 400);
        }

        if ($reservation->status === 'paid') {
            return response()->json([
                'error' => 'Cette réservation est déjà payée'
            ], 400);
        }

        // 4. Calcul montant
        $amount = $reservation->seance->price * $reservation->seats->count();

        try {
            // 5. Paiement Stripe
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => $amount * 100,
                'currency' => 'mad',
                'payment_method' => $request->payment_method_id,
                'confirm' => true
            ]);

            // 6. Save payment
            $payment = Payment::create([
                'reservation_id' => $reservation->id,
                'amount' => $amount,
                'method' => 'stripe',
                'transaction_id' => $paymentIntent->id,
                'status' => 'success'
            ]);

            // 7. Update réservation
            $reservation->update(['status' => 'paid']);

            // 8. Create ticket
            $ticket = Ticket::create([
                'reservation_id' => $reservation->id,
                'user_id' => auth()->id(),
                'qr_code' => Str::random(32),
                'pdf_path' => null
            ]);

            // 9. Response
            return response()->json([
                'success' => true,
                'message' => 'Paiement réussi !',
                'payment' => $payment,
                'ticket' => $ticket
            ]);

        } catch (\Exception $e) {

            // Save failed payment
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount' => $amount,
                'method' => 'stripe',
                'status' => 'failed'
            ]);

            return response()->json([
                'error' => 'Paiement échoué: ' . $e->getMessage()
            ], 500);
        }
    }
}