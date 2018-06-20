
appZooMov.controller("messagesCtrl", function($rootScope,$http, $scope, $filter) {

    $scope.init = function (projects, invitations, messages) {
        $scope.message = {id:0};
        $scope.mail = {subject:'', body:''};
        var invitations = angular.fromJson(invitations);
        $scope.in_invitations_cnt = 0;
        $scope.out_invitations_cnt = 0;

        if(invitations.length == 2){
            $scope.in_invitations_cnt = invitations[0].cnt;
            $scope.out_invitations_cnt = invitations[1].cnt;
        }
        else if(invitations.length == 1){
            if(invitations[0].outbox < 1)
                $scope.in_invitations_cnt = invitations[0].cnt;
            else
                $scope.out_invitations_cnt = invitations[0].cnt;
        }

        var messages = angular.fromJson(messages);
        $scope.in_messages_cnt = 0;
        $scope.out_messages_cnt = 0;

        if(messages.length == 2){
            $scope.in_messages_cnt = messages[0].cnt;
            $scope.out_messages_cnt = messages[1].cnt;
        }
        else if(messages.length == 1){
            if(messages[0].outbox < 1)
                $scope.in_messages_cnt = messages[0].cnt;
            else
                $scope.out_messages_cnt = messages[0].cnt;
        }

        $scope.projects = angular.fromJson(projects);
        if($scope.projects.length > 0){
            $scope.invitation = {message:"", projects:$scope.projects, project:$scope.projects[0]};
        }

        $scope.selectTopTab('invitations', 'in');
        $scope.invitation = {};

        $rootScope.loaded();
    }

    $scope.pageChanged = function () {
        $http.get('/api/' +  $scope.selectedType, {
            params:{box:$scope.selectedBox, page:$scope.pagination.currentPage}
        })
            .success(function (result) {
                $scope.messages = result.data;
            })
    }

    $scope.invite = function () {
        if(!$scope.invitation.receivers){
            $http.get('/api/teams')
                .success(function (result) {
                    $scope.invitation.receivers = result;
                    $scope.invitation.receiver = result[0];
                    $('#invitationModal').modal('show')
                })
        }
        else{
            $('#invitationModal').modal('show')
        }
    }

    $scope.write = function () {
        if(!$scope.userIndex){
            $http.get('/users')
                .success(function (result) {
                    $scope.userIndex = result;
                    $('#messageModal').modal('show');
                })
        }
        else{
            $('#messageModal').modal('show');
        }
    }
    $scope.cancel = function () {
        $scope.selectedView = 0;
    }

    $scope.cancelResponse = function () {
        $scope.message = {id:0};
    }

    $scope.callback = function (message) {
        $scope.selectedView = 0;
        if(!message){
            return;
        }

        if($scope.selectedType == "invitations")
            $scope.out_invitations_cnt += 1;
        else
            $scope.out_messages_cnt += 1;

        if($scope.selectedBox == 'out'){
            $http.get('/api/' + $scope.selectedType, {
                params:{box:'out'}
            })
                .success(function (result) {
                    $scope.messages = result.data;
                    $scope.pagination = $rootScope.setPage(result);
                })
        }
    }

    $scope.selectTopTab = function (index, box) {
        $scope.selectedType = index;
        $scope.selectedBox = box;

        $http.get('/api/' + index, {
            params:{box:box}
        })
            .success(function (result) {
                $scope.messages = result.data;
                $scope.pagination = $rootScope.setPage(result);
            })
    }

    $scope.read = function (message, checked) {
        if($scope.message.id == message.id){
            $scope.message = {id:0};
        }
        else{
            var message = $filter('getById')($scope.messages, message.id);
            if(!message.letter){
                $http.get('/api/' + $scope.selectedType + '/' + message.id, {
                    params:{checked: checked ? checked: message.checked, parent:message.parent_id ? message.parent_id : 0}
                })
                    .success(function (result) {
                        message.letter = result.letter;
                        if(result.replies){
                            message.replies = result.replies;
                        }
                        if(!checked && !message.checked){
                            message.checked = 1;
                            var sup = $("#" + $scope.selectedBox + "_" + $scope.selectedType);
                            var count = parseInt(sup.text().replace(/[\D]*/, ''));

                            if (count) {
                                count -= 1;
                            }

                            sup.text(count ? count : '');
                        }

                        $scope.message = message
                    })
            }
            else
                $scope.message = message
        }
    }

    $scope.sendMail = function (user, invalid) {
        if(invalid)
            return;
        var message = $scope.mail;
        message.loading = true;

        $http.post('/admin/messages', {receiver_id:user.originalObject.id, subject:message.subject, body:message.body})
            .success(function (result) {
                message.loading = false;
                $scope.out_messages_cnt += 1;
                $scope.mail = {subject:'', body:''};
                $scope.selectedView = 0;
                $('#messageModal').modal('hide');
            })
            .error(function (err) {
                alert(JSON.stringify(err));
            })
    }

    $scope.sendResponse = function (message, response, invalid) {
        if(invalid)
            return;
        message.deleting = true;

        $http.post('/admin/messages', {parent_id:message.parent_id ? message.parent_id : message.id, receiver_id:message.sender_id, subject:message.subject, body:response})
            .success(function (result) {
                message.deleting = false;
                message.replied = 1;
                $scope.out_messages_cnt += 1;
                $scope.message = {id:0};
            })

    }

    $scope.deleteMessage = function (message, all) {
        $scope.messageToDelete = message;
        $scope.messageToDelete.all = all?1:0;
        $('#deleteModal').modal('show');
    }

    $scope.messageDeleted = function () {
        var message = $scope.messageToDelete;

        $http.delete('/admin/' + $scope.selectedType + '/' + message.id, {
            params:{box:$scope.selectedBox, parent: $scope.messageToDelete.all}
        })
            .success(function (result) {
                if($scope.selectedBox == "in"){
                    $scope.messages = result.data;
                    $scope.pagination = $rootScope.setPage(result);
                    $scope.in_messages_cnt = $scope.pagination.total;
                }
                else{
                    $scope.loadPage(message.id);
                    $scope.out_messages_cnt = $scope.pagination.total;
                }
                $('#deleteModal').modal('hide');
                $scope.message = {id:0};
            })
    }

    $scope.updateInvitation = function (message, accept) {
        $scope.invitationToUpdate = message;
        $scope.invitationToUpdate.type = accept;
        $('#updateInvitationModal').modal('show');
    }

    $scope.invitationUpdated = function () {
        var message =  $filter('getById')($scope.messages, $scope.invitationToUpdate.id);
        $http.put('/admin/' + $scope.selectedType + '/' + message.id, {accept: $scope.invitationToUpdate.type})
            .success(function (result) {
                if(!result)
                    return;

                if(result != 'OK'){
                    return;
                }

                var sup = $("#" + $scope.selectedType + "_cnt");
                var count = parseInt(sup.text().replace(/[\D]*/, ''));

                if (!count) {
                    count = 1;
                }
                else {
                    count -= 1;
                }

                sup.text(count > 0 ? count : '');
                message.accepted = $scope.invitationToUpdate.type;
                message.deleting = false;
                $('#updateInvitationModal').modal('hide');
            })
    }
    $scope.deleteInvitation = function (message) {
        $scope.invitationToDelete = message;
        $('#deleteInvitationModal').modal('show');
    }

    $scope.invitationDeleted = function () {
        var message = $scope.invitationToDelete;
        $http.delete('/admin/' + $scope.selectedType + '/' + message.id, {
            params:{box: $scope.selectedBox}
        })
            .success(function (result) {
                if(!result)
                    return;

                $scope.loadPage(message.id);

                if($scope.selectedBox == 'in')
                    $scope.in_invitations_cnt = $scope.pagination.total;
                else
                    $scope.out_invitations_cnt = $scope.pagination.total;
                message.deleting = false;
                $('#deleteInvitationModal').modal('hide');
            })
    }


    $scope.loadPage = function (id) {
        if (!$scope.pagination.show || ($scope.pagination.currentPage == $scope.pagination.lastPage && $scope.messages.length > 1)) {
            var index = -1;
            for (var i = 0; i < $scope.messages.length && index < 0; i++) {
                if ($scope.messages[i].id == id) {
                    index = i;
                    $scope.messages.splice(index, 1);
                    $scope.pagination.total -= 1
                }
            }
        }
        else {
            if($scope.pagination.currentPage > 1 && $scope.messages.length == 1){
                $scope.pagination.currentPage -= 1;
            }

            $http.get('/api/' +  $scope.selectedType, {
                params:{box:$scope.selectedBox, page:$scope.pagination.currentPage}
            })
                .success(function (result) {
                    $scope.messages = result.data;
                    $scope.pagination = $rootScope.setPage(result);
                })
        }
    }
});
