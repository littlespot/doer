/**
 * Created by Jieyun on 2016/12/1.
 */
appZooMov.controller("reportsCtrl", function($rootScope, $scope, $http, $log, $uibModal) {
    $scope.selectedTab = 'writes';
    $scope.filter = {order:'created_at'};

    $scope.selectTab = function (tab) {
        $scope.selectedTab = tab;

        $scope.loading = true;
        $http.get('/person/' +  $scope.selectedTab +'/' + $scope.id,  {params:$scope.filter})
            .success(function (result) {
                $scope.results = result.data;
                $scope.pagination = $rootScope.setPage(result);
                $scope.loading = false;
            })
            .error(function (err) {
                $log.error('faild to load ' + tab + ' of user ' + $scope.id, err);
            })
    }

    $scope.pageChanged = function () {
        $scope.loading = true;
        $http.get('/person/' +  $scope.selectedTab +'/' + $scope.id, {
            params: angular.extend({}, $scope.filter, {page: $scope.pagination.currentPage})
        })
            .success(function (result) {
                $scope.results = result.data;
                $scope.loading = false;
            })
            .error(function (err) {
                $log.error('faild to load ' + $scope.selectedTab + ' of user ' + $scope.id, err);
            })
    }


    $scope.init = function (id, tab) {
        $scope.id = id;
        $scope.selectedTab = tab;
        $rootScope.loaded();
    }

    $scope.deleteComment = function (comment) {

        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'confirm.html',
            controller: function($scope) {
                $scope.confirm = 'confirmC';
            }
        });

        modalInstance.result.then(function (confirm) {
            if (!confirm)
                return;

            $http.delete('/admin/reports/comment/' + comment.id, {
                params: {_token: $("body input[name='csrfmiddlewaretoken']").val()}
            })
                .then(function successCallback(result) {
                    if(!result)
                        return;

                    $scope.pageReload(comment.id, comment.deleting);

                }, function errorCallback(err) {
                    $log.error('faled to delete commet', err);
                });
        })
    }

    $scope.supportComment = function (comment) {
        comment.supporting = true;

        if (comment.mysupport) {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'confirm.html',
                controller: function ($scope) {
                    $scope.confirm = 'confirmC';
                }
            });

            modalInstance.result.then(function (confirm) {
                if (!confirm)
                    return;
                $scope.supportConfirmed(comment);
            })
        }
        else {
            $scope.supportConfirmed(comment);
        }
    }

    $scope.supportConfirmed = function(comment)
    {
        $http.put('/admin/reports/comment/' + comment.id, {
            _token: $("body input[name='csrfmiddlewaretoken']").val()
        })
            .success(function (result) {
                if(!result)
                    return;

                comment.mysupport = result.mysupport;
                comment.supports_cnt += result.cnt;
                comment.supporting = false;
            })
            .error(function (err) {
                $log.error('failure support for comment ' + comment.id, err);
            });
    }

    $scope.loveReport = function (report, admin) {
        if(report.mine)
            return;

        report.loving = true;
        if(report.mylove){
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
                $scope.loveConfirmed(report, admin);
            })
        }
        else{
            $scope.loveConfirmed(report, admin);
        }
    }

    $scope.loveConfirmed = function (report, admin) {
        $http.put('/admin/reports/love/' + report.id, {
            _token: $("body input[name='csrfmiddlewaretoken']").val()
        })
            .success(function (result) {
                if(!result)
                    return;
                if (admin) {
                    $scope.pageReload(report.id, report.loving);
                }
                else{
                    report.mylove = result.mylove;
                    report.lovers_cnt += result.cnt;
                    report.loving = false;
                }
            })
            .error(function (err) {
                $log.error('failure love for report ' + report.id, err);
            });
    }


    $scope.pageReload = function (id, flag) {
        if($scope.pagination.show && ($scope.pagination.currentPage != $scope.pagination.lastPage || $scope.results.length == 1))
        {
            if($scope.results.length == 1){
                $scope.pagination.currentPage -= 1;
            }

            $http.get('/person/' +  $scope.selectedTab +'/' + $scope.id,  {
                params: angular.extend({}, $scope.filter, {page: $scope.pagination.currentPage})
            })
                .success(function (result) {
                    $scope.results = result.data;
                    $scope.pagination = $rootScope.setPage(result);
                    flag = false;
                })
                .error(function (err) {
                    $log.error('faild to load ' + $scope.selectedTab + ' of user ' + $scope.id, err);
                })

            $("#tab-"+$scope.selectedTab+">.text-important").text($scope.pagination.total);
        }
        else {
            var index = -1;
            for(var i = 0; i < $scope.results.length && index < 1; i++){
                if($scope.results[i].id == id){
                    index = i;
                    $scope.results.splice(index, 1);

                    var questions = $("#tab-"+$scope.selectedTab+">.text-important");
                    var questions_cnt = parseInt(questions.text().replace(/[\D]*/,''));
                    if(questions_cnt){
                        questions.text(questions_cnt-1);
                    }

                    $scope.pagination.total -= 1;
                }
            }

            flag = false;
        }
    }
})