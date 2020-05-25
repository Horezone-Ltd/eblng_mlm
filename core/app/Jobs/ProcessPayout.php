<?php

namespace App\Jobs;

use App\Helpers\PaystackEndpoints;
use App\Helpers\Peach;
use App\PayoutHistory;
use App\Traits\Curl;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPayout implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Curl;
    protected $user;
    protected $peach;
    protected $data;
    /**
     * Create a new job instance.
     * @param User $user
     * @param array $data
     * @param Peach $peach
     */
    public function __construct(Peach $peach, User $user, array $data)
    {
        Log::info('public function __construct');
        $this->user = $user;
        $this->data = $data;
        $this->peach = $peach;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $response = $this->curl(PaystackEndpoints::Transfer, $this->data);
        $payout_history = new PayoutHistory([
            'amount' => $this->data['amount'],
            'narration' => $this->data['reason'],
            'status' => $response->status,
            'pv' => $user->point_value ?? 0
        ]);
        $this->user->payoutHistories()->save($payout_history);
    }
}
