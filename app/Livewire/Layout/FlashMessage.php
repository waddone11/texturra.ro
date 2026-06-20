<?php

namespace App\Livewire\Layout;

use Livewire\Component;

class FlashMessage extends Component
{
    public $message = ''; // Default empty message
    public $type = 'success'; // Default type to 'success'

    // Listen for the 'flashMessage' event
    protected $listeners = ['flashMessage'];

    public function flashMessage($data)
    {
        // Set the message and type from the event payload
        $this->type = $data['type'];
        $this->message = $data['message'];
    }

    public function render()
    {
        return view('livewire.layout.flash-message');
    }
}
