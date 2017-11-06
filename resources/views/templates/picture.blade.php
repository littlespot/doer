<div>
    <div class="loader-content" style="display: none" id="picture_loader"><div class="loader"></div></div>
    @if ($errors->has('poster'))
        <div class="text-danger">
            {{ $errors->first('poster') }}
        </div>
    @endif
    <div class="text-center margin-top-sm margin-bottom-sm img-original">
        <a class="poster {{ $errors->has('poster') ? ' has-error' : '' }}"
           onclick="$('#avatarInput').click()">
            <img ng-src="/context/projects/{{$project->id}}.jpg"
                 onError="this.onerror=null;this.src='/images/poster.png';"/>
        </a>
        <div class="text-center text-danger">{{trans("project.PLACES.poster")}}</div>
        <br/>
        <div class="text-center margin-top-sm ">
            <button class="btn btn-default" onclick="$('#avatarInput').click()">
                {{trans("project.BUTTONS.upload")}}
            </button>
        </div>
    </div>
    <div class="img-container col-xs-12 margin-top-sm">
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
<form id="picture-form" picture-content="project" enctype="multipart/form-data">
    <div class="picture-upload">
        <input type="text" class="avatar-src" name="picture_src" ng-model="url">
        <input type="text" class="avatar-data" name="picture_data">
        <input type="text" name="picture_name" value="<%pcitureName%>">
        <input type="file" id="avatarInput" name="picture_file" accept="image/*"
               onchange="angular.element(this).scope().pictureChanged()">
        <input type="hidden" name="picture_dst" value="/context/projects">
    </div>
    <script type="text/ng-template" id="picture.html">
        <div class="modal-body flex-center">
            <div id="picture_wrapper"><img ng-src="<%url%>" id="picture_cropper"></div>
        </div>
        <div class="modal-footer">
            <div ng-click="$close(false)" class="btn btn-default">
                <span class="fa fa-undo"></span>
            </div>
            <div ng-click="$close(true)" class="btn btn-primary">
                <span class="fa fa-check"></span>
            </div>
        </div>
    </script>
</form>
