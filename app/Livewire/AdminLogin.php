<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AdminLogin extends Component
{
    public $login = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'login' => 'required|string',
        'password' => 'required|min:6',
    ];

    public function authenticate()
    {
        $this->validate();

        // Determine if login input is email or username
        $loginType = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        
        $credentials = [
            $loginType => $this->login,
            'password' => $this->password,
            'is_admin' => true
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();
            
            $this->dispatch('notify', [
                'message' => 'Welcome back, Admin!',
                'type' => 'success'
            ]);
            
            return $this->redirect(route('admin'), navigate: true);
        }

        $this->addError('login', 'Invalid admin credentials.');
        $this->password = '';
    }

    public function render()
    {
        return view('livewire.admin-login')->layout('layouts.app');
    }
} 