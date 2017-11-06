/**
 * Created by Jieyun on 28/02/2016.
 */
appZooMov.controller("preparationCtrl", function($rootScope, $scope, $timeout, $log, $http, $uibModal) {
    $scope.loaded = function () {
    }

    $scope.alert = function () {
       $uibModal.open({
            animation: true,
            templateUrl: 'alert.html',
            controller: function($scope) {
                $scope.alert = 'description';
            }
        });
    }

    $scope.save = function () {
        $('#descriptionForm').submit();
    }
});