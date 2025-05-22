@extends('layouts/basic')

@section('content')
<style>
    .login-page-wrapper {
        display: flex;
        min-height: 100vh;
        background: #f5f5f5;
    }
    
    .login-background {
        flex: 2;
        background-image: url('{{ asset("img/background.jpg") }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
    }
    
    .login-form-wrapper {
        width: 400px;
        background: white;
        padding: 40px;
        position: relative;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .login-header {
        text-align: center;
        margin-bottom: 30px;
        padding: 20px 0;
    }

    .login-logo {
        max-width: 200px;
        height: auto;
        margin: 0 auto 15px;
        display: block;
    }

    .login-title {
        font-size: 18px;
        color: #454545;
        font-weight: 400;
        margin: 10px 0 0;
        font-family: 'Colibri';
        src: url('{{ asset("fonts/colibri.woff2") }}') format('woff2'),
             url('{{ asset("fonts/colibri.woff") }}') format('woff');
        font-weight: bold;
        font-size: 24px;

    }

    .box.login-box {
        border: none;
        box-shadow: none;
        margin: 0;
    }

    .box-header {
        text-align: left;
        padding: 0;
        border: none;
    }

    .box-header .box-title {
        font-size: 24px;
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
        border: 1px solid #e0e0e0;
        padding: 8px 12px;
        font-size: 14px;
    }

    .form-control:focus {
        border-color: #00a4e4;
        box-shadow: 0 0 0 2px rgba(0,164,228,0.2);
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
        color: #666;
        font-size: 13px;
        border-top: 1px solid #eee;
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
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
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
                    <h1 class="box-title"> {{ trans('auth/general.login_prompt')  }}</h1>
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
                                    <input class="form-control" placeholder="{{ trans('admin/users/table.username')  }}" name="username" type="text" id="username" autocomplete="{{ (config('auth.login_autocomplete') === true) ? 'on' : 'off'  }}" autofocus>
                                    {!! $errors->first('username', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                                </div>
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password">
                                        <x-icon type="password" />
                                        {{ trans('admin/users/table.password')  }}
                                    </label>
                                    <input class="form-control" placeholder="{{ trans('admin/users/table.password')  }}" name="password" type="password" id="password" autocomplete="{{ (config('auth.login_autocomplete') === true) ? 'on' : 'off'  }}">
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
            <p>{{ $snipeSettings->site_name ?? config('app.name', 'UI-Trackin (IT)') }} v0.8</p>
            <p>Need help? Contact <a href="mailto:idhelp@universalleaf.com">IDHelp</a></p>
        </div>
    </div>
</div>
@stop