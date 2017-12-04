/**
 * Created by Jieyun on 2017/2/19.
 */
appZooMov.controller("reportNewCtrl", function($sce,$rootScope, $scope, $filter, $http, $uibModal) {

    $scope.init = function (tags) {
        $scope.newTags = "";
        $scope.report = {tags:[]};

        if(tags && tags.length> 0){
            $scope.report.tags = angular.fromJson(tags);
        }
        $http.get('/api/reports')
            .success(function (tags) {
                $scope.tags = tags;
                $rootScope.loaded();
            });
    }

    $scope.addTag = function (tag) {
        if(!$scope.report.tags)
            $scope.report.tags = new Array();
        var oldtag = $filter('getById')($scope.report.tags, tag.id);
        if(!oldtag || oldtag.length < 1) {
            if (!tag.chosen) {
                $scope.report.tags.push(tag);
                tag.chosen = true;
            }
        }
        else{
            tag.chosen = true;
        }
    }

    $scope.removeTag = function (tag, index) {
        $scope.report.tags.splice(index, 1);
        tag.chosen = false;
    }

    $scope.storeTags = function () {
        var newTags = $scope.newTags.split(',');
        var tag;
        $.each(newTags, function (i, item) {
            item = item.trim();
            if(item.length) {
                tag = $filter('filter')($scope.report.tags, {label: item});
                if(!tag || tag.length < 1) {
                    var found = false;
                    for(var i = 0; i < $scope.tags.length && !found; i++){
                        var oldtag = $scope.tags[i];
                        if(oldtag.label.equals(item)){
                            found = true;
                            oldtag.chosen = true;
                            $scope.report.tags.push(oldtag);
                        }
                    }
                    if (!found) {
                        $scope.report.tags.push({'id': 0, 'label': item});
                    }
                }
            }
        });

        $scope.newTags = "";
    }

    $scope.delete = function () {
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'confirm.html',
            controller: function($scope) {
                $scope.confirm = 'confirmD';
            }
        });

        modalInstance.result.then(function (confirm) {
            if (!confirm)
                return;
            $rootScope.loading();
            $http.delete('/admin/reports/' + $scope.id)
                .success(function () {
                    window.location.href = "/project/" + $scope.project +'?tab=2';
                })
                .error(function (err) {
                    $log.error('failed to delete report '+$scope.id, err)
                })
        })
    }

    $scope.save = function () {
        var content = $('#editor').text();
        if(content.length < 1 || content.length < 15){
            $scope.error = true;
        }
        else if($scope.report.tags.length > 0){
            $rootScope.loading();
            $("#reportForm").submit();
        }
    }
});