@extends('layouts.film')

@section('filmForm')
    <form name="filmForm" action="/{{$film->type}}s" method="POST" ng-controller="filmCtrl"
          ng-init="init()">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$film->id}}" />
        <input type="hidden" name="step" value="{{$step+1}}" />
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center" id="modal-body">
                        <div>{{trans('film.alert.delete_synopsis')}}<span class="pl-1 text-primary" ng-bind="synopsisToDelete.language"></span> </div>
                    </div>
                    <div class="modal-footer d-flex px-5">
                        <button class="btn btn-danger mr-auto" type="button" data-dismiss="modal" >
                            {{trans("project.BUTTONS.cancel")}}
                        </button>
                        <button class="btn btn-primary" type="button" ng-click="synopsisDeleted('{{$film->id}}')" >
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <ul class="alert alert-dark text-primary small px-5" role="alert">
            <li>{!! trans('film.alert.synopsis') !!}</li>
            <li class="pt-1">{!! trans('layout.ALERTS.compulsive') !!}</li>
        </ul>
        <div class="row text-primary my-5 pl-2 pr-4">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified required">{!! trans('film.label.summary') !!}</label>
            <div class="col-lg-10 col-md-8 col-sm-12">
                <div  ng-show="synopsisCopy.language_id != '{{$lang->id}}'">
                    <div id="content_{{$lang->id}}">{{$synopsis?$synopsis->content:''}}</div>
                    <div class="my-3 btn btn-block btn-primary" ng-click="edit('{{$lang->id}}')">
                        {{trans('layout.BUTTONS.edit')}}
                    </div>
                </div>
                <div ng-if="synopsisCopy.language_id == '{{$lang->id}}'">
                    <div class="input input--isao">
                        <textarea id="text_content_{{$lang->id}}" name="synopsis" rows="4" autofocus class="input__field input__field--isao" ng-model="synopsisCopy.content"></textarea>
                        <label class="input__label input__label--isao" for="text_content_{{$lang->id}}" data-content="{{trans('film.placeholder.synopsis', ['cnt'=>400])}}">
                            <span class="input__label-content input__label-content--isao">
                                <span ng-if="errors.content == 0 && synopsisCopy.content.length <= 400">{{trans('film.placeholder.synopsis', ['cnt'=>400])}}</span>
                                    <span class="text-danger" ng-if="errors.content > 0 || synopsisCopy.content.length > 400">{{trans('film.error.maxlength_synopsis_content', ['cnt'=>400])}}</span>
                            </span>
                        </label>
                    </div>
                    <div class="m-3 text-right">
                        @if(!$synopsis ||is_null($synopsis->content))
                            <div class="btn btn-outline-primary fa fa-undo" ng-click="cancel()"></div>
                        @endif
                        <div class="btn btn-primary fa fa-check ml-5" ng-click="update('{{$film->id}}')"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row text-primary my-5">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified">{{trans('film.label.summary_trans')}}</label>
            <div class="col-lg-10 col-md-8 col-sm-12 pl-3">
                <div class="pr-4">
                    <div class="btn btn-block" ng-show="!addNew" ng-class="{'btn-outline-primary':!addNew, 'btn-outline-secondary':addNew}" ng-click="addNew=true;">{{trans('film.label.summary_add')}}</div>
                </div>
                <div ng-show="addNew">
                    <div class="input input--isao pt-4" >
                        <select id="newsynopsis_language" name="newsynopsis_language" ng-model="newsynopsis.language_id" class="input__field input__field--isao" required>
                            @foreach($languages as $key=>$language)
                                <option value="{{$key}}" id="opt_lang_{{$key}}" {{$list->where('language_id', $key)->first() ? 'disabled':''}}>{{$language}}</option>
                            @endforeach
                        </select>
                        <label class="input__label input__label--isao" for="synopsis_language" data-content="{{trans('film.placeholder.language')}}">
                            <span class="input__label-content input__label-content--isao">{{trans('film.placeholder.language')}}</span>
                        </label>
                    </div>
                    <div role="alert" class="error" ng-class="{'visible':errors.newsynopsis.language}">
                        <span ng-show="errors.newsynopsis.language == 1">{{trans("film.error.require_synopsis_language")}}</span>
                        <span ng-show="errors.newsynopsis.language > 1">{{trans("film.error.double_synopsis_language")}}</span>
                    </div>
                    <div class="input input--isao">
                    <textarea  id="newsynopsis_content" class="input__field input__field--isao" name="newsynopsis_content" rows="4"
                               ng-model="newsynopsis.content" minlength="1" ng-maxlength="400">{{trans('film.placeholder.synopsis', ['cnt'=>400])}}</textarea>
                        <label class="input__label input__label--isao" for="newsynopsis_content" data-content="{{trans('film.placeholder.synopsis', ['cnt'=>400])}}">
                            <span class="input__label-content input__label-content--isao">{{trans('film.placeholder.synopsis', ['cnt'=>400])}}</span>
                        </label>
                    </div>
                    <div class="text-right px-4">
                        <div class="btn btn-outline-primary fa fa-undo mr-5" ng-click="addNew=false;"></div>
                        <div class="btn btn-primary fa fa-check" ng-click="save('{{$film->id}}')"></div>
                    </div>
                </div>
                <br/>
                @foreach($list as $s)
                    <div id="synopsis_{{$s->language_id}}">
                        <div class="d-flex">
                            <span id="lang_{{$s->language_id}}" class="mr-auto">{{$s->language}}</span>
                            <span class="btn text-muted fa fa-edit" ng-click="edit('{{$s->language_id}}')"></span>
                        </div>
                        <div class="pt-3" ng-show="synopsisCopy.language_id != '{{$s->language_id}}'">
                            <div id="content_{{$s->language_id}}" class="text-primary pr-5" style="word-break:break-all; ">
                               {{$s->content}}
                            </div>
                            <div class="text-right">
                                <div class="btn text-danger fa fa-trash-o" ng-click="deleteSynopsis('{{$s->language_id}}')"></div>
                            </div>
                        </div>
                        <div ng-if="synopsisCopy.language_id == '{{$s->language_id}}'">
                            <div class="input input--isao">
                                <textarea id="text_{{$s->language_id}}" class="input__field input__field--isao"
                                          name="text_{{$s->language_id}}" rows="4" ng-model="synopsisCopy.content">
                                </textarea>
                                <label class="input__label input__label--isao" for="text_{{$s->language_id}}" data-content="{{trans('film.placeholder.synopsis', ['cnt'=>400])}}">
                                    <span class="input__label-content input__label-content--isao">
                                        <span ng-if="errors.content == 0 && synopsisCopy.content.length <= 400">{{trans('film.placeholder.synopsis', ['cnt'=>400])}}</span>
                                        <span class="text-danger" ng-if="errors.content > 0 || synopsisCopy.content.length > 400">{{trans('film.error.maxlength_synopsis_content', ['cnt'=>400])}}</span>
                                    </span>
                                </label>
                            </div>
                            <div class="text-right px-4">
                                <div class="btn btn-outline-primary fa fa-undo mr-5" ng-click="cancel()"></div>
                                <div class="btn btn-primary fa fa-check" ng-click="update('{{$film->id}}', synopsisCopy)"></div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                @endforeach
                <div ng-repeat="s in list" id="synopsis_<%s.language_id%>">
                    <div  class="d-flex">
                        <span class="mr-auto" id="lang_<%s.language_id%>" ng-bind="s.language"></span>
                        <span class="btn text-muted fa fa-edit" ng-click="edit(s.language_id, true)"></span>
                    </div>
                    <div class="pt-3" ng-show="synopsisCopy.language_id != s.language_id">
                        <div id="content_<%s.language_id%>" class="text-primary pr-5" style="word-break:break-all; " ng-bind="s.content"></div>
                        <div class="text-right">
                            <div class="btn text-danger fa fa-trash-o" ng-click="deleteSynopsis(s.language_id, true)"></div>
                        </div>
                    </div>
                    <div ng-if="synopsisCopy.language_id == s.language_id">
                        <div class="input input--isao">
                            <textarea id="text_<%s.language_id%>" class="input__field input__field--isao" name="text_<%s.language_id%>" rows="4"
                                       ng-model="synopsisCopy.content">{{trans('film.placeholder.synopsis', ['cnt'=>400])}}</textarea>
                            <label class="input__label input__label--isao" for="text_<%s.language_id%>" data-content="{{trans('film.placeholder.synopsis', ['cnt'=>400])}}">
                                <span class="input__label-content input__label-content--isao">
                                    <span ng-if="errors.content == 0 && synopsisCopy.content.length <= 400">{{trans('film.placeholder.synopsis', ['cnt'=>400])}}</span>
                                    <span class="text-danger" ng-if="errors.content > 0 || synopsisCopy.content.length > 400">{{trans('film.error.maxlength_synopsis_content', ['cnt'=>400])}}</span>
                                </span>
                            </label>
                        </div>
                        <div class="text-right px-4">
                            <div class="btn btn-outline-primary fa fa-undo mr-5" ng-click="cancel()"></div>
                            <div class="btn btn-primary fa fa-check" ng-click="update('{{$film->id}}')"></div>
                        </div>
                    </div>
                    <hr/>
                </div>
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
    <script src="/js/controllers/film/synopsis.js"></script>
@endsection