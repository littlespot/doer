@extends('film.card')

@section('filmForm')
    <link href="/bower_components/bootstrap/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
    <script src="/bower_components/bootstrap/plugins/piexif.min.js" type="text/javascript"></script>
    <script src="/bower_components/bootstrap/plugins/purify.min.js" type="text/javascript"></script>
    <script src="/bower_components/bootstrap/js/fileinput.min.js"></script>
    <script src="/bower_components/bootstrap/locale/{{App::getLocale()}}.js"></script>
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
    <h4 class="header-slogan">{{trans('film.buttons.upload')}}</h4>
    <div class="alert alert-info row" role="alert">
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
    <hr>
    <div id="progress">
        <div id="finish" ng-style="{'width':percent}"></div>
    </div>
    <video width="400" controls  id="video">
        @if(is_null($file))
            <source src="/storage/player.swf" type="video/swf">
        @else
            <source src="/storage/{{$file.'?'.time()}}" type="video/{{$ext}}">
        @endif
    </video>
    <form id="previewForm">
        {{csrf_field()}}
        <div class="progress">
            <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
            <span>60% complete</span>
        </div>
        <div class="input-group">
            <input readonly="readonly" placeholder="Select file" class="form-control" id="filename" type="text">
            <span class="input-group-btn" id="buttons" >
                 <span class="btn btn-success" id='btnStart' style="display: none" ng-click="upload()"><span class="fa fa-upload"></span></span>
                 <span class="btn btn-danger" id='btnStop' style="display: none"  ng-click="stopUpload()"><span class=" fa fa-stop"></span></span>
                 <span class="btn btn-default" id='btnWait' style="display: none"><span class="fa fa-spinner"></span></span>
                <span class="btn btn-success" id='btnContinue' style="display: none" ng-click="continueUpload()"><span class="fa fa-repeat"></span></span>
            </span>
            <span class="input-group-btn">
                <span class="btn btn-primary btn-file" ng-disabled="stop == 1">
                  Browse...
                  <input accept="video/mp4,video/mov,video/m4v,video/*"  type="file" id="previewFile" onchange="angular.element(this).scope().selectFile()">
                </span>
            </span>
        </div>
    </form>

    <h6>{{trans('film.header.inlaid_subtitle')}}</h6>

    <div class="row">
        <div class="col-lg-8 col-md-6 col-sm-12 col-xs-12">
            <div class="form-group row">
                <label class="col-md-6 col-sm-8 text-right">{{trans('layout.LABELS.zh')}}</label>
                <div class="col-md-6 col-sm-4">
                    <input type="radio" value="1" name="subtitle_zh">{{trans('layout.LABELS.with')}}
                    <input type="radio" value="0" name="subtitle_zh">{{trans('layout.LABELS.without')}}
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-6 col-sm-8 text-right">{{trans('layout.LABELS.en')}}</label>
                <div class="col-md-6 col-sm-4">
                    <input type="radio" value="1" name="subtitle_en">{{trans('layout.LABELS.with')}}
                    <input type="radio" value="0" name="subtitle_en">{{trans('layout.LABELS.without')}}
                </div>
            </div>
            <div class="form-group row">
                <select class="col-md-6 col-sm-8 text-right" name="subtitle_other">
                    @foreach($languages as $language)
                        <option value="{{$language->id}}">{{$language->name}}</option>
                    @endforeach
                </select>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-md-6 col-sm-8 text-right">{{trans('film.label.version')}}</label>
                <div class="col-md-6 col-sm-4">
                    <input type="radio" value="0" name="progress">{{trans('film.label.preview')}}
                    <input type="radio" value="1" name="progress">{{trans('film.label.final')}}
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-md-6 col-sm-8 text-right">{{trans('film.label.360')}}</label>
                <div class="col-md-6 col-sm-4">
                    <input type="radio" value="1" name="progress">{{trans('layout.LABELS.yes')}}
                    <input type="radio" value="0" name="progress">{{trans('layout.LABELS.no')}}
                </div>
            </div>
            <hr>
        </div>
    </div>
    <hr/>
    <div class="text-right">
        <button class="btn btn-primary">{{trans('layout.BUTTONS.continue')}}</button>
    </div>
</div>

@endsection
@section('script')
    <script src="/js/controllers/film/upload.js"></script>
@endsection