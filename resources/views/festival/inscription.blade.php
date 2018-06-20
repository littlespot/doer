@extends('layouts.home')

@section('content')
    <div class="container">
        <h2>{{$info['festival']->name_locale?:$info['festival']->name}}</h2>
        <div class="row">
            <div class="col-md-3 col-4">
                您的作品：
            </div>
            <div class="col-md-9 col-8">
                {{$film->title}}
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-4">
                单元：
            </div>
            <div class="col-md-9 col-8">
                {{is_null($info['unit']->name_locale)?$info['unit']->name : $info['unit']->name_locale.'('.$info['unit']->name.')'}}
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-4">
                共计：
            </div>
            <div class="col-md-9 col-8">
                {{$info['unit']->fee.' '.$info['unit']->currency}}
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-4">
                联系信息：
            </div>
            <div class="col-md-9 col-8">

            </div>
        </div>
    </div>
@endsection