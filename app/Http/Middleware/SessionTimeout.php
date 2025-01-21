<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionTimeout
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = session('last_activity', time());
            $sessionTimeout = 15 * 60; // 15 menit dalam detik

            if (time() - $lastActivity > $sessionTimeout) {
                // Logout dan invalidate session
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Menyimpan flash message
                // Session::flash('status', 'Sesi Anda telah habis. Silakan login kembali.');

                // Redirect ke halaman login
                return redirect('/login');
            }

            // Memperbarui waktu aktivitas terakhir
            session(['last_activity' => time()]);
        }

        return $next($request);
    }

}