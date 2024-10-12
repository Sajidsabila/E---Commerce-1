<?php

namespace App\Livewire\Auth;

use Password;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;

#[Title('Reset Password')]
class ResetPasswordPage extends Component
{
    public $token;

    #[Url]
    public $email;
    public $password;
    public $password_confirmation;
    public function render()
    {
        return view('livewire.auth.reset-password-page');
    }

    public function mount($token)
    {

        $this->token = $token;
    }

    public function save()
    {
        // Validasi input menggunakan Livewire
        $this->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed'
        ]);

        try {
            $status = Password::reset(
                [
                    'email' => $this->email,
                    'password' => $this->password,
                    'password_confirmation' => $this->password_confirmation,
                    'token' => $this->token
                ],
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();
                    event(new PasswordReset($user));
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                session()->flash('success', 'Password berhasil diubah');
                return redirect('/login');
            } else {
                session()->flash('error', 'Token expired atau email tidak valid');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect()->back();
        }
    }
}
