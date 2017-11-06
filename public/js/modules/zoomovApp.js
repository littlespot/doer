/**
 * Created by Jieyun on 16/10/2016.
 */
var appZooMov = angular
    .module('zooApp', ['ui.bootstrap', 'ui.bootstrap.tpls', 'ngAnimate', 'ngTouch', 'ngCookies','ngResource','ngSanitize', 'angular-svg-round-progressbar',
        'pascalprecht.translate', 'angular-scroll-animate', 'angucomplete-alt']);

appZooMov.config(function($interpolateProvider, $translateProvider, $httpProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
    $translateProvider.useStaticFilesLoader({
        prefix: window.location.origin + '/i10n/',
        suffix: '.json'
    })
        .registerAvailableLanguageKeys(['en', 'de'], {
            'en' : 'en', 'en_GB': 'en', 'en_US': 'en',
            'zh' : 'zh', 'zh_CN': 'zh', 'GBK': 'zh'
        })
        .fallbackLanguage('en')
        .useSanitizeValueStrategy('escapeParameters');
});
appZooMov
    .filter('getById', function() {
        return function(input, id) {
            if(!input || id < 1)
                return null;
            for (var i=0; i<input.length; i++) {
                if (input[i].id == id) {
                    return input[i];
                }
            }
            return null;
        }
    })
    .filter('getByUser', function() {
        return function(input, id) {
            if(!input || id < 1)
                return -1;
            for (var i=0; i<input.length; i++) {
                if (input[i].user_id == id) {
                    return i;
                }
            }
            return -1;
        }
    })
    .filter('unique', function() {
        return function(arr1, arr2) {
            if(!arr2){
                return arr1;
            }

            var output = [];
            angular.forEach(arr1, function(item) {
                var found = -1;
                for(var i = 0; i < arr2.length && found <0; i++){
                    var item2 = arr2[i];
                    if(item2.id == item.id)
                        found = i;
                }

                if(found < 0) {
                    output.push(item);
                }
            });

            return output;
        };
    })
    .filter('split', function() {
        return function(input, splitChar, splitIndex) {
            // do some bounds checking here to ensure it has that index
            return input.split(splitChar)[splitIndex];
        }
    });

appZooMov.run(function ($rootScope, $translate, $cookieStore, $http) {

    $rootScope.setValue = function (array, val, callback) {
        var index = -1;
        for(var i = 0; i < array.length && index < 0; i++){
            if(array[i].id == val.id){
                index = i;
                array[i] = val;
            }
        }

        if(callback){
            eval(callback);
        }

        return i;
    }

    $rootScope.removeValue = function (array, val, key) {
        if(!key)
            key = 'id';
        var index = -1;
        var result = null;
        for(var i = 0; i < array.length && index <0; i++){
            if(array[i][key] == val){
                index = i;
                result = array[i];
            }
        }
        array.splice(index, 1)
        return result;
    }

    $rootScope.languages = [{id:"zh", name:"中"}, {id:"en", name:"EN"}];
    $rootScope.setLanguage = function (lang) {
        $rootScope.loading();
        $http.get('/languages/'+lang, {_token:$('input[name="csrfmiddlewaretoken"]').val()})
            .success(function () {
                 window.location.reload();
            })
    }

        var href = window.location.href;

        var regex = /(\w+)/y;
        regex.lastIndex = href.lastIndexOf("/") + 1;
        var marches = href.match(regex);
        if(marches){
            $rootScope.currentPath = marches[0];
        }

        $rootScope.loading = function () {
            $("#crazyloader").show();
        }

        $rootScope.loaded = function () {
            $("#crazyloader").hide();
        }

/*        $http.get('/lang/current')
            .success(function (lang) {
                for(var i = 0; i < $rootScope.languages.length; i++)
                {
                    if($rootScope.languages[i].id === lang){
                        $rootScope.currentLang = $rootScope.languages[i];
                        i = $rootScope.languages.length;
                    }
                }

            })
            .error(function () {
                $rootScope.currentLang = $rootScope.languages[1];
            });*/


    $rootScope.setPage = function (data) {
        return {
            show:data.last_page>1,
            currentPage: data.current_page,
            lastPage: data.last_page,
            perPage: data.per_page,
            total:data.total
        };
    }

    $rootScope.getTotal = function (list) {
        var total = 0;
        if(list){
            for (var i = 0; i < list.length; i++) {
                total += list[i].quantity;
            }
        }

        return total;
    }

    $rootScope.differenceInDays = function(firstdate, seconddate) {
        var one, two;
        if(!firstdate){
            one = new Date();
        }
        else{
            var dt1 = firstdate.split(' ')[0].split('-');
            one = new Date(dt1[0], dt1[1]-1, dt1[2]);
        }

        if(!seconddate){
            two = new Date();
        }
        else{
            var dt2 = seconddate.split(' ')[0].split('-');
            two = new Date(dt2[0], dt2[1]-1, dt2[2]);
        }

        var millisecondsPerDay = 1000 * 60 * 60 * 24;
        var millisBetween = two.getTime() - one.getTime();
        var days = millisBetween / millisecondsPerDay;

        return Math.floor(days);
    };

});

appZooMov.value('sexes',['S','F','M'])
appZooMov.directive('ckEditor', function ($rootScope) {
    return {
        require: '?ngModel',
        link: function (scope, elm, attr, ngModel) {
            var ck = CKEDITOR.replace(elm[0], {
                language: $rootScope.currentLang ? $rootScope.currentLang.id : 'en',
                uiColor: '#ffffff',
                toolbarGroups: [
                    {"name":"basicstyles","groups":["basicstyles"]},
                    {"name":"links","groups":["links"]},
                    {"name":"paragraph","groups":["list","blocks"]},
                    {"name":"document","groups":["mode"]},
                    {"name":"insert","groups":["insert"]},
                    {"name":"styles","groups":["styles"]},
                    {"name":"about","groups":["about"]}
                ],
                height: '30em'
        });
            if (!ngModel) return;

            ck.on('pasteState', function () {
                scope.$apply(function () {
                    ngModel.$setViewValue(ck.getData());
                });
            });

            ngModel.$render = function (value) {
                ck.setData(ngModel.$viewValue);
            };
        }
    };
});