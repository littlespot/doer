/**
 * Created by Jieyun on 2016/11/30.
 */

/**
 * Created by Jieyun on 25/02/2016.
 */

appZooMov.controller("registerCtrl", function($rootScope, $scope, $http, $cookieStore, $timeout, $window, $translate)
{
    $rootScope.loaded();
    $scope.error = null;

    $scope.regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?])[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]{6,16}$/;
})
