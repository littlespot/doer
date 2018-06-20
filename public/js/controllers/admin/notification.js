/**
 * Created by Jieyun on 06/06/2016.
 */
appZooMov.controller("notificationsCtrl", function($rootScope, $scope, $http, $filter) {
    $scope.init = function (projects) {
        $scope.message = {id:0};
        $scope.projects = angular.fromJson(projects);
        $scope.reminder = {subject:'', project:null};
        if($scope.projects.length > 0){
            $scope.owned = $filter("filter")($scope.projects,{admin:true});
        }

        $scope.selectTopTab('applications', 'in');
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
                        if(result)
                            message.letter = result.letter;
                        if(checked && !message.checked){
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

        $http.post('/admin/reminders', $scope.reminder)
            .success(function (result) {
                if(result != 'OK'){
                    $scope.reminder.error = result;
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
                $scope.reminder = {subject:'', project:null};
                $scope.reminder.loading = false;
                $('#reminderModal').modal('hide');
            })
    }

    $scope.checkApplication = function (message) {
        message.deleting = true;
        $http.put('/admin/check/' + $scope.selectedType + '/' + message.place_id)
            .success(function () {
                message.checked = 1;
                message.deleting = false;
            });
    }

    $scope.acceptApplication = function (message) {
        $scope.applicationToUpdated = message;
        $('#acceptModal').modal('show');
    }
    $scope.refuseApplication = function (message) {
        $scope.applicationToUpdated = message;
        $('#refuseModal').modal('show');
    }
    $scope.applicationUpdated = function (accept) {
        $http.put('/admin/' + $scope.selectedType + '/' + $scope.applicationToUpdated.id, {accept: accept})
            .success(function (result) {
                if(!result)
                    return;

                var sup = $("#sup_applications");
                var count = parseInt(sup.text().replace(/[\D]*/,''));
                if(count){
                    count -= 1;
                    sup.text(count > 0 ? count:'');
                }
                $scope.applicationToUpdated.accepted = accept;
                if(accept){
                    $('#acceptModal').modal('hide');
                }
                else{
                    $('#refuseModal').modal('hide');
                }
                $scope.applicationToUpdated.deleting = false;
            })
    }

    $scope.deleteNotification = function (obj, opt, params) {
        $scope.objToDelete = {obj:obj, type:opt, params:params};
        $('#deleteModal').modal('show');
    }

    $scope.notificationDeleted = function () {
        if($scope.objToDelete.type == 'n'){
            $http.delete('/admin/notifications/' +  $scope.objToDelete.obj, {
                params: $scope.objToDelete.params
            })
                .success(function (result) {
                    $('#notification_' + $scope.objToDelete.obj).remove();
                    $('#deleteModal').modal('hide');
                })
        }
        else if($scope.objToDelete.type == 'a'){
            $http.delete('/admin/applications/' +  $scope.objToDelete.obj.id, {
                params: $scope.objToDelete.params
            })
                .success(function (result) {
                    if(!result)
                        return;
                    $('#deleteModal').modal('hide');
                    $scope.loadPage($scope.objToDelete.obj.id);
                })
        }
        else{
            $http.delete('/admin/' + $scope.selectedType + '/' + $scope.objToDelete.obj.id, {
                params: $scope.objToDelete.params
            })
                .success(function (result) {
                    $('#deleteModal').modal('hide');
                    $scope.loadPage($scope.objToDelete.obj.id);
                })
        }
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
