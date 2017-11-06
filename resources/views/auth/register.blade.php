@extends('auth.cover')
@section('background')
    <div id="layers">
        <div class="backlayer" style="background-image: url(/images/layers/BG_A03.svg);">
            &nbsp;
        </div>
        <div class="backlayer" style="background-image: url(/images/layers/BG_B02.svg);">
            &nbsp;
        </div>
        <div class="backlayer" style="background-image: url(/images/layers/BG_B01.svg);">
            &nbsp;
        </div>
    </div>
@endsection
@section('content')
    <div class="col-lg-offset-4 col-lg-4 col-md-offset-2 col-md-8 col-sm-offset-1 col-sm-10 col-xs-12"
         ng-controller="registerCtrl" ng-init="init('{{App::getLocale()}}')">
        <form id="usrform" name="usrform" action="{{ route('register') }}" method="post" novalidate class="fixed-form grey">
            {{ csrf_field() }}
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <input type="text" class="form-text" ng-disabled="usrform.email.$pending.unique"
                       name="name" placeholder="{{trans('auth.register_username')}}"
                       ng-model="username" ng-minlength="2" ng-maxlength="16"
                       required autofocus/>
                @if ($errors && $errors->has('name'))
                    <div class="text-danger small" role="alert">{{ $errors->first('name') }}</div>
                @else
                <div class="text-danger small" ng-show="usrform.name.$touched || usrform.$submitted" role="alert">
                    <span ng-show="usrform.name.$error.required">{{trans('auth.error_username_required')}}</span>
                    <span ng-show="usrform.name.$error.minlength">{{trans('auth.error_username_minlength')}}</span>
                    <span ng-show="usrform.name.$error.maxlength">{{trans('auth.error_username_maxlength')}}</span>
                </div>
                @endif
            </div>
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <input type="email" class="form-text"
                       name="email" placeholder="{{trans('auth.register_email')}}"
                       ng-model="email" ng-model-options="{ updateOn: 'blur' }"
                       ng-disabled="usrform.email.$pending.unique"
                       required email/>
                @if ($errors->has('email'))
                    <div class="text-danger samll">{{ $errors->first('email') }}</div>
                @else
                    <div class="text-danger small" role="alert" ng-show="usrform.email.$touched || usrform.$submitted">
                        <span ng-show="usrform.email.$error.required">{{trans('auth.error_email_required')}}</span>
                        <span ng-show="usrform.email.$error.email">{{trans('auth.error_email_invalid')}}</span>
                        <span ng-show="usrform.email.$error.unique">{{trans('auth.error_email_unique')}}</span>
                        <span ng-show="usrform.email.$pending.unique">{{trans('auth.pending_email')}}</span>
                    </div>
                @endif
            </div>
            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <input id="password" type="password" class="form-text" name="password" ng-disabled="usrform.email.$pending.unique"
                       placeholder="{{trans('passwords.new_password')}}" ng-model="password" ng-minlength="6" ng-maxlength="16" ng-pattern="regex" required>
                @if ($errors->has('password'))
                    <div class="text-danger samll">{{ $errors->first('password') }}</div>
                @else
                    <div class="<%(usrform.password.$touched || usrform.$submitted) ? 'text-danger' : 'text-info'%> small" role="alert">
                        <span ng-hide="(usrform.password.$touched || usrform.$submitted) && !usrform.password.$error.pattern && !usrform.password.$error.required">{{trans('auth.error_password_regex')}}</span>
                        <span ng-hide="(usrform.password.$touched || usrform.$submitted) && !usrform.password.$error.minlength && !usrform.password.$error.maxlength && !usrform.password.$error.required">
                            {{trans('auth.error_password_length')}}
                        </span>
                    </div>
                @endif
            </div>
            <div class="form-group" ng-show="usrform.password.$valid">
                <input id="password-confirm" type="password" class="form-text" ng-disabled="usrform.email.$pending.unique"
                       name="password_confirmation" placeholder="{{trans('passwords.password_confirmation')}}" ng-model="password_confirmation" pw-check="password" required />

                <div class="text-danger small" role="alert" ng-show="usrform.password_confirmation.$touched || usrform.$submitted">
                    <span ng-show="usrform.password_confirmation.$error.required && !usrform.password.$invalid">{{trans('auth.error_password_confirmation')}}</span>
                    <span ng-show="usrform.password_confirmation.$error.pwmatch">{{trans('auth.error_password_match')}}</span>
                </div>
            </div>
            <div class="form-group">
                <div class=" checkbox">
                    <input type="checkbox" id="remember" name="remember" />
                    <label for="remember"></label><span>{!! trans('auth.register_condition') !!}</span>
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-primary btn-inverse btn-block" ng-disabled="usrform.$invalid"
                     type="submit">
                    {{trans('auth.register_submit')}}
                </button>
            </div>
        </form>
        <br>
        <div class="text-center">
            <p><a class="small text-default" href="{{ route('login') }}" translate="login.Signin" ></a></p>
            <p><a class="small text-important" href="" translate="login.Help"></a> </p>
        </div>
    </div>
@endsection
@section('script')
    <script src="/js/controllers/auth/register.js"></script>
    <script lang="javascript">

        function init() {
            if( $(window).height() < 640){
                $("#layer").hide();
                return;
            }
            var xScale = ($(window).width() / 1600).toFixed(2);
            var ratio = $(window).width() / $(window).height();

            if (ratio >= 1.6)
                $('#layers .backlayer').css('background-size', 'cover');
            else
                $('#layers .backlayer').css('background-size', 'contain');

            $("#layers").show();

            var margin = $(window).height() - $('.header').height() - 330 * xScale;
            $('#content').height(margin);
        }

        $(document).ready(function () {
            init();
        })
    </script>
@endsection