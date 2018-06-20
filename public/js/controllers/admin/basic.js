/**
 * Created by Jieyun on 28/02/2016.
 */
appZooMov.controller("preparationCtrl", function($rootScope, $scope, $timeout, $log, $http, $uibModal, Preparations) {
    $scope.init = function (finish, city_id) {
        $scope.dateOptions = {
            minDate: Preparations.setDate()
        };

        $scope.calendar = {
            opened: false
        };

        $scope.errors = {title:0, genre_id:0};
        $scope.project = {title:null, genre_id:0, city_id:city_id};
        $scope.languages = [];
        if(finish){
            $scope.finish_at = new Date(finish);
        }

        $scope.loadLocation('project');
     /*  $scope.project = angular.fromJson(project);

        $scope.project.genre_id = $scope.project.genre_id.toString();

        $scope.departments = angular.fromJson(departments);
        $scope.cities = angular.fromJson(cities);

         $scope.project.finish_at = Preparations.compareSetDate($scope.project.finish_at);
         if(!$scope.project.lang)
             $scope.project.lang = [];*/

        $rootScope.loaded();
    }

    $scope.edit = function(name){
        $scope.project[name] = $('#project_'+name).val();
    }
    $scope.openCalendar = function() {
        $scope.calendar.opened = true;
    };

    $scope.addLang = function (id) {
        if($scope.newLang){
            var lang = $('#opt_lang_'+$scope.newLang);
            $http.put('/admin/preparations/' + id + '?language_id=' + $scope.newLang)
                .success(function (result) {
                    $scope.languages.push({id:lang.val(), name:lang.text()});
                    lang.removeAttr('selected').attr('disabled', true);
                    $scope.newLang = null;
                })
        }
    }

    $scope.removeLang = function (id, lang_id) {
        $http.put('/admin/preparations/' + id + '?language_id=' + lang_id)
            .success(function (result) {
                if($('#lang_'+lang_id).length > 0){
                    $('#lang_'+lang_id).remove();
                }
                else{
                    $rootScope.removeValue($scope.languages, lang_id);
                }


                $('#opt_lang_'+lang_id).removeAttr('disabled');
            })
    }

    $scope.cancel = function (name) {
        var item = $('#project_'+name);
        item.val(item.attr('alt'));
        $scope.project[name] = false;
    }

    $scope.save = function (id, name, min, max) {
        var item = $('#project_'+name);
        var val = item.val();

        if(min){
            if(!val){
                $scope.errors[name] = 1;
                return;
            }
            else if(item.attr('type') == 'number' && val < min){
                $scope.errors[name] = 1;
                return;
            }
            else if(val.length < min){
                $scope.errors[name] = 1;
                return;
            }
        }
        if(max){
            if(item.attr('type') == 'number' && val > max){
                $scope.errors[name] = 2;
                return;
            }
            else if(val.length > max){
                $scope.errors[name] = 2;
                return;
            }
        }

        $scope.errors[name] = 0;
        $http.put('/admin/preparations/' + id + '?' + item.attr('name') + '=' + val)
            .success(function () {
                item.attr('alt', val);
                $scope.project[name] = false;
            })
    }

    $scope.cancelDate = function () {
        $scope.finish_at = new Date($('#project_finish').attr('alt'));
        $scope.project.finish = false;
    }

    $scope.saveDate = function (id) {
        if(!$scope.finish_at){
            $scope.errors.finish = 1;
            return;
        }
        if(!Preparations.compareDate($scope.finish_at)){
            $scope.errors.finish = 2;
            return ;
        }
        $scope.errors.finish = 0;
        $http.put('/admin/preparations/' + id + '?finish_at=' + $scope.finish_at.toLocaleDateString()+'&offset='+$scope.finish_at.getTimezoneOffset())
            .success(function () {
                $scope.project.finish = false;
            })
    }

    $scope.loadLocation = function (opt) {
        if($scope[opt].city_id){
            $http.get('/locations/' + $scope[opt].city_id)
                .success(function (data) {
                    angular.extend($scope[opt], data);
                });
        }
        else{
            var country_id = $('#project_country').val();
            if(country_id){
                $http.get('/departments/' + country_id)
                    .success(function (departments) {
                        $scope[opt].departments =  departments;
                    })
                    .error(function (err) {
                        $scope[opt].departments =  [];
                    })
            }
        }
    }

    $scope.changeCountry = function (country_id, opt) {
        if(!country_id){
            $scope[opt].departments =  [];
        }
        $http.get('/departments/' + country_id)
            .success(function (departments) {
                $scope[opt].departments =  departments;
            })
            .error(function (err) {
                $scope[opt].departments =  [];
            })
    }

    $scope.changeDepartment = function (department_id, opt) {
        if(!department_id){
            $scope[opt].cities =  [];
        }
        $http.get('/cities/' + department_id)
            .success(function (cities) {
                $scope[opt].cities =  cities;
            })
            .error(function (err) {
                $scope[opt].cities =  [];
            })
    }
});