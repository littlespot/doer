/**
 * Created by Jieyun on 2016/12/1.
 */
appZooMov.controller("questionCtrl", function($sce, $rootScope, $scope, $log, $http,$uibModal) {
    $scope.answer = {new:true};

    $scope.answerPageChanged = function () {
        $http.get('/api/answers/' + $scope.id, {params:{page: $scope.apagination.currentPage}})
            .success(function (result) {
                $scope.answers = result.data;
            })
            .error(function (err) {
                $log.error('failure loading answers for question ' + $scope.id + ' page ' + $scope.apagination.currentPage, err);
            });
    }

    $scope.relatePageChanged = function () {
        $http.get(window.location.origin + '/api/questionRelated/' + $scope.id + '?page=' + $scope.rpagination.currentPage)
            .success(function (result) {
                $scope.relates = result.data;
            })
            .error(function (err) {
                $log.error('failure loading related questions for question ' + $scope.id + ' page ' + $scope.rpagination.currentPage, err);
            })
    }

    $scope.init = function (id, cnt, answer) {
        $scope.id = id;
        var cnt = parseInt(cnt);
        if(!cnt)
            cnt = 0;

        $scope.answer.new = answer || !cnt;

        $scope.mineCnt = cnt;

        $http.get('/api/answers/' + $scope.id)
            .success(function (result) {
                $scope.answers = result.data;
                $scope.apagination = $rootScope.setPage(result);
            })
            .error(function (err) {
                $log.error('failure loading answers for question ' + $scope.id, err);
            });

        $http.get('/api/questionRelated/' + $scope.id)
            .success(function (result) {
                $scope.relates = result.data;
                $scope.rpagination = $rootScope.setPage(result);

                $rootScope.loaded();
            })
            .error(function (err) {
                $log.error('failure loading related questions for question ' + $scope.id, err);
            })
    }

    $scope.supportAnswer = function (answer, user) {
        if (answer.user_id.equals(user))
            return;

        answer.supporting = true;
        if (answer.mysupport) {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'confirm.html',
                controller: function ($scope) {
                    $scope.confirm = 'confirmS';
                }
            });

            modalInstance.result.then(function (confirm) {
                if (!confirm)
                    return;
                $scope.supportConfirmed(answer);
            })
        }
        else {
            $scope.supportConfirmed(answer);
        }
    }

    $scope.supportConfirmed = function (answer) {
        $http.put('/admin/answers/' + answer.id)
            .success(function (result) {
                if(!result)
                    return;
                answer.mysupport = result.mysupport;
                answer.supports_cnt += result.cnt;
                answer.supporting = false;
            })
            .error(function (err) {
                $log.error('failure follow question for question ' + answer.id, err);
            });
    }

    $scope.cancel = function () {
        $scope.answer = {new:false};
    }

    $scope.follow = function () {
        $scope.following = true;
        $http.put('/admin/questions/'+ $scope.id)
            .success(function(){
                var follower = $("#followers");
                var counter = $('#count', follower);
                var cnt = parseInt(counter.text().replace(/[\D]*/,''), 0);
                if(!cnt)
                    cnt = 0;
                var mark = $('.fa', follower);
                if(mark.hasClass('fa-bookmark-o')){
                    mark.removeClass('fa-bookmark-o').addClass('fa-bookmark');
                    counter.text(cnt + 1);
                }
                else{
                    mark.removeClass('fa-bookmark').addClass('fa-bookmark-o');
                    if(cnt > 1)
                        counter.text(cnt - 1);
                    else
                        counter.text('');
                }

                $scope.following = false;
            })
            .error(function (err) {
                $log.error('failure follow question for question ' + question.id, err);
            });
    }

    $scope.send = function () {
        $scope.answer.sending = true;
        $scope.answer.question_id = $scope.id;
        $scope.answer.editor = $('#editor').html();
        $http.post('/admin/answers', $scope.answer)
            .success(function (result) {
                result.supports_cnt = 0;
                result.mine = 1;
                result.newest = 1;
                $scope.answers.splice(0,0, result);
                $scope.mineCnt += 1;
                $scope.apagination.total += 1;
                $scope.answer = {new:false};
                $scope.answer.sending = false;
            })
            .error(function (err) {
                $log.error('failure save answers for question ' + $scope.id, err);
            });
    }

    $scope.deleteAnswer = function (answer, auth) {

        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'confirm.html',
            controller: function($scope) {
                $scope.confirm = 'confirmA';
            }
        });

        modalInstance.result.then(function (confirm) {
           if(!confirm)
               return;

            var page = 0;
            if($scope.apagination.show){
                if($scope.apagination.currentPage.equals($scope.apagination.lastPage))
                {
                    page = $scope.answers.length < 0 ? $scope.apagination.currentPage -1 : 0;
                }
                else{
                    page = $scope.apagination.currentPage;
                }
            }
            answer.deleting = true;
            if(!answer.user_id.equals(auth))
                return;
            $http.delete('/admin/answers/' +answer.id)
                .success(function (result) {
                    $scope.mineCnt -= 1;
                    if(page){
                        $scope.answers = result.data;
                        $scope.apagination = $rootScope.setPage(result);
                        answer.deleting = false;
                    }
                    else {
                        var index = -1;

                        for(var i = 0; i < $scope.answers.length && index < 0; i++){
                            if($scope.answers[i].id.equals(answer.id)){
                                index = i;
                                $scope.answers.splice(index,1);
                                $scope.apagination.total -= 1;
                            }
                        }
                        answer.deleting = false;
                    }
                })
                .error(function (err) {
                    $log.error('failure delete answers ' + answer.id, err);
                })

        }, function () {
            $log.info('Modal dismissed at: ' + new Date());
        });
    }
});
