appZooMov.controller("filmCtrl", function($rootScope, $scope, $http) {
    $scope.init = function (titles) {
        $scope.titles = angular.fromJson(titles);
        $scope.cancelTitle();
        $scope.error = {title:0, title_latin:0, title_inter:0, trans:0, lang:0}
        $rootScope.loaded();
    }

    $scope.changeTitle = function (t) {
        $scope.editTitle = angular.copy(t);
    }

    $scope.cancelTitle = function () {
        $scope.editTitle = {language_id:0, title:''};
    }

    $scope.saveTitle = function (film_id, t) {
       t.title = $scope.editTitle.title;
        $http.post('/archive/'+ film_id + '/title', $scope.editTitle)
            .success(function (result) {
                if(result){
                    t = angular.copy($scope.editTitle);
                    t.id = result;
                }
                $scope.editTitle = '';
            })
            .error(function (errors) {
                $scope.error.trans = errors;
                $scope.editTitle = '';
            })
    }

    $scope.deleteTitle = function (t) {
        $scope.titleToDelete = t;
        $('#deleteTitleModal').modal('show');
    }
    
    $scope.titleDeleted = function (film_id) {
        $http.delete('/archive/'+ film_id + '/title/' + $scope.titleToDelete.language_id)
            .success(function (result) {
                var found = -1;
                for(var i = 0; i<$scope.titles.length && found < 0; i++){
                    if($scope.titles[i].id == $scope.titleToDelete.id){
                        found = i;
                    }
                }

                if(found >= 0 )
                    $scope.titles.splice(found, 1);

                $scope.titleToDelete = '';

                $('#deleteTitleModal').modal('hide');
            })
            .error(function (errors) {
                $scope.error.trans = errors;
                $scope.titleToDelete = '';
            })

    }

    $scope.addTitle = function (film_id) {
        for(var i = 0; i<$scope.titles.length; i++){
            if($scope.titles[i].language_id == $scope.newTitle.language_id){
                $scope.error.trans = 0;
                $scope.error.lang = 2;
                return false;
            }
        }

        $scope.error.trans = 0;
        $scope.error.lang = 0;
        $http.post('/archive/'+ film_id + '/title', $scope.newTitle)
            .success(function (result) {
                $scope.newTitle.language = $('#lang_trans option:selected').text();
                $scope.titles.push(angular.copy($scope.newTitle));
                if(!result){
                    $scope.error.lang = 2;
                }
                $scope.newTitle = null;
            })
            .error(function (errors) {
                $scope.error.trans = errors;
                $scope.newTitle = null;
            })
        return false;
    }

    $scope.save = function (invalid) {
        if(invalid){
            return false
        }

        if(!$scope.title || $scope.title.length > 40 || $scope.title.length == 0){
            return false;
        }

        $('#film_form').submit();
    }
});
