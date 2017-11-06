@extends('auth.cover')

@section('content')
<style>
    * {
        box-sizing: border-box;
    }

    .container{
        padding-top: 30px;
    }
    .ribbon-stiched {
        width: 80%;
        max-width: 1000px;
        height: 80px;
        margin: 40px auto;
        position: relative;
    }

    .one > div {
        height: 50px;
    }

    .ribbon-stiched-main {
        background: #0fadc0;
        position: relative;
        display: block;
        width: 90%;
        left: 50%;
        top: 0;
        padding: 5px;
        margin-left: -45%;
        z-index: 10;
        text-align: center;
        color: #FBEED2;
        font-size: 24px;
    }

    .ribbon-stiched-main > div {
        border: 1px dashed #fff;
        border-color: rgba(255, 255, 255, 0.5);
        height: 40px;
    }

    .bk {
        background: #1199a9;
        position: absolute;
        width: 8%;
        top: 12px;
    }

    .bk.l {
        left: 0;
    }

    .bk.r {
        right: 0;
    }

    .skew {
        position: absolute;
        background: #0c7582;
        width: 3%;
        top: 6px;
        z-index: 5;
    }

    .skew.l {
        left: 5%;
        transform: skew(00deg,20deg);
    }

    .skew.r {
        right: 5%;
        transform: skew(00deg,-20deg);
    }

    .bk.l > div {
        left: -30px;
    }

    .bk.r > div {
        right: -30px;
    }

    .arrow {
        height: 25px !important;
        position: absolute;
        z-index: 2;
        width: 0;
        height: 0;
    }

    .arrow.top {
        top: 0px;
        border-top: 0px solid transparent;
        border-bottom: 25px solid transparent;
        border-right: 30px solid #1199a9;
    }

    .arrow.bottom {
        top: 25px;
        border-top: 25px solid transparent;
        border-bottom:0px solid transparent;
        border-right: 30px solid #1199a9;
    }

    .r .bottom {
        border-top: 25px solid transparent;
        border-bottom: 0px solid transparent;
        border-left: 30px solid #1199a9;
        border-right: none;
    }

    .r .top {
        border-bottom: 25px solid transparent;
        border-top: 0px solid transparent;
        border-left: 30px solid #1199a9;
        border-right: none;
    }

    @media all and (max-width: 1020px) {
        .skew.l {
            left: 5%;
            transform: skew(00deg,25deg);
        }

        .skew.r {
            right: 5%;
            transform: skew(00deg,-25deg);
        }
    }

    @media all and (max-width: 680px) {
        .skew.l {
            left: 5%;
            transform: skew(00deg,30deg);
        }

        .skew.r {
            right: 5%;
            transform: skew(00deg,-30deg);
        }
    }

    @media all and (max-width: 460px) {
        .skew.l {
            left: 5%;
            transform: skew(00deg,40deg);
        }
        .skew.r {
            right: 5%;
            transform: skew(00deg,-40deg);
        }
    }

    .message{
        font-size: 16px;
        line-height: 3em;
        margin-bottom: 40px;
    }

</style>
<div class="text-center">

    <hr>
    <div class="text-info message">
        {!! $message !!}
    </div>
</div>
    @endsection
@section('script')
    <script>
        $("#crazyloader").hide();
    </script>
@endsection