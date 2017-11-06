<link href="/google-code-prettify/prettify.css" rel="stylesheet">
<link href="/wysiwyg/font-awesome.css" rel="stylesheet">
<link href="/wysiwyg/bootstrap-wysiwyg.css" rel="stylesheet">
<link href="/bower_components/crop-master/cropper.css" rel="stylesheet">

<style>
    #editor {overflow:scroll; max-height:800px}

</style>
<script src="/bower_components/jquery/jquery.hotkeys.js"></script>
<script src="/google-code-prettify/prettify.js"></script>
<script src="/wysiwyg/bootstrap-wysiwyg.js"></script>
<div  inner-editor>
    @if($picture)
        <div data-role="editor-image">
            <button id="openModal" type="button" class="btn" data-toggle="modal" data-target="#imageModal" style="visibility: hidden"></button>
            <div id="imageModal" class="modal fade" role="dialog" data-role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div id="picture_wrapper">
                                <img src="" id="picture_cropper" class="img-responsive">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <ul class="list-inline" id="img_size_selection">
                                <li image-width="original">
                                    <label><input type="radio" name="image_width" value="">{{trans('layout.EDITOR.image_original')}}(<span></span>)</label>
                                </li>
                                <li image-width="max">
                                    <label><input type="radio" value="2016" name="image_width">{{trans('layout.EDITOR.image_max', ['value'=>2016])}}(<span>2016</span>)</label>
                                </li>
                                <li image-width="medium">
                                    <label><input type="radio" value="800" name="image_width">{{trans('layout.EDITOR.image_medium', ['value'=>800])}}(<span>800</span>)</label>
                                </li>
                                <li image-width="min">
                                    <label><input type="radio" value="400" name="image_width">{{trans('layout.EDITOR.image_min')}}(<span>{{trans('layout.EDITOR.image_width', ['value'=>400])}}</span>)</label>
                                </li>
                            </ul>
                            <a class="btn btn-default" href="#" data-dismiss="modal" id="closeMondal">
                                <span class="fa fa-undo"></span>
                            </a>
                            <a href="javascript:void(0)" class="btn btn-info" data-edit="insertimage" title="{{trans('layout.BUTTONS.submit')}}"><i class="fa fa-check"></i></a>
                        </div>
                    </div>
                    <input type="hidden" value="{{$picture}}" name="picture_dst" id="picture_dst" />
                    <input type="hidden" value="{{$parent_id ? $parent_id : ''}}" name="parent_id"  id="parent_id" />
                </div>
            </div>
        </div>
    @endif
    <div id="alerts"></div>
    <div class="btn-toolbar" data-role="editor-toolbar" data-target="#editor">
        <div class="btn-group">
            <a class="btn dropdown-toggle" data-toggle="dropdown" title="{{trans('layout.EDITOR.font')}}"><i class="icon-font"></i><b class="caret"></b></a>
            <ul class="dropdown-menu" id="fontList">
                <li ng-repeat="font in fonts">
                    <a data-edit="fontName <%font%>" style="font-family:'<%font%>'" ng-bind="font"></a>
                </li>
            </ul>
        </div>
        <div class="btn-group">
            <a class="btn dropdown-toggle" data-toggle="dropdown" title="{{trans('layout.EDITOR.size')}}"><i class="icon-text-height"></i>&nbsp;<b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li><a data-edit="fontSize 5" style="font-size: large">{{trans('layout.EDITOR.huge')}}</a></li>
                <li><a data-edit="fontSize 3">{{trans('layout.EDITOR.normal')}}</a></li>
                <li><a data-edit="fontSize 1" style="font-size: small">{{trans('layout.EDITOR.small')}}</a></li>
            </ul>
        </div>
        <div class="btn-group">
            <a class="btn" data-edit="bold" title="{{trans('layout.EDITOR.bold')}} (Ctrl/Cmd+B)"><i class="icon-bold"></i></a>
            <a class="btn" data-edit="italic" title="{{trans('layout.EDITOR.italic')}} (Ctrl/Cmd+I)"><i class="icon-italic"></i></a>
            <a class="btn" data-edit="strikethrough" title="{{trans('layout.EDITOR.strike')}}"><i class="icon-strikethrough"></i></a>
            <a class="btn" data-edit="underline" title="{{trans('layout.EDITOR.underline')}} (Ctrl/Cmd+U)"><i class="icon-underline"></i></a>
        </div>
        <div class="btn-group">
            <a class="btn" data-edit="insertunorderedlist" title="{{trans('layout.EDITOR.bullet')}}"><i class="icon-list-ul"></i></a>
            <a class="btn" data-edit="insertorderedlist" title="{{trans('layout.EDITOR.number')}}"><i class="icon-list-ol"></i></a>
            <a class="btn" data-edit="outdent" title="{{trans('layout.EDITOR.reduce')}} (Shift+Tab)"><i class="icon-indent-left"></i></a>
            <a class="btn" data-edit="indent" title="{{trans('layout.EDITOR.indent')}} (Tab)"><i class="icon-indent-right"></i></a>
        </div>
        <div class="btn-group">
            <a class="btn" data-edit="justifyleft" title="{{trans('layout.EDITOR.left')}} (Ctrl/Cmd+L)"><i class="icon-align-left"></i></a>
            <a class="btn" data-edit="justifycenter" title="{{trans('layout.EDITOR.center')}} (Ctrl/Cmd+E)"><i class="icon-align-center"></i></a>
            <a class="btn" data-edit="justifyright" title="{{trans('layout.EDITOR.right')}} (Ctrl/Cmd+R)"><i class="icon-align-right"></i></a>
            <a class="btn" data-edit="justifyfull" title="{{trans('layout.EDITOR.justify')}} (Ctrl/Cmd+J)"><i class="icon-align-justify"></i></a>
        </div>
        <div class="btn-group">
            <a class="btn dropdown-toggle" data-toggle="dropdown" title="{{trans('layout.EDITOR.hyperlink')}}"><i class="icon-link"></i></a>
            <div class="dropdown-menu input-append">
                <input class="span2" placeholder="URL" type="text" data-edit="createLink"/>
                <button class="btn" type="button">{{trans('layout.EDITOR.add')}}</button>
            </div>
            <a class="btn" data-edit="unlink" title="{{trans('layout.EDITOR.remove')}}"><i class="icon-cut"></i></a>

        </div>
        @if($picture)
            <div class="btn-group">
                <a class="btn" title="{{trans('layout.EDITOR.picture')}}" id="pictureBtn"><i class="icon-picture"></i></a>
                <input type="file" data-role="magic-overlay" data-target="#pictureBtn" data-edit="insertImage"  />
            </div>
        @endif
        <div class="btn-group">
            <a class="btn" data-edit="undo" title="{{trans('layout.EDITOR.undo')}} (Ctrl/Cmd+Z)"><i class="icon-undo"></i></a>
            <a class="btn" data-edit="redo" title="{{trans('layout.EDITOR.redo')}} (Ctrl/Cmd+Y)"><i class="icon-repeat"></i></a>
        </div>
        <input type="text" data-edit="inserttext" id="voiceBtn" x-webkit-speech="">
    </div>
    <div id="editor">
       {!! $content !!}
    </div>
    <div id="picture-preview" class="width:100%;height:100%;"></div>
    <textarea id="editor-content" name="editor" style="display: none;"></textarea>
</div>
<script src="/bower_components/crop-master/cropper.js"></script>
<script>

    $(function(){
        function initToolbarBootstrapBindings() {
            $('a[title]').tooltip({container:'body'});
            $('.dropdown-menu input').click(function() {return false;})
                .change(function () {$(this).parent('.dropdown-menu').siblings('.dropdown-toggle').dropdown('toggle');})
                .keydown('esc', function () {this.value='';$(this).change();});

            $('[data-role=magic-overlay]').each(function () {
                var overlay = $(this), target = $(overlay.data('target'));
                overlay.css('opacity', 0).css('position', 'absolute').offset(target.offset()).width(target.outerWidth()).height(target.outerHeight());

            });
            if ("onwebkitspeechchange"  in document.createElement("input")) {
                var editorOffset = $('#editor').offset();
                $('#voiceBtn').css('position','absolute').offset({top: editorOffset.top, left: editorOffset.left+$('#editor').innerWidth()-35});
            } else {
                $('#voiceBtn').hide();
            }
        };
        function showErrorAlert (reason, detail) {
            var msg='';
            if (reason==='unsupported-file-type') { msg = "Unsupported format " +detail; }
            else {
                msg = reason + detail;
            }
            $('<div class="alert"> <button type="button" class="close" data-dismiss="alert">&times;</button>'+
                '<strong>File upload error</strong> '+msg+' </div>').prependTo('#alerts');
        };
        initToolbarBootstrapBindings();
        $('#editor').wysiwyg({ fileUploadError: showErrorAlert} );
        window.prettyPrint && prettyPrint();
    });
</script>
