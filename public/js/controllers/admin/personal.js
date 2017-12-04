/**
 * Created by Jieyun on 06/07/2016.
 */

appZooMov.controller("personalCtrl", function($rootScope, $scope, $http, $cookieStore, $filter, $uibModal, $window, $timeout, sexes) {
    $scope.disabled = {depart:false, city:false};
    $scope.init = function (id, city, birthday, occupations) {
        $rootScope.loaded();

        $scope.user = {id:id, city_id: parseInt(city), birthday:new Date(birthday), opened:false};
        $scope.occupations = angular.fromJson(occupations);
        $scope.sexes = sexes;
        $scope.error = {username:null, presentation:null};
    }

    $scope.openCalendar = function () {
        $scope.user.opened = true;
    }

    $scope.selectTab = function () {
        if(!$scope.sns){
            $http.get('/sns')
                .success(function (data) {
                    $scope.sns = data;
                })
        }
    }
    $scope.removeTalent = function(occupation){
        occupation.old = 0;
    }

    $scope.addTalent = function(newTalent){
        if(newTalent)
            newTalent.old = 1;
    }

    $scope.addSns = function(type){
        if(!$scope.chosenSns || !$scope.chosenSns.type.equals(type)){
            var index = -1;
            for(var i = 0; index < 0 && i < $scope.sns[type].length; i++){
                if(!$scope.sns[type][i].sns_id){
                    index = i;
                    $scope.chosenSns = $scope.sns[type][i];
                }
            }
        }
        else{
            $http.post('/sns', $scope.chosenSns)
                .success(function (id) {
                    $scope.chosenSns.sns_id = id;
                    $scope.chosenSns = null;
                })
                .error(function(err){
                    alert(err);
                })
        }
    }

    $scope.updateSns = function(sns){
        if(!$scope.editedSns || !$scope.editedSns.sns_id.equals(sns.sns_id)){
            $scope.editedSns = sns;
        }
        else{
            $http.put('/sns/' + sns.sns_id, $scope.editedSns)
                .success(function (data) {
                    $scope.editedSns = null;
                })
                .error(function (err) {
                    alert(err);
                });
        }
    }

    $scope.cancelSns = function(sns){
        if($scope.editedSns && $scope.editedSns.sns_id.equals(sns.sns_id)){
            $scope.editedSns = null;
        }
        else{
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'confirm.html',
                controller: function($scope) {
                    $scope.confirm = 'confirmS';
                }
            });

            modalInstance.result.then(function (confirm) {
                if (!confirm)
                    return false;

                $http.delete('/sns/' + sns.sns_id)
                    .success(function () {
                        sns.sns_id = null;
                        sns.sns_name = null;
                    })
                    .error(function (err) {
                        alert(err);
                    })
            })
        }
    }

    $scope.save = function(){
        var count = $("#roles input[name^='role']").length;
        if(!count)
            return;

        var username = $("#username").val();
        if(username.length < 2){
            $scope.error.username = 'i';
            return;
        }
        else if(username.length > 40){
            $scope.error.username = 'm';
            return;
        }
        else{
            $scope.error.username = null;
        }

        var presentation = $("#presentation").val();
        if(presentation.length < 10){
            $scope.error.presentation = 'i';
            return;
        }
        else if(presentation.length > 800){
            $scope.error.presentation = 'm';
            return;
        }
        else{
            $scope.error.presentation = null;
        }

        $.ajax({
            url: '/account',
            type: 'POST',
            data: $('#usrform').serialize(),
            success: function() {
                $uibModal.open({
                    animation: true,
                    templateUrl: 'alert.html',
                    controller: function($scope) {
                        $scope.alert = 'info';
                    }
                }).closed.then(function () {
                    window.location.href = '/';
                });
            },
            error:function (err) {
                $uibModal.open({
                    animation: true,
                    templateUrl: 'alert.html',
                    controller: function($scope) {
                        $scope.alert = 'active';
                        $scope.errors = $.parseJSON(err.responseText);
                    }
                }).closed.then(function () {
                });
            }
        });
    }

    $scope.changePwd = function(pwd){
        if(pwd.old.equals(pwd.new)){
            $scope.result = 'samepwd';
            $scope.openModal(true);
        }
        else {
            $http.put('/account/'+pwd.old, pwd)
                .success(function (data) {
                    $uibModal.open({
                        animation: true,
                        templateUrl: 'alert.html',
                        controller: function($scope) {
                            $scope.alert = 'pwd';
                        }
                    }).closed.then(function () {
                        window.location.href = '/';
                    });
                }).error(function (err) {
                    $scope.error = err;
                }
            );
        }
    }

    $scope.choseSns = function (s) {
        $scope.chosenSns = s;
    }
    $scope.regex = /^(?=.*[\d])(?=.*[!@#$%^&*-])[\w!@#$%^&*-]{6,16}$/;
})