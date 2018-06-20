appZooMov.controller("filmCtrl", function($rootScope, $scope, $http, $filter) {
    $scope.init = function (credits) {
        $scope.credits = angular.fromJson(credits);
        $rootScope.loaded();
    }

    $scope.creditChosen = function () {
        $('#creditListModal').modal('hide');
        if($scope.creditNew){
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

    $scope.chooseCredit = function (createNew) {
        $scope.creditNew = createNew;
        var credits = $filter('filter')($scope.credits, {selected:true});
        if(credits && credits.length)
            $scope.creditChosen()
        else
            $('#creditListModal').modal('show');
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
                if(maker.related_id){
                    var person = $filter('getById')($scope.users, maker.related_id);
                    maker.username = person.username;
                }

                maker.country = $('#nationality_edited option:selected').text();
                angular.forEach($scope.credits, function (item) {
                    var index = -1;
                    for(var i = 0; i < item.makers.length && index < 0; i++){
                        if(item.makers[i].filmaker_id == maker.filmaker_id){
                            item.makers[i] = maker;
                        }
                    }
                })
                maker = null;
                $('#editMakerModal').modal('hide');
            })
    }

    $scope.creditSaved = function (film_id) {
        var makerSelected = [];
        angular.forEach($scope.persons, function (item) {
            if(item.selected){
                makerSelected.push(item);
                item.selected = false;
            }
        })
        var credits = [];
        angular.forEach($scope.credits, function (item) {
            if(item.selected){
                credits.push(item.id);
                item.selected = false;
            }
        })
        $http.post('/movie/' + film_id + '/credit', {credits:credits, makers:makerSelected})
            .success(function (data) {
                angular.forEach(data, function (result, key) {
                    var credit = $filter('getById')($scope.credits, key);
                    angular.forEach(result, function (val) {
                        var person = $filter('getById')(makerSelected, val.maker_id);
                        if(person){
                            var maker = angular.copy(person);
                            maker.filmaker_id = val.maker_id;
                            maker.cast_id = val.cast_id;
                            maker.film_credit_id = val.id;
                            if(!credit.makers){
                                credit.makers = [maker];
                            }
                            else{
                                credit.makers.push(maker);
                            }
                        }
                    });
                })
                $('#makerListModal').modal('hide');
            })

    }
    $scope.creditSelected = function (id) {
        var credit = $filter('getById')($scope.credits, id);
        credit.selected = !credit.selected;
    }
    $scope.creditCreated = function (film_id) {
        var credits = [];
        angular.forEach($scope.credits, function (item, key) {
            if(item.selected){
                credits.push(item.id);
                item.selected = false;
            }
        });
        if(!credits){
            $('#newMakerModal').modal('hide');
        }
        if($scope.newMaker.web){
            $scope.newMaker.web = $rootScope.checkUrl($scope.newMaker.web);
        }
        $http.put('/movie/credit', {'film_id':film_id, credits:credits, maker:$scope.newMaker})
            .success(function (data) {
                $scope.newMaker.filmaker_id = data.maker_id;
                if($scope.newMaker.related_id){
                    $scope.newMaker.username = $('#newmaker_related option:selected').text();
                }
                angular.forEach(data.credits, function (val) {
                    var credit = $filter('getById')($scope.credits, val.credit_id);
                    var maker = angular.copy($scope.newMaker);
                    maker.cast_id = val.cast_id;
                    maker.film_credit_id = val.film_credit_id;
                    maker.country = $('#nationality option:selected').text();
                    if(credit.makers){
                        credit.makers.push(maker);
                    }
                    else{
                        credit.makers = [maker];
                    }
                })
                $('#newMakerModal').modal('hide');
            })
    }
    
    $scope.deleteCredit = function (credit, label, maker) {
        $scope.creditToDelete = {credit_id:credit, label:label, maker:maker}
        $('#deleteCreditModal').modal('show');
    }

    $scope.creditDeleted = function (film_id) {
        var maker ={
            credit_id: $scope.creditToDelete.credit_id,
            maker_id: $scope.creditToDelete.maker.filmaker_id
        };
        $http.delete('/movie/' + film_id + '/credit', {params:maker})
            .success(function (result) {
                if(result){
                    var credit = $filter('getById')($scope.credits, $scope.creditToDelete.credit_id);
                    $rootScope.removeValue(credit.makers, $scope.creditToDelete.maker.filmaker_id, 'filmaker_id');
                }

                $('#deleteCreditModal').modal('hide');
            })
    }
});
