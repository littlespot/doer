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
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        <form class="bg-white pt-3 pb-3 pl-4 pr-4" style="width: 360px" method="POST" action="{{ route('password.email') }}">
            {{ csrf_field() }}

            <h4 class="text-center text-info"></h4>
            <div class="form-group{{ ($errors->has('password')) ? 'error' : '' }}">
                <input id="email" type="email" class="form-control" name="email" placeholder="{{trans('auth.email')}}" ng-init="email = '{{$email}}';"
                       ng-model="email"
                       required autofocus />
                @if ($errors->has('email'))
                    <div class="text-danger small">
                        <span>{{ $errors->first('email') }}</span>
                    </div>
                @endif
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary btn-inverse btn-block">{{trans('passwords.reset_password')}}</button>
            </div>
        </form>
        <div class="text-center">
            <p>
                <a class="title small" href="{{ route('login') }}">{{trans('auth.login')}}</a>
            </p>
            <p><a class="small text-default" href="{{ route('register') }}">{{trans("auth.register")}}</a></p>
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

            var margin = $(window).height()  - 330 * xScale;
            $('#content').height(margin);
        }

        $(document).ready(function () {
            init();
        })
    </script>
@endsection
