<?php

namespace App\Livewire;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public bool $isOpen = false;
    public int $unreadCount = 0;

    public function mount()
    {
        $this->loadUnreadCount();
    }

    public function loadUnreadCount()
    {
        $this->unreadCount = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();
    }

    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            $this->loadUnreadCount();
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->unreadCount = 0;
    }

    public function handleNewNotification()
    {
        $this->loadUnreadCount();
    }

    public function render()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('livewire.notification-dropdown', [
            'notifications' => $notifications,
        ]);
    }
}
