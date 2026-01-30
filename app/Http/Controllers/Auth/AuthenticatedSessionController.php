<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Jenssegers\Agent\Agent;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }



    public function store(LoginRequest $request)
    {
        try {
            $request->authenticate();
        } catch (ValidationException $e) {

            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => $e->errors()
                ], 422);
            }

            throw $e;
        }


        $request->session()->regenerate();

        auth()->user()->update([
            'last_ip' => $request->ip(),
            'last_login' => now(),
        ]);

        $agent = new Agent;
        activity()
            ->useLog('login')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'agent' => [
                    'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
                    'os' => $agent->platform() . ' ' . $agent->version($agent->platform()),
                    'device' => $agent->device(),
                    'is_mobile' => $agent->isMobile(),
                    'is_desktop' => $agent->isDesktop(),
                    'raw' => $request->header('User-Agent'),
                ],
                'request' => [
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                ],
            ])
            ->log('Login berhasil');

        if ($request->expectsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Login berhasil'
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $agent = new Agent;

        activity()
            ->useLog('logout')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'agent' => [
                    'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
                    'os' => $agent->platform() . ' ' . $agent->version($agent->platform()),
                    'device' => $agent->device(),
                    'is_mobile' => $agent->isMobile(),
                    'is_desktop' => $agent->isDesktop(),
                    'raw' => $request->header('User-Agent'),
                ],
                'request' => [
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                ],
            ])
            ->log('Logout berhasil');


        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
