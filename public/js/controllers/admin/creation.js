/**
 * Created by Jieyun on 28/02/2016.
 */
appZooMov.controller("creationCtrl", function($rootScope, $scope, $timeout, $log, $http, $filter, Preparations) {
    $scope.error = {description:false, finish_at:false, poster:false};

    $scope.init = function () {
        $scope.dateOptions = {
            minDate: Preparations.setDate()
        };

        $scope.calendar = {
            opened: false
        };

        $scope.project = {};
        $scope.project.langs = [];
        $scope.project.departments = [];
        $scope.project.cities = [];
        $rootScope.loaded();
    }

    $scope.openCalendar = function() {
        $scope.calendar.opened = true;
    };

    $scope.addLang = function (newLang) {
        var lang_opt = $('#newLang option:selected');
        $scope.project.langs.push({language_id:lang_opt.val(), name:lang_opt.text(), rank:lang_opt.attr('rank')});
        lang_opt.attr('disabled', true);
        newLang = null;
    }

    $scope.removeLang = function (lang) {
        $rootScope.removeValue($scope.project.langs, lang, 'language_id');
        $('#opt_lang_'+lang).removeAttr('disabled');
    }

   $scope.save = function (invalid) {
        if(invalid)
            return;

       if($('#poster_image').attr('src').indexOf('default.png')>0){
           $('#alertPosterModal').modal('show');
           return false;
       }
       else if(!Preparations.compareDate($scope.project.finish_at)){
           $('#alertPreparationModal').modal('show');
           return false;
       }
       else{
           $('#basicinfo').submit();
       }
        /* $scope.error = {description:false, finish_at:false, poster:false};
        if(!Preparations.compareDate($scope.project.finish_at)){
            if ($scope.selectedTab > 0)
                $scope.selectTab(0);

            $('#alertPreparationModal').modal('show');
        }

        var lang = [];
        angular.forEach($scope.project.langs, function (item) {
            lang.push(item.language_id);
        });

        Preparations.save(angular.extend($scope.project,{languages:lang}), function (data) {
            $scope.project.id = data.id;
            $scope.pictureId = data.id;
            window.location.href='/admin/preparations/' + data.id + '?step=1';
        },function (error) {
            $('#alertPosterModal').modal('show');
        });*/
    }
});