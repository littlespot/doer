@extends('layouts.film')

@section('filmForm')
    <?php
        header('content-type:image/jpg;');
    ?>
    <div>
        <link href="/bower_components/bootstrap/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
        <script src="/bower_components/bootstrap/plugins/piexif.min.js" type="text/javascript"></script>
        <script src="/bower_components/bootstrap/plugins/purify.min.js" type="text/javascript"></script>
        <script src="/bower_components/bootstrap/js/fileinput.min.js"></script>
        <script src="/bower_components/bootstrap/js/fileinput.theme.min.js"></script>
        <script src="/bower_components/bootstrap/locale/{{app()->getLocale()}}.js"></script>
        <ul class="alert alert-dark text-primary small px-5" role="alert">
            <li>{!! trans('film.alert.media') !!}</li>
            <li class="pt-1">{!! trans('layout.ALERTS.compulsive') !!}</li>
        </ul>
       <br/>
        <div class="row">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified required">{!! trans('film.label.film_photo') !!}</label>
            <div class="col-lg-10 col-md-8 col-sm-12">
                <form enctype="multipart/form-data" id="pictureForm">
                    {{  csrf_field() }}
                    <input id="file-picture" type="file" class="file" name="pictures[]" multiple accept="image/*">
                    <span class="text-danger">({!! trans('film.alert.picture') !!})</span>
                </form>
            </div>
            <script>
                $('#file-picture').fileinput({
                    theme: "fa",
                    language: '{{app()->getLocale()}}',
                    uploadUrl: '{{"/archive/".$film->id."/upload/pictures?completed=1"}}',
                    deleteUrl:'{{"/archive/".$film->id."/remove/pictures"}}',
                    allowedFileExtensions : ['JPEG', 'JPG', 'PNG'],
                    maxFileCount: 9,
                    maxFileSize:2048,
                    showRemove : false,
                    uploadAsync:true,
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
                    initialPreviewFileType: 'image',
                    purifyHtml: true,
                    browseClass: "btn btn-primary", //按钮样式
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
        <div class="row">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified">{!! trans('film.label.film_poster') !!}</label>
            <div class="col-lg-10 col-md-8 col-sm-12">
                <form enctype="multipart/form-data" id="pictureForm">
                    {{  csrf_field() }}
                    <input id="file-poster" type="file" class="file" name="posters[]" multiple>
                </form>
                <span class="text-danger">({!! trans('film.alert.picture') !!})</span>
            </div>
            <script>
                $('#file-poster').fileinput({
                    theme: "fa",
                    language: '{{app()->getLocale()}}',
                    uploadUrl: '{{"/archive/".$film->id."/upload/posters"}}',
                    deleteUrl:'{{"/archive/".$film->id."/remove/posters"}}',
                    allowedFileExtensions : ['JPEG', 'JPG', 'PNG'],
                    maxFileCount: 9,
                    maxFileSize:2048,
                    showRemove : false,
                    uploadAsync:true,
                    dropZoneEnabled:true,
                    validateInitialCount: true,
                    overwriteInitial: false,
                    initialPreview: [@forEach(Storage::disk('public')->files("film/".$film->id."/posters") as $file) '{{ config('url').Storage::url($file)}}',@endforeach],
                    initialPreviewConfig: [
                            @forEach(Storage::disk('public')->files("film/".$film->id."/posters") as $file) {
                            key:'{{basename($file)}}'
                        },@endforeach
                    ],
                    initialPreviewAsData: true,
                    initialPreviewFileType: 'image',
                    purifyHtml: true,
                    browseClass: "btn btn-primary", //按钮样式
                    msgFilesTooMany: "选择上传的文件数量({n}) 超过允许的最大数值{m}！",
                })
            </script>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-12">
                <label class="label-justified"> {!! trans('film.label.other_file') !!}</label>
                <label class="label-justified">{{trans('film.label.other_file_2') }}</label>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-12">
                <form enctype="multipart/form-data" id="pictureForm">
                    {{  csrf_field() }}
                    <input id="file-text" type="file" class="file" name="files[]" multiple>
                </form>
                <span class="text-danger">({!! trans('film.alert.document') !!})</span>
            </div>
            <script>
                $('#file-text').fileinput({
                    theme: "fa",
                    language: '{{app()->getLocale()}}',
                    uploadUrl: '{{"/archive/".$film->id."/upload/files"}}',
                    deleteUrl:'{{"/archive/".$film->id."/remove/files"}}',
                    allowedFileExtensions : ['pdf', 'txt', 'rtf', 'doc', 'docx', 'odt'],
                    maxFileCount: 20,
                    maxFileSize:2048,
                    showRemove : false,
                    uploadAsync:true,
                    dropZoneEnabled:true,
                    validateInitialCount: true,
                    overwriteInitial: false,
                    initialPreview: [@forEach(Storage::disk('public')->files("film/".$film->id."/files") as $file)
                        '{{basename($file)}}',@endforeach],
                    initialPreviewConfig: [
                            @forEach(Storage::disk('public')->files("film/".$film->id."/files") as $file) {
                                         key:'{{basename($file)}}'
                                },@endforeach
                    ],
                    initialPreviewAsData: true,
                    initialPreviewFileType: 'text',
                    purifyHtml: true,
                    browseClass: "btn btn-primary", //按钮样式
                    msgFilesTooMany: "选择上传的文件数量({n}) 超过允许的最大数值{m}！",
                })

            </script>
        </div>
        <hr/>
        <form name="filmForm"  action="/{{$film->type}}s" method="POST"
              ng-controller="filmCtrl" ng-init="loaded()">
            {{csrf_field()}}
            <input type="hidden" name="id" value="{{$film->id}}" />
            <input type="hidden" name="step" value="{{$step+1}}" />
            <div class="d-flex justify-content-between">
                <div class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteFilmModal">{{trans('film.buttons.delete')}}</div>
                <button class="btn btn-primary" type="submit">{{trans('layout.BUTTONS.continue')}}</button>
            </div>
        </form>
    </div>
@endsection
@section('script')
    <script src="/js/controllers/film/general.js"></script>
@endsection