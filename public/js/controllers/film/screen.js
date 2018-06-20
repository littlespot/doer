appZooMov.controller("filmCtrl", function($rootScope, $scope,$http) {
    $scope.regex = /^\d+(\.\d+)*[m, g]?$/i;

    $scope.init = function (id, digital, cine, video) {
        $scope.screen = {'digital': angular.fromJson(digital), 'cine': angular.fromJson(cine), 'video': angular.fromJson(video)};
        $scope.error = {'digital':{},'cine':{},'video':{}};
        $rootScope.loaded();
    }

    $scope.edit = function (type, edited) {
        $scope.editedScreen = angular.copy(edited);
        $scope.editedType = type;
        $('#editorModal').modal('show');
    }

    $scope.cancelEdit = function () {
        $scope.editedScreen = null;
        $('#editorModal').modal('hide');
    }
    
    $scope.removeLang = function (id) {
        $rootScope.removeValue($scope.editedScreen.subtitles, id);
    }

    $scope.addLang = function () {
        if($scope.editedScreen.newlang.language_id && ($scope.editedScreen.newlang.dubbed || $scope.editedScreen.newlang.subbed)){
            var name = $('#newLang_opt_'+$scope.editedScreen.newlang.language_id).text();
            $scope.editedScreen.subtitles.push({language_id:$scope.editedScreen.newlang.language_id, name:name, dubbed:$scope.editedScreen.newlang.dubbed?1:0, subbed:$scope.editedScreen.newlang.subbed?1:0})
            $scope.editedScreen.newlang ={language_id:null, subbed:false, dubbed:false}
        }
    }

   $scope.post = function (film_id, invalid) {
        if(invalid || ! $scope.editedScreen || !$scope.editedType)
            return false;

       var data = $scope.editedScreen;
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

       var name = $scope.editedType;
       data.english_dubbed = data.english_dubbed ? 1 : 0;
       data.english_subbed = data.english_subbed ? 1 : 0;
       $http.post('/movie/' + film_id + '/screen/' + name, data)
           .success(function (result) {
               data.label = $('#format_label').find("option:selected").text();
               data.sound = $('#sound_name').find("option:selected").text();
               if(data.id)
                   $rootScope.setValue($scope.screen[name], data);
               else{
                   data.id = result;
                   $scope.screen[name].push(data);
               }
               $('#editorModal').modal('hide');
           })
           .error(function (err) {
               $scope.errors = err.message;
           })
   }

    $scope.delete = function (name,screen) {
       $scope.screenToDelete = screen;
       $scope.deletedType = name;
        $('#deleteModal').modal('show');
    }

    $scope.screenDeleted = function () {
        var name = $scope.deletedType;
        $http.delete('/movie/' + $scope.screenToDelete.id + '/screen/' + name + '/')
            .success(function (result) {
                $rootScope.removeValue($scope.screen[name], $scope.screenToDelete.id);
                $('#deleteModal').modal('hide');
            })
            .error(function (msg) {
                $scope.errors = msg;
                $('#deleteModal').modal('hide');
                $('#errorModal').modal('show');
            })
    }
});
