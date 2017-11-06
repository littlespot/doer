/**
 * Created by Jieyun on 23/02/2016.
 */

var appZooMov = angular
    .module('zooApp', ['ngCookies','ngRoute','ngResource','ngSanitize', 'ui.bootstrap',
        'angular-svg-round-progress', 'pascalprecht.translate', 'angular-scroll-animate', 'angucomplete']);

appZooMov.config(function($translateProvider) {
        $translateProvider.useStaticFilesLoader({
            prefix: 'i10n/',
            suffix: '.json'
        })
            .registerAvailableLanguageKeys(['en', 'de'], {
                'en' : 'en', 'en_GB': 'en', 'en_US': 'en',
                'zh' : 'zh', 'zh_CN': 'zh', 'GBK': 'zh'
            })
            .preferredLanguage('en')
            .fallbackLanguage('en')
            .useSanitizeValueStrategy('escapeParameters');
    });


appZooMov.run(function ($rootScope, $translate, $cookieStore, $http) {
        var userCookie = $cookieStore.get('user');
        if(userCookie != undefined){
            $rootScope.user = userCookie;
            $translate.use($rootScope.user.locale);
        }

        $rootScope.languages = [{id:"zh", name:"中", checked:false}, {id:"en", name:"EN", checked:true}];
        $rootScope.openMenu = false;
        $rootScope.setLanguage = function (lang) {
            $rootScope.loading();
            $('#current_local').val(lang);
            $('#currentLangForm').submit();
        }

        $rootScope.setLanguage = function(code){
            if($translate.use() != code) {
                var found = false;

                $.each($rootScope.languages, function (i, item) {
                    if(item.id == code){
                        found = true;
                        item.checked = true;
                        $rootScope.currentLang = item;
                    }
                    else{
                        item.checked = false;
                    }
                });

                if(found){
                    $http.get('lang/'+ $rootScope.currentLang.id);
                    $translate.use($rootScope.currentLang.id);
                }
            }

            return code;
        }

        $rootScope.openLang = function(){
            $rootScope.openMenu = true;
        }
    });

