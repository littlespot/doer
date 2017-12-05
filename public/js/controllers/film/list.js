appZooMov.controller("filmCtrl", function($scope, $http) {
    $scope.viewer = 0;
   $scope.switchViewer = function (viewer) {
       $scope.viewer = viewer;
       if(viewer){
           $('#film_form').show();
       }
       else{
           $('#film_form').hide();
       }
   }

   $scope.save = function (invalid) {
       if(invalid){
           return false
       }

       $http.post('/films', $scope.film)
           .success(function (result) {
               window.location.href = '/film/' + result + '/1';
           })
           .error(function (data) {
               $scope.error = data.errors;
           })
   }
});
