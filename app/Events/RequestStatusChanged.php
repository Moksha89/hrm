<?php

namespace App\Events;

use App\Models\Request;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('requests'),
            new PrivateChannel('user.' . $this->request->requested_by),
        ];
    }

    public function broadcastAs(): string
    {
        return 'request.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->request->id,
            'type' => $this->request->type,
            'status' => $this->request->status,
            'employee_id' => $this->request->employee_id,
            'requested_by' => $this->request->requested_by,
            'updated_at' => $this->request->updated_at->toISOString(),
        ];
    }
}
