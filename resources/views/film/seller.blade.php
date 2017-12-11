@extends('film.card')

@section('filmForm')
    <form id="time_form" name="timeForm" action="/film/rights" method="post" ng-controller="filmCtrl" ng-init="init('{{$film->id}}', '{{$sellers}}')">
        @include('film.form')
    <h4 class="header-slogan">{{trans('film.card.market')}}</h4>
    <div class="alert alert-info" role="alert">
        <div>{!! trans('film.alert.history') !!}</div>
        <div>{!! trans('layout.ALERTS.compulsive') !!}</div>
    </div>
<div class="row" ng-repeat="d in sellers">
    <label><span ng-bind="d.prefix"></span>&nbsp;<span ng-bind="d.last_name"></span>&nbsp;<span ng-bind="d.first_name"></span></label>
    <div class="row">
        <label class="col-xs-4">{{trans('personal.LABELS.company')}}</label>
        <div class="col-xs-8"><span ng-bind="d.company"></span></div>
    </div>
    <div class="row">
        <label class="col-xs-4">{{trans('personal.LABELS.address')}}</label>
        <div class="col-xs-8"><span ng-bind="d.address"></span>&nbsp;<span ng-bind="d.zip"></span>&<span ng-bind="d.city"></span></div>
    </div>
    <div class="row">
        <label class="col-xs-4">{{trans('personal.LABELS.tel')}}</label>
        <div class="col-xs-8"><span ng-bind="d.tel"></span></div>
    </div>
    <div class="row">
        <label class="col-xs-4">{{trans('personal.LABELS.mobile')}}</label>
        <div class="col-xs-8"><span ng-bind="d.mobile"></span></div>
    </div>
    <div class="row">
        <label class="col-xs-4">{{trans('personal.LABELS.email')}}</label>
        <div class="col-xs-8"><span ng-bind="d.email"></span></div>
    </div>
    <div class="text-right">
        <div ng-click="delete(d.id)" class="f fa-trash"></div>
    </div>
</div>
        <div maker="seller">
            <div class="fa fa-plus" ng-click="edit = 1"></div>
            <div class="row" ng-if="edit">
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
                        <label for="prefix" class="col-sm-4 col-xs-6">{{trans('film.label.prefix')}}<sup>*</sup></label>
                        <div class="col-sm-8 col-xs-6">
                            <select name="prefix" ng-model="maker.prefix">
                                @foreach(trans('personal.TITLES') as $key=>$title)
                                    <option value="{{$key}}">{{$title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="first_name" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.first_name')}}<sup>*</sup></label>
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
                        <label for="tel" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.tel')}}<sup>*</sup></label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="tel" class="form-text"  ng-model="maker.tel">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="mobile" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.mobile')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="mobile" class="form-text"   ng-model="maker.mobile" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.email')}}<sup>*</sup></label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="email" class="form-text"   ng-model="maker.email">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group row">
                        <label for="address_book" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.address_book')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <angucomplete-alt id="searchcontact" input-name="contact"
                                              placeholder="{{trans('film.placeholder.book')}}"
                                              pause="100"
                                              selected-object="contactSelected"
                                              local-data="contacts"
                                              focus-in="contactFocus()"
                                              search-fields="company,address"
                                              title-field="company"
                                              description-field="address"
                                              minlength="1"
                                              clear-selected = "false"
                                              input-class="form-text"
                                              match-class="highlight"
                                              text-no-results="{{trans('layout.MENU.none')}}"
                                              text-searching="{{trans('layout.MENU.searching')}}"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.company')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="address" class="form-text" ng-model="maker.contact.company">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.address')}}<sup>*</sup></label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="address" class="form-text" ng-model="maker.contact.address">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.code')}}<sup>*</sup></label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="code" class="form-text" ng-model="maker.contact.zip">
                        </div>
                    </div>
                    <div location="film">
                        <div class="form-group row">
                            <label for="country" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.country')}}<sup>*</sup></label>
                            <div class="col-sm-8 col-xs-6">
                                <select name="country" ng-model="country_id" ng-change="loadDepart(country_id)">
                                    <option value="" disabled translate="location.country"></option>
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="state" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.state')}}<sup>*</sup></label>
                            <div class="col-sm-8 col-xs-6">
                                <select class="form-control" ng-model="department_id" name="department_id"
                                        ng-options="d.id as d.name for d in departments"
                                        ng-change="loadCity(department_id)"
                                        ng-disabled="disabled.depart || disabled.city">
                                    <option value="" disabled translate="location.department"></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="city" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.city')}}<sup>*</sup></label>
                            <div class="col-sm-8 col-xs-6">
                                <select class="form-control" ng-model="maker.contact.city_id" name="city_id"
                                        ng-options="c.id as c.name for c in cities">
                                    <option value="" disabled translate="location.city"></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="web" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.web')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="web" class="form-text" ng-model="maker.web">
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="btn btn-text-default" ng-click="cancel()"><span class="fa fa-undo"></span></div>
                &nbsp;
                <div class="btn btn-text-danger" ng-click="save()"><span class="fa fa-save"></span></div>
            </div>
        </div>
    <hr/>
    <h4 class="header-slogan">{{trans('film.header.rights')}}</h4>
    <div class="form-group row">
        <label class="col-xs-2">
            {!! trans('film.label.music_rights') !!}
        </label>
        <div class="col-xs-10 col-lg-4 col-md-6 input-group">
               <span class="input-group-addon">
                    <input type="radio" name="music_rights" value="1" {{$film->music_rights  == 1 ? "checked" : ""}}>
                </span>
            {{trans('layout.LABELS.yes')}}
            <span class="input-group-addon">
                <input type="radio" name="music_rights" value="0" {{!is_null($film->music_rights) && $film->music_rights  == 0 ? "checked" : ""}}>
            </span>
            {{trans('layout.LABELS.no')}}
        </div>
        <div class="col-lg-6 col-md-4"></div>
    </div>
    <div class="form-group row">
        <label class="col-xs-2">
            {!! trans('film.label.film_rights') !!}
        </label>
        <div class="col-xs-10 col-lg-4 col-md-6 input-group">
           <span class="input-group-addon">
                <input type="radio" name="inter_rights" value="1" {{$film->inter_rights  == 1 ? "checked" : ""}}>
            </span>
            {{trans('layout.LABELS.yes')}}
            <span class="input-group-addon">
                <input type="radio" name="inter_rights" value="0" {{!is_null($film->inter_rights) && $film->inter_rights  == 0 ? "checked" : ""}}>
            </span>
            {{trans('layout.LABELS.no')}}
        </div>
        <div class="col-lg-6 col-md-4"></div>
    </div>
        <hr/>
        <h4 class="header-slogan">{{trans('film.header.history')}}</h4>
        <div class="h6 row">
            <div class=" checkbox">
                <input type="checkbox" name="festivals" id="festivals" class="form-text" value="1" ng-click="check('festivals')" {{$film->festivals == 1 || sizeof($festivals) > 0 ? "checked" : ""}}>
                <label for="festivals"></label><span>{!! trans('film.label.history_festival') !!}</span>
            </div>
        </div>
    <table id="tb_festivals" class="table table-striped" style='display: {{$film->festivals == 1 || sizeof($festivals) > 0 ? "block" : "none"}}'>
        <thead>
        <tr>
            <th>{{trans('film.label.year')}}<sup>*</sup></th>
            <th>{{trans('film.label.event')}}<sup>*</sup></th>
            <th>{{trans('personal.LABELS.country')}}</th>
            <th>{{trans('personal.LABELS.city')}}<sup>*</sup></th>
            <th>{{trans('film.label.competition')}}</th>
            <th width="200px">{{trans('film.label.award')}}</th>
            <th width="20px"><div class="btn text-important fa fa-plus" ng-click="setHistory('f')"></div></th>
        </tr>
        </thead>
        <tbody>
        @foreach($festivals as $f)
        <tr id="festivals_{{$f->id}}">
            <td>{{$f->year}}</td>
            <td>{{$f->event}}</td>
            <td>{{$f->country}}</td>
            <td>{{$f->city}}</td>
            <td>@if($f->competition)<span class="fa fa-check"></span>@endif</td>
            <td>
                <ol class="breadcrumb">
                    @foreach($f->rewards as $r)
                    <li class="breadcrumb-item">{{$r->reward}}</li>
                    @endforeach
                </ol>
            </td>
            <td><span class="fa fa-trash text-important" ng-click="delete('festivals', '{{$f->id}}')"></span></td>
        </tr>
        @endforeach
        <tr ng-repeat="f in festivals" id="festivals_<%$f.id%>">
            <td><span ng-bind="f.year"></span></td>
            <td><span ng-bind="f.event"></span></td>
            <td><span ng-bind="f.country"></span></td>
            <td><span ng-bind="f.city"></span></td>
            <td><span ng-if="f.competition" class="fa fa-check"></span></td>
            <td>
                <ol class="breadcrumb">
                    <li ng-repeat="r in f.rewards" class="breadcrumb-item"><span ng-bind="r"></span></li>
                </ol>
            </td>
            <td><span class="fa fa-trash text-important" ng-click="delete('festivals', f.id)"></span></td>
        </tr>
        <tr ng-if="history=='f'">
            <td>
                <select name="year" ng-model="festival.year">
                    <option value="" disabled>{{trans('film.placeholder.year')}}</option>
                    @for($y = $year; $y > $year -100; $y--)
                        <option value="{{$y}}">{{$y}}</option>
                    @endfor
                </select>
            </td>
            <td>
                <input type="text" name="event" class="form-text" ng-model="festival.event">
            </td>
            <td>
                <select name="country" id="fcountries" ng-model="festival.country_id" ng-change="festivalCountry()">
                    <option value="" disabled>{{trans('film.placeholder.country')}}</option>
                    @foreach($countries as $country)
                        <option id="f_country_{{$country->id}}" value="{{$country->id}}">{{$country->name}}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <angucomplete-alt id="festivalCity" input-name="festival_city"
                                  placeholder="{{trans('film.placeholder.city')}}"
                                  pause="100"
                                  selected-object="festivalSelected"
                                  local-data="fcities"
                                  search-fields="name,zip"
                                  title-field="name"
                                  description-field="zip"
                                  minlength="1"
                                  input-class="form-text"
                                  match-class="highlight"
                                  text-no-results="{{trans('layout.MENU.none')}}"
                                  text-searching="{{trans('layout.MENU.searching')}}"/>
            </td>
            <td>
                <input type="checkbox" value="1" name="competition" ng-model="festival.competition">
            </td>
            <td>
                <ol class="breadcrumb" ng-if="festival.rewards">
                    <li class="breadcrumb-item" ng-repeat="a in festival.rewards" ng-bind="a"></li>
                </ol>
                <div class="input-group" ng-if="festival.competition">
                    <input type="text" name="awards" ng-model="festival.reward" class="form-text" >
                    <span class="input-group-addon" ng-click="saveReward()">
                        <span class="btn btn-text-important fa fa-plus"></span>
                    </span>
                </div>

            </td>
            <td>
                <div class="btn text-success fa fa-save" ng-click="saveHistory('festival')"></div>
                <div class="btn text-muted fa fa-undo" ng-click="cancelHistory()"></div>
            </td>
        </tr>
        </tbody>
    </table>
    <hr/>
        <div class="h6 row">
            <div class=" checkbox">
                <input type="checkbox" name="diffusion" id="diffusion" class="form-text" value="1" ng-click="check('diffusion')"  {{$film->diffusion == 1 || sizeof($diffusion) ? "checked" : ""}}>
                <label for="diffusion"></label><span>{!! trans('film.label.history_tv') !!}</span>
            </div>

        </div>
    <table id="tb_diffusion" class="table table-striped table-responsive" style='display: {{$film->festivals == 1 || sizeof($diffusion) > 0 ? "block" : "none"}}'>
        <thead>
        <tr>
            <th>{{trans('film.label.channel')}}<sup>*</sup></th>
            <th>{{trans('film.label.name_tv')}}<sup>*</sup></th>
            <th>{{trans('personal.LABELS.country')}}</th>
            <th>{{trans('film.label.year')}}</th>
            <th width="20px"><div class="btn text-important fa fa-plus" ng-click="setHistory('d')"></div> </th>
        </tr>
        </thead>
        <tbody>
        @foreach($diffusion as $d)
            <tr id="diffusion_{{$d->id}}">
                <td>{{$d->channel}}</td>
                <td>{{$d->name}}</td>
                <td>{{$d->country}}</td>
                <td>{{$d->year}}</td>
                <td><span class="fa fa-trash text-important" ng-click="delete('diffusion', '{{$d->id}}')"></span></td>
            </tr>
        @endforeach
        <tr ng-repeat="d in diffusions" id="diffusion_<%d.id%>">
            <td><span ng-bind="d.channel"></span></td>
            <td><span ng-bind="d.name"></span></td>
            <td><span ng-bind="d.country"></span></td>
            <td><span ng-bind="d.year"></span></td>
            <td><span class="fa fa-trash text-important" ng-click="delete('diffusion', d.id)"></span></td>
        </tr>
        <tr ng-if="history == 'd'">
            <td>
                <select name="channel" ng-model="diffusion.channel">
                    <option value="" disabled>{{trans('film.placeholder.channel')}}</option>
                    @foreach(trans('film.channel') as $key=>$channel)
                        <option value="{{$key}}">{{$channel}}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" class="form-text" ng-model="diffusion.name">
            </td>
            <td>
                <select name="country"  ng-model="diffusion.country_id" ng-change="diffusionCountry()">
                    <option value="" disabled>{{trans('film.placeholder.country')}}</option>
                    @foreach($countries as $country)
                        <option id="d_country_{{$country->id}}" value="{{$country->id}}">{{$country->name}}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="year"  ng-model="diffusion.year">
                    <option value="" disabled>{{trans('film.placeholder.year')}}</option>
                    @for($y = $year; $y > $year -100; $y--)
                        <option value="{{$y}}">{{$y}}</option>
                    @endfor
                </select>
            </td>
            <td>
                <div class="btn text-success fa fa-save" ng-click="saveHistory('diffusion')"></div>
                <div class="btn text-muted fa fa-undo" ng-click="cancelHistory()"></div>
            </td>
        </tr>
        </tbody>
    </table>
    <hr/>
        <div class="h6 row">
            <div class=" checkbox">
                <input type="checkbox" name="theaters" id="theaters" class="form-text" value="1"  ng-click="check('theaters')" {{$film->theaters == 1 || sizeof($theaters) ? "checked" : ""}}>
                <label for="theaters"></label><span>{!! trans('film.label.history_theatre') !!}</span>
            </div>

        </div>
    <table id="tb_theaters" class="table table-striped table-responsive" style='display: {{$film->theaters == 1 || sizeof($theaters) > 0 ? "block" : "none"}}'>
        <thead>
        <tr>
            <th>{{trans('film.label.program')}}<sup>*</sup></th>
            <th>{{trans('film.label.name_program')}}<sup>*</sup></th>
            <th>{{trans('film.label.distributed')}}<sup>*</sup></th>
            <th>{{trans('personal.LABELS.country')}}</th>
            <th>{{trans('film.label.contact')}}</th>
            <th>{{trans('film.label.year')}}</th>
            <th width="20px"><div class="btn text-important fa fa-plus" ng-click="setHistory('t')"></div> </th>
        </tr>
        </thead>
        <tbody>
        @foreach($theaters as $t)
            <tr  id="theaters_{{$t->id}}">
                <td>{{$t->program}}</td>
                <td>{{$t->title}}</td>
                <td>{{$t->distribution}}</td>
                <td>{{$t->country}}</td>
                <td>{{$t->contact}}</td>
                <td>{{$t->year}}</td>
                <td><span class="fa fa-trash text-important" ng-click="delete('theaters', '{{$t->id}}')"></span></td>
            </tr>
        @endforeach
        <tr ng-repeat="t in theaters" id="theaters_<%t.id%>">
            <td><span ng-bind="t.program"></span></td>
            <td><span ng-bind="t.title"></span></td>
            <td><span ng-bind="t.distribution"></span></td>
            <td><span ng-bind="t.country"></span></td>
            <td><span ng-bind="t.contact"></span></td>
            <td><span ng-bind="t.year"></span></td>
            <td><span class="fa fa-trash text-important" ng-click="delete('theaters', t.id)"></span></td>
        </tr>
        <tr ng-if="history == 't'">
            <td>
                <select name="program" ng-model="theater.program">
                    <option value="" disabled>{{trans('film.placeholder.channel')}}</option>
                    @foreach(trans('film.program') as $key=>$program)
                        <option value="{{$key}}">{{$program}}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" name="name" class="form-text" ng-model="theater.title">
            </td>
            <td>
                <input type="text" name="distribution" class="form-text" ng-model="theater.distribution">
            </td>
            <td>
                <select name="country" ng-model="theater.country_id" ng-change="theaterCountry()">
                    <option value="" disabled>{{trans('film.placeholder.country')}}</option>
                    @foreach($countries as $country)
                        <option id="c_country_{{$country->id}}" value="{{$country->id}}">{{$country->name}}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" name="contact" class="form-text" ng-model="theater.contact">
            </td>
            <td>
                <select name="year" ng-model="theater.year">
                    <option value="" disabled>{{trans('film.placeholder.year')}}</option>
                    @for($y = $year; $y > $year -100; $y--)
                        <option value="{{$y}}">{{$y}}</option>
                    @endfor
                </select>
            </td>
            <td>
                <div class="btn text-success fa fa-save" ng-click="saveHistory('theater')"></div>
                <div class="btn text-muted fa fa-undo" ng-click="cancelHistory()"></div>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="text-right">
        <button class="btn btn-primary">{{trans('layout.BUTTONS.continue')}}</button>
    </div>
    </form>
@endsection
@section('script')
    <script src="/js/directives/location.js"></script>
    <script src="/js/directives/filmaker.js"></script>
    <script src="/js/controllers/film/seller.js"></script>
@endsection