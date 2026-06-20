<?php

namespace App\Livewire;

use Livewire\Component;

class FlashMessage extends Component
{
    public $message = '';
    public $type = '';
    public $show = false;

    protected $listeners = ['flashMessage'];

    public function flashMessage($payload)
    {
        // Expecting an array with 'type' and 'message' keys
        $this->message = $payload['message'] ?? '';
        $this->type = $payload['type'] ?? 'info';
        $this->show = true;

        // Auto-hide the message after 3.5 seconds
        $this->dispatch('hide-flash-message', ['timeout' => 10000]);
    }

    public function render()
    {
        return view('livewire.flash-message');
    }
}






