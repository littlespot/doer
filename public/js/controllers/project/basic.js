/**
 * Created by Jieyun on 28/02/2016.
 */
appZooMov.controller("projectCtrl", function($rootScope, $scope) {
    $scope.init = function (langs) {
        $scope.project = {lang: angular.fromJson(langs)};
        $rootScope.loaded();
    }

    $scope.addLang = function (lang) {
        alert(lang);
        var lang_opt = $('#opt_lang_' + lang);
        $scope.project.lang.push({language_id:lang, name:lang_opt.text(), rank:lang_opt.attr('rank')});
        $scope.newLang = '';
        $('#opt_lang_' + lang).remove();
    }

    $scope.removeLang = function (lang) {
        var index = -1;
        for(var i = 0; i < $scope.project.lang.length && index < 0; i++){
            var l = $scope.project.lang[i];
            if(l.language_id.equals(lang)){
                index = i;
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
        $('#basicinfo').submit();
    }
});