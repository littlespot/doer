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
    $scope.signout = function() {
        if ( $cookies.get('user') != undefined) {
            $cookies.remove('user');
        }

        $window.location.href = '/logout';
    }

    $scope.searchFocus = function () {
        if(!$scope.projectIndex.length){
            $scope.searchOn = true;
            $http.get('/api/searchItems')
                .success(function (data) {
                    $scope.projectIndex = data;
                    $scope.searchOn = false;
                })
                .error(function (err) {
                    $scope.searchOn = false;
                    $log.error('failure loading items for search', err);
                })
        }
    }
    
    $scope.projectSelected = function (selected) {
        if(selected.title)
            $window.location.href = '/project/' + selected.originalObject.id;
    }
})