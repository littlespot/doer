appZooMov.directive('inviteContent', function ($http, $log, $uibModal) {
    return {
        restrict:'A',
        link: function (scope) {
            if(!scope.invitation.occupations){
                $http.get('/occupations')
                    .success(function (result) {
                        scope.invitation.occupations = result;
                        scope.invitation.occupation = scope.invitation.occupations[0];
                    })
                    .error(function (err) {
                        $log.error('failed to get occupations', err);
                    });
            }

            if(!scope.invitation.projects){
                $http.get('/api/mine/projects')
                    .success(function (result) {
                        scope.invitation.projects = result;
                        scope.invitation.project = scope.invitation.projects[0];
                        scope.invitation.loading = false;
                    })
                    .error(function (err) {
                        $log.error('failed to get occupations', err);
                    });
            }

            scope.cancelInvite = function () {
                scope.invitation.message = "";
                scope.$apply(scope.callback(false));
            }

            scope.sendInvite = function(){
                scope.invitation.loading = true;
                $http.post('/admin/invitations/', {
                    receiver_id:scope.invitation.receiver.id,
                    occupation_id:scope.invitation.occupation.id,
                    project_id:scope.invitation.project.id,
                    message:scope.invitation.message
                })
                    .success( function(){
                        scope.invitation.loading = false;
                        var modalInstance = $uibModal.open({
                            animation: true,
                            templateUrl: 'feedback.html'
                        });

                        modalInstance.result.then(function () {
                            scope.callback(true);
                            scope.invitation.message = "";

                        })
                    })
                    .error(function(err){
                        $log.error('failed to send invitation to ' + scope.invitation.receiver.id, err)
                    });
            }
        }
    }
});
