/**
 * Created by Jieyun on 2016/12/1.
 */

appZooMov.directive('pictureContent', function($rootScope, $uibModal, $filter) {
    return {
        restrict: 'A',
        link:function(scope, elem, attr){

            var support = {
                fileList: !!$('<input type="file">').prop('files'),
                blobURLs: !!window.URL && URL.createObjectURL,
                formData: !!window.FormData
            }

            jQuery.extend(support, {datauri: support.fileList && support.blobURLs});

            if(attr["pictureContent"] === "project"){

                scope.pictureDst = 'projects';
                scope.pcitureName = scope.project ? scope.project.id : '';
                scope.ratio = 64/36;
                scope.size = 2;
            }else{
                scope.pictureDst = 'avatars';
                scope.pcitureName = scope.user ? scope.user.id : '';
                scope.ratio = 1/1;
                scope.size = 1;
            }
            scope.pictureChanged = function(){
                var file;
                if (support.datauri) {
                    var files = $("#avatarInput").prop('files');
                    if (files.length > 0) {
                        file = files[0];
                        if ((file.type && /^image\/\w+$/.test(file.type)) || /\.(jpg|jpeg|png|gif)$/.test(file)) {
                            var size = ((file.size/1024)/1024).toFixed(4);
                            if(size > scope.size){
                                scope.stopCropper();
                                $uibModal.open({
                                    animation: true,
                                    templateUrl: 'alert.html',
                                    controller: function($scope) {
                                        $scope.alert = 'picture';
                                    }
                                });
                            }
                            else if (scope.url) {
                                URL.revokeObjectURL(scope.url); // Revoke the old one
                                scope.url = URL.createObjectURL(file);
                                scope.startCropper();
                            }
                            else{
                                scope.url = URL.createObjectURL(file);
                                scope.startCropper();
                            }
                        }
                    }
                }
                else {
                    file = $("#avatarInput").val();

                    if ((file.type && /^image\/\w+$/.test(file.type)) || /\.(jpg|jpeg|png|gif)$/.test(file)) {
                        var size = ((file.size/1024)/1024).toFixed(4);
                        if(size > scope.size){
                            scope.stopCropper();
                            alert($filter("translate")("project.ERRORS.picture.size", { size: scope.size}));
                        }
                        else
                            scope.submitPicture();
                    }
                }
            }

            scope.rotate = function (degree) {
                $('#picture_cropper').cropper("rotate", degree);
            }

            scope.startCropper = function () {
                var modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: 'picture.html',
                    size:'lg',
                    controller: function($scope) {
                        $scope.url = scope.url;
                    }
                });
                modalInstance.rendered.then(function () {
                    $('#picture_cropper').cropper({
                        preview:'.img-preview',
                        aspectRatio: scope.ratio,
                        autoCropArea: 1,
                        viewMode:2,
                        crop: function (e) {
                            var json = [
                                '{"x":' + e.x,
                                '"y":' + e.y,
                                '"height":' + e.height,
                                '"width":' + e.width,
                                '"rotate":' + e.rotate + '}'
                            ].join();
                            $('.avatar-data', elem).val(json);
                        }
                    });
                });
                modalInstance.result.then(function (confirm) {
                    if(confirm)
                    {
                        $('.img-original').hide();
                        $('.img-container').show();
                    }
                    else
                        scope.stopCropper();
                })
            }

            scope.stopCropper = function () {
                $('.img-original').show();
                $('.img-container').hide();
                $('#picture_loader').hide();
                $('#picture_cropper').cropper('destroy');
            }

            scope.submitPicture = function (form) {
                if (!scope.url && !$(".avatar-src", elem).val()) {
                    return false;
                }
                if(support.formData) {
                    $('#picture_loader').show();
                    var form = $('#picture-form')[0];

                    var data = new FormData(form);

                     $.ajax(
                        '/crop',
                        {
                            type: 'post',
                            data: data,
                            dataType: 'json',
                            processData: false,
                            contentType: false,

                            beforeSend: function () {
                                $('#crazyloader').fadeIn();
                            },

                            complete: function (data) {
                                if (data.status === 200) {
                                    scope.url = data.responseText;
                                    var picToUpdate = $("#" + scope.pictureDst + "-img");
                                    if(picToUpdate){
                                        picToUpdate.attr("src", scope.url + "?" + new Date().getTime());
                                    }
                                    if (!!$('<input type="file">').prop('files') && !!window.URL && URL.createObjectURL) {
                                        $('.poster img').attr('src', scope.url + "?" + new Date().getTime());
                                        scope.stopCropper();
                                    }
                                    else {
                                        $(".avatar-src", elem).val(scope.url);
                                        scope.startCropper();
                                    }
                                } else {
                                    scope.alert(data.responseText);
                                    scope.stopCropper();
                                }

                                $('#crazyloader').fadeOut();
                            },

                            error: function (XMLHttpRequest, textStatus, errorThrown) {
                                scope.alert(textStatus || errorThrown);
                            }
                        });
                    return false;
                }
            }

            scope.alert = function (msg) {
                var $alert = [
                    '<div class="alert alert-danger avatar-alert alert-dismissable">',
                    '<button type="button" class="close" data-dismiss="alert">&times;</button>',
                    msg,
                    '</div>'
                ].join('');

                $('.avatar-upload',elem).after($alert);
            }
        }
    }
});