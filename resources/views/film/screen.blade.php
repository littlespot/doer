@extends('film.card')

@section('filmForm')
    <form id="time_form" name="screenForm" action="/film/screen" method="post" ng-controller="filmCtrl" ng-init="init('{{$film->id}}','{{$pscreens}}', '{{$cscreens}}', '{{$vscreens}}')">
        @include('film.form')
    <h4 class="header-slogan">{{trans('film.card.screen')}}</h4>
        <div class="alert alert-info" role="alert">
        <ul>
            <li>{!! trans('film.alert.screen1') !!}</li>
            <li>{!! trans('film.alert.screen2') !!}</li>
            <li>{!! trans('film.alert.screen3') !!}</li>
        </ul>
        <div>{!! trans('layout.ALERTS.compulsive') !!}</div>
    </div>
    <div class="form">
        <div class="form-group row">
            <label class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">
                {!! trans('film.label.process') !!}
            </label>
            <div  class="col-md-8 col-sm-8 col-xs-8">
                <div class="col-sm-4 col-xs-6">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input type="radio"  name="color" value="1" {{$film->color == 1 ? 'checked':''}}>
                        </span>
                        {{trans('film.label.color')}}
                    </div>
                </div>
                <div class="col-sm-4 col-xs-6">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input type="radio"  name="color" value="0" {{!is_null($film->color) && $film->color == 0 ? 'checked':''}}>
                        </span>
                        {{trans('film.label.wb')}}
                    </div>
                </div>
                <div class="col-sm-4 col-xs-6">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input type="radio"  name="color" value="2" {{$film->color == 2 ? 'checked':''}}>
                        </span>
                        {{trans('film.label.cwb')}}
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
        <div class="form-group row">
            <label class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">
                {!! trans('film.label.special') !!}
            </label>
            <div  class="col-md-8 col-sm-8 col-xs-8">
                <div class="col-sm-4 col-xs-6">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input type="radio"  name="special" value="2D" {{$film->special == '2D' ? 'checked':''}}>
                        </span>
                        2D
                    </div>
                </div>
                <div class="col-sm-4 col-xs-6">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input type="radio"  name="special" value="3D" {{$film->special == '3D' ? 'checked':''}}>
                        </span>
                        3D
                    </div>
                </div>
                <div class="col-sm-4 col-xs-6">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input type="radio"  name="special" value="VR" {{$film->special == 'VR' ? 'checked':''}}>
                        </span>
                        VR
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
    </div>
    <hr/>
    <h6 class="header-slogan">{{trans('film.label.digital_files')}}<sup>*</sup></h6>
    <table class="table table-striped table-responsive">
        <thead>
            <tr>
                <th>{{trans('film.label.format')}}<sup>*</sup></th>
                <th>{{trans('film.label.ratio')}}<sup>*</sup></th>
                <th>{{trans('film.label.resolution')}}<sup>*</sup></th>
                <th>{{trans('film.label.size')}}(G)</th>
                <th>{{trans('film.header.subtitle')}}</th>
                <th width="20px"><div ng-click="editor('digital')"  ng-if="!screen.digital.edit" class="btn text-important fa fa-plus"></div></th>
            </tr>
        </thead>
        <tbody id="form_play" >
            <tr ng-repeat="screen in screen['digital']">
                <td><span ng-bind="screen.label"></span></td>
                <td><span ng-bind="screen.ratio"></span></td>
                <td>
                    <span ng-bind="screen.resolution_x"></span> x <span ng-bind="screen.resolution_y"></span>
                </td>
                <td><span ng-bind="screen.size"></span></td>
                <td>
                    <div class="inline" ng-if="screen.english_dubbed">
                        <label>{{trans('film.label.english')}}</label>
                        <span ng-switch="screen.english_dubbed">
                            <span ng-switch-when="1">
                                {{trans('film.label.subtitle')}}
                            </span>
                            <span ng-switch-when="3">
                                {{trans('film.label.subbed')}}
                            </span>
                            <span ng-switch-default>
                                 {{trans('film.label.dubbed')}}
                            </span>
                        </span>
                    </div>
                    <div class="inline" ng-if="!screen.language && screen.length > 0">
                        <label><span ng-bind="screen.language"></span> :</label>
                        <span ng-switch="screen.dubbed">
                            <span ng-switch-when="1">
                                {{trans('film.label.subtitle')}}
                            </span>
                            <span ng-switch-when="3">
                                {{trans('film.label.subbed')}}
                            </span>
                            <span ng-switch-default>
                                 {{trans('film.label.dubbed')}}
                            </span>
                        </span>
                    </div>
                </td>
                <td>
                    <div class="btn text-success fa fa-trash" ng-click="delete('digital',screen.id)"></div>
                </td>
            </tr>
            <tr ng-if="screen.digital.edit">
                <td>
                    <select id="format_digital_id" name="format_digital" ng-model="data.digital.format_digital_id">
                        <option value="" disabled>{{trans('film.placeholder.playFormat')}}</option>
                        @foreach($pformats as $format)
                            <option id="#option_format_paly_{$format->id}}" value="{{$format->id}}">{{$format->decode}}{{$format->annotation}}</option>
                        @endforeach
                    </select>

                </td>
                <td>
                    <select name="ratio" ng-model="data.digital.ratio">
                        <option value="" disabled>{{trans('film.placeholder.ratio')}}</option>
                        <option value="4:3">4:3</option>
                        <option value="16:9">16:9</option>
                        <option value="1.66:1">1.66:1</option>
                        <option value="1.85:1">1.85:1</option>
                        <option value="2.35:1">2.35:1</option>
                        <option value="VR">VR</option>
                    </select>
                </td>
                <td>
                    <input type="number" ng-model="data.digital.resolution_x" name="resolution_x" style="width: 60px"> x <input type="number" ng-model="data.digital.resolution_y" name="resolution_y" style="width: 60px">
                </td>
                <td>
                    <input type="text" ng-model="data.digital.size"  name="size" class="form-text" ng-pattern="regex" placeholder="{{trans('film.placeholder.size')}}">
                    <div role="alert" class="error" ng-class="{'visible':screenForm.size.$touched && screenForm.size.$error.pattern}">
                        {{trans('film.placeholder.size')}}
                    </div>
                </td>
                <td>
                    <div class="inline margin-bottom-sm">
                        <div style="width: 150px;padding-left:12px;">
                            {{trans('layout.LABELS.en')}}
                        </div>
                        <div>
                            <input type="checkbox" ng-model="data.digital.english_dubbed[0]" name="english_dubbed" value="1"> {{trans('film.label.subtitle')}}
                            <input type="checkbox" ng-model="data.digital.english_dubbed[1]" name="english_dubbed" value="2"> {{trans('film.label.dubbed')}}
                        </div>
                    </div>
                    <div class="inline">
                        <div style="width: 150px">
                           <select name="subtitle" id="play_subtitle" class="form-control"  ng-model="data.digital.subtitle">
                               <option value="" disabled>{{trans('film.placeholder.language')}}</option>
                                @foreach($languages as $language)
                                   <option value="{{$language->id}}">{{$language->name}}</option>
                               @endforeach
                           </select>
                        </div>
                        <div>
                            <input type="checkbox" ng-model="data.digital.dubbed[0]" name="dubbed" value="1"> {{trans('film.label.subtitle')}}
                            <input type="checkbox" ng-model="data.digital.dubbed[1]" name="dubbed" value="2"> {{trans('film.label.dubbed')}}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="btn text-success fa fa-save" ng-click="post('digital', screenForm.$invalid)"></div>
                    <div class="btn text-muted fa fa-undo" ng-click="cancel('digital')"></div>
                </td>
            </tr>
        </tbody>
    </table>
    <hr/>
    <h6 class="header-slogan">{{trans('film.label.film_print')}}</h6>
    <table class="table table-striped table-responsive">
        <thead>
        <tr>
            <th>{{trans('film.label.format')}}<sup>*</sup></th>
            <th>{{trans('film.label.fps')}}<sup>*</sup></th>
            <th>{{trans('film.label.ratio')}}<sup>*</sup></th>
            <th>{{trans('film.label.sound')}}<sup>*</sup></th>
            <th>{{trans('film.label.reel')}}</th>
            <th>{{trans('film.label.subtitle')}}</th>
            <th width="20px"><div ng-click="editor( 'cine')" ng-if="!screen.cine.edit" class="btn text-important fa fa-plus"></div> </th>
        </tr>
        </thead>
        <tbody>
            <tr ng-repeat="screen in screen['cine']">
                <td ng-bind="screen.label">
                </td>
                <td ng-bind="screen.speed">
                </td>
                <td ng-bind="screen.ratio">
                </td>
                <td ng-bind="screen.sound">
                </td>
                <td>
                    <span ng-bind="screen.reel_length"></span> x <span ng-bind="screen.reel_count"></span>
                </td>
                <td>
                    <div class="inline" ng-if="screen.english_dubbed">
                        <label>{{trans('film.label.english')}}</label>
                        <span ng-switch="screen.english_dubbed">
                            <span ng-switch-when="1">
                                {{trans('film.label.subtitle')}}
                            </span>
                            <span ng-switch-when="3">
                                {{trans('film.label.subbed')}}
                            </span>
                            <span ng-switch-default>
                                 {{trans('film.label.dubbed')}}
                            </span>
                        </span>
                    </div>
                    <div class="inline" ng-if="!screen.language && screen.length > 0">
                        <label><span ng-bind="screen.language"></span> :</label>
                        <span ng-switch="screen.dubbed">
                            <span ng-switch-when="1">
                                {{trans('film.label.subtitle')}}
                            </span>
                            <span ng-switch-when="3">
                                {{trans('film.label.subbed')}}
                            </span>
                            <span ng-switch-default>
                                 {{trans('film.label.dubbed')}}
                            </span>
                        </span>
                    </div>
                </td>
                <td>
                    <div class="btn text-success fa fa-trash" ng-click="delete('cine', screen.id)"></div>
                </td>
            </tr>
            <tr ng-if="screen.cine.edit">
                <td>
                    <select name="format_cine" id="format_cine_id" ng-model="data.cine.format_cine_id">
                        <option value="" disabled>{{trans('film.placeholder.cineFormat')}}</option>
                        @foreach($cformats as $format)
                            <option value="{{$format->id}}">{{$format->label}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="speed" ng-model="data.cine.speed">
                        <option value="" disabled>FPS</option>
                        <option value="24">24</option>
                        <option value="25">25</option>
                    </select>
                </td>
                <td>
                    <select name="ratio" ng-model="data.cine.ratio">
                        <option value="" disabled>{{trans('film.placeholder.ratio')}}</option>
                        <option value="1.37:1">1.37:1</option>
                        <option value="1.66:1">1.66:1</option>
                        <option value="1.85:1">1.85:1</option>
                        <option value="2.35:1">2.35:1</option>
                    </select>
                </td>
                <td>
                    <select name="sound"  ng-model="data.cine.sound_id" id="cine_sound">
                        <option value="" disabled>{{trans('film.placeholder.sound')}}</option>
                        @foreach($sounds->where('digital', '<>', '1') as $sound)
                            <option value="{{$sound->id}}">{{$sound->label}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">
                            {{trans('film.header.reel_nbr')}}
                        </span>
                        <input type="number" name="reel_count" class="form-text" style="width: 40px" ng-model="data.cine.reel_count">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            {{trans('film.header.reel_length')}}
                        </span>
                        <input type="number" name="reel_length" class="form-text" style="width: 40px" ng-model="data.cine.reel_length">
                    </div>
                </td>
                <td>
                    <div class="inline margin-bottom-sm">
                        <div style="width: 150px;padding-left:12px;">
                            {{trans('layout.LABELS.en')}}
                        </div>
                        <div>
                            <input type="checkbox" ng-model="data.cine.english_dubbed[0]" name="english_dubbed" value="1"> {{trans('film.label.subtitle')}}
                            <input type="checkbox" ng-model="data.cine.english_dubbed[1]" name="english_dubbed" value="2"> {{trans('film.label.dubbed')}}
                        </div>
                    </div>
                    <div class="inline">
                        <div style="width: 150px">
                            <select name="subtitle" id="cine_subtitle" class="form-control"  ng-model="data.cine.subtitle">
                                <option value="">{{trans('film.placeholder.language')}}</option>
                                @foreach($languages as $language)
                                    <option value="{{$language->id}}">{{$language->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <input type="checkbox" ng-model="data.cine.dubbed[0]" name="dubbed" value="1"> {{trans('film.label.subtitle')}}
                            <input type="checkbox" ng-model="data.cine.dubbed[1]" name="dubbed" value="2"> {{trans('film.label.dubbed')}}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="btn text-success fa fa-save" ng-click="post('cine');"></div>
                    <div class="btn text-muted fa fa-undo" ng-click="cancel('cine');"></div>
                </td>
            </tr>
        </tbody>
    </table>
    <hr/>
    <h6 class="header-slogan">{{trans('film.label.video_copy')}}</h6>
    <table class="table table-striped table-responsive">
        <thead>
        <tr>
            <th>{{trans('film.label.format')}}<sup>*</sup></th>
            <th>{{trans('film.label.standard')}}<sup>*</sup></th>
            <th>{{trans('film.label.ratio')}}<sup>*</sup></th>
            <th>{{trans('film.label.sound')}}<sup>*</sup></th>
            <th>{{trans('film.label.subtitle')}}</th>
            <th width="20px"><div ng-click="editor('video');" ng-if="!screen.video.edit" class="btn text-important fa fa-plus"></div> </th>
        </tr>
        </thead>
        <tbody>
            <tr ng-repeat="screen in screen['video']">
                <td><span ng-bind="screen.label"></span></td>
                <td><span ng-bind="screen.standard"></span></td>
                <td><span ng-bind="screen.ratio"></span></td>
                <td><span ng-bind="screen.sound"></span></td>
                <td>
                    <div class="inline" ng-if="screen.english_dubbed">
                        <label>{{trans('film.label.english')}}</label>
                        <span ng-switch="screen.english_dubbed">
                                <span ng-switch-when="1">
                                    {{trans('film.label.subtitle')}}
                                </span>
                                <span ng-switch-when="3">
                                    {{trans('film.label.subbed')}}
                                </span>
                                <span ng-switch-default>
                                     {{trans('film.label.dubbed')}}
                                </span>
                            </span>
                    </div>
                    <div class="inline" ng-if="!screen.language && screen.length > 0">
                        <label><span ng-bind="screen.language"></span> :</label>
                        <span ng-switch="screen.dubbed">
                                <span ng-switch-when="1">
                                    {{trans('film.label.subtitle')}}
                                </span>
                                <span ng-switch-when="3">
                                    {{trans('film.label.subbed')}}
                                </span>
                                <span ng-switch-default>
                                     {{trans('film.label.dubbed')}}
                                </span>
                            </span>
                    </div>
                </td>
                <td>
                    <div class="btn text-success fa fa-trash" ng-click="delete('video', screen.id)"></div>
                </td>
            </tr>
            <tr ng-if="screen.video.edit">
                <td>
                    <select id="format_video_id" name="format_video" ng-model="data.video.format_video_id">
                        <option value="" disabled>{{trans('film.placeholder.playFormat')}}</option>
                        @foreach($vformats as $format)
                            <option value="{{$format->id}}">{{$format->label}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="standard" ng-model="data.video.standard">
                        <option value="" disabled>{{trans('film.placeholder.standard')}}</option>
                        <option value="PAL">PAL</option>
                        <option value="NTSC">NTSC</option>
                    </select>
                </td>
                <td>
                    <select name="video_ratio" ng-model="data.video.ratio">
                        <option value="" disabled>{{trans('film.placeholder.ratio')}}</option>
                        <option value="4:3">4:3</option>
                        <option value="16:9">16:9</option>
                        <option value="2.35:1">2.35:1</option>
                    </select>
                </td>
                <td>
                    <select name="video_sound" ng-model="data.video.sound_id" id="video_sound">
                        <option value="" disabled>{{trans('film.placeholder.sound')}}</option>
                        @foreach($sounds->where('digital', '<>', '0') as $sound)
                            <option value="{{$sound->id}}">{{$sound->label}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <div class="inline margin-bottom-sm">
                        <div style="width: 150px;padding-left:12px;">
                            {{trans('layout.LABELS.en')}}
                        </div>
                        <div>
                            <input type="checkbox" ng-model="data.video.english_dubbed[0]" name="english_dubbed" value="1"> {{trans('film.label.subtitle')}}
                            <input type="checkbox" ng-model="data.video.english_dubbed[1]" name="english_dubbed" value="2"> {{trans('film.label.dubbed')}}
                        </div>
                    </div>
                    <div class="inline">
                        <div style="width: 150px">
                            <select name="subtitle" id="video_subtitle" class="form-control"  ng-model="data.video.subtitle">
                                <option value="" disabled>{{trans('film.placeholder.language')}}</option>
                                @foreach($languages as $language)
                                    <option value="{{$language->id}}">{{$language->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <input type="checkbox" ng-model="data.video.dubbed[0]" name="dubbed" value="1"> {{trans('film.label.subtitle')}}
                            <input type="checkbox" ng-model="data.video.dubbed[1]" name="dubbed" value="2"> {{trans('film.label.dubbed')}}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="btn text-success fa fa-save" ng-click="post('video');"></div>
                    <div class="btn text-muted fa fa-undo" ng-click="cancel('video')"></div>
                </td>
            </tr>
        </tbody>
    </table>

    <hr/>
    <div class="text-right margin-bottom-md">
        <button class="btn btn-primary" type="submit">{{trans('layout.BUTTONS.continue')}}</button>
    </div>
    </form>
@endsection
@section('script')
    <script src="/js/controllers/film/screen.js"></script>
@endsection