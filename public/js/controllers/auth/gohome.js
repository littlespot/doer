/**
 * Created by Jieyun on 25/05/2017.
 */

appZooMov.controller("loginCtrl", function($rootScope, $scope, $http, $cookieStore, $timeout, $window, $uibModal, $translate) {
    $scope.error = false;
    $scope.singin = function (valid) {
        $scope.submitted = true;
        if (!valid) {
            return false;
        }

        $rootScope.loading = true;
        $timeout(function () {
            var csrf = $('input[name="csrfmiddlewaretoken"]').val();

            $http.defaults.headers.post['X-CSRF-TOKEN'] = csrf;
            $scope.error = false;
            $scope.user._token = $("body input[name='csrfmiddlewaretoken']").val();
            $http({
                method: 'POST',
                url: 'login',
                data: $scope.user
            }).then(function successCallback(response) {
                if (response.data == 'OK') {
                    $window.location.href = "/"
                }
                else {
                    $scope.error = true;
                    $rootScope.loading = false;
                }
            }, function errorCallback(response) {
                $rootScope.loading = false;
                $uibModal.open({
                    animation: true,
                    templateUrl: 'error.html'
                });
            });
        }, 300);
    };

    $scope.resetForm = function () {
        $scope.user = {id: ""};
    }

    $scope.forget = function () {

        $rootScope.loading = true;
        $timeout(function () {
            $http({
                method: 'POST',
                url: 'forget',
                data: {
                    email: $scope.user.email,
                    lang: $scope.user.locale,
                    _token: $("body input[name='csrfmiddlewaretoken']").val(),
                    token: $("body input[name='csrfmiddlewaretoken']").val()
                }
            }).then(function successCallback(response) {
                $rootScope.loading = false;
                $uibModal.open({
                    animation: true,
                    templateUrl: 'result.html'
                });
                $timeout(function () {
                    $window.location = "/login";
                }, 10000);
            }, function errorCallback(response) {
                $rootScope.loading = false;
                $uibModal.open({
                    animation: true,
                    templateUrl: 'error.html'
                });

                $timeout(function () {
                    $window.location = "/login";
                }, 10000);
            });
        }, 300);
    }

    angular.element(document).ready(function () {
        $('.outer').show();
    });
});