/**
 * Created by Jieyun on 28/02/2016.
 */
appZooMov.controller("creationCtrl", function($rootScope, $scope, $timeout, $log, $http, $uibModal, Preparations) {
    $scope.error = {description:false, finish_at:false, poster:false};

    $scope.init = function (project) {
        $scope.dateOptions = {
            minDate: Preparations.setDate()
        };

        $scope.calendar = {
            opened: false
        };

        $scope.project = angular.fromJson(project);
        if(!$scope.project.lang){
            $scope.project.lang = [];
        }
        $rootScope.loaded();
    }

    $scope.openCalendar = function() {
        $scope.calendar.opened = true;
    };

    $scope.addLang = function (lang) {
        $scope.project.lang.push({language_id:lang, name:$('#opt_lang_' + lang).text()});
        $scope.newLang = '';
        $('#opt_lang_' + lang).hide();
    }

    $scope.removeLang = function (lang) {
        var index = -1;
        for(var i = 0; i < $scope.project.lang.length && index < 0; i++){
            if($scope.project.lang[i].id == lang){
                index = i;
            }
        }
        if(index >= 0){
            $scope.project.lang.splice(index,1);
            $('#opt_lang_' + lang).show();
        }
    }

    $scope.save = function (invalid) {

        if(invalid)
            return;
        $scope.error = {description:false, finish_at:false, poster:false};
        if(!Preparations.compareDate($scope.project.finish_at)){
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'alert.html',
                controller: function($scope) {
                    $scope.alert = 'date';
                }
            });

            modalInstance.result.then(function () {
                if ($scope.selectedTab > 0)
                    $scope.selectTab(0);

                return false;
            });
        }

        $scope.project._token= $("body input[name='csrfmiddlewaretoken']").val();
        var lang = [];
        angular.forEach($scope.project.lang, function (item) {
            lang.push(item.language_id);
        });

        Preparations.save(angular.extend($scope.project,{languages:lang}), function (data) {
            $scope.project.id = data.id;
            $scope.pictureId = data.id;
            window.location.href='/admin/preparations/' + data.id + '?step=1';
        },function (error) {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'alert.html',
                controller: function($scope) {
                    $scope.alert = 'poster';
                }
            });

            modalInstance.result.then(function () {

                return false;
            });
        });
    }
});