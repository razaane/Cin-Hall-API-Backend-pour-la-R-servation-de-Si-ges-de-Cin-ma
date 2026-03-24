<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Reservation;

class ExpireReservation implements ShouldQueue
{
    use Queueable;
    public $reservation_id;
    /**
     * Create a new job instance.
     */
    public function __construct(int $reservation_id)
    {
       $this->reservation_id = $reservation_id;
    }

    /**
     * Execute the job.
     */
    public function handle() : void
    {
        $reservation = Reservation::find($this->reservation_id);

        // $reservation->delete();

        if($reservation && $reservation->status =='pending'){
            $reservation->status = 'canceled';
            $reservation->save();
        }
    }
}
