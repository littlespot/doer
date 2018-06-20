appZooMov.controller("festivalCtrl", function($rootScope, $scope, $http) {
    $scope.init = function(festival){
        $scope.festivals = angular.fromJson(festival);
        $scope.pagination = $rootScope.setPage(festival);
        $scope.params = {status:-1, script: -1, genre:-1, page:$scope.pagination.currentPage};
        $rootScope.loaded();
    }

    $scope.display = function(param, val){
        if(param){
            $scope.params[param] = ($scope.params[param] == val) ? -1 : val;
        }
        else{
            angular.forEach($scope.params, function(value, key){
                $scope.params[key] = -1;
            })
        }
        $rootScope.loading();
        $http({
                method: "get",
                params:$scope.params,
                url:'/my/festivals'
            })
            .success(function(result){
                $scope.festivals = result;
                $scope.pagination = $rootScope.setPage(result);
                $rootScope.loaded();
            })
    }

    $scope.toggleFavorite = function (festival) {
        $http({
            method: "put",
            url:'/festivals/'+ festival.id
        })
            .success(function(){
               $rootScope.removeValue($scope.festivals, festival.id);
                $rootScope.loaded();
            })
    }

    $scope.pageChanged = function(page){
        $scope.params.page = page;
    }
});
