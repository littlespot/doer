appZooMov.controller("filmCtrl", function($rootScope, $scope,$http, $filter) {
    $scope.init = function () {
        $scope.festival = {};
        $scope.diffusion ={};
        $scope.theater = {};
        $scope.contacts = [];
        $scope.festivals = [];
        $scope.diffusions = [];
        $scope.theaters = [];
        $rootScope.loaded();
    }

    $scope.check = function (name) {
        var val = $('input[name="'+name+'"]:checked').val();
        if(val > 0)
        {
            $('#tb_'+name).show();
        }
        else{
            $('#tb_'+name).hide();
        }
    }

    $scope.editHistory = function (name, val, json) {
        if(!val){
            $scope.history = {id:0, rewards:[]}
        }
        else if(json){
            $scope.history = angular.copy(val);
        }
        else{
            $scope.history = angular.fromJson(val);
        }

        if( $scope.history.city_id){
            $http.get('/locations/' +  $scope.history.city_id)
                .success(function (result) {
                    $scope.contactDepartments = result.departments;
                    $scope.contactCities = result.cities;
                });
        }
        else if( $scope.history.department_id){
            $http.get('/departCities/' +  $scope.history.department_id)
                .success(function (result) {
                    $scope.contactDepartments = result.departments;
                    $scope.contactCities = result.cities;
                });
        }
        else if( $scope.history.country_id){
            $http.get('/departments/' +  $scope.history.country_id)
                .success(function (result) {
                    $scope.contactDepartments = result;
                    $scope.contactCities = [];
                });
        }
        else{
            $scope.contactDepartments = [];
            $scope.contactCities = [];
        }
        $scope.history.type = name;
        $('#'+name+'NewModal').modal('show');
    }

    $scope.deleteHistory = function (name, id){
        $scope.historyToDelete = {name:name, id:id};
        $('#historyDeleteModal').modal('show');
    }

    $scope.historyDeleted = function (film_id) {
        var opt = $scope.historyToDelete.name;
        $http.delete('/movie/'+film_id + '/' +opt+'/' +$scope.historyToDelete.id)
            .success(function () {
                if(!$rootScope.removeValue($scope[opt+'s'], $scope.historyToDelete.id)){
                    $('#'+opt+'s_' + $scope.historyToDelete.id).remove();
                }
                $scope.historyToDelete = null;
                $('#historyDeleteModal').modal('hide');
            })
    }
    $scope.historySaved = function(film_id){
        var name = $scope.history.type;

        $http.post('/movie/' + film_id + '/'+ name, $scope.history)
            .success(function (result) {
                $scope.history.country = $('#'+ name + '_country option:selected').text();

                if(name == 'festival')
                    $scope.history.city = $('#' + name + '_city option:selected').text();
                else if(name == 'theater')
                    $scope.history.program_name = $('#' + name + '_program option:selected').text();
                else
                    $scope.history.channel_name = $('#diffusion_channel option:selected').text();
                if($scope.history.id){
                    if(name == 'festival'){
                        if(!result){
                            $scope.history.rewards = [];
                        }
                        else if(result.rewards){
                            $scope.history.rewards = result.rewards;
                        }
                    }
                   var tr = $('#'+ name + 's_' + $scope.history.id);
                   if(tr.length > 0){
                       tr.remove();
                       $scope[name+'s'].push(angular.copy($scope.history));
                   }
                   else{
                       $rootScope.setValue($scope[name + 's'], angular.copy($scope.history))
                   }
               }
               else{
                   $scope.history.id = result.id;

                   if(name == 'festival')
                       $scope.history.rewards = result.rewards;
                   $scope[name+'s'].push(angular.copy($scope.history));
               }

                $('#'+name+'NewModal').modal('hide');
            })
            .error(function (err) {
                $scope.errors[name] = err.message;
            })
    }

    $scope.addReward = function () {
        var reward = {id:0, name:'', competition:false, edited:1};
        $scope.history.rewards.push(reward);
    }

    $scope.editReward = function (reward) {
        $scope.rewardCopy = angular.copy(reward);
        reward.edited = 1;
        reward.competition = reward.competition == 1;
    }

    $scope.cancelReward = function (r) {
        if(r.edited){
            $rootScope.removeValue($scope.history.rewards, 1, 'edited');
        }
        else{
            $scope.rewardCopy = 0;
        }
    }

    $scope.saveReward = function (r) {
        if(r.name && r.name.length < 80){
            r.edited = 0;
            $scope.rewardCopy = null;
        }
    }

    $scope.diffusionCountry = function () {
        $scope.diffusion.country = $('#d_country_'+$scope.diffusion.country_id).text();
    }

    $scope.theaterCountry = function () {
        $scope.theater.country = $('#c_country_'+$scope.theater.country_id).text();
    }

    $scope.delete = function (name, id) {
        $http.delete('/movie/' + name + '/' + id)
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

    $scope.save = function (fcnt, dcnt, tcnt) {
        $scope.confirmed = true;
        $scope.confirmation = {festivals:0, diffusion:0, theaters:0};
        if(!$scope.in_festivals && fcnt > 0){
            $scope.confirmed &= false;
            $scope.confirmation.festivals = 1;
        }

        if(!$scope.in_diffusion && dcnt > 0){
            $scope.confirmed &= false;
            $scope.confirmation.diffusion = 1;
        }

        if(!$scope.in_theater && tcnt >0){
            $scope.confirmed &= false;
            $scope.confirmation.theaters = 1;
        }

        if($scope.confirmed){
            $scope.submit();
        }
        else{
            $('#sellerConfirmModal').modal('show');
        }
    }

    $scope.submit = function () {
        $('#filmForm').submit();
    }
});
