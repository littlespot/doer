/**
 * Created by Jieyun on 25/02/2016.
 */
appZooMov.controller("homeCtrl", function($rootScope, $scope, $timeout, $http, $log) {
    $scope.myInterval = 3000;
    $scope.slides = [];
    $scope.setHeight = function(maxratio) {
        var height = $(window).innerHeight();
        var width = $(window).innerWidth();
        var ratio = Math.floor(height/width);

        if(ratio < maxratio){
            $('.zooCarousel').css('min-height',  height - $("nav").height() - 80);
        }
        else{
            $('.zooCarousel').css('min-height', width * 0.8 * maxratio);
            if(ratio > 1){
                $('.zooCarousel img').css('width', '100%').css('height', 'auto');
            }
        }
    };

    $(window).resize(function() {
        $scope.setHeight();
    });
    $scope.openCatalogue = function (opt) {
        $scope.filterOpt = opt;
        $scope.filterChosen = $scope.filters[$scope.filterOpt];
        $scope.overlay = true;
    }

    $scope.setFilter = function (id, name) {
        if (id == 0) {
            $scope.filters[$scope.filterOpt] = {id: '!!', name: 'Catalogue'};
        }
        else {
            $scope.filters[$scope.filterOpt] = {id:id, name:name}
        }

        $scope.overlay = false;
    }

    $scope.changeLocation = function (path, project) {
        $location.path(path + '/' + project.id);
    }

    $scope.init = function (pictures, ratio) {
        var files = pictures.split(',');
        angular.forEach(files, function (key, value) {
            if(key && key.length > 0)
                $scope.slides.push({id:value + 1, image:key})
        })
        $scope.setHeight(ratio);

        $scope.filterOpt = 0;
        $scope.filters = [{id: '!!', name: 'Catalogue'} , {id: '!!', name: 'Catalogue'}];
        $scope.filterChosen = $scope.filters[$scope.filterOpt];

        var promise = $http({
            headers:{
                'token': $("body input[name='csrfmiddlewaretoken']").val()
            },
            method: 'GET',
            url: '/api/home/projects',
            isArray:false,
            cache:true
        });

        promise.then(
            function(items) {
                $rootScope.loaded();
                $scope.recommendations = items.data.recommendations;
                $scope.latest = items.data.latest
            },
            function(error) {
                $log.error('failure loading projects', error);
            });
    }
})