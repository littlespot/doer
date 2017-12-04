/**
 * Created by Jieyun on 25/02/2016.
 */

appZooMov.controller("loginCtrl", function($rootScope, $scope, $http, $cookieStore, $timeout, $window, $uibModal){
    $scope.error = false;
    $scope.setEmail = function (email) {
        $scope.email = email;
    }

    $scope.getUser = function(valid){
        if (!valid || $scope.email.length < 4) {
            return false;
        }

        $rootScope.loading();
        $('#loginForm').submit();
    }

    $scope.login = function (valid) {
        if (!valid || $scope.password.length < 6 || $scope.password.length > 16) {
            return false;
        }

        $rootScope.loading();
        $('#loginForm').submit();
    };

    $scope.resetForm = function(){
        $scope.user = {id:""};
    }

    $scope.forget = function () {

        $rootScope.loading();
        $timeout(function () {
            $http({
                method: 'POST',
                url: 'forget',
                data: {
                    email: $scope.user.email,
                    lang: $scope.user.locale
                }
            }).then(function successCallback() {
                $rootScope.loaded();
                $uibModal.open({
                    animation: true,
                    templateUrl: 'result.html'
                });
                $timeout(function(){
                    $window.location = "/login";
                }, 10000);
            }, function errorCallback() {
                $rootScope.loaded();
                $uibModal.open({
                    animation: true,
                    templateUrl: 'error.html'
                });

                $timeout(function(){
                    $window.location = "/login";
                }, 10000);
            });
        },300);
    }

    angular.element(document).ready(function () {
        $('.outer').show();
    });
})
