<?php

namespace App\Events;

use App\Models\Employee;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Employee $employee;
    public string $action;

    public function __construct(Employee $employee, string $action = 'updated')
    {
        $this->employee = $employee;
        $this->action = $action;
    }

    public function broadcastOn(): array
    {
        $channels = [
            new Channel('employees'),
        ];
        
        // Also broadcast to the team channel if employee has a team
        if ($this->employee->team_id) {
            $channels[] = new PrivateChannel('team.' . $this->employee->team_id);
        }
        
        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'employee.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->employee->id,
            'name' => $this->employee->user->name ?? 'Unknown',
            'status' => $this->employee->status,
            'team_id' => $this->employee->team_id,
            'action' => $this->action,
            'updated_at' => $this->employee->updated_at->toISOString(),
        ];
    }
}
