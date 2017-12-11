@extends('film.card')

@section('filmForm')
    <?php
        header('content-type:image/jpg;');
    ?>
    <form id="time_form" name="timeForm" action="/film/media" method="post">
        @include('film.form')
    <link href="/bower_components/bootstrap/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
    <script src="/bower_components/bootstrap/plugins/piexif.min.js" type="text/javascript"></script>
    <script src="/bower_components/bootstrap/plugins/purify.min.js" type="text/javascript"></script>
    <script src="/bower_components/bootstrap/js/fileinput.min.js"></script>
    <script src="/bower_components/bootstrap/locale/{{App::getLocale()}}.js"></script>
    <h4 class="header-slogan">{{trans('film.card.attachments')}}</h4>
    <div class="alert alert-info" role="alert">
        <div>{!! trans('film.alert.media') !!}</div>
        <div>{!! trans('layout.ALERTS.compulsive') !!}</div>
    </div>
    <div>
        <div class="row">
            <label class="col-sm-2 col-xs-4">{!! trans('film.label.film_photo') !!}</label>
            <div class="col-sm-10 col-xs-8">
                <form enctype="multipart/form-data" id="pictureForm">
                    {{  csrf_field() }}
                    <input id="file-picture" type="file" class="file" name="pictures[]" multiple>
                    <span class="text-danger">({!! trans('film.alert.picture') !!})</span>
                </form>
            </div>
            <script>
                $('#file-picture').fileinput({
                    language: '{{App::getLocale()}}',
                    uploadUrl: '{{"/film/".$film->id."/upload/pictures"}}',
                    deleteUrl:'{{"/film/".$film->id."/remove/pictures"}}',
                    browseIcon: "<i class=\"glyphicon glyphicon-picture\"></i> ",
                    allowedFileExtensions : ['JPEG', 'JPG', 'PNG'],
                    maxFileCount: 9,
                    maxFileSize:2097152,
                    showRemove : false,
                    uploadAsync:false,
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
                    showCaption: false,
                    browseClass: "btn btn-primary", //按钮样式
                    previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
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
            <label class="col-sm-2 col-xs-4">{!! trans('film.label.film_poster') !!}</label>
            <div class="col-sm-10 col-xs-8">
                <form enctype="multipart/form-data" id="pictureForm">
                    {{  csrf_field() }}
                    <input id="file-poster" type="file" class="file" name="posters[]" multiple>
                </form>
                <span class="text-danger">({!! trans('film.alert.picture') !!})</span>
            </div>
            <script>
                $('#file-poster').fileinput({
                    language: '{{App::getLocale()}}',
                    uploadUrl: '{{"/film/".$film->id."/upload/posters"}}',
                    deleteUrl:'{{"/film/".$film->id."/remove/posters"}}',
                    allowedFileExtensions : ['JPEG', 'JPG', 'PNG'],
                    maxFileCount: 9,
                    maxFileSize:2097152,
                    showRemove : false,
                    uploadAsync:false,
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
                    previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
                    msgFilesTooMany: "选择上传的文件数量({n}) 超过允许的最大数值{m}！",
                })
            </script>
        </div>
    </div>
    <hr>
    <div class="row">
        <label class="col-sm-2 col-xs-4">{!! trans('film.label.other_file') !!}</label>
        <div class="col-sm-10 col-xs-8">
            <form enctype="multipart/form-data" id="pictureForm">
                {{  csrf_field() }}
                <input id="file-text" type="file" class="file" name="files[]" multiple>
            </form>
            <span class="text-danger">({!! trans('film.alert.document') !!})</span>
        </div>
        <script>
            $('#file-text').fileinput({
                language: '{{App::getLocale()}}',
                uploadUrl: '{{"/film/".$film->id."/upload/files"}}',
                deleteUrl:'{{"/film/".$film->id."/remove/files"}}',
                allowedFileExtensions : ['pdf', 'txt', 'rtf', 'doc', 'docx', 'odt'],
                maxFileSize:2097152,
                showRemove : false,
                uploadAsync:false,
                dropZoneEnabled:true,
                overwriteInitial: true,
                initialPreview: [@forEach(Storage::disk('public')->files("film/".$film->id."/files") as $file) '{{ config('url').Storage::url($file)}}',@endforeach],
                initialPreviewConfig: [
                        @forEach(Storage::disk('public')->files("film/".$film->id."/files") as $file) {
                        key:'{{basename($file)}}'
                    },@endforeach
                ],
				hideThumbnailContent: true,
                initialPreviewAsData: false,
                initialPreviewFileType: 'text',
                purifyHtml: true,
                showCaption: false,
                browseClass: "btn btn-primary", //按钮样式
                previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
                msgFilesTooMany: "选择上传的文件数量({n}) 超过允许的最大数值{m}！",
            })
        </script>
    </div>
    <hr/>
    <div class="text-right">
        <a class="btn btn-primary" href="/film/{{$film->id}}">{{trans('layout.BUTTONS.continue')}}</a>
    </div>
    </form>
@endsection