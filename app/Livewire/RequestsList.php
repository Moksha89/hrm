<?php

namespace App\Livewire;

use App\Models\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class RequestsList extends Component
{
    use WithPagination;

    #[Url]
    public string $tab = 'my-requests';

    #[Url]
    public string $status = '';

    #[Url]
    public string $type = '';

    public function setTab($tab)
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->status = '';
        $this->type = '';
        $this->resetPage();
    }

    #[On('request-created')]
    #[On('request-updated')]
    public function refreshList()
    {
        // Livewire will automatically re-render
    }

    public function handleRequestUpdate()
    {
        // Livewire will automatically re-render
    }

    public function render()
    {
        $user = Auth::user();

        if ($this->tab === 'my-requests') {
            $query = Request::with(['user'])
                ->where('user_id', $user->id);
        } else {
            // Approvals tab - show requests that need approval
            $query = Request::with(['user'])
                ->where('status', 'pending');

            // Role-based filtering for approvals
            if (!$user->hasRole('admin') && !$user->hasRole('manager') && !$user->hasRole('hr')) {
                // Non-admin users can only see their own requests
                $query->where('user_id', $user->id);
            }
        }

        $query->when($this->status, function ($q) {
            $q->where('status', $this->status);
        })
        ->when($this->type, function ($q) {
            $q->where('type', $this->type);
        })
        ->orderBy('created_at', 'desc');

        $requests = $query->paginate(10);

        $myRequestsCount = Request::where('user_id', $user->id)->count();
        $pendingApprovalsCount = 0;

        if ($user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('hr')) {
            $pendingApprovalsCount = Request::where('status', 'pending')->count();
        }

        return view('livewire.requests-list', [
            'requests' => $requests,
            'myRequestsCount' => $myRequestsCount,
            'pendingApprovalsCount' => $pendingApprovalsCount,
        ]);
    }
}
