/**
 * Created by Jieyun on 2016/12/1.
 */
appZooMov.controller("commonFavoriteCtrl", function($rootScope, $scope,$routeParams,
                                                    roundProgressService, projects) {
    $scope.currentPage = 1;
    $scope.numberPerPage = 9;
    $scope.projects = projects.data;
    $scope.profile = $routeParams;
    $scope.totalItems = $scope.projects.length;
})