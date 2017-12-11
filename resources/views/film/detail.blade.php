@extends('film.card')

@section('filmForm')
    <div>
        <h4>{{$film->title}} ({!! is_null($film->title_latin) ? '<span class="text-muted">'.trans('film.show.title_latin').'</span>' : $film->title_latin !!})</h4>
        <div class="row">
            <div class="col-xs-4">
                <img ng-src="/context/films/{{$film->id}}.jpg" class="img-responsive"
                     onError="this.onerror=null;this.src='/images/poster.png';"/>
            </div>
            <div class="col-xs-8">
                <div class="row">
                    <label class="col-xs-4">{{trans('film.card.director')}}</label>
                    <ol class="breadcrumb col-xs-8">
                        @foreach($directors as $director)
                            <li class="breadcrumb-item">{{$director->name}}</li>
                        @endforeach
                    </ol>
                </div>
                <div class="row">
                    <label class="col-xs-4">{{trans('film.card.genre')}}</label>
                    <ol class="breadcrumb col-xs-8">
                        @foreach($genres as $genre)
                            <li class="breadcrumb-item">{{$genre->name}}</li>
                        @endforeach
                    </ol>
                </div>
                <div class="row">
                    <label class="col-xs-4">{{trans('film.label.nation')}}</label>
                    <ol class="breadcrumb col-xs-8">
                        <li class="breadcrumb-item active">{{$film->country}}</li>
                        @foreach($countries as $country)
                            <li class="breadcrumb-item">{{$country->name}}</li>
                        @endforeach
                    </ol>
                </div>
                <div class="row">
                    <label class="col-xs-4">{{trans('film.label.nation_shooting')}}</label>
                    <ol class="breadcrumb col-xs-8">
                        @foreach($shootings as $shooting)
                            <li class="breadcrumb-item">{{$shooting->name}}</li>
                        @endforeach
                    </ol>
                </div>
                <div class="row">
                    <label class="col-xs-4">{{trans('film.label.dialogue_language')}}</label>
                    @if($film->dialog == 1)
                        <ol class="breadcrumb col-xs-8">
                        @foreach($languages as $language)
                            <li class="breadcrumb-item">{{$language->name}}</li>
                        @endforeach
                        </ol>
                    @elseif($film->dialog == 0)
                        <div class="col-xs-8">{{trans('film.label.has_dialog')}}</div>
                    @else
                        <div class="col-xs-8"></div>
                    @endif
                </div>
                <div class="row">
                    <label class="col-xs-4">{!! trans('film.label.date_complete') !!}</label>
                    <div class="col-xs-8">
                        {{$film->month}} / {{$film->year}}
                    </div>
                </div>
                <div class="row">
                    <label class="col-xs-4">{!! trans('film.label.duration') !!}</label>
                    <div class="col-xs-8">
                        {{$film->duration}}
                    </div>
                </div>
                <div class="row">
                    <label class="col-xs-4">{!! trans('film.label.other_title') !!}</label>
                    <ol class="breadcrumb col-xs-8">
                        @if(!is_null($film->title_inter))
                            <li class="breadcrumb-item">{{$film->title_inter}}</li>
                        @endif
                        @foreach($titles as $title)
                                <li class="breadcrumb-item">{{$title}}</li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
        <hr>
        <div>
            <h6>{{trans('film.card.synopsis')}}</h6>
            <div>
                {{$film->synopsis}}
            </div>
        </div>
    </div>
@endsection