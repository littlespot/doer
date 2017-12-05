appZooMov.controller("filmCtrl", function($rootScope, $scope,$http) {
    $scope.regex = /^\d+(\.\d+)*[m, g]?$/i;

    $scope.init = function (id, play, cine, video) {
        $scope.screen = {'play': angular.fromJson(play), 'cine': angular.fromJson(cine), 'video': angular.fromJson(video)};
        $scope.data = {'play':{'film_id': id},'cine':{'film_id': id},'video':{'film_id': id}};
        $scope.error = {'play':{},'cine':{},'video':{}};
        $rootScope.loaded();
    }

    $scope.editor = function (name) {
        $scope.screen[name].edit = 1;
    }
    $scope.cancel = function (name) {
        $scope.screen[name].edit = 0;
    }
   $scope.post = function (name, invalid) {
        if(invalid)
            return false;
       var data = angular.copy($scope.data[name]);
       if(data.size){
           var size = data.size.toString().toLowerCase();
           var supfix = size.substr(size.length - 1, 1);

           if(!parseInt(supfix)){
               size = size.substr(0, size.length - 1);

               if(supfix == 'm'){
                   size = (size/1024).toFixed(2);
               }
               data.size = size;
           }
       }

       $http.put('/film/screen/'+name, data)
           .success(function (result) {
               if(result.length > 1){

                   data['label'] = $('#'+ name + '_format_id option:selected').text();
                   if(data['sound_id']){
                       data['sound'] = $('#'+ name + '_sound option:selected').text();
                   }
                   data['english_dubbed'] = result[0];
                   data['dubbed'] = result[1];
                   data['language'] = $('#' + name + '_subtitle option:selected').text();
                   $scope.screen[name].push(data);
                   $scope.data[name] = {'film_id': data.film_id};
                   $scope.screen[name].edit = 0;
               }
           })
           .error(function (msg) {
               $scope.error[name] = msg.errors;
           })
   }

    $scope.delete = function (name,id) {
        $http.delete('/film/screen/' + name + '/' + id)
            .success(function (result) {
                var data = $scope.screen[name];
                var found = 0;
                for (var i = 0; i < data.length && !found; i++) {
                    if(data[i].id == result){
                        found = i;
                    }
                }

                $scope.screen[name].splice(found, 1);
            })
    }
});
