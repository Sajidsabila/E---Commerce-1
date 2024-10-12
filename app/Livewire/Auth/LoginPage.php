<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Session;
#[Title('Login')]
class LoginPage extends Component
{
    public $email;
    public $password;
    public function render()
    {
        return view('livewire.auth.login-page');
    }

    public function save()
    {
        $this->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        if (!auth()->attempt(['email' => $this->email, 'password' => $this->password])) {
            Session()->flash('error', 'Ussrname atau Paswword salah');
            return back();
        }

        return redirect()->intended();
    }
}
