<?php

namespace App\Livewire;

use Livewire\Component;

class Announcements extends Component
{
    // Sample data (you can replace it with dynamic data from the database)
    public $announcements = [
        'Announcement 1: Site maintenance on Dec 5.',
        'Announcement 2: New features released.',
        'Announcement 3: Security update scheduled for Dec 10.'
    ];

    public function render()
    {
        return view('livewire.announcements');
    }
}
