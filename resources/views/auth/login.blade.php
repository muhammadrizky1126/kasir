@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="login-container">
    <div class="login-wrapper">
        <div class="login-header">
            <div class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#7e22ce">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                </svg>
            </div>
            <h2>Welcome To TakaSir</h2>
            <p>Please login to continue</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf

            <div class="form-group">
                <div class="input-with-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#7e22ce">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    <input id="email" type="email"
                        class="form-input @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email"
                        autofocus placeholder="Username or Email">
                </div>
                @error('email')
                <span class="error-message" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <div class="input-with-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#7e22ce">
                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                    </svg>
                    <input id="password" type="password"
                        class="form-input @error('password') is-invalid @enderror"
                        name="password" required autocomplete="current-password" placeholder="Password">
                </div>
                @error('password')
                <span class="error-message" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <button type="submit" class="login-button">
                {{ __('Login') }}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                    <path d="M10 17l5-5-5-5v10z"/>
                </svg>
            </button>
        </form>


    </div>
</div>

<style>
    :root {
        --primary-color: #7e22ce;
        --primary-light: #a855f7;
        --primary-dark: #6b21a8;
        --accent-color: #e9d5ff;
        --error-color: #ef4444;
    }

    body {
        overflow: hidden; /* Disable scrolling */
        height: 100vh; /* Full viewport height */
    }

    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh; /* Full viewport height */
        background: linear-gradient(135deg, #f3e8ff 0%, #d8b4fe 100%);
        padding: 20px;
        overflow: hidden; /* Prevent any internal scrolling */
    }

    .login-wrapper {
        width: 100%;
        max-width: 450px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 15px 30px rgba(126, 34, 206, 0.2);
        padding: 40px;
        position: relative;
        overflow: hidden;
    }

    .login-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 8px;
        background: linear-gradient(90deg, #7e22ce 0%, #a855f7 50%, #7e22ce 100%);
    }

    .login-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .logo {
        width: 80px;
        height: 80px;
        margin: 0 auto 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--accent-color);
        border-radius: 50%;
    }

    .logo svg {
        width: 40px;
        height: 40px;
    }

    .login-header h2 {
        color: var(--primary-color);
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .login-header p {
        color: #6b7280;
        font-size: 16px;
        margin: 0;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .input-with-icon {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-with-icon svg {
        position: absolute;
        left: 15px;
        width: 20px;
        height: 20px;
    }

    .form-input {
        width: 100%;
        padding: 15px 15px 15px 45px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 16px;
        transition: all 0.3s;
        background-color: #f9fafb;
    }

    .form-input:focus {
        border-color: var(--primary-light);
        outline: none;
        box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.2);
        background-color: white;
    }

    .login-button {
        width: 100%;
        padding: 15px;
        background: linear-gradient(to right, var(--primary-color), var(--primary-light));
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(126, 34, 206, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 10px;
    }

    .login-button svg {
        width: 20px;
        height: 20px;
    }

    .login-button:hover {
        background: linear-gradient(to right, var(--primary-dark), var(--primary-color));
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(126, 34, 206, 0.4);
    }

    .login-footer {
        margin-top: 30px;
        text-align: center;
    }

    .social-login p {
        color: #6b7280;
        font-size: 14px;
        margin-bottom: 15px;
        position: relative;
    }

    .social-login p::before,
    .social-login p::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 30%;
        height: 1px;
        background-color: #e5e7eb;
    }

    .social-login p::before {
        left: 0;
    }

    .social-login p::after {
        right: 0;
    }

    .social-icons {
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .social-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .social-icon svg {
        width: 24px;
        height: 24px;
    }

    .social-icon:hover {
        transform: translateY(-3px);
    }

    .social-icon.google {
        background-color: rgba(219, 68, 55, 0.1);
    }

    .social-icon.facebook {
        background-color: rgba(66, 103, 178, 0.1);
    }

    .social-icon.twitter {
        background-color: rgba(29, 161, 242, 0.1);
    }

    .error-message {
        color: var(--error-color);
        font-size: 14px;
        margin-top: 8px;
        display: block;
        text-align: left;
    }

    .is-invalid {
        border-color: var(--error-color) !important;
    }

    @media (max-width: 480px) {
        .login-wrapper {
            padding: 30px 20px;
        }

        .logo {
            width: 70px;
            height: 70px;
        }

        .logo svg {
            width: 35px;
            height: 35px;
        }
    }
</style>
@endsection
