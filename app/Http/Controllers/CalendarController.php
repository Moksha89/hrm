<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $currentDate = Carbon::create($year, $month, 1);
        $daysInMonth = $currentDate->daysInMonth;
        $firstDayOfWeek = $currentDate->dayOfWeek;
        
        $birthdays = Employee::with('user')
            ->whereNotNull('dob')
            ->where('status', 'active')
            ->get()
            ->filter(function ($employee) use ($month) {
                return Carbon::parse($employee->dob)->month == $month;
            })
            ->map(function ($employee) {
                $dob = Carbon::parse($employee->dob);
                return [
                    'type' => 'birthday',
                    'day' => $dob->day,
                    'employee_name' => $employee->user->name,
                    'title' => $employee->user->name . "'s Birthday",
                    'color' => 'blue',
                ];
            });
        
        $today = now();
        $reminders = Document::with(['employee.user'])
            ->whereNotNull('expiry_date')
            ->whereHas('employee', function ($query) {
                $query->where('status', 'active');
            })
            ->get()
            ->map(function ($document) use ($today) {
                $expiryDate = Carbon::parse($document->expiry_date);
                $daysUntilExpiry = $today->diffInDays($expiryDate, false);
                
                $reminderDays = [30, 15, 7, 3, 2, 1];
                
                foreach ($reminderDays as $days) {
                    if ($daysUntilExpiry == $days) {
                        $color = match(true) {
                            $days <= 3 => 'red',
                            $days == 7 => 'orange',
                            default => 'yellow'
                        };
                        
                        return [
                            'type' => 'reminder',
                            'date' => $today->copy(),
                            'day' => $today->day,
                            'month' => $today->month,
                            'year' => $today->year,
                            'employee_name' => $document->employee->user->name,
                            'document_name' => $document->document_name,
                            'days_until_expiry' => $days,
                            'expiry_date' => $expiryDate->format('Y-m-d'),
                            'title' => $document->document_name . ' expires in ' . $days . ' day' . ($days > 1 ? 's' : ''),
                            'color' => $color,
                        ];
                    }
                }
                
                return null;
            })
            ->filter()
            ->filter(function ($reminder) use ($month, $year) {
                return $reminder['month'] == $month && $reminder['year'] == $year;
            });
        
        $events = $birthdays->concat($reminders)->groupBy('day');
        
        return view('calendar.index', compact(
            'currentDate',
            'daysInMonth',
            'firstDayOfWeek',
            'events',
            'month',
            'year'
        ));
    }
}
