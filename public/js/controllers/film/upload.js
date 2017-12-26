appZooMov.controller('filmCtrl', function ($scope, $timeout, $interval) {
    $scope.length = 1024 * 1024 * 5;
    $scope.length_kb = 1024*15;
    $scope.init = function (id) {
        $scope.url = '/film/' + id + '/preview';
    }

    var xhr;
    $scope.selectFile = function () {
        var file = document.getElementById("previewFile").files[0];
        $scope.file = file;
        if(file){
            $('#filename').val(file.name);
            $('#btnStart').show();
        }
    }

    $scope.upload = function () {
        if(!$scope.file)
            return;
        $scope.total = Math.ceil($scope.file.size / $scope.length);
        $scope.start = 0;
        $scope.blob_num = 1;
        $scope.ext = $scope.file.name.substr($scope.file.name.lastIndexOf('.'));
        $scope.size = $scope.file.size;
        $('.progress-bar').attr('aria-valuenow',0).width('0%');
        $scope.origin = $('#video>source').attr('src');
        $scope.startUpload();
    }

    $scope.startUpload = function () {
        $('#btnStart').hide();
        $('#btnStop').show();
        $('.btn-file').attr('disabled', true);
        if($scope.origin.indexOf('player.swf')<0){
            $('#video>source').attr('src', '/storage/player.swf');
            $('#video').load();
        }
        if(angular.isUndefined(xhr))
            xhr = new XMLHttpRequest();
        $scope.cutFile();
    }

    $scope.cutFile = function (){
        if($scope.file && angular.isDefined(xhr)){
            $scope.end = $scope.start + $scope.length;
            var file_blob = $scope.file.slice($scope.start, $scope.end);
            $scope.sendFile(file_blob);
        }
        else{
            $scope.endUpload();
        }
    }

    $scope.stopUpload = function () {
        $('#btnContinue').hide();
        $('#btnStop').attr('disabled', true);
        $scope.endUpload();
    }

    $scope.continueUpload = function () {
        $('#btnContinue').hide();
        $scope.startUpload();
    }

    $scope.sendFile = function (blob){
        if(!xhr)
            return;
        var form_data = new FormData($('#previewForm')[0]);
        form_data.append('total_blob_num',$scope.total);
        form_data.append('ext',$scope.ext);
        form_data.append('blob_num',$scope.blob_num);
        form_data.append('preview', blob);

        xhr.open('POST',$scope.url, {dataType:'json'});

        xhr.onreadystatechange  = function () {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                if( xhr.status==200) {
                    var percent = $scope.total == 1 ? '100' : Math.min(100, Math.round(($scope.blob_num / $scope.total) * 100));
                    var vit = '..';
                    var data = angular.fromJson(xhr.responseText);
                    if(data.completed < 1){
                        vit = '(' + Math.round($scope.length_kb/(data.result * 1000)) + 'KB/s)';
                    }
                    $('.progress-bar').attr('aria-valuenow',percent).width(percent+'%');
                    $('.progress span').text(percent+'%'+ vit);
                    $timeout(function() {
                        if ($scope.end < $scope.size) {
                            $scope.start = $scope.end;
                            $scope.blob_num++;
                            $scope.timer++;
                            $scope.cutFile();
                        }
                        else {
                            $('.progress-bar').text('100%');
                            $scope.completed();
                        }
                    }, 1000);
                }
                else if(xhr.status == 413){
                    $timeout($scope.cutFile, 1000);
                }
            }
        }

        xhr.send(form_data);
    }

    $scope.endUpload = function () {
        if(angular.isDefined(xhr)){
            xhr = null;
        }

        $('.progress span').text('');
        if($scope.end < $scope.size){
            $('#btnStop').removeAttr('disabled').hide();
            $('.btn-file').removeAttr('disabled');
            $('#btnContinue').show();
            $scope.end = $scope.size;
            $('#video>source').attr('src', $scope.origin);
            $('#video').load();
        }
        else{
            $scope.completed();
        }
    }

    $scope.completed = function () {
        $('#buttons .btn').hide();
        $('.btn-file').removeAttr('disabled');
        $('#video>source').attr('src', '/storage' +  $scope.url + '/preview' + $scope.ext + '?' + Date.now());
        $('.progress span').text('');
        $('#video').load();
    }
})