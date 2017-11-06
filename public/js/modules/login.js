/**
 * Created by Jieyun on 23/02/2016.
 */

var appZooMov = angular
    .module('zooApp', ['ui.bootstrap','ngCookies', 'pascalprecht.translate']);

appZooMov.config(function($interpolateProvider, $translateProvider) {
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

    $rootScope.getParam = function () {
        var path = window.location.pathname;
        if(path[path.length - 1] === '/')
            path = path.substring(0, path.length - 2);
        var startpath = path.lastIndexOf('/') + 1;
        var endpath = path.lastIndexOf('?', startpath);
        endpath = endpath > 0 ? endpath : path.length;
        return path.substring(startpath, endpath);
    }

    $rootScope.languages = [{id:"zh", name:"中", checked:false}, {id:"en", name:"EN", checked:true}];
    $rootScope.getCurrentLanguage = function (lang) {
        for(var i = 0; i < $rootScope.languages.length; i++)
        {
            if($rootScope.languages[i].id == lang){
                $rootScope.currentLang = $rootScope.languages[i];
                i = $rootScope.languages.length;
            }
        }

        if($translate.use() != lang){
            $translate.use(lang)
        }
    }

    $rootScope.setLanguage = function (lang) {
        $rootScope.loading();
        $http.get('/languages/'+lang, {_token:$('input[name="csrfmiddlewaretoken"]').val()})
            .success(function () {
                window.location.reload();
            })
    }
    $rootScope.loading = function () {
        $("#crazyloader").show();
    }

    $rootScope.loaded = function () {
        $("#crazyloader").hide();
    }
    $rootScope.init = function (lang) {
        $rootScope.loaded();
        $rootScope.getCurrentLanguage(lang);
    }
});


appZooMov.controller('pwdCtrl', function () {
    $scope.changePwd = function(pwd){
        if(pwd.old == pwd.new){
            $scope.result = 'samepwd';
            $scope.openModal(true);
        }
        else {
            $http.put('/account/'+ pwd, {_token: $('input[name="csrfmiddlewaretoken"]').val()})
                .success(function (data) {
                    if (data == '1') {
                        $uibModal.open({
                            animation: true,
                            templateUrl: 'alert.html',
                            controller: function($scope) {
                                $scope.alert = 'pwd';
                            }
                        });
                    }
                    else {
                        $uibModal.open({
                            animation: true,
                            templateUrl: 'alert.html',
                            controller: function($scope) {
                                $scope.alert = 'errpwd';
                            }
                        });
                    }
                }).error(function (err) {
                    alert('error' + err);
                }
            );
        }
    }

    $scope.regex = /^(?=.*[\d])(?=.*[!@#$%^&*-])[\w!@#$%^&*-]{6,16}$/;
})