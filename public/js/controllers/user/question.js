/**
 * Created by Jieyun on 2016/12/1.
 */

appZooMov.controller("answersCtrl", function($rootScope, $scope, $http, $log, $uibModal) {
    $scope.selectedTab = 'asks';
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

    $scope.deleteQuestion = function (question) {
        if(!question.mine)
            return;
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'confirm.html',
            controller: function($scope) {
                $scope.confirm = 'confirmQ';
            }
        });

        modalInstance.result.then(function (confirm) {
            if (!confirm)
                return;
            question.deleting = true;
            $http.delete('/admin/questions/' + question.id)
            .success(function (result) {
                if(!result)
                    return;

                $scope.pageReload(question.id, question.deleting);
            })
            .error(function (err) {
                $log.error('failure delete question ' + question.id, err);
            });
        });
    }

    $scope.deleteAnswer = function (answer) {
        if(!answer.mine)
            return;
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'confirm.html',
            controller: function($scope) {
                $scope.confirm = 'confirmA';
            }
        });

        modalInstance.result.then(function (confirm) {
            if (!confirm)
                return;
            answer.deleting = true;
            $http.delete('/admin/answers/' + answer.id)
                .success(function (result) {
                    if(!result)
                        return;

                    $scope.pageReload(answer.id, answer.deleting);
                })
                .error(function (err) {
                    $log.error('failure delete question ' + answer.id, err);
                });
        });
    }

    $scope.followQuestion = function(question, admin){
        if(question.mine)
            return;

        question.following = true;
        if(question.myfollow){
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'confirm.html',
                controller: function($scope) {
                    $scope.confirm = 'confirmF';
                }
            });

            modalInstance.result.then(function (confirm) {
                if (!confirm)
                    return;
                $scope.followConfirmed(question, admin);
            })
        }
        else{
            $scope.followConfirmed(question, admin);
        }
    }

    $scope.followConfirmed = function (question, admin) {
        $http.put('/admin/questions/' + question.id)
            .success(function (result) {
                if(!result)
                    return;
                if (admin) {
                    $scope.pageReload(question.id, question.following);
                }
                else{
                    question.myfollow = result.myfollow;
                    question.followers_cnt += result.cnt;
                    question.following = false;
                }
            })
            .error(function (err) {
                $log.error('failure follow question for question ' + question.id, err);
            });
    }

    $scope.supportAnswer = function (answer, admin) {
        if(answer.mine)
            return;

        answer.supporting = true;
        if(answer.mysupport){
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'confirm.html',
                controller: function($scope) {
                    $scope.confirm = 'confirmS';
                }
            });

            modalInstance.result.then(function (confirm) {
                if (!confirm)
                    return;
                $scope.supportConfirmed(answer, admin);
            })
        }
        else{
            $scope.supportConfirmed(answer, admin);
        }
    }

    $scope.supportConfirmed = function (answer, admin) {
        $http.put('/admin/answers/' + answer.id)
            .success(function (result) {
                if(!result)
                    return;
                if (admin) {
                    $scope.pageReload(answer.id, answer.supporting);
                }
                else{
                    answer.mysupport = result.mysupport;
                    answer.supports_cnt += result.cnt;
                    answer.supporting = false;
                }
            })
            .error(function (err) {
                $log.error('failure follow question for question ' + answer.id, err);
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
                if($scope.results[i].id.equals(id)){
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