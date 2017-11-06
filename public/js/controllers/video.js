/**
 * Created by Jieyun on 28/02/2016.
 */

appZooMov.controller("videosCtrl", function($rootScope, $scope, $http, $filter, $log) {
    $scope.orderoptions = [{id:'view_cnt', label:'Popularity'}, {id:'created_at', label:"updated_at"}, {id:'duration',label:'Duration'}];
    $scope.durations = [{id:2, min:5, max:10},
        {id:3, min:10, max:20},
        {id:4, min:20, max:30},
        {id:5, min:30, max:45},
        {id:6, min:45, max:60}
    ];
    $scope.filter = {genre: 0, language: 0, order: 'created_at', duration:'>0', direction:'desc'};

    $scope.setOrder = function (o) {
        $scope.filter.order = o;
    }

    $scope.setGenre = function (id, name) {
        $scope.filter.genre = id;
        $scope.chosenGenre = name ? name : $filter('translate')('genre.Catalogue');
    }

    $scope.setLang = function (id, name) {
        $scope.filter.language = id;
        $scope.chosenLang = name ? name : $filter('translate')('FOOTER.languages');
    }

    $scope.setDuration = function (id, min, max) {
        if(!min)
            $scope.filter.duration = '<' + max;
        else if(!max)
            $scope.filter.duration = '<' + max;
        else
            $scope.filter.duration = 'BETWEEN ' + min + ' AND ' + max;

        $scope.chosenDuration = {id:id, label:$('#duration_'+id).text()};
    }

    $scope.setDirection = function () {
        $scope.filter.direction = $scope.filter.direction == 'desc' ? 'asc' : 'desc';
    }

    $scope.refresh = function (page) {
        $rootScope.loading();

        var promise = $http({
            method: 'GET',
            url: '/api/videos',
            params: page ? angular.extend({}, $scope.filter, {page: $scope.pagination.currentPage}) : $scope.filter,
            isArray: true,
            cache: true
        });

        promise.then(
            function (videos) {
                $scope.chosenGenre = $filter('translate')('genre.Catalogue');
                $scope.chosenLang = $filter('translate')('FOOTER.languages');
                $scope.chosenDuration = {id:0, label:$filter('translate')('project.PLACES.duration')};
                $scope.videos = videos.data;
                if (!page) {
                    $scope.pagination = $rootScope.setPage(videos);
                }

                $rootScope.loaded();
            },
            function (error) {
                $log.error('failure loading videos', error);
            });
    }

    $scope.refresh(false);
})