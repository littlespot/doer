appZooMov.controller("filmCtrl", function($rootScope, $scope, $http) {

    $scope.init = function (dialogs, production, shooting) {
        $scope.doubleValue = {id:'', name:''};
        if(production){
            $scope.productions = angular.fromJson(production);
        }
        else{
            $scope.productions = [];
        }


        if(shooting){
            $scope.shootings = angular.fromJson(shooting);
        }
        else{
            $scope.shootings = [];
        }

        if(dialogs){
            $scope.dialogs = angular.fromJson(dialogs);
        }
        else{
            $scope.dialogs = [];
        }

        $rootScope.loaded();
    }

    $scope.editCountry = function () {
        $scope.countryEdited = $scope.principal;
    }

    $scope.cancelCountry = function () {
        $scope.countryEdited = null;
    }

    $scope.changeCountry = function (film_id) {
        var country_id = $('#nation_principal').val();

        if($scope.principal == country_id)
            return false;

        $http.put('/archive/'+ film_id + '/country', {'country_id': country_id})
            .success(function (result) {
                $('#opt_country_'+ $scope.principal).removeAttr('disabled');
                $('#production_country_'+ $scope.principal).removeAttr('disabled');
                $('#opt_country_'+ country_id).attr('disabled', true);
                $('#production_country_'+ country_id).attr('disabled', true);
                $scope.principal = country_id;


                $scope.country_name =  $('#nation_principal option:selected').text();
                $scope.countryEdited = null;
            })
            .error(function (err) {
                $scope.country_id = null;
            });
    }

    $scope.addProduction = function (film_id) {
        var country_id = $('#nation_productions').val();
        if(!country_id)
            return false;
        angular.forEach($scope.productions, function (production, key) {
            if(key == country_id && production){
               $scope.doubleValue = {id:'p', name:production};
               $('#alertAddModal').modal('show');
            }
        });

        if($scope.productionToAdd){
            return false;
        }
        $http.post('/archive/'+ film_id + '/productions', {'country_id': country_id})
            .success(function (result) {
                $scope.productions.push({id:country_id, name:$('#production_country_'+country_id).text(), order:result});
                $('#opt_country_'+ country_id).attr('disabled', true);
                $('#production_country_'+ country_id).attr('disabled', true);
            })
            .error(function (err) {
            });
    }

    $scope.removeProduction = function (id, name) {
        $scope.productionToDelete = {id:id, name:name};
        $('#deleteConfirmModal').modal('show');
    }

    $scope.productionDeleted = function (film_id) {
        $http.delete('/archive/'+ film_id + '/productions/' + $scope.productionToDelete.id)
            .success(function () {
                $('#opt_country_'+ $scope.productionToDelete.id).removeAttr('disabled');
                $('#production_country_'+ $scope.productionToDelete.id).removeAttr('disabled');
                $rootScope.removeValue($scope.productions, $scope.productionToDelete.id);
                $scope.productionToDelete = null;
                $('#deleteConfirmModal').modal('hide');
            })
            .error(function (err) {
                $scope.productionToDelete = null;
                $('#deleteConfirmModal').modal('hide');
            });
    }

    $scope.addShooting = function (film_id) {
        var country_id = $('#nation_shootings').val();
        if(!country_id)
            return false;
        angular.forEach($scope.shootings, function (shooting, key) {
            if(key == country_id && shooting){
                $scope.doubleValue = {id:'s', name:shooting};
                $('#alertAddModal').modal('show');
            }
        });

        if($scope.shootingToAdd){
            return false;
        }
        $http.post('/movie/'+ film_id + '/shootings', {'country_id': country_id})
            .success(function (result) {
                $scope.shootings.push({id:country_id, name:$('#shooting_country_'+country_id).text(), order:result});
                $('#shooting_country_'+ country_id).attr('disabled', true);
            })
            .error(function (err) {
            });
    }

    $scope.removeShooting = function (id, name) {
        $scope.shootingToDelete = {id:id, name:name};
        $('#deleteConfirmModal').modal('show');
    }

    $scope.shootingDeleted = function (film_id) {
        $http.delete('/movie/'+ film_id + '/shootings/' + $scope.shootingToDelete.id)
            .success(function () {
                $('#shooting_country_'+ $scope.shootingToDelete.id).removeAttr('disabled');
                $rootScope.removeValue($scope.shootings, $scope.shootingToDelete.id)
                $scope.shootingToDelete = null;
                $('#deleteConfirmModal').modal('hide');
            })
            .error(function (err) {
                $scope.shootingToDelete = null;
                $('#deleteConfirmModal').modal('hide');
            });
    }

    $scope.addDialog = function (film_id) {
        var language_id = $('#dialog_lang').val();
        if(!language_id)
            return false;
        angular.forEach($scope.dialogs, function (language, key) {
            if(key == language_id && language){
                $scope.doubleValue = {id:'d', name:language};
                $('#alertAddModal').modal('show');
            }
        });

        if($scope.languageToAdd){
            return false;
        }
        $http.post('/archive/'+ film_id + '/languages', {'language_id': language_id})
            .success(function (result) {
                $scope.dialogs.push({id:language_id, name:$('#dialog_' + language_id).text(), order:result});
                $('#dialog_'+ language_id).attr('disabled', true);
            })
            .error(function (err) {
            });
    }

    $scope.removeDialog= function (dialog) {
        $scope.dialogToDelete = dialog;
        $('#deleteConfirmModal').modal('show');
    }

    $scope.dialogDeleted = function (film_id) {
        $http.delete('/archive/'+ film_id + '/languages/' + $scope.dialogToDelete.id)
            .success(function () {
                $('#dialog_'+ $scope.dialogToDelete.id).removeAttr('disabled');
                $rootScope.removeValue($scope.dialogs, $scope.dialogToDelete.id);
                $scope.dialogToDelete = null;
                $('#deleteConfirmModal').modal('hide');
            })
            .error(function (err) {
                $scope.dialogToDelete = null;
                $('#deleteConfirmModal').modal('hide');
            });
    }

    $scope.editConlange = function () {
        $scope.conlangeEdited = true;
    }
    $scope.changeConlange = function (film_id) {
        $http.put('/archive/'+ film_id + '/conlange', {'conlange': $('#conlange').val()})
            .success(function (result) {
                $scope.conlange = result;
                $scope.conlangeEdited = null;
            })
            .error(function (err) {
                $scope.conlangeEdited = null;
            });
    }
    
    $scope.removeConlange = function (film_id) {
        $http.put('/archive/'+ film_id + '/conlange')
            .success(function (result) {
                $scope.conlange = result;
                $scope.conlangeEdited = null;
            })
            .error(function (err) {
                $scope.conlangeEdited = null;
            });
    }

    $scope.changeSound = function (val) {

        if(val > 0){
            $("#block_lang").show();
        }
        else{
            $("#block_lang").hide();
        }
    }
});
