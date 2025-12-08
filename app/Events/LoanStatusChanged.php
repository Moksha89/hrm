<?php

namespace App\Events;

use App\Models\Loan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoanStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Loan $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('loans'),
            new PrivateChannel('user.' . $this->loan->employee->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'loan.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->loan->id,
            'employee_id' => $this->loan->employee_id,
            'status' => $this->loan->status,
            'loan_amount' => $this->loan->loan_amount,
            'remaining_balance' => $this->loan->remaining_balance,
            'updated_at' => $this->loan->updated_at->toISOString(),
        ];
    }
}
