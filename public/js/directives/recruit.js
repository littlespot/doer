/**
 * Created by Jieyun on 2016/12/1.
 */
appZooMov.directive('recruitContent', function ($rootScope, $http, $filter,$log) {
    return {
        restrict:'A',
        link: function (scope) {
            var url = '/admin/recruitment/';
            scope.newrecruit={quantity:1, submitted:scope.submitted};
            scope.error ={role:false, quantity:false, description:false}

            scope.recruitInEdit = -1;
            angular.forEach(scope.recruit, function (recruit) {
                $("#opt_role_" +recruit.occupation_id).hide();
                $("#role_opt_" +recruit.occupation_id).hide();
            })
            scope.switchEditRecruit = function (recruit) {
                $("#opt_role_" +recruit.occupation_id).show();
                $("#role_opt_" +recruit.occupation_id).show();
                scope.recruitInEdit = angular.copy(recruit);
            }

            scope.cancelEditRecruit = function () {
                scope.recruitInEdit = null;
            }

            scope.addRecruit = function(){
                scope.error.role =  !parseInt(scope.newrecruit.occupation_id);
                scope.error.quantity =  !parseInt(scope.newrecruit.quantity) && parseInt(scope.newrecruit.quantity) <= 0;
                scope.error.description = !scope.newrecruit.description || scope.newrecruit.description.length < 15 || scope.newrecruit.description.length > 400;
                if(scope.error.role || scope.error.quantity || scope.error.description)
                    return false;

                scope.recruit.loading = true;
                scope.newrecruit.project_id = scope.project.id;

                $http.post(url, scope.newrecruit)
                    .success(function (result) {
                        $("#opt_role_" +result.occupation_id).hide();
                        $("#role_opt_" +result.occupation_id).hide();
                        result.name = $('#opt_role_' + result.occupation_id).text();
                        scope.recruit.push(result);
                        scope.newrecruit={quantity:1, submitted:scope.submitted};
                        scope.error ={role:false, quantity:false, description:false};
                        scope.recruit.loading = false;
                    })
                    .error(function (err) {
                        scope.recruit.loading = false;
                        $log.error("failed to add recruitment", err);
                    });
            }

            scope.saveEditRecruit = function(){
                scope.recruitInEdit.submitted = scope.submitted;
                scope.recruit.editing = true;
                $http.put(url + scope.recruitInEdit.id,  scope.recruitInEdit)
                    .success(function (result) {
                        $("#opt_role_" +result.occupation_id).hide();
                        $("#role_opt_" +result.occupation_id).hide();
                        $("#opt_role_" +result.occupation_id).hide();
                        $("#role_opt_" +result.occupation_id).hide();
                        result.name =$('#role_opt_' + result.occupation_id).text();;
                        $rootScope.setValue(scope.recruit, result);
                        scope.recruitInEdit = null;
                        scope.recruit.editing = false;
                    })
                    .error(function (err) {
                        scope.recruit.loading = false;
                        $log.error("failed to update recruitment" + recruit.id, err);
                    });
            }

            scope.removeRecruit = function(id){
                scope.recruitToDeleted = id;
                $('#recruitDeleteModal').modal('show');
            }

            scope.recruitDeleted = function () {
                $rootScope.loading();
                $http.delete(url + scope.recruitToDeleted, {params:{submitted:scope.submitted}})
                    .then(function successCallback() {
                        var old = $rootScope.removeValue(scope.recruit, scope.recruitToDeleted, 'id');
                        $("#opt_role_" +old.occupation_id).show();
                        $("#role_opt_" +old.occupation_id).show();
                        $('#recruitDeleteModal').modal('hide');
                        $rootScope.loaded();
                    }, function errorCallback(response) {
                        $rootScope.loaded();
                    });
            }
        }
    }
});