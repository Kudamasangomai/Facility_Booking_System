<?php

namespace App\Jobs;

use App\Enum\UserType;
use App\Mail\FacilityBooked;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessBookingEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue,SerializesModels, Queueable;

    /**
     * Create a new job instance.
     */

    protected $booking;
    protected $facility;
    // public $user;
    public function __construct(Facility $facility , Booking $booking)
    {
        $this->booking =  $booking;
        // $this->user = $user;
        $this->facility = $facility;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $admins= User::where('usertype',UserType::Admin->value)->get();

        foreach ($admins as $user) {
            Mail::to($user->email)->send(new FacilityBooked($this->facility ,$this->booking));
        }
    }
}
