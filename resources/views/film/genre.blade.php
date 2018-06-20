@extends('layouts.film')

@section('filmForm')
    <form id="time_form" action="/{{$film->type}}s" method="POST"
          ng-controller="filmCtrl" ng-init="loaded()">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$film->id}}" />
        <input type="hidden" name="step" value="{{$step+1}}" />
        <ul class="alert alert-dark text-primary small px-5" role="alert">
            @if(!$film->screenplay)
            <li>{!! trans('film.alert.genre1') !!}</li>
            <li class="py-1">{!! trans('film.alert.genre2') !!}</li>
            @endif
            <li>{!! trans('layout.ALERTS.compulsive') !!}</li>
        </ul>
        <div class="row text-primary my-5">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified required">{!! trans('film.label.genre') !!}</label>
            <div class="col-lg-10 col-md-8 col-sm-12 row" id="block_genre">
                @if($film->screenplay)
                    @foreach($genres as $genre)
                        <div class="col-lg-4 col-md-6 col-sm-12 checkbox-inline pb-3">
                            <input type="radio"  name="genre[0]" value="{{$genre->id}}" {{$genre->chosen ? "checked" : ''}}>
                            {{$genre->name}}
                        </div>
                    @endforeach
                @else
                    @foreach($genres as $genre)
                        <div class="col-lg-4 col-md-6 col-sm-12 checkbox-inline pb-3" ng-init="change('genre')">
                            <input type="checkbox"  name="genre[]" ng-click="change('genre')" value="{{$genre->id}}" {{$genre->chosen ? "checked" : ''}}>
                            {{$genre->name}}
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="row text-primary">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified required">{!! trans('film.label.style') !!}</label>
            <div class="col-lg-10 col-md-8 col-sm-12 row" id="block_style">
                @foreach($styles as $style)
                    <div class="col-lg-4 col-md-6 col-sm-12 checkbox-inline pb-3">
                        <input type="checkbox" name="style[]" value="{{$style->id}}" {{$style->chosen ? "checked" : ''}}>
                        {{$style->name}}
                    </div>
                @endforeach
            </div>
        </div>
        <div class="row text-primary my-5">
            @if($film->screenplay)
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified">{!! trans('film.label.audience') !!}</label>
            @else
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified required">{!! trans('film.label.subject') !!}</label>
            @endif
            <div class="col-lg-10 col-md-8 col-sm-12 row" id="block_subject">
                @foreach($subjects as $subject)
                    <div class="col-lg-4 col-md-6 col-sm-12 checkbox-inline pb-3">
                        <input type="checkbox" name="subject[]"  value="{{$subject->id}}" {{$subject->chosen ? "checked" : ''}}>
                        {{$subject->name}}
                    </div>
                @endforeach
            </div>
        </div>
        <hr/>
        <div class="d-flex justify-content-between">
            <div class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteFilmModal">{{trans('film.buttons.delete')}}</div>
            <button class="btn btn-primary" type="submit">{{trans('layout.BUTTONS.continue')}}</button>
        </div>
    </form>
@endsection
@section('script')
    <script src="/js/controllers/film/genre.js"></script>
@endsection