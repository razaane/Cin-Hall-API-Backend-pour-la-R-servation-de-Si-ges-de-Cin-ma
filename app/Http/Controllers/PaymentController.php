<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $stripe;

    public function __construct()
    {
        // Initialise Stripe avec la clé secrète
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
    }

    public function payWithStripe(Request $request)
    {
        // 1. Valider les données
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'payment_method_id' => 'required|string'
        ]);

        // 2. Récupérer la réservation
        $reservation = Reservation::with('seance', 'seats')
            ->where('id', $request->reservation_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // 3. Vérifier si la réservation est expirée
        if ($reservation->status === 'expired') {
            return response()->json([
                'error' => 'Cette réservation a expiré. Veuillez recommencer.'
            ], 400);
        }

        // 4. Vérifier si déjà payée
        if ($reservation->status === 'paid') {
            return response()->json([
                'error' => 'Cette réservation est déjà payée'
            ], 400);
        }

        // 5. Calculer le montant total
        $amount = $reservation->seance->price * $reservation->seats->count();

        try {
            // 6. Demander à Stripe de traiter le paiement
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => $amount * 100, // Stripe utilise les centimes
                'currency' => 'mad',        // Dirham marocain
                'payment_method' => $request->payment_method_id,
                'confirm' => true
            ]);

            // 7. Enregistrer le paiement dans notre base de données
            $payment = Payment::create([
                'reservation_id' => $reservation->id,
                'amount' => $amount,
                'method' => 'stripe',
                'transaction_id' => $paymentIntent->id,
                'status' => 'success'
            ]);

            // 8. Mettre à jour le statut de la réservation
            $reservation->update(['status' => 'paid']);

            // 9. Générer un ticket simple
            $ticket = Ticket::create([
                'reservation_id' => $reservation->id,
                'user_id' => auth()->id(),
                'qr_code' => Str::random(32), // Code unique
                'pdf_path' => null
            ]);

            // 10. Retourner la réponse
            return response()->json([
                'success' => true,
                'message' => 'Paiement réussi !',
                'payment' => $payment,
                'ticket' => $ticket
            ]);

        } catch (\Exception $e) {
            // Si erreur, enregistrer l'échec
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