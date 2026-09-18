@extends('layouts/basic')

@section('content')
<style>
    .login-page-wrapper {
        display: flex;
        width: 100%;
        min-height: 100vh;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        box-sizing: border-box;
        padding: 24px 16px;
    }
    
    .login-background {
        position: absolute;
        inset: 0;
        background-image: url('{{ asset("img/background.jpg") }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
    
    .login-form-wrapper {
        width: min(100% - 32px, 500px);
        background: transparent;
        padding: 0;
        position: relative;
        margin: 0 auto;
        top: auto;
        right: auto;
        left: auto;
        transform: none;
        z-index: 1;
    }

    .login-header {
        text-align: center;
        margin: 0 auto 22px;
        padding: 0;
    }

    .login-logo {
        max-width: 200px;
        height: auto;
        margin: 0 auto 15px;
        display: block;
    }

    .login-title {
        color: #fff;
        font-weight: 400;
        margin: 10px 0 0;
        font-family: 'Helvetica', sans-serif;
        /* src: url('{{ asset("fonts/colibri.woff2") }}') format('woff2'),
             url('{{ asset("fonts/colibri.woff") }}') format('woff'); */
        font-weight: bold;
        font-size: 30px;

    }

    .box.login-box {
        margin: 0;
        padding: 34px 48px 30px;
        border: 1px solid rgba(255, 255, 255, 0.85);
        border-radius: 22px;
        background: rgba(8, 53, 63, 0.42);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25);
        -webkit-backdrop-filter: blur(12px);
        backdrop-filter: blur(12px);
    }

    .box-header {
        text-align: left;
        padding: 0;
        border: none;
        background: transparent;
    }

    .login-box .login-box-body {
        background: transparent;
    }

    .box-header .box-title {
        font-size: 18px;
        font-weight: 300;
        color: #fff;
        margin-bottom: 30px;
    }
    
    .box-title {
        font-size: 18px;
        font-weight: 300;
        color: #454545;
        margin-bottom: 30px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        height: 44px;
        border-radius: 3px;
        border: 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.8);
        background: transparent;
        padding: 8px 12px;
        font-size: 14px;
        color: #fff;
    }

    .form-control:focus {
        border-color: #fff;
        box-shadow: none;
    }

    .login-box label,
    .login-box .checkbox,
    .login-box .help-block {
        color: #fff;
    }

    .btn-primary {
        background: #00a4e4;
        border: none;
        padding: 12px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 20px;
    }

    .btn-primary:hover {
        background: #0093cd;
    }

    .login-footer {
        margin-top: 40px;
        text-align: center;
        color: #fff;
        font-size: 13px;
        border-top: 1px solid rgba(255, 255, 255, 0.35);
        padding-top: 20px;
    }

    .login-footer a {
        color: #00a4e4;
    }

    .forgot-password {
        text-align: right;
        margin-top: 15px;
    }

    .forgot-password a {
        color: #00a4e4;
        font-size: 13px;
        text-decoration: none;
    }

    @media (max-width: 768px) {
        .login-background {
            display: none;
        }
        
        .login-form-wrapper {
            width: calc(100% - 32px);
        }

        .box.login-box {
            padding: 28px 24px 24px;
        }
    }
</style>

<div class="login-page-wrapper">
    <div class="login-background"></div>
    
    <div class="login-form-wrapper">
        <!-- Login Header with Logo -->
        <div class="login-header">
            <img src="{{ asset('img/universal_bottom.png') }}" alt="Universal Leaf Logo" class="login-logo">
            <h1 class="login-title">{{ $snipeSettings->site_name ?? config('app.name', 'UI-Trackin (IT)') }}</h1>
        </div>

        <!-- Login Form -->
        <form role="form" action="{{ url('/login') }}" method="POST" autocomplete="{{ (config('auth.login_autocomplete') === true) ? 'on' : 'off' }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            
            <!-- Prevent Chrome autofill hack -->
            <input type="text" name="prevent_autofill" id="prevent_autofill" value="" style="display:none;" aria-hidden="true">
            <input type="password" name="password_fake" id="password_fake" value="" style="display:none;" aria-hidden="true">

            <div class="box login-box">
                <div class="box-header with-border">
                    <h3 class="box-title"> {{ trans('auth/general.login_prompt')  }}</h3>
                </div>

                <div class="login-box-body">
                    <div class="row">

                        @if ($snipeSettings->login_note)
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    {!!  Helper::parseEscapedMarkedown($snipeSettings->login_note)  !!}
                                </div>
                            </div>
                        @endif

                        <!-- Notifications -->
                        @include('notifications')

                        @if (!config('app.require_saml'))
                        <div class="col-md-12">
                            <!-- CSRF Token -->

                            <fieldset>

                                <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                    <label for="username">
                                        <x-icon type="user" />
                                        {{ trans('admin/users/table.username')  }}
                                    </label>
                                    <input class="form-control" placeholder="Use Windows Username" name="username" type="text" id="username" autocomplete="{{ (config('auth.login_autocomplete') === true) ? 'on' : 'off'  }}" autofocus>
                                    {!! $errors->first('username', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                                </div>
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password">
                                        <x-icon type="password" />
                                        {{ trans('admin/users/table.password')  }}
                                    </label>
                                    <input class="form-control" placeholder="Use Windows Password" name="password" type="password" id="password" autocomplete="{{ (config('auth.login_autocomplete') === true) ? 'on' : 'off'  }}">
                                    {!! $errors->first('password', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                                </div>
                                <div class="form-group">
                                    <label class="form-control">
                                        <input name="remember" type="checkbox" value="1"> {{ trans('auth/general.remember_me')  }}
                                    </label>
                                </div>
                            </fieldset>
                        </div> <!-- end col-md-12 -->
                        @endif
                    </div> <!-- end row -->

                    @if (!config('app.require_saml') && $snipeSettings->saml_enabled)
                    <div class="row">
                        <div class="text-right col-md-12">
                            <a href="{{ route('saml.login')  }}">{{ trans('auth/general.saml_login')  }}</a>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="box-footer">
                    @if (config('app.require_saml'))
                        <a class="btn btn-primary btn-block" href="{{ route('saml.login')  }}">{{ trans('auth/general.saml_login')  }}</a>
                    @else
                        <button class="btn btn-primary btn-block">{{ trans('auth/general.login')  }}</button>
                    @endif
<!-- 
                    @if ($snipeSettings->custom_forgot_pass_url)
                        <div class="col-md-12 text-right forgot-password">
                            <a href="{{ $snipeSettings->custom_forgot_pass_url  }}" rel="noopener">{{ trans('auth/general.forgot_password')  }}</a>
                        </div>
                    @elseif (!config('app.require_saml'))
                        <div class="col-md-12 text-right forgot-password">
                            <a href="{{ route('password.request')  }}">{{ trans('auth/general.forgot_password')  }}</a>
                        </div>
                    @endif -->

                </div>
            </div> <!-- end login box -->
        </form>

        <!-- Login Footer -->
        <div class="login-footer">
            <p>&copy; {{ date('Y') }} Universal Leaf. All rights reserved.</p>
            <p>{{ $snipeSettings->site_name ?? config('app.name', 'UI-Trackin (IT)') }} v1.0</p>
            <p>Need help? Contact <a href="mailto:idhelp@universalleaf.com">IDHelp</a></p>
        </div>
    </div>
</div>
@stop
