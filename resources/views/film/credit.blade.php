@extends('film.card')

@section('filmForm')
    <form id="time_form" name="timeForm" action="/film/credit" method="post" ng-controller="filmCtrl" ng-init="init('{{$film->id}}')">
        @include('film.form')
    <h4 class="header-slogan">{{trans('film.card.credits')}}</h4>
    <div class="alert alert-info" role="alert">
        <div>{!! trans('layout.ALERTS.compulsive') !!}</div>
    </div>
    <div class="form-group row">
        <label class="col-xs-2">
            {{trans('film.label.music_original')}}
        </label>
        <div class="col-xs-10 input-group">
               <span class="input-group-addon">
                    <input type="radio" name="music_original" value="1" {{$film->music_original == 1 ? "checked":"" }}>
                </span>
            {{trans('layout.LABELS.yes')}}
            <span class="input-group-addon">
                <input type="radio" name="music_original" value="0" {{!is_null($film->music_original) && $film->music_original == 0 ? "checked":"" }}>
            </span>
            {{trans('layout.LABELS.no')}}
        </div>
    </div>
    <div class="form-group row">
        <label class="col-xs-2">
            {{trans('film.label.script_original')}}
        </label>
        <div class="col-xs-10 input-group">
            <span class="input-group-addon">
                <input type="radio" name="screenplay_original" value="1" {{$film->screenplay_original == 1 ? "checked":"" }} />
            </span>
            {{trans('layout.LABELS.yes')}}
            <span class="input-group-addon">
                <input type="radio" name="screenplay_original" value="0" {{!is_null($film->screenplay_original) && $film->screenplay_original == 0 ? "checked":"" }} />
            </span>
            {{trans('layout.LABELS.no')}}
        </div>
    </div>
    <table class="table table-responsive table-bordered">
        <thead>
            <tr>
                <th class=" bg-primary" width="150px">{{trans('film.label.function')}}</th>
                <th>{{trans('film.header.cast')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($credits as $credit)
                <tr>
                    <td class="table-bordered bg-primary">
                        {{$credit->label}}
                    </td>
                    <td id="td_{{$credit->id}}">
                        @if(array_key_exists($credit->id, $casts))
                            @foreach($casts[$credit->id] as $cast)
                                {{$cast->name}}
                                @if (!$loop->last)
                                    <span>,&nbsp;</span>
                                @endif
                            @endforeach
                        @endif
                    </td>
                    <td>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
        <hr>
        <div class="btn btn-default"  ng-click="edit = 1" ng-show="!edit">{{trans('film.buttons.credit')}}</div>
        <div ng-if="edit">
            <div class="form-group">
                <label for="birthday" >{{trans('film.label.function')}}</label>
                <div class="row">
                    @foreach($credits as $credit)
                        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                            <div class="input-group">
                                    <span class="input-group-addon">
                                        <input id="credit_{{$credit->id}}" type="checkbox" name="credits" value="{{$credit->id}}">
                                    </span>
                                {{$credit->label}}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="form-group row">
                        <label for="director_name" class="col-sm-4 col-xs-6">{{trans('film.label.name_book')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <angucomplete-alt id="searchmaker" input-name="maker"
                                              placeholder="{{trans('film.placeholder.search')}}"
                                              pause="100"
                                              selected-object="makerSelected"
                                              local-data="makers"
                                              focus-in="makerFocus()"
                                              search-fields="first_name,last_name"
                                              title-field="last_name"
                                              description-field="first_name"
                                              minlength="1"
                                              clear-selected = "false"
                                              input-class="form-text"
                                              match-class="highlight"
                                              text-no-results="{{trans('layout.MENU.none')}}"
                                              text-searching="{{trans('layout.MENU.searching')}}"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="prefix" class="col-sm-4 col-xs-6">{{trans('film.label.prefix')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <select name="prefix" ng-model="maker.prefix">
                                @foreach(trans('personal.TITLES') as $key=>$title)
                                    <option value="{{$key}}">{{$title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="first_name" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.first_name')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="first_name" class="form-text"  ng-model="maker.first_name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="last_name" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.last_name')}}<sup>*</sup></label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="last_name" class="form-text" ng-model="maker.last_name">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group row">
                        <label for="birthday" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.birthday')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <select name="birthday" ng-model="maker.born">
                                @for($y = $year - 14; $y > $year-130; $y--)
                                    <option value="{{$y}}">{{$y}}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tel" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.tel')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="tel" class="form-text" ng-model="maker.tel">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="mobile" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.mobile')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="mobile" class="form-text"  ng-model="maker.mobile">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.email')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="email" class="form-text"   ng-model="maker.email">
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="btn btn-default" ng-click="cancel()"><span class="fa fa-undo"></span></div>
                <div class="btn btn-success" ng-click="save()"><span class="fa fa-save"></span></div>
            </div>
        </div>
    <hr/>
    <div class="text-right">
        <button class="btn btn-primary" type="submit">{{trans('layout.BUTTONS.continue')}}</button>
    </div>
    </form>
@endsection
@section('script')
    <script src="/js/controllers/film/credit.js"></script>
@endsection