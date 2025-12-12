<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExctractCsv implements ShouldQueue
{
    use Queueable;

    public $queue = 'default';

    public $delay = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        dd('Extracting data from user' . $this->user->email);
    }
}
