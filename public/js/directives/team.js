/**
 * Created by Jieyun on 2016/12/1.
 */
appZooMov.directive('teamContent', function ($rootScope, $http, $filter, $log, $uibModal) {
    return {
        restrict:'A',
        link: function (scope, attr) {
            var url = '/admin/teams/';
            scope.team_error = {user:null, role:null};
            scope.inTeam = function (id) {
                for(var i = 0; i < scope.team.length; i++){
                    if(scope.team[i].user_id == id){
                        return scope.team[i];
                    }
                }

                return null;
            }

            scope.memberSelected = function (selected) {
                scope.team_error.user = null;
                if (!selected.title) {
                    var modalInstance = $uibModal.open({
                        animation: true,
                        templateUrl: 'script.html',
                        size: 'lg',
                        controller: function ($scope) {
                            $scope.author = {name: selected.originalObject, email:scope.teamInEdit.location, link:scope.teamInEdit.link};
                        }
                    });

                    modalInstance.result.then(function (author) {
                        if (!author){
                            scope.team_error.user = 'i';
                            return false;
                        }

                        if (!author.email || !author.email.length){
                            scope.team_error.user = 'i';
                            return false;
                        }

                        if(!author.name  || !author.name.length){
                            scope.team_error.user = 'n';
                            return false;
                        }
                        var index = -1;
                        for (var i = 0; i < scope.users.length && index < 0; i++) {
                            if (scope.users[i].location == author.email) {
                                if (scope.users[i].username == author.name) {
                                    index = i;
                                    var member = scope.inTeam(scope.users[i]);
                                    if(member){
                                        var modalInstance = $uibModal.open({
                                            animation: true,
                                            templateUrl: 'confirm.html',
                                            controller: function ($scope) {
                                                $scope.confirm = 'doubleM';
                                            }
                                        });

                                        modalInstance.result.then(function (confirm) {
                                            if (confirm)
                                                scope.editTeam(member);
                                            else
                                                selected = null;

                                            return false;
                                        });
                                    }
                                    else{
                                        scope.teamInEdit.user_id = scope.users[i].id;
                                        scope.teamInEdit.username = scope.users[i].username;
                                        scope.teamInEdit.location = scope.users[i].location;
                                        scope.teamInEdit.link =  scope.users[i].link;
                                        scope.postTeam();
                                    }
                                }
                                else {
                                    i = scope.users.length;
                                    scope.team_error.user = 'e';
                                    return false;
                                }
                            }
                        }


                        if(index < 0){
                            scope.teamInEdit.user_id = 0;
                            scope.teamInEdit.username = author.name;
                            scope.teamInEdit.location = author.email;
                            scope.teamInEdit.link = author.link;
                            scope.postTeam();
                        }
                    })
                }
                else {
                    var member = scope.inTeam(selected.originalObject.id);
                    if (!member) {
                        scope.teamInEdit.user_id = selected.originalObject.id;
                        scope.teamInEdit.username = selected.title;
                        scope.teamInEdit.location = selected.originalObject.location;
                        scope.teamInEdit.link = selected.originalObject.link;
                        scope.postTeam();
                    }
                    else {
                        var modalInstance = $uibModal.open({
                            animation: true,
                            templateUrl: 'confirm.html',
                            controller: function ($scope) {
                                $scope.confirm = 'doubleM';
                            }
                        });

                        modalInstance.result.then(function (confirm) {
                            if (confirm)
                                scope.editTeam(member);
                            else
                                selected = null;

                            return false;
                        });
                    }
                }
            }

            scope.editMember = function (member) {
                var modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: 'script.html',
                    size: 'lg',
                    controller: function ($scope) {
                        $scope.author = {id:member.user_id, name:member.username, email:member.location, link:member.link};
                        $scope.script = 0;
                    }
                });

                modalInstance.result.then(function (author) {
                    if (!author)
                        return false;

                    var index = -1;
                    for(var i = 0; i < scope.users.length && index <0; i++) {
                        if (scope.users[i].location == author.email && scope.users[i].id != author.id) {
                            index = i;
                            scope.team_error.user = 'e';
                            return false;

                        }
                    }

                    member.username = author.name;
                    member.link = author.link;
                    member.location = author.email;
                })
            }

            scope.addTeam = function () {
                scope.teamInEdit = {id:0, project_id: scope.project.id, occupation:[], step:4, submitted:scope.submitted};
            }

            scope.cancelSave = function () {
                scope.teamInEdit = null;
            }

            scope.saveTeam = function () {
                if(scope.teamInEdit.username && scope.teamInEdit.username.length && (scope.teamInEdit.member.title == scope.teamInEdit.username || scope.teamInEdit.member.originalObject == scope.teamInEdit.username)){
                    scope.postTeam();
                }
                else{
                    scope.memberSelected(scope.teamInEdit.member);
                }
            }

            scope.postTeam = function () {

                if (!scope.teamInEdit.location || !scope.teamInEdit.location.length){
                    scope.team_error.user = 'i';
                    return false;
                }

                if(!scope.teamInEdit.username  || !scope.teamInEdit.username.length){
                    scope.team_error.user = 'n';
                    return false;
                }
                var roles = [];
                angular.forEach(scope.teamInEdit.occupation, function (occupation) {
                    roles.push(occupation.occupation_id)
                });

                if(roles.length == 0){
                    scope.team_error.role = 'r';
                    return false;
                }

                scope.teamInEdit.roles = roles.join();
                scope.teamInEdit._token = $("body input[name='csrfmiddlewaretoken']").val();

                $http.post(url, scope.teamInEdit)
                    .success(function (result) {
                        var team = angular.copy(scope.teamInEdit);
                        team.id = result.id;
                        if(!team.user_id){
                            team.user_id = result.outsider_id;
                            team.outsider = 1;
                        }
                        else{
                            team.outsider = team.user_id.substr(0,1) == 'o';
                        }

                        if(team.outsider){
                            var index = -1;
                            for(var i = 0; i < scope.team.length && index < 0; i++){
                                if(scope.team[i].outsider){
                                    index = i;
                                    scope.team.splice(index, 0, team);
                                }
                            }
                            scope.pagination.totatl += 1;
                            if(index < 0)
                                scope.team.push(team);
                        }
                        else if(!scope.submitted){
                            scope.team.splice(0,0, team);
                            scope.pagination.totatl += 1;
                        }
                        else{

                        }

                        scope.teamInEdit = null;
                    })
                    .error(function (err) {
                        $log.error('failed to save team', err);
                        alert(err);
                    });
            }

            scope.editTeam = function (member) {
                scope.team_error = null;
                if(scope.teamInEdit && scope.teamInEdit.id == member.id){
                    var roles = [];
                    var occupation = angular.copy(scope.teamInEdit.occupation);
                    angular.forEach(scope.teamInEdit.occupation, function (occupation) {
                        $('#opt_role_'+occupation.occupation_id).hide();
                        roles.push(occupation.occupation_id)
                    });

                    if(scope.submitted && !member.outsider && member.user_id != scope.project.user_id){
                        if(!roles.length){
                            scope.quit(member, 'confirmM')
                        }
                        else{
                            scope.quit(member, 'confirmN', roles)
                        }
                    }

                    if(roles.length == 0){
                        scope.team_error = 'r';
                        return false;
                    }

                    scope.teamInEdit.roles = roles.join();
                    scope.teamInEdit.project_id = scope.project.id;
                    scope.teamInEdit._token = $("body input[name='csrfmiddlewaretoken']").val();
                    $http.put(url + scope.teamInEdit.id, scope.teamInEdit)
                        .success(function (result) {
                            if(result.indexOf('P') >= 0){
                                var modalInstance = $uibModal.open({
                                    animation: true,
                                    templateUrl: 'alert.html',
                                    controller: function ($scope) {
                                        $scope.alert = 'deleteP';
                                    }
                                });

                                modalInstance.result.then(function () {
                                    member.occupation.push({id:20, name:'Planner'});
                                });
                            }
                            else if(result.indexOf('T') >= 0){
                                var modalInstance = $uibModal.open({
                                    animation: true,
                                    templateUrl: 'alert.html',
                                    controller: function ($scope) {
                                        $scope.alert = 'deleteT';
                                    }
                                });

                                modalInstance.result.then(function () {
                                    occupation.push({occupation_id:9, name:'Writer'});
                                });
                            }

                            member.occupation = occupation;
                            scope.teamCopy = null;
                            scope.teamInEdit = null;
                        })
                        .error(function (err) {
                            $log.error('failed to update team ' + scope.teamInEdit.id, err);
                            alert(err);
                        });
                }
                else{
                    scope.teamCopy = angular.copy(member.occupation);
                    scope.teamInEdit = member;
                    var cnt = 0;
                    for(var i = 0; i < scope.occupations.length && cnt < scope.teamInEdit.occupation.length; i++){
                        var found = false;
                        for(var j = 0; j < scope.teamInEdit.occupation.length && !found; j++){
                            if(scope.teamInEdit.occupation[j].occupation_id == scope.occupations[i].id){
                                found = true;
                                scope.occupations[i].old =1;
                                cnt++;
                            }
                        }
                    }

                    scope.teamInEdit.submitted = scope.submitted;
                }
            }

            scope.cancelTeam = function (member, submitted) {
                if(scope.teamInEdit && scope.teamInEdit.id == member.id){
                    member.occupation = angular.copy(scope.teamCopy);
                    scope.teamCopy = null;
                    scope.teamInEdit = null;

                    angular.forEach(scope.occupations, function(occupation) {
                        occupation.old = 0;
                    });
                }
                else{
                    if(!member.outider && member.user_id == scope.project.user_id){
                        var modalInstance = $uibModal.open({
                            animation: true,
                            templateUrl: 'alert.html',
                            controller: function ($scope) {
                                $scope.alert = 'deleteY';
                            }
                        });

                        modalInstance.result.then(function () {
                            return false;
                        });
                    }
                    else if(submitted && !member.outsider){
                        scope.quit(member, 'confirmM')
                    }
                    else{
                        var modalInstance = $uibModal.open({
                            animation: true,
                            templateUrl: 'confirm.html',
                            controller: function ($scope) {
                                $scope.confirm = 'confirmT';
                            }
                        });

                        modalInstance.result.then(function (confirm) {
                            if(!confirm)
                                return;

                            scope.team.loading = true;
                            $http.delete(url + member.id, {params:{_token: $("body input[name='csrfmiddlewaretoken']").val(), submitted: scope.submitted}})
                                .then(function successCallback() {
                                    if(scope.submitted){

                                        return;
                                    }
                                    if (!scope.pagination.show || (scope.pagination.currentPage == scope.pagination.lastPage && scope.team.length > 1)) {
                                        $rootScope.removeValue(scope.team,  member.id);
                                        scope.team.loading = false;
                                    }
                                    else {
                                        if (scope.pagination.currentPage > 1 && scope.comments.length == 1) {
                                            scope.pagination.currentPage -= 1;
                                        }

                                        scope.loadTeam(scope.pagination.currentPage);
                                    }
                                }, function errorCallback(response) {
                                    scope.team.loading = false;
                                    $uibModal.open({
                                        animation: true,
                                        templateUrl: 'alert.html',
                                        controller: function ($scope) {
                                            $scope.alert = 'delete'+response.data;
                                        }
                                    });
                                });
                        });
                    }
                }
            }

            scope.addRole = function(role){
                var opt = $("#opt_role_"+role);
                scope.teamInEdit.occupation.push({occupation_id:role, name: opt.text()});
                opt.hide();
                role='';
            }

            scope.removeRole = function(role){
                if(role.occupation_id==20){
                    $uibModal.open({
                        animation: true,
                        templateUrl: 'alert.html',
                        controller: function ($scope) {
                            $scope.alert = 'deleteP';
                        }
                    });
                }
                else{
                    $rootScope.removeValue(scope.teamInEdit.occupation, role.occupation_id, 'occupation_id');
                    $filter("getById")(scope.occupations, role.occupation_id).old = 0;
                    if(!scope.teamInEdit.occupation.length){
                        scope.team_error.role = 'r';
                    }
                }
            }


            scope.quit = function (member, confirm, occupations) {
                var modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: 'team.html',
                    size:'lg',
                    controller: function ($scope) {
                        $scope.confirm = confirm;
                        $scope.user = member.username;
                    }
                });
                scope.team.loading = true;
                modalInstance.result.then(function (message) {
                    if (!message)
                        return;

                    $http.post('/admin/invitations/', {
                        receiver_id: member.user_id,
                        project_id:scope.project.id,
                        message:message,
                        occupations:occupations,
                        quit:1,
                        _token:$('input[name="csrfmiddlewaretoken"]').val()
                    })
                        .success( function(){
                            scope.team.loading = false;
                            member.deleted = !occupations;
                        })
                        .error(function(err){
                            $log.error('failed to send invitation to ' + member.user_id, err)
                        });
                });
            }
        }
    }
});


