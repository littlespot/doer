/**
 * Created by Jieyun on 28/02/2016.
 */
appZooMov.controller("creationCtrl", function($rootScope, $scope, $timeout, $http, $uibModal, Preparations) {
    $scope.error = {description:false, finish_at:false, poster:false};

    $scope.init = function () {
        $scope.dateOptions = {
            minDate: Preparations.setDate()
        };

        $scope.calendar = {
            opened: false
        };

        $scope.langs = [];
        $rootScope.loaded();
    }

    $scope.openCalendar = function() {
        $scope.calendar.opened = true;
    };

    $scope.addLang = function (lang) {
        var lang_opt = $('#opt_lang_' + lang);
        $scope.langs.push({language_id:lang, name:lang_opt.text(), rank:lang_opt.attr('rank')});
        $scope.newLang = '';
        $('#opt_lang_' + lang).remove();
    }

    $scope.removeLang = function (lang) {
        var index = -1;
        for(var i = 0; i < $scope.langs.length && index < 0; i++){
            var l = $scope.langs[i];
            if(l.language_id == lang){
                index = i;
            }
        }

        if(index >= 0){
            $scope.langs.splice(index,1);
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
        $scope.error = {finish_at:false, poster:false};

        if($('#poster_image').attr('src').indexOf('poster.png')>0){
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'alert.html',
                controller: function($scope) {
                    $scope.alert = 'poster';
                }
            });

            modalInstance.result.then(function () {
                window.scrollTo(0, $('#poster_image').offsetTop-36);
            });
        }
        else if(!Preparations.compareDate($scope.finish_at)){
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

        else{
            var lang = [];
            angular.forEach($scope.langs, function (item) {
                lang.push(item.language_id);
            });

            $('#basicinfo').submit();
        }
    }
});