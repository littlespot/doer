appZooMov.controller("filmCtrl", function($rootScope, $scope) {
    $scope.init = function (titles) {
        $scope.titles = angular.fromJson(titles);
        $scope.cancelTitle();
        $scope.error = {title:0, lang:0}
        $rootScope.loaded();
    }

    $scope.changeTitle = function (t) {
        $scope.editTitle = angular.copy(t);
    }

    $scope.cancelTitle = function () {
        $scope.editTitle = {language_id:0, title:''};
    }

    $scope.saveTitle = function (t) {
       t.title = $scope.editTitle.title;
       $scope.cancelTitle();
    }

    $scope.deleteTitle = function (t) {
        var found = -1;
        for(var i = 0; i<$scope.titles.length && found < 0; i++){
            if($scope.titles[i].language_id.equalsContent(t.language_id)){
                $scope.error = {title:false, lang:2};
                found = i;
            }
        }

        if(found >= 0 )
            $scope.titles.splice(found, 1);
    }

    $scope.addTitle = function () {
        if(!$scope.newTitle || $scope.newTitle.length < 1 || $scope.newTitle.length > 80){
            if(!$scope.langSelected)
                $scope.error = {title:(!$scope.newTitle || $scope.newTitle.length) ? 1 : 2, lang:1};
            else
                $scope.error = {title:(!$scope.newTitle || $scope.newTitle.length) ? 1 : 2, lang:0};

            return false;
        }

        if(!$scope.langSelected){
            $scope.error = {title:0, lang:1};
            return false;
        }


        for(var i = 0; i<$scope.titles.length && !found; i++){
            if($scope.titles[i].language_id.equalsContent($scope.langSelected.originalObject.id)){
                $scope.error = {title:0, lang:2};
                return false;
            }
        }

        $scope.error.lang = {title:0, lang:0};

        $scope.titles.push({language:$scope.langSelected.originalObject.name, language_id:$scope.langSelected.originalObject.id, title:$scope.newTitle});

        $scope.newTitle = '';
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
