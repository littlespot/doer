appZooMov.controller("festivalCtrl", function($rootScope, $scope, $http) {
    $scope.film_index = 0;
    $scope.film_shown_count = 10;
    $scope.init = function (rule_id, film_id) {
        $scope.rule_id = rule_id;
        $scope.chooseFilm(film_id)
    }

    $scope.upFilmIndex = function (max) {
        if($scope.film_index + $scope.film_shown_count < max){
            $scope.film_index += $scope.film_shown_count;
        }
    }

    $scope.downFilmIndex = function () {
        if($scope.film_index > $scope.film_shown_count){
            $scope.film_index -= $scope.film_shown_count;
        }
        else if($scope.film_index > 0){
            $scope.film_index = 0;
        }
    }

    $scope.chooseFilm = function (id) {
        $scope.film_id = id;
        $scope.film_title = $('#film_'+id).text();
        $http.post('/rules/'+$scope.rule_id, {'film_id':id})
            .success(function (rules) {
                $scope.rules = rules;
                $rootScope.loaded();
            }).
            error(function (errors) {
                $scope.errors = errors;
                $rootScope.loaded();
            })
    }

    $scope.findInArray = function (arr, val) {
        if(!arr)
            return -1;
        for(var i = 0; i < arr.length; i++){
            if(arr[i] == val){
                return i;
            }
        }

        return -1;
    }
})