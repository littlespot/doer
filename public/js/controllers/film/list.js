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
    $scope.movies = [];
    $scope.shown_count = 3;
    $scope.upIndex = function (index, max) {
        if($scope.movies[index].index + $scope.shown_count < max){
            $scope.movies[index].index += $scope.shown_count;
        }
    }

    $scope.downIndex = function (index) {
        if($scope.movies[index].index > $scope.shown_count){
            $scope.movies[index].index -= $scope.shown_count;
        }
        else if($scope.movies[index].index > 0){
            $scope.movies[index].index = 0;
        }
    }
    $('.card-footer').on('hidden.bs.collapse', function () {
        $(this).siblings('.btn-block').show();
    })

    $('.card-footer').on('show.bs.collapse', function () {
        $(this).siblings('.btn-block').hide();
    })
});
