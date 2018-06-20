<div>
    <div class="modal fade" id="pictureErrorModal" tabindex="-1" role="dialog" aria-labelledby="pictureErrorModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-4" id="modal-body">
                    <h6>{{trans('film.alert.attachment')}}</h6>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-dismiss="modal" >
                        {{trans("layout.BUTTONS.submit")}}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="loader-content" style="display: none" id="picture_loader"><div class="loader"></div></div>
    @if ($errors->has('poster'))
        <div class="text-danger">
            {{ $errors->first('poster') }}
        </div>
    @endif
    <div class="text-center my-3 img-original">
        <a class="poster {{ $errors->has('poster') ? ' has-error' : '' }}"
           onclick="$('#avatarInput').click()">
            <img ng-src="/{{$uploadUrl}}/{{$pictureFolder}}/{{$pictureId}}.jpg?{{time()}}" id="poster_image"
                 onError="this.onerror=null;this.src='/context/{{$pictureFolder}}/default.png';"/>
        </a>
        <div class="text-center text-danger">{{trans("project.PLACES.poster")}}</div>
        <br/>
        <div class="text-center my-1">
            <button class="btn btn-outline-primary" onclick="$('#avatarInput').click()">
                {{trans("project.BUTTONS.upload")}}
            </button>
        </div>
    </div>
    <div class="img-container col-xs-12 py-3">
        <div class="img-preview preview-lg"></div>
        <div class="img-preview preview-md"></div>
        <div class="img-preview preview-sm"></div>
    </div>
    <div class="img-container modal-footer">
        <button class="btn btn-default" type="button" ng-click="stopCropper()">{{trans("project.BUTTONS.cancel")}}</button>
        <button class="btn btn-success text-uppercase" type="button"
                ng-click="submitPicture(this.form)">{{trans("project.BUTTONS.confirm")}}</button>
    </div>

</div>
<br>
<form id="picture-form" picture-content="{{$pictureFolder}}" enctype="multipart/form-data" action="/crop" method="POST">
    {{ csrf_field() }}
    <div class="picture-upload">
        <input type="text" class="avatar-src" name="picture_src" ng-model="url">
        <input type="text" class="avatar-data" name="picture_data">
        <input type="text" name="picture_name" value="{{$pictureId}}">
        <input type="file" id="avatarInput" name="picture_file" accept="image/*"
               onchange="angular.element(this).scope().pictureChanged()">
        <input type="hidden" name="picture_dst" value="/{{$uploadUrl}}/{{$pictureFolder}}">
    </div>
    <div class="modal fade bd-example-modal-lg" id="pictureCropperModal" tabindex="-1" role="dialog" aria-labelledby="pictureCropperModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body d-flex justify-content-center" id="modal-body">
                    <div id="picture_wrapper"><img src="" id="picture_cropper"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" type="button" ng-click="stopCropper()" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-success" type="button" ng-click="submitPicture()">{{trans("project.BUTTONS.confirm")}}</button>
                </div>
            </div>
        </div>
    </div>
</form>
