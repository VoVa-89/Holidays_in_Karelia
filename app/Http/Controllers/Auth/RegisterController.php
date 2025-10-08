<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        // DEV: смягчаем лимит для локальной разработки
        $this->middleware('throttle:30,1')->only(['showRegistrationForm', 'register']);
        // Honeypot для защиты от ботов на регистрации
        $this->middleware('honeypot')->only(['register']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Имя обязательно для заполнения.',
            'name.string' => 'Имя должно быть строкой.',
            'name.max' => 'Имя не может быть длиннее 255 символов.',
            'name.unique' => 'Пользователь с таким именем уже существует.',
            'email.required' => 'Email обязателен для заполнения.',
            'email.email' => 'Email должен быть действительным адресом электронной почты.',
            'email.unique' => 'Пользователь с таким email уже существует.',
            'password.required' => 'Пароль обязателен для заполнения.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
            'password.confirmed' => 'Подтверждение пароля не совпадает.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        // Логируем регистрацию и запускаем событие (триггер отправки письма)
        Log::info('User registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
        // Отправка письма верификации (в локалке уходит в лог). Не роняем регистрацию при ошибках SMTP
        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            Log::warning('Email verification dispatch failed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }
        return $user;
    }

    /**
     * После регистрации перенаправляем на страницу подтверждения email
     */
    protected function registered(Request $request, $user)
    {
        // Если email не подтвержден — показать уведомление о подтверждении
        if (method_exists($user, 'hasVerifiedEmail') && !$user->hasVerifiedEmail()) {
            // В локальной среде генерируем и передаем в сессию HTTP-ссылку для удобной верификации
            if (App::environment('local')) {
                $verificationUrl = URL::temporarySignedRoute(
                    'verification.verify',
                    now()->addMinutes(120),
                    [
                        'id' => $user->getKey(),
                        'hash' => sha1($user->getEmailForVerification()),
                    ]
                );
                session()->flash('dev_verification_url', $verificationUrl);
                Log::info('Dev verification URL', ['url' => $verificationUrl, 'user_id' => $user->id]);
            }
            return redirect()->route('verification.notice');
        }

        // Иначе — стандартный редирект
        return redirect($this->redirectTo);
    }
}
