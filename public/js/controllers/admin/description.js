/**
 * Created by Jieyun on 28/02/2016.
 */
appZooMov.controller("preparationCtrl", function($rootScope, $scope) {
    $scope.init = function (count) {
        $scope.project = {};
        if(count < 200){
            $scope.project.description = true;
            $scope.errors = 1;
        }
        else{
            $scope.project.description = false;
            $scope.errors = 0;
        }

        $rootScope.loaded();
    }

    $scope.save = function () {
       var length = $('#editor').text().length;
       if(length < 40){
            $scope.errors = 1;
        }
        else{
           $('#editor').find('div[data-role=image] img').each(function () {
               var input = $('<input type="hidden" name="images[]">');
               var src = $(this).attr('src');
               input.val(src.substring(src.lastIndexOf('/')+1));
               $('#descriptionForm').append(input);
           });

           $('#editor-content').text($('#editor').html());
           $('#descriptionForm').submit();
        }
    }
});