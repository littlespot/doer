@extends('layouts.film')

@section('filmForm')
    <form name="filmForm" action="/{{$film->type}}s" method="POST"
          ng-controller="filmCtrl" ng-init="init('{{$film->id}}','{{$pscreens}}', '{{$cscreens}}', '{{$vscreens}}')">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$film->id}}" />
        <input type="hidden" name="step" value="{{$step+1}}" />
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header text-danger">
                        {{trans('film.alert.delete_screen')}}
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="delete-body">
                        <div ng-if="deletedType == 'digital'">
                            <div class="row py-3">
                                <div class="col-6 text-right">{{trans('film.label.format')}}:</div>
                                <div class="col-6"><span ng-bind="screenToDelete.label"></span></div>
                            </div>
                            <div class="row">
                                <div class="col-6 text-right">{{trans('film.label.ratio')}}:</div>
                                <div class="col-6"><span ng-bind="screenToDelete.ratio"></span></div>
                            </div>
                            <div class="row  py-3">
                                <div class="col-6 text-right">{{trans('film.label.resolution')}}:</div>
                                <div class="col-6">
                                    <span ng-bind="screenToDelete.resolution_x"></span>
                                    <span ng-if="screenToDelete.resolution_x && screenToDelete.resolution_y" class="fa fa-times px-2"></span>
                                    <span ng-bind="screenToDelete.resolution_y"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 text-right">{{trans('film.label.size')}}(G):</div>
                                <div class="col-6"><span ng-bind="screenToDelete.size"></span></div>
                            </div>
                        </div>
                        <div ng-if="deletedType == 'cine'">
                            <div class="row py-3">
                                <div class="col-6 text-right">{{trans('film.label.format')}}:</div>
                                <div class="col-6"><span ng-bind="screenToDelete.label"></span></div>
                            </div>
                            <div class="row">
                                <div class="col-6 text-right">{{trans('film.label.ratio')}}:</div>
                                <div class="col-6"><span ng-bind="screenToDelete.ratio"></span></div>
                            </div>
                            <div class="row  py-3">
                                <div class="col-6 text-right">FPS:</div>
                                <div class="col-6"><span ng-bind="screenToDelete.speed"></span></div>
                            </div>
                        </div>
                        <div ng-if="deletedType == 'video'">
                            <div class="row py-3">
                                <div class="col-6 text-right">{{trans('film.label.format')}}:</div>
                                <div class="col-6"><span ng-bind="screenToDelete.label"></span></div>
                            </div>
                            <div class="row">
                                <div class="col-6 text-right">{{trans('film.label.ratio')}}:</div>
                                <div class="col-6"><span ng-bind="screenToDelete.ratio"></span></div>
                            </div>
                            <div class="row  py-3">
                                <div class="col-6 text-right">{{trans('film.label.standard')}}:</div>
                                <div class="col-6"><span ng-bind="screenToDelete.standard"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex px-5">
                        <button class="btn btn-outline-primary mr-auto" type="button" data-dismiss="modal">
                            {{trans("project.BUTTONS.cancel")}}
                        </button>
                        <button class="btn btn-danger" type="button" ng-click="screenDeleted('{{$film->id}}')">
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bd-example-modal-lg" id="editorModal" tabindex="-1" role="dialog" aria-labelledby="editorModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-5" id="edit-body" name="editForm">
                        <div ng-if="editedType == 'video'">
                            <div class="row">
                                <div class="col-lg-8 col-md-12 input input--isao" >
                                    <select id="format_label" name="format_video_id" ng-model="editedScreen.format_video_id" class="input__field input__field--isao" required>
                                        @foreach($vformats as $format)
                                            <option value="{{$format->id}}">{{$format->label}}</option>
                                        @endforeach
                                    </select>
                                    <label class="input__label input__label--isao" for="format_label" data-content="{{trans('film.placeholder.playFormat')}}">
                                        <span class="input__label-content input__label-content--isao"><i class="text-danger pr-1">*</i>{{trans('film.placeholder.playFormat')}}</span>
                                    </label>
                                    <div role="alert" class="error" ng-class="{'visible':editForm.format_video.$touched}">
                                        <span ng-show="editForm.format_video.$error.required">{{trans("film.error.require_title")}}</span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-12 input input--isao">
                                    <select id="video_standard" name="standard" ng-model="editedScreen.standard" class="input__field input__field--isao" required>
                                        <option value="PAL">PAL</option>
                                        <option value="NTSC">NTSC</option>
                                    </select>
                                    <label class="input__label input__label--isao" for="video_standard" data-content="{{trans('film.placeholder.standard')}}">
                                        <span class="input__label-content input__label-content--isao">{{trans('film.placeholder.standard')}}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-sm-12 input input--isao">
                                    <select name="video_ratio" ng-model="editedScreen.ratio" class="input__field input__field--isao" required>
                                        <option value="4:3">4:3</option>
                                        <option value="16:9">16:9</option>
                                        <option value="2.35:1">2.35:1</option>
                                    </select>
                                    <label class="input__label input__label--isao" for="title_original" data-content="{{trans('film.placeholder.ratio')}}">
                                        <span class="input__label-content input__label-content--isao"><i class="text-danger pr-1">*</i>{{trans('film.placeholder.ratio')}}</span>
                                    </label>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12 input input--isao">
                                    <select id="sound_name" name="video_sound" ng-model="editedScreen.sound_id" class="input__field input__field--isao" required>
                                        @foreach($sounds->where('digital', '<>', '0') as $sound)
                                            <option value="{{$sound->id}}"  ng-selected="editedScreen.sound_id == {{$sound->id}}">{{$sound->label}}</option>
                                        @endforeach
                                    </select>
                                    <label class="input__label input__label--isao" for="title_original" data-content="{{trans('film.placeholder.sound')}}">
                                        <span class="input__label-content input__label-content--isao"><i class="text-danger pr-1">*</i>{{trans('film.placeholder.sound')}}</span>
                                    </label>
                                </div>
                                <div class="col-lg-4"></div>
                            </div>
                        </div>
                        <div ng-if="editedType == 'cine'">
                            <div class="row">
                                <div class="col-lg-4 col-md-12 input input--isao" >
                                    <select id="format_label" name="format_cine_id" ng-model="editedScreen.format_cine_id" class="input__field input__field--isao" required>
                                        @foreach($cformats as $format)
                                            <option value="{{$format->id}}" ng-selected="editedScreen.format_cine_id == '{{$format->id}}'">{{$format->label}}</option>
                                        @endforeach
                                    </select>
                                    <label class="input__label input__label--isao" for="format_label" data-content="{{trans('film.placeholder.cineFormat')}}">
                                        <span class="input__label-content input__label-content--isao"><i class="text-danger pr-1">*</i>{{trans('film.placeholder.cineFormat')}}</span>
                                    </label>
                                </div>
                                <div class="col-lg-5 col-md-12 input input--isao">
                                    <select id="cine_ratio" name="ratio" ng-model="editedScreen.ratio" class="input__field input__field--isao" required>
                                        <option value="1.37:1">1.37:1</option>
                                        <option value="1.66:1">1.66:1</option>
                                        <option value="1.85:1">1.85:1</option>
                                        <option value="2.35:1">2.35:1</option>
                                    </select>
                                    <label class="input__label input__label--isao" for="cine_ratio" data-content="{{trans('film.placeholder.ratio')}}">
                                        <span class="input__label-content input__label-content--isao"><i class="text-danger pr-1">*</i>{{trans('film.placeholder.ratio')}}</span>
                                    </label>
                                </div>
                                <div class="col-lg-3 col-md-12 input input--isao">
                                    <select id="cine_speed" name="speed" ng-model="editedScreen.speed" class="input__field input__field--isao" required>
                                        <option value="24" ng-selected="editedScreen.speed == 24">24</option>
                                        <option value="25" ng-selected="editedScreen.speed == 25">25</option>
                                    </select>
                                    <label class="input__label input__label--isao" for="cine_speed" data-content="FPS">
                                        <span class="input__label-content input__label-content--isao"><i class="text-danger pr-1">*</i>FPS</span>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-12 input input--isao">
                                    <select id="sound_name" name="sound"  ng-model="editedScreen.sound_id" class="input__field input__field--isao" required>
                                        @foreach($sounds->where('digital', '<>', '1') as $sound)
                                            <option value="{{$sound->id}}" ng-selected="editedScreen.sound_id == {{$sound->id}}">{{$sound->label}}</option>
                                        @endforeach
                                    </select>
                                    <label class="input__label input__label--isao" for="cine_sound" data-content="{{trans('film.placeholder.sound')}}">
                                        <span class="input__label-content input__label-content--isao"><i class="text-danger pr-1">*</i>{{trans('film.placeholder.sound')}}</span>
                                    </label>
                                </div>
                                <div class="col-lg-6 col-md-12 d-flex justify-content-center">
                                    <div class=" input input--isao" style="width:100px">
                                        <input type="number" id="reel_count" name="reel_length" class="input__field input__field--isao" ng-model="editedScreen.reel_length">
                                        <label class="input__label input__label--isao" for="reel_length" data-content="{{trans('film.header.reel_length')}}">
                                            <span class="input__label-content input__label-content--isao">{{trans('film.header.reel_length')}}</span>
                                        </label>
                                    </div>
                                    <div class="fa fa-times px-2"></div>
                                    <div class=" input input--isao" style="width:100px">
                                        <input type="number" id="reel_count" name="reel_count" class="input__field input__field--isao" ng-model="editedScreen.reel_count">
                                        <label class="input__label input__label--isao" for="reel_count" data-content="{{trans('film.header.reel_nbr')}}">
                                            <span class="input__label-content input__label-content--isao">{{trans('film.header.reel_nbr')}}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div ng-if="editedType == 'digital'">
                            <div class="row">
                                <div class="col-lg-4 col-md-12 input input--isao" >
                                    <select id="format_label" name="format_digital_id" ng-model="editedScreen.format_digital_id" class="input__field input__field--isao" required>
                                        @foreach($pformats as $format)
                                            <option id="#option_format_paly_{$format->id}}" value="{{$format->id}}">{{$format->label}}</option>
                                        @endforeach
                                    </select>
                                    <label class="input__label input__label--isao" for="format_label" data-content="{{trans('film.placeholder.playFormat')}}">
                                        <span class="input__label-content input__label-content--isao"><i class="text-danger pr-1">*</i>{{trans('film.placeholder.playFormat')}}</span>
                                    </label>
                                </div>
                                <div class="col-lg-5 col-md-12 input input--isao">
                                    <select id="cine_ratio" name="ratio" ng-model="editedScreen.ratio" class="input__field input__field--isao" required>
                                        <option value="4:3">4:3</option>
                                        <option value="16:9">16:9</option>
                                        <option value="1.66:1">1.66:1</option>
                                        <option value="1.85:1">1.85:1</option>
                                        <option value="2.35:1">2.35:1</option>
                                        <option value="VR">VR</option>
                                    </select>
                                    <label class="input__label input__label--isao" for="cine_ratio" data-content="{{trans('film.placeholder.ratio')}}">
                                        <span class="input__label-content input__label-content--isao"><i class="text-danger pr-1">*</i>{{trans('film.placeholder.ratio')}}</span>
                                    </label>
                                </div>
                                <div class="col-lg-3 col-md-12 input input--isao">
                                    <input id="digital_size" type="text" ng-model="editedScreen.size"  name="size" class="input__field input__field--isao" ng-pattern="regex" />
                                    <label class="input__label input__label--isao" for="digital_size" data-content="{{trans('film.placeholder.size')}}">
                                        <span class="input__label-content input__label-content--isao">{{trans('film.placeholder.size_example')}}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="pt-3 d-flex">
                                <div class="input input--isao" style="width: 100px">
                                    <input id="resolution_x" type="number" ng-model="editedScreen.resolution_x" name="resolution_x" class="input__field input__field--isao">
                                    <label class="input__label input__label--isao" for="resolution_x" data-content="{{trans('film.placeholder.resolution_x')}}">
                                        <span class="input__label-content input__label-content--isao">{{trans('film.placeholder.resolution_x')}}</span>
                                    </label>
                                </div>
                                <div class="fa fa-times px-2"></div>
                                <div class="input input--isao" style="width: 100px">
                                    <input id="resolution_y" type="number" ng-model="editedScreen.resolution_y" name="resolution_y" class="input__field input__field--isao">
                                    <label class="input__label input__label--isao" for="resolution_y" data-content="{{trans('film.placeholder.resolution_y')}}">
                                        <span class="input__label-content input__label-content--isao">{{trans('film.placeholder.resolution_y')}}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <h6 class="mt-5">{{trans('film.header.subtitle')}}</h6>
                        <div class="form-group row pt-2">
                            <label class="col-6 text-right px-3">{{trans('film.label.english')}}</label>
                            <div class="col-6">
                                <span class="checkbox-inline">
                                <input type="checkbox" ng-click="editedScreen.english_subbed = !editedScreen.english_subbed;" ng-checked="editedScreen.english_subbed" />
                                    {{trans('film.label.subbed')}}
                                 </span>
                                <span class="checkbox-inline px-5">
                                <input type="checkbox" ng-click="editedScreen.english_dubbed = !editedScreen.english_dubbed;" ng-checked="editedScreen.english_dubbed" />
                                    {{trans('film.label.dubbed')}}
                                </span>
                            </div>
                        </div>
                        <div class="form-group row"  ng-repeat="s in editedScreen.subtitles" ng-class="{'text-muted':!s.subbed && !s.dubbed}">
                            <label class="col-6 text-right px-3" ng-bind="s.name"></label>
                            <div class="col-6" >
                                 <span class="checkbox-inline">
                                    <input type="checkbox" name="dubbed" ng-click="s.subbed = !s.subbed;" ng-checked="s.subbed"/>
                                     {{trans('film.label.subbed')}}
                                </span>
                                <span class="checkbox-inline px-5">
                                    <input type="checkbox" name="subbed" ng-click="s.dubbed = !s.dubbed;" ng-checked="s.dubbed"/>
                                    {{trans('film.label.dubbed')}}
                                </span>
                                <span class="btn text-danger fa fa-trash" ng-hide="!s.subbed && !s.dubbed" ng-click="removeLang(s.id)"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-xs-12 input input--isao">
                                <select name="subtitle" id="play_subtitle" class="input__field input__field--isao"  ng-model="editedScreen.newlang.language_id">
                                    @foreach($languages as $language)
                                        <option id="newLang_opt_{{$language->id}}" value="{{$language->id}}">{{$language->name}}</option>
                                    @endforeach
                                </select>
                                <label class="input__label input__label--isao" for="title_original" data-content="{{trans('film.placeholder.language')}}">
                                    <span class="input__label-content input__label-content--isao  text-right">{{trans('film.placeholder.language')}}</span>
                                </label>
                            </div>
                            <div class="col-md-6 col-xs-12" ng-class="{'text-muted':!editedScreen.newlang.language_id}">
                                <span class="checkbox-inline">
                                    <input type="checkbox" ng-model="editedScreen.newlang.subbed" ng-disabled="!editedScreen.newlang.language_id" />
                                    {{trans('film.label.subbed')}}
                                </span>
                                <span class="checkbox-inline px-5">
                                    <input type="checkbox" ng-model="editedScreen.newlang.dubbed" ng-disabled="!editedScreen.newlang.language_id" />
                                    {{trans('film.label.dubbed')}}
                                </span>
                                <span class="btn text-success fa fa-check" ng-click="addLang()"
                                      ng-class="{'text-muted':!editedScreen.newlang.language_id || (!editedScreen.newlang.subbed && !editedScreen.newlang.dubbed)}"></span>
                            </div>
                        </div>
                        <div class="alert alert-danger" ng-show="errors">{{trans("validation.construct")}}</div>
                    </div>
                    <div class="modal-footer d-flex px-5">
                        <button class="btn btn-danger mr-auto" type="button" ng-click="cancelEdit()" >
                            {{trans("project.BUTTONS.cancel")}}
                        </button>
                        <button class="btn btn-primary" type="button" ng-click="post('{{$film->id}}', editForm.$invalid)" ng-disabled="editForm.$invalid">
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <ul class="alert alert-dark text-primary small px-5" role="alert">
            <li>{!! trans('film.alert.screen1') !!}</li>
            <li class="py-1">{!! trans('film.alert.screen2') !!}</li>
            <li>{!! trans('film.alert.screen3') !!}</li>
            <li class="pt-1">{!! trans('layout.ALERTS.compulsive') !!}</li>
        </ul>
        <div class="form-group row text-primary pt-5">
            <label class="col-lg-2 col-md-4 col-xs-12 label-justified required">
                {!! trans('film.label.process') !!}
            </label>
            <div  class="col-lg-10 col-md-8 col-sm-12 row pl-5">
                <div class="radio-inline col-4">
                    <input type="radio"  name="color" value="0" {{$film->color === 0 ? 'checked':''}}>
                    {{trans('film.label.wb')}}
                </div>
                <div class="radio-inline col-4">
                    <input type="radio"  name="color" value="1" {{$film->color == 1 ? 'checked':''}}>
                    {{trans('film.label.color')}}
                </div>
                <div class="radio-inline col-4">
                    <input type="radio"  name="color" value="2" {{$film->color == 2 ? 'checked':''}}>
                    {{trans('film.label.cwb')}}
                </div>
            </div>
        </div>
        <div class="form-group row text-primary">
            <label class="col-lg-2 col-md-4 col-xs-12 label-justified required">
                {!! trans('film.label.special') !!}
            </label>
            <div  class="col-lg-10 col-md-8 col-sm-12 row pl-5">
                <div class="radio-inline col-4">
                    <input type="radio"  name="special" value="2D" {{$film->special == '2D' ? 'checked':''}}>
                    2D
                </div>
                <div class="radio-inline col-4">
                    <input type="radio"  name="special" value="3D" {{$film->special == '3D' ? 'checked':''}}>
                    3D
                </div>
                <div class="radio-inline col-4">
                    <input type="radio"  name="special" value="VR" {{$film->special == 'VR' ? 'checked':''}}>
                    VR
                </div>
            </div>
        </div>
        <h5 class="pt-5">{{trans('film.label.digital_files')}}</h5>
        <table class="table table-striped table-hover text-primary">
            <thead class="thead-light">
                <tr>
                    <th>{{trans('film.label.format')}}</th>
                    <th>{{trans('film.label.ratio')}}</th>
                    <th>{{trans('film.label.resolution')}}</th>
                    <th>{{trans('film.label.size')}}(G)</th>
                    <th>{{trans('film.header.subtitle')}}</th>
                    <th width="20px">
                        <div ng-click="edit('digital', {subtitles:[]})" class="btn text-important fa fa-plus"></div>
                    </th>
                </tr>
            </thead>
            <tbody id="form_play" >
                <tr ng-repeat="screen in screen['digital']">
                    <td><span ng-bind="screen.label"></span></td>
                    <td><span ng-bind="screen.ratio"></span></td>
                    <td>
                        <span ng-bind="screen.resolution_x"></span> <span ng-if="screenToDelete.resolution_x && screenToDelete.resolution_y" class="fa fa-times"></span> <span ng-bind="screen.resolution_y"></span>
                    </td>
                    <td><span ng-bind="screen.size"></span></td>
                    <td>
                        <div class="inline" ng-if="screen.english_dubbed || screen.english_subbed">
                            <label>{{trans('film.label.english')}}: </label>
                            <span ng-if="screen.english_subbed" class="badge badge-pill">
                                 {{trans('film.label.subbed')}}
                            </span>
                            <span ng-if="screen.english_dubbed" class="badge badge-pill">
                                {{trans('film.label.dubbed')}}
                            </span>
                        </div>
                        <div class="inline" ng-repeat="lang in screen.subtitles">
                            <label><span ng-bind="lang.name"></span>: </label>
                            <span ng-if="lang.subbed" class="badge badge-pill">
                                 {{trans('film.label.subbed')}}
                            </span>
                            <span ng-if="lang.dubbed" class="badge badge-pill">
                                {{trans('film.label.dubbed')}}
                            </span>
                        </div>
                    </td>
                    <td class="btn-group">
                        <!--
                        <div class="btn text-primary fa fa-edit" ng-click="edit('digital',screen)"></div>
                        -->
                        <div class="btn text-danger fa fa-trash" ng-click="delete('digital',screen)"></div>
                    </td>
                </tr>
            </tbody>
        </table>
        <h5 class="pt-5">{{trans('film.label.film_print')}}</h5>
        <table class="table table-striped table-hover text-primary">
            <thead class="thead-light">
                <tr>
                    <th>{{trans('film.label.format')}}</th>
                    <th>{{trans('film.label.fps')}}</th>
                    <th>{{trans('film.label.ratio')}}</th>
                    <th>{{trans('film.label.sound')}}</th>
                    <th>{{trans('film.label.reel')}}</th>
                    <th>{{trans('film.label.subbed')}}</th>
                    <th width="20px">
                        <div ng-click="edit('cine', {subtitles:[]})" class="btn text-important fa fa-plus"></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="screen in screen['cine']">
                    <td ng-bind="screen.label"></td>
                    <td ng-bind="screen.speed"></td>
                    <td ng-bind="screen.ratio"></td>
                    <td ng-bind="screen.sound"></td>
                    <td>
                        <span ng-bind="screen.reel_length"></span> x <span ng-bind="screen.reel_count"></span>
                    </td>
                    <td>
                        <div class="inline" ng-if="screen.english_dubbed || screen.english_subbed">
                            <label>{{trans('film.label.english')}}: </label>
                            <span ng-if="screen.english_subbed" class="badge badge-pill">
                                 {{trans('film.label.subbed')}}
                            </span>
                            <span ng-if="screen.english_dubbed" class="badge badge-pill">
                                {{trans('film.label.dubbed')}}
                            </span>
                        </div>
                        <div class="inline" ng-repeat="lang in screen.subtitles">
                            <label><span ng-bind="lang.name"></span>: </label>
                            <span ng-if="lang.subbed" class="badge badge-pill">
                                 {{trans('film.label.subbed')}}
                            </span>
                            <span ng-if="lang.dubbed" class="badge badge-pill">
                                {{trans('film.label.dubbed')}}
                            </span>
                        </div>
                    </td>
                    <td class="btn-group">
                        <!--
                        <div class="btn text-primary fa fa-edit" ng-click="edit('cine', screen)"></div>
                        -->
                        <div class="btn text-danger fa fa-trash" ng-click="delete('cine', screen)"></div>
                    </td>
                </tr>
            </tbody>
        </table>
        <h5 class="pt-5">{{trans('film.label.video_copy')}}</h5>
        <table class="table table-striped">
            <thead class="thead-light">
                <tr>
                    <th>{{trans('film.label.format')}}</th>
                    <th>{{trans('film.label.standard')}}</th>
                    <th>{{trans('film.label.ratio')}}</th>
                    <th>{{trans('film.label.sound')}}</th>
                    <th>{{trans('film.label.subbed')}}</th>
                    <th width="40px">
                        <div ng-click="edit('video', {subtitles:[]})" ng-if="!screen.video.edit" class="btn text-important fa fa-plus"></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="screen in screen['video']">
                    <td><span ng-bind="screen.label"></span></td>
                    <td><span ng-bind="screen.standard"></span></td>
                    <td><span ng-bind="screen.ratio"></span></td>
                    <td><span ng-bind="screen.sound"></span></td>
                    <td>
                        <div class="inline" ng-if="screen.english_dubbed || screen.english_subbed">
                            <label>{{trans('film.label.english')}}: </label>
                            <span ng-if="screen.english_subbed" class="badge badge-pill">
                                 {{trans('film.label.subbed')}}
                            </span>
                            <span ng-if="screen.english_dubbed" class="badge badge-pill">
                                {{trans('film.label.dubbed')}}
                            </span>
                        </div>
                        <div class="inline" ng-repeat="lang in screen.subtitles">
                            <label><span ng-bind="lang.name"></span>: </label>
                            <span ng-if="lang.subbed" class="badge badge-pill">
                                 {{trans('film.label.subbed')}}
                            </span>
                            <span ng-if="lang.dubbed" class="badge badge-pill">
                                {{trans('film.label.dubbed')}}
                            </span>
                        </div>
                    </td>
                    <td class="btn-group">
                        <!--
                        <span class="btn text-primary fa fa-edit" ng-click="edit('video', screen)"></span>
                        -->
                        <span class="btn text-danger fa fa-trash" ng-click="delete('video', screen)"></span>
                    </td>
                </tr>
            </tbody>
        </table>

        <hr/>
        <div class="d-flex justify-content-between">
            <div class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteFilmModal">{{trans('film.buttons.delete')}}</div>
            <button class="btn btn-primary" type="submit">{{trans('layout.BUTTONS.continue')}}</button>
        </div>
    </form>
@endsection
@section('script')
    <script src="/js/controllers/film/screen.js"></script>
@endsection