<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;

class AuthController extends Controller
{
    public function showLogin(array $params): void
    {
        if (Auth::check()) {
            redirect('dashboard');
        }
        $this->render('auth/login', [], 'guest');
    }

    public function login(array $params): void
    {
        Csrf::verify();

        $usuario  = $this->input('usuario');
        $password = $this->input('password');

        if (Auth::attempt($usuario, $password)) {
            clear_old();
            redirect('dashboard');
        }

        set_old(['usuario' => $usuario]);
        flash('error', 'Usuario o contraseña incorrectos.');
        redirect('login');
    }

    public function logout(array $params): void
    {
        Auth::logout();
        redirect('login');
    }
}
