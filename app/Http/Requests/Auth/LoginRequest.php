<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
  

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = $this->input('email');
        $password = $this->input('password');

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'no_wa';

        // $user = \App\Models\User::where($field, $login)->first();

        // if ($user && $user->isBanned()) {

        //     // Ambil ban terakhir
        //     $ban = $user->bans()->latest()->first();

        //     $reason = $ban->comment ?? 'Tidak ada alasan.';
        //     $expired = $ban->expired_at;

        //     // Tentukan pesan durasi
        //     $durationText = 'Permanen';
        //     if ($expired) {
        //         $durationText = 'Hingga ' . $expired->timezone('Asia/Jakarta')->format('d M Y H:i');
        //     }

        //     throw ValidationException::withMessages([
        //         'email' => "Akun Anda telah diban.<br>
        //                     <b>Alasan:</b> $reason<br>
        //                     <b>Durasi:</b> $durationText",
        //     ]);
        // }


        // 🔥 CEK USER SEBELUM ATTEMPT LOGIN
        $user = \App\Models\User::where($field, $login)->first();

        if ($user && $user->isBanned()) {

            // Ambil ban terakhir
            $ban = $user->bans()->latest()->first();

            if ($ban) {
                // pastikan expired_at jadi Carbon
                $expired = $ban->expired_at ? \Carbon\Carbon::parse($ban->expired_at) : null;

                // Kalau ada expired_at dan SUDAH LEWAT → auto-unban & lanjut login
                if ($expired && now()->greaterThan($expired)) {

                    // lepas ban (pakai method dari laravel-ban)
                    if (method_exists($user, 'unban')) {
                        $user->unban();
                    }

                    // opsional: log auto-unban
                    activity('auto-unban')
                        ->causedBy(null)
                        ->performedOn($user)
                        ->withProperties([
                            'expired_at' => $expired->toDateTimeString(),
                            'ip' => $this->ip(),
                        ])
                        ->log('Auto-unban user karena masa ban berakhir saat login');

                } else {
                    // MASIH BAN (permanent atau belum expired) → blokir
                    $reason = $ban->comment ?? 'Tidak ada alasan.';
                    $durationText = 'Permanen';

                    if ($expired) {
                        $durationText = 'Hingga ' . $expired->timezone('Asia/Jakarta')->format('d M Y H:i');
                    }

                    throw ValidationException::withMessages([
                        'email' => "Akun Anda telah diban.<br>
                                    <b>Alasan:</b> $reason<br>
                                    <b>Durasi:</b> $durationText",
                    ]);
                }
            }
        }



        if (! Auth::attempt([$field => $login, 'password' => $password], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey()); // ✔ benar
            throw ValidationException::withMessages([
                'email' => 'Login gagal, periksa kembali akun Anda.',
            ]);
        }


        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // Maksimal 3 percobaan
        $maxAttempts = 3;

        // Durasi lockout = 60 detik
        $decaySeconds = 60;

        if (! RateLimiter::tooManyAttempts($this->throttleKey(), $maxAttempts)) {
            return;
        }

        // Event laravel untuk lockout
        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => "Terlalu banyak percobaan gagal. Coba lagi dalam $seconds detik.",
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
            return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());

    }
}
