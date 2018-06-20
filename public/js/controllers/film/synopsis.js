appZooMov.controller("filmCtrl", function($rootScope, $scope,$filter, $http) {
    $scope.init = function () {
        $scope.list = [];
        $scope.errors = {newsynopsis:{invalid:0, language:0, content:0}, content:0};
        $rootScope.loaded();
    }

    $scope.edit = function (id, json) {
        if(json) {
            var index = -1;
            for (var i = 0; i < $scope.list.length && index < 0; i++) {
                if ($scope.list[i].language_id == id) {
                    index = i;
                    $scope.synopsisCopy = angular.copy($scope.list[i]);
                }
            }
        }
        else{
            $scope.synopsisCopy = {language_id:id, content:$('#content_'+id).text().trim(), language:$('#lang_'+id).text()};
        }
    }

    $scope.cancel = function () {
        $scope.synopsisCopy = {language_id:0, content:'', language:''}
    }

    $scope.update = function (film_id) {
        if(!$scope.synopsisCopy.content.length){
            $scope.errors.content = 1;
            return
        }

        if($scope.synopsisCopy.content.length > 400){
            $scope.errors.content = 2;
            return;
        }

        $http.post('/archive/' + film_id + '/synopsis', $scope.synopsisCopy)
            .success(function (result) {
                $('#content_' + $scope.synopsisCopy.language_id).text($scope.synopsisCopy.content);

                $scope.cancel();
            })
    }

    $scope.save = function (film_id) {
        $scope.errors.newsynopsis = {invalid:0, language:0, content:0};
        if(!$scope.newsynopsis){
            $scope.errors.newsynopsis = {invalid:1, language:1, content:1};
            return;
        }

        if(!$scope.newsynopsis.language_id) {
            $scope.errors.newsynopsis.language = 1;
            $scope.errors.newsynopsis.invalid = 1;
        }
        else if($('#content_'+$scope.newsynopsis.language_id).length > 0){
            $scope.errors.newsynopsis.language = 2;
            $scope.errors.newsynopsis.invalid = 1;
        }
        if(!$scope.newsynopsis.content){
            $scope.errors.newsynopsis.content = 1;
            $scope.errors.newsynopsis.invalid = 1;
        }

        if($scope.newsynopsis.content.length > 400){
            $scope.errors.newsynopsis.content = 2;
            $scope.errors.newsynopsis.invalid = 1;
        }

        if($scope.errors.newsynopsis.invalid){
            return;
        }
        $http.post('/archive/' +  film_id + '/synopsis', $scope.newsynopsis)
            .success(function (result) {
                $scope.newsynopsis.id = result;
                $scope.newsynopsis.language = $('#newsynopsis_language option:selected').text();
                $scope.list.push($scope.newsynopsis);
                $('#opt_lang_'+ $scope.newsynopsis.language_id).attr('disabled', true);
                $scope.addNew = false;
                $scope.newsynopsis = null;
            })
    }

    $scope.deleteSynopsis  = function (id, json) {
        if($scope.synopsisCopy && id == $scope.synopsisCopy.id){
          $scope.cancel();
        }
        if(json) {
            var index = -1;
            for (var i = 0; i < $scope.list.length && index < 0; i++) {
                if ($scope.list[i].language_id == id) {
                    index = i;
                    $scope.synopsisToDelete = $scope.list[i];
                    $scope.synopsisToDelete.json = true;
                }
            }
        }
        else{
            $scope.synopsisToDelete = {language_id:id, language:$('#lang_'+id).text(), json:false};
        }

        $('#deleteModal').modal('show');
    }

    $scope.synopsisDeleted = function (film_id) {
        $http.delete('/archive/'+ film_id + '/synopsis/' + $scope.synopsisToDelete.language_id)
            .success(function (result) {
                $('#opt_lang_'+ $scope.synopsisToDelete.language_id).removeAttr('disabled');
                if($scope.synopsisToDelete.json){
                    $rootScope.removeValue($scope.list, $scope.synopsisToDelete.language_id, 'language_id');
                }
                else{
                    $('#synopsis_'+$scope.synopsisToDelete.language_id).remove();
                }

                $scope.synopsisToDelete = null;
                $('#deleteModal').modal('hide');
            })
            .error(function (msg) {
                $scope.errors = msg;
                $('#deleteModal').modal('hide');
                $('#errorModal').modal('show');
            })
    }
});
