/**
 * Created by Jieyun on 2016/12/1.
 */
appZooMov.directive('scriptContent', function ($rootScope, $http, $filter, $log, $uibModal) {
    return {
        restrict:'A',
        link: function (scope) {
            var url = '/admin/scripts/';

            scope.editAuthor = function (author) {
                if(!author.email || author.email.length == 0)
                    return;

                var modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: 'script.html',
                    size: 'lg',
                    controller: function ($scope) {
                        $scope.author = author;
                        $scope.script = 1;
                    }
                });

                modalInstance.result.then(function (author) {
                    if (!author)
                        return false;

                    var index = -1;
                    for(var i = 0; i < scope.authors.length && index <0; i++) {
                        if (scope.authors[i].location == author.email && scope.authors[i].id != author.id) {
                            index = i;
                            scope.scripts.error = 'email';
                            return false;

                        }
                    }

                    author._token = $("body input[name='csrfmiddlewaretoken']").val();

                    $http.put('/admin/users/' + author.id, author)
                        .success(function (result) {
                            var index = -1;
                            for(var i = 0; i < scope.authors.length && index < 0; i++){
                                if(scope.authors[i].id == result.id){
                                    index = i;
                                    scope.authors[index] = {id:result.id, username:result.name, location:result.email, link:result.link};
                                }
                            }

                            author = result;
                        })
                        .error(function (err) {
                            alert(err);
                        });
                })
            }

            scope.userSelected = function (selected) {
                scope.scripts.error = null;
                if(!selected.title) {
                }
                else{
                    if(selected.originalObject.love > 0){
                        var already = $filter('getById')(scope.scriptInEdit.authors, selected.originalObject.id);
                        if(!already)
                            scope.scriptInEdit.authors.push({id:selected.originalObject.id, name:selected.title, email:selected.originalObject.location, link:selected.originalObject.link});
                    }
                    else{

                        $uibModal.open({
                            animation: true,
                            templateUrl: 'script_alert.html',
                            size: 'lg',
                            controller: function ($scope) {
                                $scope.alert_message = $filter('translate')(window.document.location.pathname.indexOf('projects') > 0 ? 'project.MESSAGES.notteam': 'project.MESSAGES.notfriends',
                                    {'user_id': selected.originalObject.id, 'username': selected.title});
                            }
                        });
                    }
                }
            }
            scope.authorSelected = function (selected) {
                scope.scripts.error = null;
                if(!selected.title) {
                    var modalInstance = $uibModal.open({
                        animation: true,
                        templateUrl: 'script.html',
                        size: 'lg',
                        controller: function ($scope) {
                            $scope.author = {name:selected.originalObject};
                        }
                    });

                    modalInstance.result.then(function (author) {
                        if (!author)
                            return false;

                        var index = -1;
                        for(var i = 0; i < scope.authors.length && index <0; i++){
                            if(scope.authors[i].location == author.email){
                                if(scope.authors[i].username == author.name) {
                                    index = i;
                                    if (!$filter('getById')(scope.scriptInEdit.authors, scope.authors[i].id))
                                        scope.scriptInEdit.authors.push({id:scope.authors[i].id, name:scope.authors[i].username, email:scope.authors[i].location, link:scope.authors[i].link});

                                    return false;
                                }
                                else{
                                    i = scope.authors.length;
                                    scope.scripts.error = 'email';
                                    return false;
                                }
                            }
                        }

                        author._token = $("body input[name='csrfmiddlewaretoken']").val();

                        $http.post('/admin/users', author)
                            .success(function (result) {
                                if (result.email) {
                                    scope.authors.push({
                                        id: result.id,
                                        username: result.name,
                                        location: result.email,
                                        link: result.link
                                    });

                                    scope.scriptInEdit.authors.push(result);
                                }
                                else {
                                    var already = $filter('getById')(scope.scriptInEdit.authors, result.id);
                                    if (!already)
                                        scope.scriptInEdit.authors.push(result);
                                }
                            })
                            .error(function (err) {
                                $uibModal.open({
                                    animation: true,
                                    templateUrl: 'script_alert.html',
                                    size: 'lg',
                                    controller: function ($scope) {
                                        $scope.alert_message = err;
                                    }
                                });
                            });
                    })
                }
                else{
                    var already = $filter('getById')(scope.scriptInEdit.authors, selected.originalObject.id);
                    if(!already)
                        scope.scriptInEdit.authors.push({id:selected.originalObject.id, name:selected.title, email:selected.originalObject.location, link:selected.originalObject.link});
                }
            }

            scope.removeAuthor = function(id){
                $rootScope.removeValue(scope.scriptInEdit.authors, id);
            }

            scope.openScriptDate = function () {
                scope.scriptInEdit.opened = true;
            }

            scope.addScript = function () {
                scope.scriptInEdit = {id:0, project_id:scope.project.id, authors:[], submitted: scope.submitted, opened:false};
            }

            scope.cancelScript = function () {
                scope.scriptInEdit = null;
            }

            scope.deleteScript = function (script) {

                var modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: 'delete.html',
                    size: 'lg',
                    controller: function ($scope) {
                        $scope.title = script.title;
                        $scope.deleteauthor = false;
                    }
                });

                modalInstance.result.then(function (author) {
                   if(author == null)
                       return;
                    scope.scripts.loading = true;
                    $http.delete(url + script.id, {params:{_token: $("body input[name='csrfmiddlewaretoken']").val(), author:author,submitted: scope.submitted}})
                        .then(function successCallback() {
                            $rootScope.removeValue(scope.scripts, script.id);
                            scope.scripts.loading = false;
                        }, function errorCallback(response) {
                            alert(response.message);
                            scope.scripts.loading = false;
                        });
                });
            }

            scope.editScript = function (script) {
                scope.scripts.error = null;
                if(scope.scriptInEdit && scope.scriptInEdit.id == script.id)
                    scope.scriptInEdit = null;
                else{
                    scope.scriptInEdit = {id:script.id, project_id:script.project_id, title:script.title, link:script.link, description:script.description,
                        created_at:new Date(script.created_at), submitted: scope.submitted, opened:false};

                    scope.scriptInEdit.authors = script.authors;
                }
            }

            scope.saveScript = function (invalid) {
                if(invalid  || scope.scriptInEdit.authors.length == 0 || scope.scripts.error)
                    return;

                var newAuthor = "";

                angular.forEach(scope.scriptInEdit.authors, function(author){
                    newAuthor += author.id + ",";
                })

                var authors = angular.copy(scope.scriptInEdit.authors);
                if(newAuthor.length > 0)
                    scope.scriptInEdit.newAuthor = newAuthor.substr(0, newAuthor.length - 1);

                scope.scripts.loading = true;
                scope.scriptInEdit._token = $("body input[name='csrfmiddlewaretoken']").val();
                if(scope.scriptInEdit.id == 0){
                    $http.post(url, scope.scriptInEdit)
                        .success(function (result) {
                            result.authors = authors;
                            scope.scripts.push(result);
                            scope.scripts.loading = false;
                        })
                        .error(function (err) {
                            $log.error("failed to save script", err);
                            scope.scripts.loading = false;
                        });
                }
                else{
                    $http.put(url + scope.scriptInEdit.id, scope.scriptInEdit)
                        .success(function (result) {
                            result.authors = authors;
                            $rootScope.setValue(scope.scripts, result);
                            scope.scripts.loading = false;
                        })
                        .error(function (err) {
                            alert(err);
                            $log.error("failed to update script "+ scope.scriptInEdit.id, err);
                            scope.scripts.loading = false;
                        });
                }

                scope.scriptInEdit = null;
            }
        }
    }
});