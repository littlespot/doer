appZooMov.controller("filmCtrl", function($rootScope, $scope,$http) {
    $scope.init = function (id, directors) {
       // $scope.makers = angular.fromJson(makers);
    //    $scope.contacts = angular.fromJson(contacts);
        $scope.directors = angular.fromJson(directors);
        $scope.film_id = id;
        $scope.makers = [];
        $scope.contacts = [];
    }

    $scope.delete = function (id) {
        $http.delete('/film/directors/' + id)
            .success(function (result) {
                var data = $scope.directors;
                var found = 0;
                for (var i = 0; i < data.length && !found; i++) {
                    if(data[i].id == result){
                        found = i;
                    }
                }

                $scope.directors.splice(found, 1);
            })
    }


});
