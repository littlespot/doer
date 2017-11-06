/**
 * Created by Jieyun on 2016/12/17.
 */
appZooMov.controller("activationCtrl", function($rootScope, $scope, $http){
    $scope.init = function (email, key) {
        $scope.user = {email:email, key:key};
    }

    $scope.changePwd = function (valid) {
        if(!valid){
            return false;
        }

        $rootScope.loading();

        $http.post('/active', $('#zooform').serialize())
            .success(function (data) {
                alert(JSON.stringify(data));
            })
            .error(function (err) {
                alert(err);
            })
       // $('#zooform').submit()
    }

    $scope.regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?])[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]{6,16}$/;
    $rootScope.loaded();
})