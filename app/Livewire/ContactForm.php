<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $subject = '';
    public string $message = '';
    // Honeypot (should remain empty)
    public string $hp = '';

    public bool $sent = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'min:3', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'hp' => ['nullable', 'size:0'],
        ];
    }

    public function submit(): void
    {
        $this->validate();

        // Basic rate limiting to avoid spam: 5 attempts per minute per IP
        $rateKey = 'contact:' . (string) request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $this->addError('rate', 'Terlalu banyak percubaan. Sila cuba lagi sebentar lagi.');
            return;
        }

        $toAddress = config('mail.from.address') ?: 'assyaafi96@gmail.com';

        // Compose plain-text message
        $text = "Name: {$this->name}\n" .
            "Email: {$this->email}\n\n" .
            $this->message;

        // Send email as raw text; set reply-to to the submitter
        Mail::raw($text, function ($mail) use ($toAddress) {
            $mail->to($toAddress)
                ->subject('[Contact] ' . $this->subject)
                ->from(config('mail.from.address'), config('mail.from.name'))
                ->replyTo($this->email, $this->name);
        });

        RateLimiter::hit($rateKey, 60);

        $this->reset(['name', 'email', 'subject', 'message']);
        $this->sent = true;
        $this->dispatch('flash', ['type' => 'success', 'message' => 'Terima kasih! Mesej anda telah dihantar.']);
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
