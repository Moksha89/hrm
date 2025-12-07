<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Loan;
use App\Models\Request;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class DashboardStats extends Component
{
    public int $totalEmployees = 0;
    public int $activeEmployees = 0;
    public int $totalTeams = 0;
    public int $pendingRequests = 0;
    public int $activeLoans = 0;
    public string $totalLoanAmount = '0';

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $user = Auth::user();

        $this->totalEmployees = Employee::count();
        $this->activeEmployees = Employee::where('status', 'active')->count();
        $this->totalTeams = Team::count();

        if ($user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('hr')) {
            $this->pendingRequests = Request::where('status', 'pending')->count();
        } else {
            $this->pendingRequests = Request::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count();
        }

        $this->activeLoans = Loan::where('status', 'active')->count();
        $this->totalLoanAmount = number_format(Loan::where('status', 'active')->sum('remaining_balance'), 2);
    }

    #[On('stats-updated')]
    public function refreshStats()
    {
        $this->loadStats();
    }

    #[On('echo-private:dashboard,StatsUpdated')]
    public function handleStatsUpdate()
    {
        $this->loadStats();
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
