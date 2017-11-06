/**
 * Created by Jieyun on 2016/12/1.
 */
appZooMov.controller("reportCtrl", function($rootScope, $scope, $http, $log, $uibModal) {
    
    $scope.init = function (id, project) {
        $scope.id = id;
        $scope.project = project;
        $rootScope.loaded();

        $scope.commenting = true;

        $http.get('/api/reports/comment/' + $scope.id)
            .success(function (result) {
                $scope.comments = result.data;
                $scope.pagination = $rootScope.setPage(result);
                $scope.commenting = false;
            })
            .error(function (err) {
                $log.error('faled to load comments for report ' + $scope.id, err)
            })

        $scope.reporting = true;
        $http.get('/api/project/reports/' + $scope.project+ '?report_id='+$scope.id)
            .success(function(reports){
                $scope.reports = reports.data;
                $scope.rpagination = $rootScope.setPage(reports);
                $scope.reporting = false;
            })
            .error(function (err) {
                $log.error('faled to load related reports for report ' + $scope.id, err)
            })
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
            $http.delete('/admin/reports/' + $scope.id,{
                params:{_token: $("body input[name='csrfmiddlewaretoken']").val()}
            })
                .success(function () {
                    window.location.href = "/project/" + $scope.project +'?tab=2';
                })
                .error(function (err) {
                    $log.error('failed to delete report '+$scope.id, err)
                })
        })
    }

    $scope.love = function () {
        $scope.loving = true;
        if($('#lovers >.fa').hasClass('fa-heart')){
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'confirm.html',
                controller: function($scope) {
                    $scope.confirm = 'confirmL';
                }
            });

            modalInstance.result.then(function (confirm) {
                if (!confirm)
                    return;
                $scope.loveConfirmed();
            })
        }
        else{
            $scope.loveConfirmed();
        }
    }

    $scope.loveConfirmed = function () {
        $http.put('/admin/reports/love/' + $scope.id, {_token: $("body input[name='csrfmiddlewaretoken']").val()})
            .success(function (result) {
                if(!result)
                    return;

                var count = parseInt($("#count").text().replace(/[\D]*/,''));
                if(!count)
                    count = 0;

                if(result.mylove){
                    $('#lovers >.fa').removeClass('fa-heart-o').addClass('fa-heart');
                }
                else{
                    $('#lovers >.fa').removeClass('fa-heart').addClass('fa-heart-o');
                }

                count += result.cnt;

                $("#count").text(count ? count : '');

                $scope.loving = false;
            })
            .error(function (err) {
                $log.error('failure love for report ' + $scope.id, err);
            });
    }


});
