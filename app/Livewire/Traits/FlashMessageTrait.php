<?php

namespace App\Livewire\Traits;

trait FlashMessageTrait
{
    public function emitFlashMessage($type, $message)
    {
        session()->flash('flashMessage', [
            'type' => $type,
            'message' => $message,
        ]);

        // Dispatch a browser event for Livewire updates
        $this->dispatch('flash-message', [
            'type' => $type,
            'message' => $message,
        ]);
    }
}
