@extends('layouts.film')

@section('filmForm')
    <link href="/bower_components/bootstrap/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
    <script src="/bower_components/bootstrap/plugins/piexif.min.js" type="text/javascript"></script>
    <script src="/bower_components/bootstrap/plugins/purify.min.js" type="text/javascript"></script>
    <script src="/bower_components/bootstrap/js/fileinput.min.js"></script>
    <script src="/bower_components/bootstrap/js/fileinput.theme.min.js"></script>
    <script src="/bower_components/bootstrap/locale/{{app()->getLocale()}}.js"></script>
    <?php
    header('content-type:image/jpg;');
    ?>
    <div ng-controller="filmCtrl" ng-init="loaded()">
        <ul class="alert alert-dark text-primary small px-5" role="alert">
            <li>{!! trans('film.alert.media_script') !!}</li>
            <li class="py-1">{!! trans('film.alert.cover_script') !!}</li>
            <li>{!! trans('layout.ALERTS.compulsive') !!}</li>
        </ul>
        <div class="row py-5">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified required">{!! trans('film.label.upload_script') !!}</label>
            <div class="col-lg-10 col-md-8 col-sm-12">
                <form enctype="multipart/form-data" id="pictureForm">
                    {{  csrf_field() }}
                    <input id="file-script" type="file" class="file" name="preview[]" accept="application/pdf" multiple>
                    <span class="text-danger">({!! trans('film.alert.upload_script') !!})</span>
                </form>
            </div>
            <script>
                $('#file-script').fileinput({
                    theme: "fa",
                    language: '{{app()->getLocale()}}',
                    uploadUrl: '{{"/archive/".$film->id."/upload/preview?uploaded=1"}}',
                    deleteUrl:'{{"/archive/".$film->id."/remove/preview"}}',
                    allowedFileExtensions : ['PDF'],
                    maxFileCount: 9,
                    maxFileSize:2048,
                    showRemove : false,
                    uploadAsync:true,
                    dropZoneEnabled:true,
                    validateInitialCount: true,
                    overwriteInitial: false,
                    initialPreview: [@forEach(Storage::disk('public')->files("film/".$film->id."/preview") as $file) '{{ config('url').Storage::url($file)}}',@endforeach],
                    initialPreviewConfig: [
                            @forEach(Storage::disk('public')->files("film/".$film->id."/preview") as $file) {
                            key:'{{basename($file)}}'
                        },@endforeach
                    ],
                    initialPreviewAsData: true,
                    initialPreviewFileType: 'pdf',
                    purifyHtml: true,
                    browseClass: "btn btn-primary",
                    msgFilesTooMany: "选择上传的文件数量({n}) 超过允许的最大数值{m}！",
                })
                    .on("filebatchuploadsuccess", function(event, outData) {
                        if(outData.response.completed)
                            window.location.reload();
                    })
                    .on('filedeleted', function(event) {
                        if(outData.response.completed)
                            window.location.reload();
                    });
            </script>
        </div>
        <div class="row py-5">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified">{!! trans('film.label.upload_cover') !!}</label>
            <div class="col-lg-10 col-md-8 col-sm-12">
                <form enctype="multipart/form-data" id="pictureForm">
                    {{  csrf_field() }}
                    <input id="file-cover" type="file" class="file" name="cover[]" accept="image/jpeg, image/png">
                    <span class="text-danger">({!! trans('film.alert.upload_cover') !!})</span>
                </form>
            </div>
            <script>
                $('#file-cover').fileinput({
                    theme: "fa",
                    language: '{{app()->getLocale()}}',
                    uploadUrl: '{{"/archive/".$film->id."/upload/cover?max=1"}}',
                    deleteUrl:'{{"/archive/".$film->id."/remove/cover"}}',
                    allowedFileExtensions : ['JPEG', 'JPG', 'PNG'],
                    maxFileCount: 1,
                    maxFileSize:2048,
                    showRemove : false,
                    uploadAsync:true,
                    dropZoneEnabled:true,
                    validateInitialCount: true,
                    overwriteInitial: false,
                    initialPreview: [@forEach(Storage::disk('public')->files("film/".$film->id."/cover") as $file) '{{ config('url').Storage::url($file)}}',@endforeach],
                    initialPreviewConfig: [
                            @forEach(Storage::disk('public')->files("film/".$film->id."/cover") as $file) {
                            key:'{{basename($file)}}'
                        },@endforeach
                    ],
                    initialPreviewAsData: true,
                    purifyHtml: true,
                    browseClass: "btn btn-primary",
                    msgFilesTooMany: "选择上传的文件数量({n}) 超过允许的最大数值{m}！",
                })
                    .on("filebatchuploadsuccess", function(event, outData) {
                        if(outData.response.completed)
                            window.location.reload();
                    })
                    .on('filedeleted', function(event) {
                        if(outData.response.completed)
                            window.location.reload();
                    });
            </script>
        </div>
        <div class="row py-5">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified">{!! trans('film.label.picture') !!}</label>
            <div class="col-lg-10 col-md-8 col-sm-12">
                <form enctype="multipart/form-data" id="pictureForm">
                    {{  csrf_field() }}
                    <input id="file-pictures" type="file" class="file" name="pictures[]" accept="image/jpeg, image/png" multiple>
                    <span class="text-danger">({!! trans('film.alert.picture') !!})</span>
                </form>
            </div>
            <script>
                $('#file-pictures').fileinput({
                    theme: "fa",
                    language: '{{app()->getLocale()}}',
                    uploadUrl: '{{"/archive/".$film->id."/upload/pictures"}}',
                    deleteUrl:'{{"/archive/".$film->id."/remove/pictures"}}',
                    allowedFileExtensions : ['JPEG', 'JPG', 'PNG'],
                    maxFileCount: 9,
                    maxFileSize:2048,
                    showRemove : false,
                    uploadAsync: true,
                    dropZoneEnabled:true,
                    validateInitialCount: true,
                    overwriteInitial: false,
                    initialPreview: [@forEach(Storage::disk('public')->files("film/".$film->id."/pictures") as $file) '{{ config('url').Storage::url($file)}}',@endforeach],
                    initialPreviewConfig: [
                            @forEach(Storage::disk('public')->files("film/".$film->id."/pictures") as $file) {
                            key:'{{basename($file)}}'
                        },@endforeach
                    ],
                    initialPreviewAsData: true,
                    purifyHtml: true,
                    browseClass: "btn btn-primary",
                    msgFilesTooMany: "选择上传的文件数量({n}) 超过允许的最大数值{m}！",
                })
                    .on("filebatchuploadsuccess", function(event, outData) {
                        if(outData.response.completed)
                            window.location.reload();
                    })
                    .on('filedeleted', function(event) {
                        if(outData.response.completed)
                            window.location.reload();
                    });
            </script>
        </div>
        <hr/>
        <form name="uploadForm" action="/play/{{$film->id}}/complete" method="POST">
            <div class="d-flex justify-content-between">
                <div class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteFilmModal">{{trans('film.buttons.delete')}}</div>
                <button class="btn btn-primary">{{trans('layout.BUTTONS.complete')}}</button>
            </div>
        </form>
    </div>

@endsection
@section('script')
    <script src="/js/controllers/film/general.js"></script>
@endsection