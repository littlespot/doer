/**
 * Created by Jieyun on 2016/12/1.
 */
appZooMov.directive('scriptContent', function ($rootScope, $http, $filter, $log, $uibModal) {
    return {
        restrict:'A',
        link: function (scope, elm, attrs) {
            var url = '/admin/scripts/';
            scope.isProject = attrs['scriptContent'] == 'project';
            scope.editAuthor = function (author) {
                if(!author.email || author.email.length == 0)
                    return;
                scope.author = author;
                $('#newAuthorModal').modal('show');
            }

            scope.openUser = function () {
                window.open('/profile/' + scope.selectedAuthor);
            }
            scope.userSelected = function (selected) {
                scope.scripts.error = null;
                if(selected.title){
                    if(selected.originalObject.love > 0){
                        var already = $filter('getById')(scope.scriptInEdit.authors, selected.originalObject.id);
                        if(!already)
                            scope.scriptInEdit.authors.push({id:selected.originalObject.id, name:selected.title, email:selected.originalObject.location, link:selected.originalObject.link});
                    }
                    else{
                        scope.selectedAuthor = selected.originalObject.id
                        $('#authorErrorModal').modal('show');
                    }
                }
            }
            scope.authorSelected = function (selected) {
                scope.scripts.error = null;
                if(!selected.title) {
                    $('#newAuthorModal').modal('show');
                }
                else{
                    var already = $filter('getById')(scope.scriptInEdit.authors, selected.originalObject.id);
                    if(!already)
                        scope.scriptInEdit.authors.push({id:selected.originalObject.id, name:selected.title, email:selected.originalObject.location, link:selected.originalObject.link});
                }
            }
            scope.authorAdded = function (author) {
                if (!author)
                    return false;

                var index = -1;
                for(var i = 0; i < scope.authors.length && index <0; i++){
                    if(scope.authors[i].location ==  author.email){
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

                if(author.id){
                    $http.put('/admin/users/' + author.id, author)
                        .success(function (result) {
                            var index = -1;
                            for(var i = 0; i < scope.authors.length && index < 0; i++){
                                if(scope.authors[i].id == result.id){
                                    index = i;
                                    scope.authors[index] = {id:result.id, username:result.name, location:result.email, link:result.link};
                                }
                            }

                            scope.scripts.error = err;
                        })
                        .error(function (err) {
                            alert(err);
                        });
                }
                else{
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
                            scope.scripts.error = err;
                        });
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
                scope.scriptToDelete = script;
                $('#deleteScriptModal').modal('show');
            }

            scope.scriptDeleted = function (author) {
                scope.scripts.loading = true;
                $http.delete(url + scope.scriptToDelete.id, {params:{author:author,submitted: scope.submitted}})
                    .then(function successCallback() {
                        $rootScope.removeValue(scope.scripts, scope.scriptToDelete.id);
                        scope.scripts.loading = false;
                        $('#deleteScriptModal').modal('hide');
                    }, function errorCallback(response) {
                        scope.scripts.error = response;
                        scope.scripts.loading = false;
                    });
            }
            scope.editScript = function (script) {
                scope.scripts.error = null;
                if(scope.scriptInEdit && scope.scriptInEdit.id.equals(script.id))
                    scope.scriptInEdit = null;
                else{
                    scope.scriptInEdit = {id:script.id, project_id:script.project_id, title:script.title, link:script.link, description:script.description,
                        created_at:new Date(script.created_at), submitted: scope.submitted, opened:false};

                    scope.scriptInEdit.authors = script.authors;
                }
            }

            scope.saveScript = function (invalid) {
                if(invalid  || scope.scriptInEdit.authors.length < 1 || scope.scripts.error)
                    return;

                var newAuthor = "";

                angular.forEach(scope.scriptInEdit.authors, function(author){
                    newAuthor += author.id + ",";
                })

                var authors = angular.copy(scope.scriptInEdit.authors);
                if(newAuthor.length > 0)
                    scope.scriptInEdit.newAuthor = newAuthor.substr(0, newAuthor.length - 1);

                $rootScope.loading();
                scope.scriptInEdit.link = $rootScope.checkUrl(scope.scriptInEdit.link);
                if(scope.scriptInEdit.id < 1){
                    $http.post(url, scope.scriptInEdit)
                        .success(function (result) {
                            result.authors = authors;
                            scope.scripts.push(result);
                            $rootScope.loaded();
                        })
                        .error(function (err) {
                            $log.error("failed to save script", err);
                            $rootScope.loaded();
                        });
                }
                else{
                    $http.put(url + scope.scriptInEdit.id, scope.scriptInEdit)
                        .success(function (result) {
                            result.authors = authors;
                            $rootScope.setValue(scope.scripts, result);
                            $rootScope.loaded();
                        })
                        .error(function (err) {
                            alert(err);
                            $log.error("failed to update script "+ scope.scriptInEdit.id, err);
                            $rootScope.loaded();
                        });
                }

                scope.scriptInEdit = null;
            }
        }
    }
});