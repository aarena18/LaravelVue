<?php

namespace App\Listeners;

use App\Events\PlaylistCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BillUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PlaylistCreated $event): void
    {
        // dd('User is billed');
    }
}
