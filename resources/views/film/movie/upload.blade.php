@extends('layouts.film')

@section('filmForm')
    <link href="/bower_components/bootstrap/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
    <script src="/bower_components/bootstrap/plugins/piexif.min.js" type="text/javascript"></script>
    <script src="/bower_components/bootstrap/plugins/purify.min.js" type="text/javascript"></script>
    <script src="/bower_components/bootstrap/js/fileinput.min.js"></script>
    <script src="/bower_components/bootstrap/js/fileinput.theme.min.js"></script>
    <script src="/bower_components/bootstrap/locale/{{app()->getLocale()}}.js"></script>
<div ng-controller="filmCtrl" ng-init="init('{{$film->id}}')">
    <style>
        .btn-file {
            position: relative;
            overflow: hidden;
        }
        .btn-file input[type=file] {
            position: absolute;
            top: 0;
            right: 0;
            min-width: 100%;
            min-height: 100%;
            font-size: 999px;
            text-align: right;
            filter: alpha(opacity=0);
            opacity: 0;
            background: red;
            cursor: inherit;
            display: block;
        }
        input[readonly] {
            background-color: white !important;
            cursor: text !important;
        }

        .progress span {
            position: absolute;
            display: flex;
            width: 100%;
            justify-content: center;
            align-items: center;
        }
    </style>
    <div class="modal fade" id="stopModal" tabindex="-1" role="dialog" aria-labelledby="stopModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{trans('film.header.stop_upload')}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-5 py-3" id="modal-body">
                    <div>{{trans('film.alert.stop_upload')}}</div>
                </div>
                <div class="modal-footer d-flex px-5">
                    <button class="btn btn-outline-danger mr-auto" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-primary" type="button" ng-click="stopUpload()" data-dismiss="modal">
                        {{trans("project.BUTTONS.confirm")}}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <ul class="alert alert-dark text-primary small px-5" role="alert">
        <li class="py-1">{!! trans('film.alert.complete') !!}</li>
        <li class="text-danger">{!! trans('film.alert.upload') !!}</li>
    </ul>
    <div class="alert alert-info row mx-1" role="alert">
        <div class="col-md-6 col-sm-12">
            <h6>{!! trans('film.label.format_accepted') !!}</h6>
            <div><b>MOV</b>, <b>MP4</b>, <b>M4V</b></div>
            <div style="word-wrap: normal;word-break: keep-all">3GP, ASF, AVI, DIVX, F4V, FLV, MKV, MOD, MPG, VOB, WMV, XVID.</div>
            <br>
            <label>{!! trans('film.label.format_recom') !!}</label>
            <div>.mov, .mp4, .m4v </div>
            <br>
            <label>{!! trans('film.label.size_maxium') !!}</label>
            <div>2 Go</div>
        </div>
        <div class="col-md-6 col-sm-12">
            <h6>{!! trans('film.label.parameter_recom') !!}</h6>
            <label>{{trans('film.label.video')}}</label>
            <ul>
                <li>{{trans('film.label.video_codec')}}H.264</li>
                <li>{{trans('film.label.frame_rate')}}24/25/30FPS</li>
                <li>{{trans('film.label.data_rate')}}5000 kbps</li>
                <li>{{trans('film.label.resolution')}}1920x1080</li>
            </ul>
            <br>
            <label>{{trans('film.label.audio')}}</label>
            <ul>
                <li>{{trans('film.label.audio_codec')}}AAC</li>
                <li>{{trans('film.label.data_rate')}}320 kbps</li>
                <li>{{trans('film.label.sample_rate')}}44.1 kHZ</li>
            </ul>
        </div>
    </div>
    @if(!$size && !$link)
        <div id="copyalert" class="alert alert-danger">{{trans('film.progress.copy_toupload', ['cnt'=>1])}}</div>
    @else
        @if($link)
            <div class="alert alert-success"><a href="{{$link->url}}" target="_blank">{{trans('film.label.copy_link')}}</a></div>
        @endif
        @if($size)
            <div id="copyalert" class="alert alert-success">{{trans('film.progress.copy_uploaded', ['cnt'=>1, 'size'=>$size, 'ext'=>$ext])}}</div>
        @endif
    @endif

    <form id="previewForm">
        {{csrf_field()}}
        <div class="progress">
            <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
            <span class="text-warning"></span>
        </div>
        <div class="input-group">
            <input readonly="readonly" placeholder=" {{trans('layout.BUTTONS.browse')}}" class="form-control" id="filename" type="text">
            <span class="input-group-btn" id="buttons" >
                 <span class="btn btn-success" id='btnStart' style="display: none" ng-click="upload()"><span class="fa fa-upload"></span></span>
                 <span class="btn btn-danger" id='btnStop' style="display: none"  data-toggle="modal" data-target="#stopModal"><span class=" fa fa-stop"></span></span>
            </span>
            <span class="input-group-btn">
                <span class="btn btn-primary btn-file">
                  {{trans('layout.BUTTONS.browse')}}
                  <input accept="video/mp4,video/mov,video/m4v,video/*"  type="file" id="previewFile" onchange="angular.element(this).scope().selectFile()">
                </span>
            </span>
        </div>
    </form>
    <form name="uploadForm" action="/movie/{{$film->id}}/complete" method="POST" ng-init="linkEdited = {{is_null($link)?1:0}}">
        <div class="my-5 px-4 py-3 bg-light">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon3">https://</span>
                </div>
                <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3"
                       placeholder="{{trans('film.label.copy_link')}}"  value="{{$link?$link->url:''}}"
                       ng-disabled="!linkEdited"
                       name="url">
            </div>
            <div class="input input--isao">
                <input class="input__field input__field--isao"  name="name" value="{{$link?$link->name:''}}" ng-disabled="!linkEdited"/>
                <label class="input__label input__label--isao" for="nation_principal" data-content="{{trans('film.label.linkname')}}">
                    <span class="input__label-content input__label-content--isao">{{trans('film.label.linkname')}}</span>
                </label>
            </div>
            <div class="input input--isao">
                <input class="input__field input__field--isao" name="code" value="{{$link?$link->code:''}}" ng-disabled="!linkEdited"/>
                <label
                        class="input__label input__label--isao" for="nation_principal" data-content="{{trans('film.label.linkcode')}}">
                    <span class="input__label-content input__label-content--isao">{{trans('film.label.linkcode')}}</span>
                </label>
            </div>
            @if($link)
            <div class="text-right">
                <div ng-disabled="linkEdited" class="btn btn-primary" ng-click="linkEdited = true">{{trans('layout.BUTTONS.edit')}}</div>
            </div>
                @endif
        </div>
        <div class="row">
            <label class="col-lg-2 col-md-3 col-sm-12 label-justified">
                {{trans('film.header.inlaid_subtitle')}}
            </label>
            <div class="col-lg-3 col-md-3 col-sm-12 checkbox-inline checkbox-primary">
                <input type="checkbox" value="1" name="subtitle_zh" {{$film->subtitle_zh?'checked':''}}>
                <label>{{trans('layout.LABELS.zh')}}</label>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 checkbox-inline checkbox-primary">
                <input type="checkbox" value="1" name="subtitle_en"  {{$film->subtitle_en?'checked':''}}>
                <label>{{trans('layout.LABELS.en')}}</label>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 checkbox-inline checkbox-primary">
                <input type="checkbox" value="1"  ng-model="other_language">
                <label>{{trans('layout.LABELS.others')}}<span ng-if="subtitle_language.id && !subtitle_language.edited">
                    (<i ng-bind="subtitle_language.name"></i><span class="btn fa fa-edit text-info" ng-click="subtitle_language.edited = true;"></span>)
                    <input type="hidden" ng-value="subtitle_language.id" name="subtitle_other"/>
                </span></label>
            </div>
        </div>
        <div class="row pt-1" ng-if="other_language && subtitle_language.edited">
            <div class="col-lg-2 col-md-3 col-sm-12"></div>
            <div class="col-lg-9 col-md-7 col-sm-10 input input--isao">
                <select id="subtitle_other" class="input__field input__field--isao" ng-model="subtitle_language.id">
                    @foreach($languages as $language)
                        <option id="opt_language_{{$language->id}}" value="{{$language->id}}" ng-selected="subtitle_language.id == {{$language->id}}" >{{$language->name}}</option>
                    @endforeach
                </select>
                <label class="input__label input__label--isao" for="nation_principal" data-content="{{trans('film.placeholder.language')}}">
                    <span class="input__label-content input__label-content--isao">{{trans('film.label.conlange')}}</span>
                </label>
            </div>
            <div class="col-lg-1 col-md-2 col-sm-2 btn-group">
                <span class="btn text-danger fa fa-undo" ng-click="subtitle_language.edited = false;"></span>
                <span class="btn text-primary fa fa-check" ng-click="subtitleSaved(subtitle_language.id)"></span>
            </div>
        </div>
        <div class="form-group row py-5">
            <label class="col-lg-2 col-md-3 col-sm-12 label-justified required">{{trans('film.label.version')}}</label>
            <div class="col-lg-3 col-md-3 col-sm-12 radio-inline">
                <input type="radio" value="0" name="final" {{!$film->final?'checked':''}}>
                <label>{{trans('film.label.preview')}}</label>
            </div>
            <div class="col-lg-7 col-md-4 col-sm-12 radio-inline">
                <input type="radio" value="1" name="final"  {{$film->final?'checked':''}}>
                <label>{{trans('film.label.final')}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5 col-sm-12 label-justified required">{{trans('film.label.360')}}</label>
            <div class="col-lg-3 col-md-3 col-sm-12 radio-inline">
                <input type="radio" value="1" name="fullvision" {{$film->fullvision?'checked':''}}>
                <label>{{trans('layout.LABELS.yes')}}</label>

            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 radio-inline">
                <input type="radio" value="0" name="fullvision" {{!$film->fullvision?'checked':''}}>
                <label>{{trans('layout.LABELS.no')}}</label>
            </div>
        </div>
        <hr/>
        <div class="d-flex justify-content-between">
            <div class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteFilmModal">{{trans('film.buttons.delete')}}</div>
            <button class="btn btn-primary">{{trans('layout.BUTTONS.complete')}}</button>
        </div>
    </form>
</div>

@endsection
@section('script')
    <script src="/js/controllers/film/upload.js"></script>
@endsection