/**
 * Created by Jieyun on 2017/2/13.
 */
appZooMov.controller("headerCtrl", function($scope, $rootScope, $filter, $http, $window, $translate, $log) {

    $scope.projectIndex = [];

    $scope.init = function (code) {
        $translate.use(code);
        $rootScope.currentLang = $filter('getById')($rootScope.languages, code);
        $http.get('/admin/messagesCount/')
            .success(function(result){
                $scope.messageCnt = result[0];
                $scope.notificationCnt = result[1];
            });

        $http.get('/admin/preparationsCount/')
            .success(function(result){
                $rootScope.preparations = result;
            });
    }

    $scope.load = function (query) {
        return $http.get('/api/searchItems?query=' + query);
    }

    $scope.itemSelected = function (selected) {
        if(selected.title){
            if(selected.originalObject.id.startsWith('z'))
                $window.location.href = '/profile/' + selected.originalObject.id;
            else
                $window.location.href = '/project/' + selected.originalObject.id;
        }
    }
})