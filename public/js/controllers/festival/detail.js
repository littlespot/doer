appZooMov.controller("festivalCtrl", function($rootScope, $scope, $http) {
    $('.card').on('hidden.bs.collapse', function () {
        $('.btn-floor', this).show();
        var hide = $('.ng-hide', this);
        hide.siblings().addClass('ng-hide')
        hide.removeClass('ng-hide');
    });

    $('.card').on('shown.bs.collapse', function () {
        $('.btn-floor', this).hide();
        var hide = $('.ng-hide', this);
        hide.siblings().addClass('ng-hide')
        hide.removeClass('ng-hide');
    });

    $scope.toggleFavorite = function (id) {
        $http({
            method: "put",
            url:'/festivals/' + id
        })
            .success(function(result){
                $scope.favorite = result;
            })
    }

})