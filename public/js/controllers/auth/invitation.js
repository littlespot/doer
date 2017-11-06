appZooMov.controller("invitationCtrl", function($rootScope, $scope, $http, $filter, $timeout, $window, $uibModal) {
    $rootScope.loaded();
    $scope.init = function (lang) {
        $scope.user = {email:null, email2:null};
        $scope.currentPage = 0;

        $http.get('/occupations/', {params:{lang:$rootScope.currentLang.id}})
            .success(function (occupations) {
                $scope.occupations = occupations;
            });


        $scope.newWork = {title:'',url:'',description:'', occupations:[]};
        $translate.use(lang);
    }


    $scope.setPage = function (number) {
        if(number > 0 && $scope.currentPage == $scope.works.length){
            return;
        }

        if(number <0 && $scope.currentPage == 1)
            return;

        $scope.currentPage += number;
    }

    $scope.removeTalent = function(occupation){
        occupation.uid = null;
        var occupations = $filter('filter')($scope.occupations, {uid: ''});

        if(occupations.length == 0){
            $scope.error.occupation = true;
        }
    }

    $scope.works = [];
    $scope.error = {url:false, occupation:false, work:false};

    $scope.removeWork = function (index) {
        $scope.works.splice(index);
        $scope.currentPage = $scope.works.length;
        if($scope.works.length == 0){
            $scope.error.work = true;
        }
    }

    $scope.addWork = function (invalid) {
        if(invalid)
            return false;
        $scope.wform.submit = true;
        $scope.error.url = false;

        if($scope.newWork.url && $scope.newWork.url.length > 0 && $scope.works.length > 0){
            var url = $filter('filter')($scope.works, {url: $scope.newWork.url}, true);

            if(url.length){
                $scope.error.url = true;
                return;
            }
        }

        var occupations = $filter('filter')($scope.occupations, {uid: ''});

        if(occupations.length == 0){
            $scope.error.occupation = true;
            return;
        }

        $scope.error.occupation = false;

        for (var i=0; i<$scope.occupations.length; i++) {
            $scope.occupations[i].uid = null;
        }


        angular.copy(occupations, $scope.newWork.occupations);

        $scope.works.push($scope.newWork);

        $scope.newWork = {title:'',url:'',description:'', occupations:[]};

        $scope.wform.submit = false;
        $scope.wform.show = false;
        $scope.error.work = false;
        $scope.currentPage = $scope.works.length
    }

    $scope.addTalent = function(){
        var found = false;
        for (var i=0; i<$scope.occupations.length && !found; i++) {
            if ($scope.occupations[i].id == $scope.newTalent) {
                if($scope.occupations[i].old){
                    $scope.occupations[i].uid = $scope.occupations[i].old;
                }
                else{
                    $scope.occupations[i].uid = 0
                }

                $scope.error.occupation = false;
                $scope.newTalent = null;
                found = true;
            }
        }

    }

    $scope.save = function (valid) {
        if(!valid){
            return false;
        }

        if($scope.works.length == 0){
            $scope.error.work = true;
            return;

        }
        $scope.user.works = $scope.works;
        $scope.user.lang = $rootScope.currentLang.id;
        $scope.user._token = $("body input[name='csrfmiddlewaretoken']").val();
        $rootScope.loading = true;
        $timeout(function () {
            $http.post('invitation', $scope.user)
                .success(function () {
                    $rootScope.loading = false;
                    $uibModal.open({
                        animation: true,
                        templateUrl: 'result.html'
                    });
                    $timeout(function(){
                        $window.location = "/login";
                    }, 10000);
                })
                .error(function (err) {
                    $rootScope.loading = false;
                    $uibModal.open({
                        animation: true,
                        templateUrl: 'error.html'
                    });
                    $log.error('unable to to inscription of invitation', err)
                })
        },300)

    }
})