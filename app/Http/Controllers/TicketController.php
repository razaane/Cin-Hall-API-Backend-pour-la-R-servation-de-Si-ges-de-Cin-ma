<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Ticket;
use App\Models\Reservation;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketController extends Controller
{

    public function generateTicket($reservation_id){
        //recuperer la reservation 
        $reservation = Reservation::find($reservation_id);
        //c'est reservation non trouvée
        if(!$reservation){
            return response()->json(['erreur'=>"reservation non trouvée !!"],404);
        }
        //generée une qr code 
        $qrContent="ticket_".$reservation_id."_".time().Str::random(5);
        //créer une ticket 
        $ticket=Ticket::create([
            'reservation_id'=>$reservation_id,
            'user_id'=>$reservation->user_id,
            'qr_code'=>$qrContent
        ]);

        return $ticket;
    }

    public function generationPdf($ticket_id){
        $ticket = Ticket::find($ticket_id);

        if(!$ticket){
            return response()->json(['erreur'=>"ticket non trouvée !!"],404);
        }

        $reservation = $ticket->reservation;

        $qrSvg = QrCode::format('svg')
            ->size(200)
            ->generate($ticket->qr_code);

        $qrBase64 = base64_encode($qrSvg);

        $qrImage = "data:image/svg+xml;base64," . $qrBase64;

        PDF::setOptions([
            'isRemoteEnabled' => true,
            'dpi' => 96,
        ]);

        $pdf = PDF::loadHTML("
            <h2 style='text-align:center;'>Ticket Cinéma</h2>

            <p><strong>Nom:</strong> {$reservation->user->name}</p>
            <p><strong>Film:</strong> ".($reservation->seance->film->title ?? 'N/A')."</p>
            <p><strong>Salle:</strong> ".($reservation->seance->salle->name ?? 'N/A')."</p>
            <p><strong>Siège:</strong> ".($reservation->seat ?? 'N/A')."</p>
            <p><strong>Date:</strong> ".($reservation->seance->date ?? 'N/A')."</p>

            <div style='text-align:center; margin-top:20px;'>
                <img src='{$qrImage}' width='150'>
            </div>
        ");

        $folder = public_path('tickets');

        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $fileName = "ticket_{$ticket->id}.pdf";
        $path = $folder.'/'.$fileName;

        $pdf->save($path);

        $ticket->update([
            'pdf_path' => 'tickets/'.$fileName
        ]);

        return $pdf->download($fileName);
    }
}
    