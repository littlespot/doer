/**
 * Created by Jieyun on 28/02/2016.
 */
appZooMov.controller("preparationCtrl", function($rootScope, $scope, $timeout, $log, $http) {
    $scope.init = function (project, users) {
        $scope.project =  angular.fromJson(project);
        $scope.users = angular.fromJson(users);
        $http.get('/api/teams/' + $scope.project.id)
            .success(function (team) {
                $scope.team = team.data;
                $scope.pagination = $rootScope.setPage(team);
                $rootScope.loaded();
            })
    }

    $scope.loadTeam = function (page) {
        var params = {unactive:2};
        if(page){
            params.page = page;
        }
        $http.get('/api/teams/' + $scope.project.id, {params:params})
            .success(function (team) {
                $scope.team = team.data;
                $scope.pagination = $rootScope.setPage(team);
                $scope.loading = false;
            })
    }

    $scope.pageChanged = function () {
        $scope.loading = true;
        $http.get('/api/teams/' + $scope.project.id + '?page=' + $scope.pagination.currentPage)
            .success(function (result) {
                $scope.team = result.data;
                $scope.loading = false;
            })
            .error(function (err) {
                $log.error('failure loading team for project ' + $scope.id + tab ? '': '?page ' + $scope.pagination.currentPage, err);
            })
    }
});