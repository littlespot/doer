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
    <div class="container">
        <div class="row">
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
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
            </div>
            <div class="col-lg-10 col-md-9 col-sm-8 col-xs-6">
                <form class="form">
                    <div class="form-group row">
                        <label for="title_original" class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">{{trans('film.label.title_original')}} <sup>*</sup></label>
                        <div class="col-md-8 col-sm-8 col-xs-8">
                            <input class="form-text" type="text" value="Artisanal kale" id="title_original" name="title_original">
                        </div>
                        <div class="col-md-2 col-sm-1"></div>
                    </div>
                    <div class="form-group row">
                        <label for="title_latin" class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">{{trans('film.label.title_latin')}}</label>
                        <div class="col-md-8 col-sm-8 col-xs-8">
                            <input class="form-text" type="text" value="Artisanal kale" id="title_latin" name="latin">
                        </div>
                        <div class="col-md-2 col-sm-1"></div>
                    </div>
                    <div class="form-group row">
                        <label for="title_international" class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">{{trans('film.label.title_inter')}}</label>
                        <div class="col-md-8 col-sm-8 col-xs-8">
                            <input class="form-text" type="text" value="Artisanal kale" id="title_international" name="international">
                        </div>
                        <div class="col-md-2 col-sm-1"></div>
                    </div>

                    <button class="btn btn-primary btn-block">{{trans('film.buttons.add')}}</button>
                </form>
                <hr>
                <div class="row">
                    <div class="col-md-6 col-xs-12">
                        @if(is_null($films) || $films->count() == 0)
                            <div>{{trans('film.progress.form_completed', ['cnt'=>0])}}</div>
                            <div>{{trans('film.progress.form_tocomplete', ['cnt'=>0])}}</div>
                        @elseif($films->count() == 1)
                            @if($films[0]->completed  ==0)
                                <div>{{trans('film.progress.form_completed', ['cnt'=> $films[0]->cnt ])}}</div>
                                <div>{{trans('film.progress.form_tocomplete', ['cnt'=>0])}}</div>
                            @else
                                <div>{{trans('film.progress.form_completed', ['cnt'=> 0])}}</div>
                                <div>{{trans('film.progress.form_tocomplete', ['cnt'=> $films[0]->cnt])}}</div>
                            @endif
                        @else
                        <div>{{trans('film.progress.form_completed', ['cnt'=>$films[0]->cnt])}}</div>
                        <div>{{trans('film.progress.form_tocomplete', ['cnt'=>$films[1]->cnt])}}</div>
                        @endif
                            <br>
                            <div>{{trans('film.progress.copy_uploaded', ['cnt'=>$copies])}}</div>
                            <div>{{trans('film.progress.copy_toupload', ['cnt'=>$copies])}}</div>
                    </div>
                    <div class="col-md-6 col-xs-12">
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
            </div>
        </div>
    </div>
@endsection
@section('script')

@endsection