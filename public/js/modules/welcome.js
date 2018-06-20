/**
 * Created by Jieyun on 16/10/2016.
 */
var appZooMov = angular
    .module('zooApp', ['angular-svg-round-progressbar']);

appZooMov.config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
});
appZooMov.controller("welcomeCtrl", function($rootScope, $scope) {
    $scope.loaded = function () {
        $('.crazyloader').fadeOut(1000);
    }
})