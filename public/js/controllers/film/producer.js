appZooMov.controller("filmCtrl", function($rootScope, $scope,$http) {
    $scope.delete = function (id) {
        $http.delete('/archive/producers/' + id)
            .success(function (result) {
                var data = $scope.producers;
                var found = 0;
                for (var i = 0; i < data.length && !found; i++) {
                    if(data[i].id == result){
                        found = i;
                    }
                }

                $scope.producers.splice(found, 1);
            })
    }
});
