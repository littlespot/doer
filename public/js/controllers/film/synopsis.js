appZooMov.controller("filmCtrl", function($rootScope, $scope, $http) {
    $scope.init = function ($id) {
        $scope.film_id = $id;
        $scope.synopsis = {language_id:'', content:''};
        $scope.list = [];
    }

    $scope.save = function () {
        var data = angular.copy($scope.synopsis);
        var language = $('#synopsis_language option:selected').text();
        $http.post('/film/'+$scope.film_id+ '/synopsis/', data)
            .success(function (result) {
                data.id = result;
                data.language = language;
                $scope.list.push(data);
                $scope.synopsis = {language_id:'', content:''};
                $scope.edit = 0;
            })
    }

    $scope.remove = function (id) {
        $http.delete('/film/'+$scope.film_id+ '/synopsis/'+id)
            .success(function (result) {
                var len = $scope.list.length;
                var found = -1;
                for(var i = 0; i < len && found < 0; i++){
                  if($scope.list[i].language_id == id){
                      found = i;
                  }
                }
                if(found >= 0)
                    $scope.list.splice(found,1);
                else
                    $('#content_' + id).remove();
            })
    }
});
