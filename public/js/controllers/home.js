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
            $('#zooCarousel').css('min-height',  height - $("nav").height() - 80);
        }
        else{
            $('#zooCarousel').css('min-height', width * 0.8 * maxratio);
            if(ratio > 1){
                $('#zooCarousel img').css('width', '100%').css('height', 'auto');
            }
        }
    };

    $(window).resize(function() {
        $scope.setHeight();
    });

    $scope.setFilter = function (index, id, name) {
        if (id == 0) {
            $scope.filters[index] = {id: '!!', name: 'Catalogue'};
        }
        else {
            $scope.filters[index] = {id:id, name:name}
        }
    }

    $scope.changeLocation = function (path, project) {
        $location.path(path + '/' + project.id);
    }

    $scope.init = function (pictures, height) {
        var files = pictures.split(',');
        angular.forEach(files, function (key, value) {
            if(key && key.length > 0)
                $scope.slides.push({id:value + 1, image:key})
        })

        $('.carousel-item').height(height);
        //$scope.setHeight(ratio);
        $scope.filters = [{id: '!!', name: 'Catalogue'} , {id: '!!', name: 'Catalogue'}];
        $rootScope.loaded();
        var promise = $http({
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