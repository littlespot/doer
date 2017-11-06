appZooMov.controller("menuCtrl", function($rootScope, $scope, $timeout, $http, $uibModal) {

    $scope.cancel = function (id) {
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'confirm.html',
            controller: function($scope) {
                $scope.confirm = 'cancelEdit';
            }
        });

        modalInstance.result.then(function (confirm) {
            if (!confirm)
                return false;
            else
                window.location.href = '/project/' + id;
        });
    }

    $scope.send = function (step, id) {
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'complete.html'
        });

        modalInstance.result.then(function (confirm) {
            if (!confirm)
                return false;
            else if(step < 2){
                $('input[name="returnFlag"]').val(confirm - 1);
                if(step == 1)
                    $('#descriptionForm').submit();
                else
                    $('#basicinfo').submit();
            }
            else
                window.location.href = '/project/' + id;
        });
    }
})