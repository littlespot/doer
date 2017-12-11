@extends('layouts.zoomov')

@section('content')
    <style>
        button{
            background: transparent;
        }
    </style>
    <script type="text/ng-template" id="confirm.html">
        <div class="modal-body" id="modal-body">
            <h3 translate="project.MESSAGES.<%confirm%>"></h3>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default" type="button" ng-click="$close(false)">{{trans("project.BUTTONS.cancel")}}</button>
            <button class="btn btn-danger" type="button" ng-click="$close(true)">{{trans("project.BUTTONS.confirm")}}</button>
        </div>
    </script>
    <div class="container" ng-controller="filmCtrl">
        <div class="row">
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                <h4>{{trans('film.header.my')}}</h4>
                <ul class="list-unstyled">
                    @foreach($films as $film)
                        <li><a href="/films/{{$film->id}}" class="{{$film->completed ? 'text-success' : 'text-important'}}">{{$film->title}}</a></li>
                    @endforeach
                </ul>
                <hr>
                <h4>{{trans('personal.LABELS.contact')}}</h4>
                <div class="row">
                    <label class="col-sm-5 col-xs-12">{{trans('personal.LABELS.title')}}</label>
                    <div class="col-sm-7 col-xs-12">{{$contact->last_name}} {{$contact->first_name}}</div>
                </div>
                <div class="row">
                    <label class="col-sm-5 col-xs-12">{{trans('personal.LABELS.address')}}</label>
                    <div class="col-sm-7 col-xs-12">{{$contact->address}}</div>
                </div>
                <div class="row">
                    <label class="col-sm-5 col-xs-12">{{trans('personal.LABELS.code')}}</label>
                    <div class="col-sm-7 col-xs-12">{{$contact->zip}}</div>
                </div>
                <div class="row">
                    <label class="col-sm-5 col-xs-12">{{trans('personal.LABELS.city')}}</label>
                    <div class="col-sm-7 col-xs-12">{{$contact->city}}</div>
                </div>
                <div class="row">
                    <label class="col-sm-5 col-xs-12">{{trans('personal.LABELS.country')}}</label>
                    <div class="col-sm-7 col-xs-12">{{$contact->country}}</div>
                </div>
                <div class="row">
                    <label class="col-sm-5 col-xs-12">{{trans('personal.LABELS.tel')}}</label>
                    <div class="col-sm-7 col-xs-12">{!! is_null($contact->tel) ? $contact->mobile : $contact->tel.'<br>'.$contact->mobile !!}</div>
                </div>
                <div class="row">
                    <div>{{trans('film.progress.form_completed', ['cnt'=>$films->where('completed', 1)->count()])}}</div>
                    <div>{{trans('film.progress.form_tocomplete', ['cnt'=>$films->where('completed',0)->count()])}}</div>
                    <br>
                    <div>{{trans('film.progress.copy_uploaded', ['cnt'=>$copies])}}</div>
                    <div>{{trans('film.progress.copy_toupload', ['cnt'=>$films->where('completed', 1)->count() - $copies])}}</div>
                </div>
                <div class="row">
                    <div>{{trans('film.progress.submission_tofinish', ['cnt'=>0])}}</div>
                    <div>{{trans('film.progress.submission_forward', ['cnt'=>0])}}</div>
                    <div>{{trans('film.progress.submission_confirmed', ['cnt'=>0])}}</div>
                    <div>{{trans('film.progress.film_selected', ['cnt'=>0])}}</div>
                    <div>{{trans('film.progress.film_unselected', ['cnt'=>0])}}</div>
                    <div>{{trans('film.progress.film_another', ['cnt'=>0])}}</div>
                    <div>{{trans('film.progress.film_award', ['cnt'=>0])}}</div>
                    <div>{{trans('film.progress.submission_canceled', ['cnt'=>0])}}</div>
                </div>
            </div>
            <div class="col-lg-10 col-md-9 col-sm-8 col-xs-6">
                <div>
                    <div >
                        @include('templates.'.App::getLocale().'.instruction')
                    </div>
                    <form class="form" name="film_form" id="film_form">
                        <div class="form-group row">
                            <label for="title_original" class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">{!! trans('film.label.title_original') !!}</label>
                            <div class="col-md-8 col-sm-8 col-xs-8">
                                <input class="form-text" ng-model="film.title" ng-maxlength="80" required autofocus
                                       type="text" placeholder="{{trans('film.placeholder.title_original')}}" id="title" name="title">
                                <div role="alert" class="error text-right" ng-class="{'visible':errors.title || film_form.title.$dirty || film_form.$submitted}">
                                    <span ng-show="film_form.title.$error.required || errors.title">
                                        {{trans("film.error.require_title")}}
                                    </span>
                                    <span ng-show="film_form.title.$error.maxlength">
                                        {{trans("film.error.maxlength_title", ['cnt'=>80])}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-1"></div>
                        </div>
                        <div class="form-group row">
                            <label for="title_original" class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">{!! trans('film.label.title_latin') !!}</label>
                            <div class="col-md-8 col-sm-8 col-xs-8">
                                <input class="form-text" type="text" ng-maxlength="80" ng-model="film.title_latin"
                                       placeholder="{{trans('film.placeholder.title_latin')}}" id="title_latin" name="title_latin">
                                <div role="alert" class="error text-right" ng-class="{'visible':errors.title_latin || film_form.title_latin.$dirty || film_form.$submitted}">
                                    <span ng-show="errors.title_latin || film_form.title_latin.$error.maxlength">
                                        {{trans("film.error.maxlength_title", ['cnt'=>80])}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-1"></div>
                        </div>
                        <div class="form-group row">
                            <label for="title_original" class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">{!! trans('film.label.title_inter') !!}</label>
                            <div class="col-md-8 col-sm-8 col-xs-8">
                                <input class="form-text" type="text" ng-maxlength="80" ng-model="film.title_inter"
                                       placeholder="{{trans('film.placeholder.title_inter')}}" id="title_inter" name="title_inter">
                                <div role="alert" class="error text-right" ng-class="{'visible':errors.title_inter || film_form.title_inter.$dirty || film_form.$submitted}">
                                    <span ng-show="errors.title_inter || film_form.title_inter.$error.maxlength">
                                        {{trans("film.error.maxlength_title", ['cnt'=>80])}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-1"></div>
                        </div>
                        <div class="form-group row">
                            <div class=" checkbox">
                                <input type="checkbox" id="rights" name="rights" value="1" ng-model="film.rights" />
                                <label for="rights"></label><span>{!! trans('film.declaration.copyright') !!}</span>
                            </div>
                            <div role="alert" class="error text-right" ng-class="{'visible':errors.rights}">
                                {{trans("film.error.maxlength_title", ['cnt'=>80])}}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="btn btn-default" ng-disabled="film_form.$invalid"
                                 id="submit" ng-click="switchViewer(0)">
                                {{trans('layout.BUTTONS.cancel')}}
                            </div>&nbsp;
                            <div class="btn btn-primary" ng-disabled="film_form.$invalid"
                                 id="submit" ng-click="save(film_form.$invalid)">
                                {{trans('layout.BUTTONS.submit')}}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="/js/controllers/film/list.js"></script>
@endsection