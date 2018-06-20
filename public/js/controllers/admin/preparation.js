/**
 * Created by Jieyun on 28/02/2016.
 */
appZooMov.controller("menuCtrl", function($rootScope, $scope, $timeout, $log, $http) {
    $scope.preparationDeleted = function (id) {
        $rootScope.loading();
        $http.delete('/admin/preparations/'+id)
            .success(function () {
                $rootScope.loaded();
                $('#deletePreparationModal').modal('hide');
                $('#informationChangedModal').modal('show');
                $scope.pageJump = $timeout(function(){
                    window.location.href = '/home';
                },6000)
            })
            .error(function (err) {
                $rootScope.loaded();
                $scope.msg = err.message;
            });
    }

    $scope.changeStep = function (id, current, forward) {
        window.location.href = '/admin/preparations/' + id +'?step=' + forward;
    }

    $scope.send = function (step) {
        $scope.step = step;
        $('#submitPreparationModal').modal('show');
    }
    
    $scope.preparationSubmit = function () {
        $rootScope.loading();
        if($scope.step < 2){
            $('#sendFlag').val(1);
            if($scope.step == 1){
                $('#descriptionForm').submit();
            }
            else{
                $('#basicinfo').submit();
            }
        }
        else{
            $('#sendForm').submit();
        }
    }
});