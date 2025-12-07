<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeTable extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $team = '';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingTeam()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->team = '';
        $this->resetPage();
    }

    #[On('employee-created')]
    #[On('employee-updated')]
    #[On('employee-deleted')]
    public function refreshList()
    {
        // Livewire will automatically re-render
    }

    public function render()
    {
        $user = Auth::user();

        $query = Employee::with(['user', 'team'])
            ->when($this->search, function ($q) {
                $q->whereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('mobile', 'like', '%' . $this->search . '%');
                })
                ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($q) {
                $q->where('status', $this->status);
            })
            ->when($this->team, function ($q) {
                $q->where('team_id', $this->team);
            });

        // Role-based filtering
        if ($user->hasRole('team-leader')) {
            $teamIds = $user->employee?->team_id ? [$user->employee->team_id] : [];
            $query->whereIn('team_id', $teamIds);
        } elseif ($user->hasRole('manager')) {
            $managedTeamIds = Team::where('manager_id', $user->id)->pluck('id');
            $query->whereIn('team_id', $managedTeamIds);
        }

        // Sorting
        if ($this->sortBy === 'name') {
            $query->join('users', 'employees.user_id', '=', 'users.id')
                ->orderBy('users.name', $this->sortDirection)
                ->select('employees.*');
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        $employees = $query->paginate(15);
        $teams = Team::orderBy('name')->get();

        return view('livewire.employee-table', [
            'employees' => $employees,
            'teams' => $teams,
        ]);
    }
}
