/**
 * Created by Jieyun on 28/02/2016.
 */
appZooMov.controller("menuCtrl", function($rootScope, $scope, $timeout, $log, $http, $uibModal, Preparations) {
    $scope.delete = function (id) {
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'confirm.html',
            controller: function($scope) {
                $scope.confirm = 'confirmU';
            }
        });

        modalInstance.result.then(function (confirm) {
            if(!confirm)
                return false;

            $rootScope.loading();
            Preparations.delete({id:id},
                function () {
                    window.location.href='/';
                },function (err) {
                    $rootScope.loaded();
                })
        });
    }

    $scope.changeStep = function (id, current, forward) {
        if(current  < 2){
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'confirm.html',
                controller: function($scope) {
                    $scope.confirm = 'changeStep';
                }
            });

            modalInstance.result.then(function (confirm) {
                if (!confirm)
                    return false;
                else
                    window.location.href = '/admin/preparations/' + id +'?step=' + forward;
            });
        }
        else
            window.location.href = '/admin/preparations/' + id +'?step=' + forward;
    }

    $scope.send = function (step) {
       var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'confirm.html',
            size:'lg',
            controller: function($scope) {
                $scope.confirm = 'confirmV';
            }
        });

        modalInstance.result.then(function (confirm) {
            if (!confirm)
                return false;

            $rootScope.loading();
            if(step < 2){
                $('#sendFlag').val(1);
                if(step == 1){
                    $('#descriptionForm').submit();
                }
                else{
                    $('#basicinfo').submit();
                }
            }
            else{
                $('#sendForm').submit();
            }
        });
    }
});