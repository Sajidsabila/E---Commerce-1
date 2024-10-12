<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Password;
#[Title('Forgot Password')]
class ForgotPasswordPage extends Component
{

    public $email;

    public function save()
    {
        $this->validate([
            'email' => 'required|email|exists:users,email'
        ]);
        $status = Password::sendResetLink(['email' => $this->email]);
        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('success', 'Email berhasil dikirim di email anda');
            $this->email = '';
        }

    }
    public function render()
    {
        return view('livewire.auth.forgot-password-page');
    }
}
