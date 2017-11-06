/**
 * Created by Jieyun on 2017/2/19.
 */
appZooMov.controller("questionNewCtrl", function($sce,$rootScope, $scope, $filter, $http) {

    $scope.init = function (tags) {
        if(tags == null || tags == undefined)
            $scope.qtags = [];
        else
            $scope.qtags = angular.fromJson(tags);

        $scope.error = {require:false, maxlength:false, minlength:false};

        $scope.newTags = "";

        $http.get('/api/questionTags')
            .success(function (tags) {
                $scope.tags = tags;
                $rootScope.loaded();
            });
    }

    $scope.addTag = function (tag) {
        if(!$scope.qtags)
            $scope.qtags = new Array();
        var oldtag = $filter('getById')($scope.qtags, tag.id);
        if(!oldtag || oldtag.length == 0) {
            if (!tag.chosen) {
                $scope.qtags.push(tag);
                tag.chosen = true;
            }
        }
        else{
            tag.chosen = true;
        }
    }

    $scope.removeTag = function (tag, index) {
        $scope.qtags.splice(index, 1);
        tag.chosen = false;
    }

    $scope.storeTags = function () {
        var newTags = $scope.newTags.split(',');
        var tag;
        $.each(newTags, function (i, item) {
            item = item.trim();
            if(item.length) {
                tag = $filter('filter')($scope.qtags, {label: item});
                if(!tag || tag.length == 0) {
                    var found = false;
                    for(var i = 0; i < $scope.tags.length && !found; i++){
                        var oldtag = $scope.tags[i];
                        if(oldtag.label == item){
                            found = true;
                            oldtag.chosen = true;
                            $scope.qtags.push(oldtag);
                        }
                    }
                    if (!found) {
                        $scope.qtags.push({'id': 0, 'label': item});
                    }
                }
            }
        });

        $scope.newTags = "";
    }

    $scope.save = function () {
    /*    var reg=new RegExp("/uploads/questions/\w+/","g"); //
        $("#editor img").each(function(i){
            var src = $(this).attr('src');

            src.replace(reg, "") ;
            $(this).attr("src", src);
        });
        return*/
        var length = $('#editor').text().length;
        if(length < 15){
            $scope.error.required = false;
            $scope.error.maxlength = false;
            $scope.error.minlength = true;
        }
        else if(length > 4000){
            $scope.error.required = false;
            $scope.error.maxlength = true;
            $scope.error.minlength = false;
        }
        else{
            $scope.error.required = false;
            $scope.error.maxlength = false;
            $scope.error.minlength = false;
        }
        if($scope.qtags.length > 0){
            $rootScope.loading();
            $('#editor-content').html($('#editor').html());
            $("#questionForm").submit();
        }
        else{
            return false;
        }
    }
});