/**
 * Created by Jieyun on 2016/11/30.
 */

/**
 * Created by Jieyun on 25/02/2016.
 */

appZooMov.controller("resetCtrl", function($rootScope, $scope)
{
    $rootScope.loaded();

    $scope.init = function (email) {
        $scope.email = email ? email : '';
    }

    $scope.regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?])[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]{6,16}$/;
})
