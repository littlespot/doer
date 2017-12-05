appZooMov.controller("filmCtrl", function($rootScope, $scope,$http) {
    $scope.init = function (id, producers, school) {
       // $scope.makers = angular.fromJson(makers);
    //    $scope.contacts = angular.fromJson(contacts);
        $scope.producers = angular.fromJson(producers);
        $scope.film_id = id;
        $scope.school = school;
        $scope.makers = [];
        $scope.contacts = [];

        $scope.changeSchool($('input[name="school"]:checked').val());
    }

    $scope.changeSchool = function (val) {
        if(val){
            $('#school_name').removeAttr('disabled');
        }
        else{
            $('#school_name').attr('disabled', true);
        }
    }

    $scope.delete = function (id) {
        $http.delete('/film/producers/' + id)
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
