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
    <div class="col-xs-12" ng-controller="resetCtrl">
        <div class="inner">
            <div class="middle">
                <h1>{{trans('auth.reset_password')}}</h1>
                <form name="zooform" class="grey" method="POST" action="{{ route('password.request') }}">
                    {{ csrf_field() }}

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <input id="email" type="email" class="form-text" name="email" placeholder="{{trans('auth.register_email')}}" ng-model="email" ng-init="init('{{ $email or old('email') }}')" required autofocus>

                        @if ($errors->has('email'))
                            <div class="text-danger small">
                                <span>{{ $errors->first('email') }}</span>
                            </div>
                            @else
                            <div class="text-danger small" role="alert" ng-show="zooform.email.$touched || zooform.submitted">
                                <span ng-show="zooform.email.$error.required">{{trans('auth.error_email_required')}}</span>
                                <span ng-show="zooform.email.$error.email">{{trans('auth.error_email_invalid')}}</span>
                            </div>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <input id="password" type="password" class="form-text" name="password"
                               placeholder="{{trans('passwords.new_password')}}" ng-model="password" ng-minlength="6" ng-maxlength="16" ng-pattern="regex" required>
                        @if ($errors->has('password'))
                            <div class="text-danger samll">{{ $errors->first('password') }}</div>
                        @else
                            <div class="<%(zooform.password.$touched || zooform.$submitted) ? 'text-danger' : 'text-info'%> small" role="alert">
                                <span ng-hide="(zooform.password.$touched || zooform.$submitted) && !zooform.password.$error.pattern && !zooform.password.$error.required">{{trans('auth.error_password_regex')}}</span>
                                <span ng-hide="(zooform.password.$touched || zooform.$submitted) && !zooform.password.$error.minlength && !zooform.password.$error.maxlength && !zooform.password.$error.required">
                                    {{trans('auth.error_password_length')}}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <input id="password-confirm" type="password" class="form-text" ng-disabled="zooform.password.$invalid"
                               name="password_confirmation" placeholder="{{trans('passwords.password_confirmation')}}" ng-model="password_confirmation" pw-check="password" required />
                        @if ($errors->has('password_confirmation'))
                            <div class="text-danger small" >{{ $errors->first('password_confirmation') }}</div>
                        @else
                            <div class="text-danger small" role="alert" ng-show="zooform.password_confirmation.$touched || zooform.$submitted">
                                <span ng-show="zooform.password_confirmation.$error.required && !zooform.password.$invalid">{{trans('auth.error_password_confirmation')}}</span>
                                <span ng-show="zooform.password_confirmation.$error.pwmatch">{{trans('auth.error_password_match')}}</span>
                            </div>
                        @endif
                    </div>
                    <br/>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary btn-inverse btn-block">{{trans('passwords.reset_password')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="/js/controllers/auth/email.js"></script>
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
