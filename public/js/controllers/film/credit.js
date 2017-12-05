appZooMov.controller("filmCtrl", function($rootScope, $scope,$http) {
    $scope.init = function (id) {
        $scope.film_id = id;
        $scope.makers = [];
        $scope.maker = {};
        $scope.errors = {credits:0};
    }

    $scope.makerFocus = function () {
        if (!$scope.makers.length) {
            $scope.searchOn = true;
            $http.get('/makers')
                .success(function (data) {
                    $scope.makers = data;
                    $scope.searchOn = false;
                })
                .error(function (err) {
                    $scope.searchOn = false;
                })
        }
    }

    $scope.makerSelected = function (selected) {
        if (selected.title) {
            $scope.maker = selected.originalObject;
        }
    }

    $scope.save = function () {
        $scope.maker.last_name = $scope.maker.last_name.toString().toUpperCase();
        var data = angular.copy($scope.maker);
        $scope.maker.credits = [];
        $('input[name="credits"]:checked').each(function () {
            $scope.maker.credits.push($(this).val())
        });
        if($scope.maker.credits.length == 0){
            $scope.errors.credits = 1;
            return false;
        }
        else{
            $scope.errors.credits = 0;
        }

        $http.post('/film/' + $scope.film_id + '/credit', $scope.maker)
            .success(function (result) {
                data['id'] = result;
                var name = data.last_name + ' ' + data.first_name;
                var credits = $scope.maker.credits;
                var len = credits.length;
                for(var i = 0; i <len; i++) {
                    var text = $('#td_' + credits[i]).text();
                    if(text.length > 0){
                        $('#td_' + credits[i]).text(text + ',' + name);
                    }
                    else{
                        $('#td_' + credits[i]).text(name);
                    }
                }
                $scope.maker = {};
                $scope.edit = 0;;
            })
    }

    $scope.cancel = function () {
        $scope.edit = 0;
    }
});
