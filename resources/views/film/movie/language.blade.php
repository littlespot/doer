@extends('layouts.film')

@section('filmForm')
    <form name="filmForm" action="/{{$film->type}}s" method="POST"
          ng-controller="filmCtrl" ng-init="init( '{{$dialogs}}', '{{$productions}}','{{$shootings}}')">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$film->id}}" />
        <input type="hidden" name="step" value="{{$step+1}}" />
        <div class="modal fade" id="alertAddModal" tabindex="-1" role="dialog" aria-labelledby="alertAddModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center" id="modal-body">
                        <div>
                            <span class="pr-1 text-primary" ng-bind="doubleValue.name"></span>
                            <span ng-switch="doubleValue.id">
                                <i ng-switch-when="p">{{trans('film.alert.add_production')}}</i>
                                <i ng-switch-when="s">{{trans('film.alert.add_shooting')}}</i>
                                <i ng-switch-when="i">{{trans('film.alert.add_dialog')}}</i>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer d-flex px-5">
                        <button class="btn btn-primary" type="button" data-dismiss="modal" ng-if="">
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center" id="modal-body">
                        <div ng-if="productionToDelete">{{trans('film.alert.delete_production')}}<span class="pl-1 text-primary" ng-bind="productionToDelete.name"></span></div>
                        <div ng-if="shootingToDelete">{{trans('film.alert.delete_shooting')}}<span class="pl-1 text-primary" ng-bind="shootingToDelete.name"></span> </div>
                        <div ng-if="dialogToDelete">{{trans('film.alert.delete_dialog')}}<span class="pl-1 text-primary" ng-bind="dialogToDelete.name"></span> </div>
                    </div>
                    <div class="modal-footer d-flex px-5">
                        <button class="btn btn-danger mr-auto" type="button" data-dismiss="modal" >
                            {{trans("project.BUTTONS.cancel")}}
                        </button>
                        <button ng-if="productionToDelete" class="btn btn-primary" type="button" ng-click="productionDeleted('{{$film->id}}')" >
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                        <button ng-if="shootingToDelete" class="btn btn-primary" type="button" ng-click="shootingDeleted('{{$film->id}}')" >
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                        <button ng-if="dialogToDelete" class="btn btn-primary" type="button" ng-click="dialogDeleted('{{$film->id}}')" >
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <ul class="alert alert-dark text-primary small px-5" role="alert">
            <li>{!! trans('film.alert.nation1') !!}</li>
            <li class="py-1">{!! trans('film.alert.nation2') !!}</li>
            <li>{!! trans('layout.ALERTS.compulsive') !!}</li>
        </ul>
        <div class="form">
            <div class="form-group row px-3 my-5" ng-init="principal = '{{$film->country_id ?: 0}}';country_name='{{$film->country_id ? $countries[$film->country_id]:''}}'">
                <label for="title_original" class="col-md-4 col-xs-12  required label-justified">
                    {!!trans('film.label.nation_principal')!!}
                </label>
                <div class="col-md-6 col-xs-12">
                    <span ng-show="principal && !countryEdited" id="country_name" class="ml-3 text-primary" ng-bind="country_name"></span>
                    <div class="input input--isao" ng-show="principal == 0 || countryEdited == principal">
                        <select id="nation_principal" name="country_id" class="input__field input__field--isao">
                            <option value="" disabled>{{trans('film.placeholder.principal')}}</option>
                            @foreach($countries as $key=>$country)
                                @if(array_key_exists($key, $productions) || $key == $film->country_id)
                                    <option id="opt_country_{{$key}}" value="{{$key}}" ng-selected="countryEdited == {{$key}}" disabled>{{$country}}</option>
                                @else
                                    <option id="opt_country_{{$key}}" value="{{$key}}" ng-selected="countryEdited == {{$key}}">{{$country}}</option>
                                @endif
                            @endforeach
                        </select>
                        <label class="input__label input__label--isao" for="nation_principal" data-content="{{trans('film.placeholder.principal')}}">
                            <span class="input__label-content input__label-content--isao">
                                 @if($film->country_id)
                                    {{trans('layout.ALERTS.checkToSelect')}}
                                @else
                                    {{trans('film.placeholder.principal')}}
                                @endif
                            </span>
                        </label>
                    </div>
                </div>
                <div class="col-md-2 col-sm-1">
                    @if($film->country_id)
                    <div class="btn text-primary" ng-if="principal >0 && !countryEdited" ng-click="editCountry()"><span class="fa fa-edit"></span></div>
                    <div class="btn text-success" ng-if="principal==0 || countryEdited == principal"
                         ng-click="changeCountry('{{$film->id}}')"><span class="fa fa-check"></span></div>
                    <div class="btn text-danger" ng-if="principal >0 && countryEdited == principal" ng-click="cancelCountry()"><span class="fa fa-undo"></span></div>
                    @endif
                </div>
            </div>
            <div class="form-group row px-3 mt-5">
                <label for="block_production" class="col-md-4 col-xs-12 label-justified">
                    {{trans('film.label.nation_other')}}
                </label>
                <div class="col-md-8 col-xs-12" id="block_production">
                     <span class="px-3 btn" ng-repeat="p in productions" id="production-<%p.id%>">
                        <span ng-bind="p.name"></span><sup class="text-danger fa fa-times-circle" ng-click="removeProduction(p.id, p.name)"></sup>
                     </span>
                </div>
            </div>
            <div class="px-3 mb-5" ng-if="productions.length > 4">
                <div class="alert alert-info text-center" role="alert">{{trans('film.alert.productions')}}</div>
            </div>
            <div class="form-group row px-3 mb-5" ng-if="productions.length <5">
                <div class="col-md-4"></div>
                <div class="col-md-6 col-xs-12 input input--isao" id="block_production">
                    <select id="nation_productions" name="production_id" class="input__field input__field--isao">
                        <option value="">{{trans('film.placeholder.other')}}</option>
                        @foreach($countries as $key=>$country)
                            <option id="production_country_{{$key}}" value="{{$key}}" {{$key == $film->country_id || $productions->where('id',$key)->first()?'disabled' : ''}}>{{$country}}</option>
                        @endforeach
                    </select>
                    <label class="input__label input__label--isao" for="nation_productions" data-content="{{trans('film.label.other')}}">
                        <span class="input__label-content input__label-content--isao">{{trans('layout.ALERTS.plusToSelect')}}</span>
                    </label>
                </div>
                <div class="col-md-2 col-sm-1" id="btn_production" >
                    <div class="btn text-primary" ng-click="addProduction('{{$film->id}}')"><span class="fa fa-plus"></span></div>
                </div>
            </div>
            <div class="form-group row px-3 mt-5">
                <label for="shooting" class="col-md-4 col-xs-12 label-justified">
                    {{trans('film.label.nation_shooting')}}<span title="{{trans('film.tip.location')}}"><span class="btn text-primary fa fa-question-circle"></span></span>
                </label>
                <div class="col-md-6 col-xs-12" id="block_shooter">
                     <span class="px-3 btn" ng-repeat="s in shootings" id="shooting-<%s.id%>">
                        <span ng-bind="s.name"></span><sup class="text-danger fa fa-times-circle" ng-click="removeShooting(s.id, s.name)"></sup>
                     </span>
                </div>
            </div>
            <div class="px-3 mb-5" ng-if="shootings.length > 8">
                <div class="alert alert-info text-center" role="alert">{{trans('film.alert.shootings')}}</div>
            </div>
            <div class="form-group row px-3 mb-5"  ng-if="shootings.length < 9">
                <div class="col-md-4"></div>
                <div class="col-md-6 col-xs-12 input input--isao" id="block_shooting">
                    <select id="nation_shootings" name="shooting_id" class="input__field input__field--isao">
                        <option value="">{{trans('layout.ALERTS.plusToSelect')}}</option>
                        @foreach($countries as $key=>$country)
                            <option id="shooting_country_{{$key}}" value="{{$key}}" {{$shootings->where('id', $key)->first()?'disabled' : ''}}>{{$country}}</option>
                        @endforeach
                    </select>
                    <label class="input__label input__label--isao" for="nation_shootings" data-content="{{trans('film.placeholder.shooting')}}">
                        <span class="input__label-content input__label-content--isao">{{trans('layout.ALERTS.plusToSelect')}}</span>
                    </label>
                </div>
                <div class="col-md-2 col-sm-1" id="btn_production" >
                    <div class="btn text-primary" ng-click="addShooting('{{$film->id}}')"><span class="fa fa-plus"></span></div>
                </div>
            </div>
            <div class="form-group row my-5">
                <label for="silent" class="col-md-4 col-sm-3 col-xs-12 text-right required">
                    {!! trans('film.label.silent') !!}
                </label>
                <div class="col-md-6 col-sm-8 col-xs-8 row" ng-init="silent = {{is_null($film->silent) ? 0 : $film->silent}}">
                    <div class="radio-inline col-6">
                        <input type="radio" name="silent" ng-value="1"  ng-model="silent" >
                        {{trans('layout.LABELS.yes')}}
                    </div>
                    <div class="radio-inline col-6">
                        <input type="radio" name="silent" ng-value="0"  ng-model="silent">
                        {{trans('layout.LABELS.no')}}
                    </div>
                </div>
                <div class="col-md-2 col-sm-1"></div>
            </div>
            <div class="form-group row my-5">
                <label for="dialogue" class="col-md-4 col-sm-3 col-xs-12 text-right required" >
                    {!! trans('film.label.dialogue') !!}
                </label>
                <div class="col-md-6 col-sm-8 col-xs-12 row" ng-init="mute = {{is_null($film->mute) ? 0 : $film->mute}}">
                    <div class="radio-inline col-6 ">
                        <input type="radio" name="mute" ng-value="0" ng-model="mute">
                        {{trans('film.label.has_dialog')}}
                    </div>
                    <div class="radio-inline col-6">
                        <input type="radio" name="mute" ng-value="1" ng-model="mute">
                        {{trans('film.label.no_dialog')}}
                    </div>
                </div>
                <div class="col-md-2 col-sm-1"></div>
            </div>
            <div  ng-hide="mute">
                <div class="form-group row my-5" id="block_lang">
                    <label for="block_dialog" class="col-md-4 col-xs-12 text-right required">
                        {{trans('film.label.dialogue_language')}}
                    </label>
                    <div class="col-md-8 col-xs-12" id="block_dialog">
                         <span class="px-3 btn" ng-repeat="d in dialogs" id="dialog-<%d.id%>">
                            <span ng-bind="d.name"></span><sup class="text-danger fa fa-times-circle" ng-click="removeDialog(d)"></sup>
                         </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-6 col-xs-10 input input--isao">
                        <select id="dialog_lang" name="language_id" class="input__field input__field--isao">
                            <option value="">{{trans('layout.ALERTS.plusToSelect')}}</option>
                            @foreach ($languages as $key=>$language)
                                <option id="dialog_{{$key}}" value="{{$key}}" {{$dialogs->where('id', $key)->first()?'disabled':''}}>{{$language}}</option>
                            @endforeach
                        </select>
                        <label class="input__label input__label--isao" for="dialog_lang" data-content="{{trans('film.placeholder.dialog')}}">
                            <span class="input__label-content input__label-content--isao">{{trans('layout.ALERTS.plusToSelect')}}</span>
                        </label>
                    </div>
                    <div class="col-md-2 col-xs-2">
                        <div class="btn text-primary" ng-click="addDialog('{{$film->id}}')"><span class="fa fa-plus"></span></div>
                    </div>
                </div>
                <div class="form-group row my-5" ng-init="conlange = '{{$film->conlange}}'">
                    <label for="block_conlange" class="col-md-4 col-xs-12 text-right">
                        {{trans('film.label.conlange')}}
                    </label>
                    <div class="col-md-6 col-xs-12" id="block_conlange">
                        <div ng-if="conlange && !conlangeEdited" ng-bind="conlange"></div>
                        <div ng-if="!conlange || conlangeEdited" class=" input input--isao">
                            <input id="conlange" type="text" ng-model="conlange"
                                   name="conlange" class="input__field input__field--isao" placeholder="{{trans('layout.ALERTS.plusToInput')}}">
                            <label class="input__label input__label--isao" for="conlange" data-content="{{trans('film.placeholder.other_lang')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('layout.ALERTS.plusToInput')}}</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 col-xs-2">
                        <div ng-if="conlange && !conlangeEdited" class="btn text-primary" ng-click="editConlange()">
                            <span class="fa fa-edit"></span>
                        </div>
                        <div ng-if="conlange && !conlangeEdited" class="btn text-danger" ng-click="removeConlange('{{$film->id}}')">
                            <span class="fa fa-times"></span>
                        </div>
                        <div ng-if="!conlange || conlangeEdited" class="btn" ng-click="changeConlange('{{$film->id}}')">
                            <span class="fa" ng-class="{'fa-check text-success':conlange, 'fa-plus text-primary':!conlange}"></span>
                        </div>
                        <div ng-if="conlange && conlangeEdited" class="btn text-danger" ng-click="conlangeEdited = false;">
                            <span class="fa fa-undo"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr/>
        <div class="d-flex justify-content-between">
            <div class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteFilmModal">{{trans('film.buttons.delete')}}</div>
            <button class="btn btn-primary">{{trans('layout.BUTTONS.continue')}}</button>
        </div>
    </form>
@endsection
@section('script')
    <script src="/js/controllers/film/production.js"></script>
@endsection