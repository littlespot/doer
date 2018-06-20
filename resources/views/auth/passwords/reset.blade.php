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
    <div>
        <form name="zooform" id="zooform" novalidate method="POST"  action="{{ route('password.request') }}" class="bg-white pt-3 pb-3 pl-4 pr-4" style="width: 360px">
            {{ csrf_field() }}
            <input type="hidden" value="{{$token}}" name="token">
            <div class="mt-1 row">
                <div class="col-md-4">

                </div>
                <div class="col-md-4 col-12">
                    <img id="avatar" class="rounded-circle img-fluid border border-secondary"
                         ng-src="/context/avatars/default.png" />
                </div>
                <div class="col-md-4">

                </div>
            </div>
            <div class="mt-3 form-group">
                <input id="email" type="email" class="form-control" name="email" placeholder="{{trans('auth.email')}}"
                       ng-model="email" ng-init="setEmail('{{ old('email') }}')"
                       ng-keyup="$event.keyCode == 13"
                       required autofocus />

                @if ($errors && count($errors) > 0)
                    <div class="text-danger mt-1" role="alert">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @else
                    <div class="text-danger mt-1" role="alert" ng-show="zooform.email.$touched || zooform.submitted">
                        <span ng-show="zooform.email.$error.required">{{trans('auth.error_email_required')}}</span>
                        <span ng-show="zooform.email.$error.email">{{trans('auth.error_email_invalid')}}</span>
                    </div>
                @endif
            </div>

            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <input id="password" type="password" class="form-control" name="password"
                       placeholder="{{trans('passwords.new_password')}}" ng-model="password" ng-minlength="6" ng-maxlength="16" ng-pattern="regex" required>
                @if ($errors->has('password'))
                    <div class="text-danger samll">{{ $errors->first('password') }}</div>
                @else
                    <div class="small" role="alert">
                        <span ng-show="(zooform.password.$touched || zooform.$submitted) && zooform.password.$error.pattern && zooform.password.$error.required">{{trans('auth.error_password_regex')}}</span>
                        <span ng-show="(zooform.password.$touched || zooform.$submitted) && (zooform.password.$error.minlength || zooform.password.$error.maxlength || zooform.password.$error.required)">
                            {{trans('auth.error_password_length')}}
                        </span>
                    </div>
                @endif
            </div>
            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                <input id="password-confirm" type="password" class="form-control" ng-disabled="zooform.password.$invalid"
                       name="password_confirmation" placeholder="{{trans('passwords.password_confirmation')}}" ng-model="password_confirmation" pw-check="password" required />
                @if ($errors->has('password_confirmation'))
                    <div class="text-danger small" >{{ $errors->first('password_confirmation') }}</div>
                @else
                    <div class="text-danger small" role="alert" ng-show="zooform.password_confirmation.$touched || zooform.$submitted">
                        <span ng-show="zooform.password_confirmation.$error.required">{!!trans('auth.error_password_confirmation')  !!}</span>
                        <span ng-show="zooform.password_confirmation.$error.pwmatch">{!! trans('auth.error_password_match') !!}</span>
                    </div>
                @endif
            </div>
            <button class="btn btn-primary btn-block" ng-disabled="zooform.$invalid" type="submit">{{trans('layout.BUTTONS.submit')}}</button>
        </form>
        <div class="text-center">
            <a class="small text-default" href="{{ route('register') }}">{{trans('auth.register')}}  </a>
            <br/>
            <a href="" class="small text-important">{{trans('auth.help')}}</a>
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

            var margin = $(window).height();
            $('#content').height(margin);
        }

        $(document).ready(function () {
            init();
        })
    </script>
@endsection
