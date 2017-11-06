/**
 * Created by Jieyun on 06/06/2016.
 */
appZooMov.controller("notificationsCtrl", function($rootScope, $scope, $http, $filter,$uibModal, $log) {
    $scope.selectedView = 0;

    $scope.init = function (projects) {
        $scope.message = {id:0};
        $scope.projects = angular.fromJson(projects);
        if($scope.projects.length > 0){
            $scope.reminder = {message:"", project:$scope.projects[0]};
            $scope.owned = $filter("filter")($scope.projects,{admin:true});
        }

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

    $scope.remind = function () {
        if($scope.selectedView == 2)
            $scope.selectedView = 0;
        else{
            $scope.selectedView = 2;
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
                    params:{checked: checked ? checked: message.checked}
                })
                    .success(function (result) {
                        message.letter = result.letter;
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

    $scope.sendReminder = function (invalid) {
        if(invalid)
            return;
        $scope.reminder.project_id = $scope.reminder.project.id;
        $scope.reminder._token = $('input[name="csrfmiddlewaretoken"]').val();
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

            $scope.reminder.loading = true;

            $http.post('/admin/reminders', $scope.reminder)
                .success(function (result) {
                    if(result != 'OK'){
                        alert(result);
                        return;
                    }

                    var sup = $("#reminders_cnt");
                    var count = parseInt(sup.text().replace(/[\D]*/, ''));

                    if (!count) {
                        count = 1;
                    }
                    else {
                        count += 1;
                    }

                    sup.text(count);

                    sup = $("#out_reminders");
                    count = parseInt(sup.text().replace(/[\D]*/, ''));

                    if (!count) {
                        count = 1;
                    }
                    else {
                        count += 1;
                    }

                    sup.text(count);

                    if($scope.selectedType == 'reminders' && $scope.selectedBox == 'out'){
                        if($scope.pagination.currentPage == 1)
                            $scope.messages.splice(0, 0, $scope.reminder);

                        $scope.pagination.total += 1;
                    }
                    $scope.selectedView = 0;
                    $scope.reminder = {subject:"", message:"", project:$scope.projects[0]};
                    $scope.reminder.loading = false;
                })
        });
    }

    $scope.checkApplication = function (message) {
        message.deleting = true;
        $http.put('/admin/check/' + $scope.selectedType + '/' + message.place_id, {
            _token: $('input[name="csrfmiddlewaretoken"]').val()
        })
            .success(function () {
                message.checked = 1;
                message.deleting = false;
            });
    }

    $scope.updateApplication = function (message, accept) {
        var modalInstance = $uibModal.open({
            animation: true,
            size:'lg',
            templateUrl: 'confirm.html',
            controller: function ($scope) {
                $scope.confirm = accept ? 'confirmA' : 'confirmR';
            }
        });

        modalInstance.result.then(function (confirm) {
            if (!confirm)
                return;

            message.deleting = true;

            $http.put('/admin/' + $scope.selectedType + '/' + message.id, {accept: accept, _token:$('input[name="csrfmiddlewaretoken"]').val()})
                .success(function (result) {
                    if(!result)
                        return;

                    var sup = $("#sup_applications");
                    var count = parseInt(sup.text().replace(/[\D]*/,''));
                    if(count){
                        count -= 1;
                        sup.text(count > 0 ? count:'');
                    }

                    message.accepted = accept;
                    message.deleting = false;
                    $scope.message = {id:0};
                })
        });
    }

    $scope.deleteNotification = function (id) {
        var modalInstance = $uibModal.open({
            animation: true,
            size:'lg',
            templateUrl: 'confirm.html',
            controller: function ($scope) {
                $scope.confirm = 'confirmN';
            }
        });

        modalInstance.result.then(function (confirm) {
            if (!confirm)
                return;

           $rootScope.loading();

            $http.delete('/profile/' + id, {params:{_token: $('input[name="csrfmiddlewaretoken"]').val()}})
                .success(function () {
                   $('#notification_' + id).remove();
                    $rootScope.loaded();
                })
        });
    }
    $scope.deleteApplication = function (message) {
        var modalInstance = $uibModal.open({
            animation: true,
            size:'lg',
            templateUrl: 'confirm.html',
            controller: function ($scope) {
                $scope.confirm = 'confirmD';
            }
        });

        modalInstance.result.then(function (confirm) {
            if (!confirm)
                return;

            message.deleting = true;

            $http.delete('/admin/' + $scope.selectedType + '/' + message.id, {
                params:{box: 'in', _token: $('input[name="csrfmiddlewaretoken"]').val()}
            })
                .success(function (result) {
                    if(!result)
                        return;

                    $scope.loadPage(message.id);
                    message.deleting = false;
                })
        });
    }

    $scope.removeApplication = function (message) {
        var modalInstance = $uibModal.open({
            animation: true,
            size:'lg',
            templateUrl: 'confirm.html',
            controller: function ($scope) {
                $scope.confirm = 'confirmM';
            }
        });

        modalInstance.result.then(function (confirm) {
            if (!confirm)
                return;

            message.deleting = true;

            $http.delete('/admin/' + $scope.selectedType + '/' + message.id, {
                params:{box: 'out'}
            })
                .success(function (result) {
                    if(!result)
                        return;

                    $scope.loadPage(message.id);
                    message.deleting = false;
                })
        });
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
                })
        }
    }
});
