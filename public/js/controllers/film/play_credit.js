appZooMov.controller("filmCtrl", function($rootScope, $scope, $http, $filter) {
    $scope.init = function () {
        $scope.errors = {filmaker:{another:null}, credit:false};
        $rootScope.loaded();
    }

    $scope.creditChosen = function (creditNew) {
        if(!$scope.creditSelected){
            $scope.errors.credit = true;
            return;
        }
        var found = false;
        angular.forEach($scope.creditSelected, function (item) {
            found |= item;
        })
       if(!found){
           $scope.errors.credit = true;
            return;
       }
        $scope.errors.credit = false;
        if(creditNew){
            if(!$scope.users){
                $http.get('/users')
                    .success(function (data) {
                        $scope.users = data;
                        $('#newMakerModal').modal('show');
                    })
                    .error(function (err) {

                    })
            }
            else{
                $('#newMakerModal').modal('show');
            }
        }
        else{
            $scope.chooseFromList();
        }
    }

    $scope.chooseFromList = function () {
        $scope.errors.filmaker.another = null;
        $scope.makerSelected = [];
        if (!$scope.persons) {
            $scope.persons = []
            $http.get('/archive/makers')
                .success(function (data) {
                    $scope.persons = data;
                    $('#makerListModal').modal('show');
                })
                .error(function (err) {
                })
        }
        else{
            $('#makerListModal').modal('show');
        }
    }
    
    $scope.updateCredit = function (maker) {
        if(maker.web){
            maker.web = $rootScope.checkUrl(maker.web);
        }
        maker.last_name = maker.last_name.toString().toUpperCase();
        $http.put('/filmaker/' + maker.filmaker_id, maker)
            .success(function (result) {
                maker.username = $('#searchUser option:selected').text();
                maker.country = $('#nationality_'+maker.id+' option:selected').text();
                $rootScope.updateValue($scope.adapters, maker, 'filmaker_id');
                $rootScope.updateValue($scope.producers, maker, 'filmaker_id');
                $rootScope.updateValue($scope.directors, maker, 'filmaker_id');
                maker = null;
                $('#editMakerModal').modal('hide');
            })
    }
    $scope.editMaker = function (maker) {
        $scope.makerCopy = angular.copy(maker);
        if(!$scope.makerPool){
            $http.get('/filmaker')
                .success(function (data) {
                    $scope.makerPool = data;
                })
                .error(function (err) {
                })
        }
        if(!$scope.users){
            $http.get('/users')
                .success(function (data) {
                    $scope.users = data;
                })
                .error(function (err) {
                })
        }
        $('#editMakerModal').modal('show');
    }
    $scope.creditSaved = function (film_id) {
        var persons = $filter('filter')($scope.persons, {selected:true});
        var makers = [];

        $http.put('/play/' + film_id + '/credit', {credits:$scope.creditSelected, makers:makers})
            .success(function (data) {
                if($scope.creditSelected['adapters']) {
                    $scope.creditSelected['adapters'] = false;
                    angular.merge($scope.adapters, persons);
                }

                if($scope.creditSelected['directors']) {
                    $scope.creditSelected['directors'] = false;
                    angular.merge($scope.directors,persons);
                }

                if($scope.creditSelected['producers']) {
                    $scope.creditSelected['producers'] = false;
                    angular.merge($scope.producers,persons);
                }

                $('#makerListModal').modal('hide');
            })
    }

    $scope.creditCreated = function (film_id) {
        if($scope.newMaker.web){
            $scope.newMaker.web = $rootScope.checkUrl($scope.newMaker.web);
        }
        $http.post('/filmaker', {film_id:film_id, positions:$scope.creditSelected, maker:$scope.newMaker})
            .success(function (maker) {
                $scope.newMaker.id = maker.id;
                $scope.newMaker.filmaker_id = maker.id;
                if($scope.newMaker.related_id){
                    $scope.newMaker.username = $('#newmaker_related option:selected').text();
                }
                $scope.newMaker.country = $('#nationality option:selected').text();
                angular.forEach($scope.creditSelected, function (item, key) {
                    $scope[key].push(angular.copy($scope.newMaker));
                })

                $scope.newMaker = null;
                $('#newMakerModal').modal('hide');
            })
    }
    
    $scope.deleteCredit = function (type, label, maker) {
        $scope.creditToDelete = {type:type, label:label, maker:maker}
        $('#deleteCreditModal').modal('show');
    }

    $scope.creditDeleted = function (film_id) {
        $http.delete('/play/' + film_id + '/credit', {params:{type:$scope.creditToDelete.type, filmaker_id:$scope.creditToDelete.maker.filmaker_id}})
            .success(function () {
                $rootScope.removeValue($scope[$scope.creditToDelete.type], $scope.creditToDelete.maker.filmaker_id, 'filmaker_id');
                $('#deleteCreditModal').modal('hide');
            })
    }
});
