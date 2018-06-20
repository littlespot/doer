appZooMov.controller("filmCtrl", function($rootScope, $scope,$http) {
    $scope.delete = function (id) {
        $http.delete('/movie/directors/' + id)
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
