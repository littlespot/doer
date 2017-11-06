/**
 * Created by Jieyun on 28/02/2016.
 */
appZooMov.controller("preparationCtrl", function($rootScope, $scope) {
    $scope.init = function (id, authors, types, budgets, sponsors, scripts) {
        $scope.project = {id:id};
        $scope.authors = angular.fromJson(authors);
        $scope.budgetTypes = angular.fromJson(types);
        $scope.budgets = angular.fromJson(budgets);
        $scope.sponsors = angular.fromJson(sponsors);
        $scope.scripts = angular.fromJson(scripts);
        $rootScope.loaded();
    }
});