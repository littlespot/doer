appZooMov.controller("filmCtrl", function($rootScope, $scope,$http) {
    $scope.init = function (id, sellers) {
        $scope.sellers = angular.fromJson(sellers);
        $scope.festival = {};
        $scope.diffusion ={};
        $scope.theater = {};
        $scope.film_id = id;
        $scope.contacts = [];
        $scope.festivals = [];
        $scope.diffusions = [];
        $scope.theaters = [];
    }

    $scope.check = function (name) {
        if($('input[name="'+name+'"]').is(":checked")){
            $('#tb_'+name).show();
        }
        else{
            $('#tb_'+name).hide();
        }
    }
    $scope.setHistory = function (name) {
        $scope.history = name
    }
    $scope.cancelHistory = function () {
        $scope.history = '';
    }

    $scope.festivalCountry = function () {
        if($scope.festival.country_id){
            $scope.festival.country = $('#f_country_'+$scope.festival.country_id).text();
            $http.get('/country/'+$scope.festival.country_id)
                .success(function (result) {
                    $scope.fcities = result;
                })
        }
        else{
            $scope.fcities = []
        }
    }

    $scope.diffusionCountry = function () {
        $scope.diffusion.country = $('#d_country_'+$scope.diffusion.country_id).text();
    }

    $scope.theaterCountry = function () {
        $scope.theater.country = $('#c_country_'+$scope.theater.country_id).text();
    }

    $scope.saveReward = function () {
        if(!$scope.festival.rewards)
            $scope.festival.rewards = [];
        $scope.festival.rewards.push(angular.copy($scope.festival.reward));
        $scope.festival.reward = '';
    }

    $scope.saveHistory = function (name) {
        var data = angular.copy($scope[name]);
        $http.post('/film/'+$scope.film_id+ '/'+name, data)
            .success(function (result) {
                data['id'] = result;
                $scope[name+'s'].push(data);
                $scope.history = '';
            })
    }


    $scope.delete = function (name, id) {
        $http.delete('/film/' + name + '/' + id)
            .success(function (result) {
                var data = $scope[name];
                var found = -1;
                for (var i = 0; i < data.length && !found; i++) {
                    if(data[i].id == result){
                        found = i;
                    }
                }
                if(found >= 0)
                    $scope[name].splice(found, 1);
                else
                    $('#'+name +"_" + id).remove();
            })
    }

    $scope.festivalSelected = function (selected) {
        if(selected.title){
            $scope.festival.city_id = selected.originalObject.id;
            $scope.festival.city = selected.originalObject.name;
        }
    }
});
