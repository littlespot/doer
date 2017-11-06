/**
 * Created by Jieyun on 28/02/2016.
 */
appZooMov.controller("preparationCtrl", function($rootScope, $scope) {
    $scope.init = function (id, recruitment) {
        $scope.project = {id:id};
        $scope.recruit = angular.fromJson(recruitment);
        $rootScope.loaded();
    }
});