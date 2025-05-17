<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            // Not logged in
            return redirect('login');
        }

        $user = Auth::user();

        // CheckRole.php
        if ($user->role !== $role) {
            switch ($user->role) {
                case 'admin':
                    return redirect('/dashboard');
                case 'dosen':
                    return redirect('/dashboard');
                case 'mahasiswa':
                    return redirect('/dashboard');
                case 'direktur':
                    return redirect('/dashboard');
                default:
                    Auth::logout();
                    return redirect('login')->with('error', 'Unauthorized access.');
            }
        }


        return $next($request);
    }
}
