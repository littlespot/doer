/**
 * Created by Jieyun on 2016/12/1.
 */
appZooMov.controller("projectCtrl", function($rootScope, $scope, $http, $timeout, $log) {
    $scope.refresh = function (page) {
        $rootScope.loading();

        var promise = $http({
            method: 'GET',
            url: '/api/filter',
            params:page ? angular.extend({},$scope.filter, {page:page}) : $scope.filter,
            isArray:true,
            cache:true
        });

        promise.then(
            function(projects) {
                $scope.projects = projects.data.data;
                if(!page){
                    $scope.pagination = $rootScope.setPage(projects.data);
                }
                else{
                    $scope.pagination.currentPage = projects.data.current_page;
                }
                $rootScope.loaded();
            },
            function(error) {
                $log.error('failure loading projects', error);
            });
    }

    $scope.init = function (param) {
        var filter  =  angular.fromJson(param);
        angular.extend(filter, {order: 'updated_at'});
        $scope.filter = filter;
        $scope.orderoptions = ['updated_at', 'created_at', 'finish_at'];
        $scope.order ='updated_at';
        $scope.refresh(false);
    }

    $scope.pageChanged = function (i) {
        $rootScope.loading();
        $scope.refresh(i);
    }


    $scope.setOrder = function(o){
        $scope.filter.order = o;
        $scope.refresh(false);
    }

    $scope.setLocationFilter = function(l){
        $scope.filter.city = l;
        $('#city_name').text($('#city_' + l).text());
        $scope.refresh(false);
    }

    $scope.setPersonFilter = function(u){
        $scope.filter.person = u;
        $('#person_name').text($('#person_' + u).text());
        $scope.refresh(false);
    }

    $scope.setGenre = function(id){
        $scope.filter.genre = id;
        $scope.refresh(false);
    }

});
