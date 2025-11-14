<?php

namespace App\Jobs;

use App\Models\Deployment;
use App\Services\AnsibleService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExecuteAnsibleDeployment implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 0; // No timeout

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Deployment $deployment
    ) {}

    /**
     * Execute the job.
     */
    public function handle(AnsibleService $ansibleService): void
    {
        $ansibleService->executeDeployment($this->deployment);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->deployment->update([
            'status' => 'failed',
            'command_output' => $exception->getMessage(),
            'completed_at' => now(),
        ]);
    }
}
