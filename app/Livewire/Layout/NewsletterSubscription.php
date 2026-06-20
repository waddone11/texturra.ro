<?php

namespace App\Livewire\Layout;

use Livewire\Component;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Validator;

class NewsletterSubscription extends Component
{
    public $email;
    public $mode = 'subscribe'; // 'subscribe' or 'unsubscribe'

    public function submit()
    {
        if ($this->mode === 'subscribe') {
            $this->subscribe();
        } else {
            $this->unsubscribe();
        }
    }

    public function subscribe()
    {
        try {
            $validated = Validator::make(
                ['email' => $this->email],
                ['email' => 'required|email|unique:newsletter_subscribers,email']
            )->validate();

            NewsletterSubscriber::create([
                'email' => $validated['email'],
                'status' => 'subscribed',
                'subscribed_at' => now(),
            ]);

            $this->dispatch('flashMessage', [
                'type' => 'success',
                'message' => 'Te-ai abonat cu succes! Primești 10% reducere la prima comandă și acces la ofertele exclusive.'
            ]);

            $this->reset('email');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (NewsletterSubscriber::where('email', $this->email)->where('status', 'subscribed')->exists()) {
                $this->dispatch('flashMessage', [
                    'type' => 'info',
                    'message' => 'Adresa de email este deja abonată la newsletter.'
                ]);
            } elseif (NewsletterSubscriber::where('email', $this->email)->where('status', 'unsubscribed')->exists()) {
                $this->dispatch('flashMessage', [
                    'type' => 'info',
                    'message' => 'Această adresă a fost dezabonată anterior. Te poți reabona folosind aceeași adresă.'
                ]);
            } else {
                $this->dispatch('flashMessage', [
                    'type' => 'error',
                    'message' => 'A apărut o eroare la abonare. Te rugăm să încerci din nou.'
                ]);
            }
        }
    }

    protected function unsubscribe()
    {
        $subscriber = NewsletterSubscriber::where('email', $this->email)->first();

        if (!$subscriber) {
            $this->dispatch('flashMessage', [
                'type' => 'error',
                'message' => 'Această adresă nu este abonată la newsletter.'
            ]);
            return;
        }

        if ($subscriber->status === 'unsubscribed') {
            $this->dispatch('flashMessage', [
                'type' => 'info',
                'message' => 'Te-ai dezabonat deja de la newsletter.'
            ]);
            return;
        }

        $subscriber->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);

        $this->dispatch('flashMessage', [
            'type' => 'success',
            'message' => 'Ai fost dezabonat cu succes. Vei pierde accesul la reducerile și ofertele exclusive TEXTURRA.'
        ]);

        $this->reset('email');
    }

    public function render()
    {
        return view('livewire.layout.newsletter-subscription');
    }
}
