appZooMov.directive('inviteContent', function ($http, $log) {
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
                        scope.invitation.sending = 0;
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
                scope.invitation.sending = 1;
                $http.post('/admin/invitations/', {
                    receiver_id:scope.invitation.receiver.id,
                    occupation_id:scope.invitation.occupation.id,
                    project_id:scope.invitation.project.id,
                    message:scope.invitation.message
                })
                    .success( function(result){
                        if(scope.selectedType == 'invitations' && scope.selectedBox == 'out'){
                            scope.invitation.id = result;
                            scope.invitation.username = scope.invitation.receiver.username;
                            scope.invitation.title = scope.invitation.project.title;
                            scope.invitation.name = scope.invitation.occupation.name;
                            var d = new Date();
                            var year = d.getFullYear();
                            var month = d.getMonth() + 1;
                            var date = d.getDate();
                            var hour = d.getHours();
                            var min = d.getMinutes();
                            scope.invitation.created_at = year + '-' + (month < 10 ? '0' + month : month) + '-' + (date < 10 ? '0' + date : date) + ' '+
                                (hour < 10 ? '0' + hour : hour) + ':' + (min < 10 ? '0' + min : min);
                            scope.messages.splice(0,0, scope.invitation);
                        }
                        scope.invitation.sending = 0;
                        $('#invitationModal').modal('hide');
                        $('#invitationConfirmModal').modal('show');
                    })
                    .error(function(err){
                        scope.invitation.sending = 0;
                        scope.invitation.error = err;
                        $log.error('failed to send invitation to ' + scope.invitation.receiver.id, err)
                    });
            }
        }
    }
});
