/**
 * Created by Jieyun on 10/04/2016.
 */

appZooMov.controller("commonFriendsCtrl", function($rootScope, $scope, $routeParams, relations) {
    $scope.topTabMenu = ['Friend', 'Idol', 'Fan'];
    $scope.selectedTopTab = 0;
    $scope.selectTopTab = function (index) {
        $scope.selectedTopTab = index;
    }


    $scope.currentPage = 1;
    $scope.numberPerPage = 9;
    $scope.friends = relations.data[0];
    $scope.idols = relations.data[1];
    $scope.fans = relations.data[2];
    $scope.profile = $routeParams;
    $scope.idolsItems = $scope.idols.length;
    $scope.fansItems = $scope.fans.length;
})


