/**
 * Created by Jieyun on 2016/12/1.
 */
appZooMov.directive('commentContent', function ($http, $log, $rootScope, $uibModal) {
    return {
        restrict:'A',
        link: function (scope, elm, attrs) {
            scope.selectedComment = {parent:{}, message:''};
            scope.newComment = {message:''};

            scope.openResponse = function (comment) {
                scope.selectedComment = {parent: comment};
            }

            scope.cancelResponse = function (reply) {
                if(reply) {
                    scope.selectedComment = {parent:{}, message:''};
                }else{
                    scope.newComment = {message:''};
                }
            }

            scope.sendResponse = function (reply) {
                var comment = reply ? scope.selectedComment: scope.newComment;
                if(!comment || !comment.message){
                    return;
                }
                else if(comment.message.length < 15){
                    return;
                }
                else if(comment.message.length > 800){
                    return;
                }
                $http({method:'POST',
                    url:'/admin/comment/'+attrs['relatedOption'],
                    data:{
                        parent_id: reply ? comment.parent.id:null,
                        message: comment.message,
                        related_id: scope.id
                    }})
                    .success(function (result) {
                        result.mine = true;
                        if (comment.parent) {
                            result.parent = comment.parent;
                        }
                        result.newest = true;
                        scope.comments.splice(0, 0, result);
                        scope.comments_cnt += 1;

                        $("#sup_comments").text(scope.comments_cnt);

                        scope.pagination.total = scope.comments_cnt;

                        scope.cancelResponse(reply);
                    })
                    .error(function (err) {
                        $log.error('faled to send comment', err);
                    });

                scope.error = null;
            }

            scope.supportComment = function (comment) {
                if(comment.mine)
                    return;
                comment.supporting = true;
                $http.put('/admin/' + attrs['relatedOption'] + '/comment/' + comment.id)
                    .success(function (result) {
                        if(comment.supported) {
                            comment.supports_cnt -= 1;
                            comment.supported = 0;
                        }
                        else {
                            comment.supports_cnt += 1;
                            comment.supported = 1;
                        }

                        comment.supporting = false;
                    })
                    .error(function (err) {
                        $log.error('failed to support comment', err)
                    })

            }

            scope.deleteComment = function (comment) {

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
                    $http.delete('/admin/' + attrs['relatedOption'] + '/comment/' + comment.id)
                        .then(function successCallback() {
                            if (!scope.pagination.show || (scope.pagination.currentPage.equals(scope.pagination.lastPage) && scope.comments.length > 1)) {
                                var index = -1;
                                for (var i = 0; i < scope.comments.length && index < 0; i++) {
                                    if (scope.comments[i].id.equals(comment.id)) {
                                        index = i;
                                        scope.comments_cnt -= 1;
                                        $("#sup_comments").text(scope.comments_cnt);
                                        scope.comments.splice(index, 1);
                                    }
                                }
                            }
                            else {
                                if(scope.pagination.currentPage > 1 && scope.comments.length == 1){
                                    scope.pagination.currentPage -= 1;
                                }

                                $http.get('/api/comments/' + scope.id + '?page=' + scope.pagination.currentPage)
                                    .success(function (result) {
                                        scope.comments = result.data;
                                        scope.comments_cnt = result.total;
                                        $("#sup_comments").text(scope.comments_cnt);
                                        scope.pagination = $rootScope.setPage(result);
                                    })
                                    .error(function (err) {
                                        $log.error('failure loading comments for project ' + scope.id + ' page ' + scope.pagination.currentPage, err);
                                    })
                            }

                            comment.deleting = false;

                        }, function errorCallback(err) {
                            $log.error('faled to delete commet', err);
                        });
                })
            }
        }
    }
})