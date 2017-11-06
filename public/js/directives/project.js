/**
 * Created by Jieyun on 03/06/2016.
 */

appZooMov.directive('descriptionContent', function($http){
    return {
        transclude:true,
        restrict:'E',
        templateUrl:'views/templates/description-content.html',
        link: function (scope) {
            scope.saveDescription = function(description){
                $http.put('/admin/projects/' + scope.project.id,
                    {text:description, opt:"d", _token:$("body input[name='csrfmiddlewaretoken']").val()})
                    .success(function(){

                    })
                    .error(function (err) {

                    });

            }
        }
    }
});

appZooMov.directive('projectItem', function($location){
    return {
        restrict:'A',
        scope: {
            p: "=projectItem",
            user: "=user"
        },
        templateUrl:window.location.origin +'/views/templates/project-content.php',
        link:function(scope){
            scope.myInterval = 3000;
            scope.changeLocation = function(id){
                $location.path('project/' + id);
            }
        }
    }
});

appZooMov.directive('projectCell', function (roundProgressService) {
    return {
        restrict: 'C',
        link:function(scope){
            scope.myInterval = 3000;

            scope.getStyle = function () {
                var transform = 'translateY(-50%) ' + 'translateX(-50%)';

                return {
                    'top': '50%',
                    'bottom': 'auto',
                    'left': '50%',
                    'transform': transform,
                    '-moz-transform': transform,
                    '-webkit-transform': transform,
                    'font-size': 80 / 3.5 + 'px'
                };
            };
        }
    }
})
