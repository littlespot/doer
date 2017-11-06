/**
 * Created by Jieyun on 28/02/2016.
 */
appZooMov.controller("preparationCtrl", function($rootScope, $scope, $timeout, $log, $http, $uibModal, Preparations) {
    $scope.init = function (project) {
        $scope.dateOptions = {
            minDate: Preparations.setDate()
        };

        $scope.calendar = {
            opened: false
        };

        $scope.project = angular.fromJson(project);

        $scope.project.genre_id = $scope.project.genre_id.toString();

        $scope.project.finish_at = Preparations.compareSetDate($scope.project.finish_at);
        if(!$scope.project.lang)
            $scope.project.lang = [];
        $rootScope.loaded();
    }

    $scope.openCalendar = function() {
        $scope.calendar.opened = true;
    };

    $scope.addLang = function (lang) {
        var lang_opt = $('#opt_lang_' + lang);
        $scope.project.lang.push({language_id:lang, name:lang_opt.text(), rank:lang_opt.attr('rank')});
        $scope.newLang = '';
        $('#opt_lang_' + lang).remove();
    }

    $scope.removeLang = function (lang) {
        var index = -1, rank=1, name='';
        for(var i = 0; i < $scope.project.lang.length && index < 0; i++){
            var l = $scope.project.lang[i];
            if(l.language_id == lang){
                index = i;
                rank = l.rank;
                name = l.name;
            }
        }

        if(index >= 0){
            $scope.project.lang.splice(index,1);
            var opt = $('<option>').text(l.name).val(lang).attr('id', 'opt_lang_'+lang).attr('rank', l.rank);
            $('#newLang option').each(function () {
                if($(this).attr('rank')> l.rank){
                    opt.insertBefore($(this));
                    return false;
                }
            })
        }
    }

    $scope.save = function (invalid) {
       if(invalid)
            return;
        $scope.error = {description:false, finish_at:false, poster:false};
        if(!Preparations.compareDate($scope.project.finish_at)){
            $uibModal.open({
                animation: true,
                templateUrl: 'alert.html',
                controller: function($scope) {
                    $scope.alert = 'date';
                }
            });

            return ;
        }
        else{
            $('#basicinfo').submit();
        }
    }
});