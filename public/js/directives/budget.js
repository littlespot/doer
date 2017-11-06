/**
 * Created by Jieyun on 2016/12/1.
 */
appZooMov.directive('budgetContent', function ($rootScope, $http, $uibModal) {
        return {
        restrict:'A',
        link: function (scope) {
            scope.addBudgetRow = function () {
                scope.budgetNew = {id: 0, type: null, quantity: 100, project_id:scope.project.id, project:scope.submitted};
            }

            scope.switchEditBudget = function (budget, invalid) {
                scope.budgetNew = null;
                if(!scope.budgetEdit || scope.budgetEdit.id != budget.id){
                    scope.budgetEdit = angular.copy(budget);
                    scope.budgetEdit.project = scope.submitted;
                }
                else if(invalid)
                    return false;
                else
                    scope.saveEditBudget(budget.id);
            }

            scope.saveEditBudget = function (id) {
                scope.budgets.loading = true;
                scope.budgetEdit._token= $("body input[name='csrfmiddlewaretoken']").val();
                    $http.put('/admin/budgets/' + id, scope.budgetEdit)
                        .success(function (result) {
                            $rootScope.setValue(scope.budgets, result);
                            scope.budgetEdit = null;
                            scope.budgets.loading =false;
                        })
                        .error(function (err) {
                            console.log(err);
                            alert(err);
                        });
            }

            scope.cancelEditBudget = function (id) {
                if(scope.budgetEdit && scope.budgetEdit.id == id)
                    scope.budgetEdit = null;
                else
                    scope.deleteBudget(id);
            }

            scope.deleteBudget = function (id) {
                var modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: 'budget.html',
                    controller: function ($scope) {
                        $scope.confirm = 'confirmB';
                    }
                });

                modalInstance.result.then(function (confirm) {
                    if (!confirm)
                        return;

                    scope.budgets.loading = true;
                    $http.delete('/admin/budgets/' + id, {params:{
                        _token: $("body input[name='csrfmiddlewaretoken']").val(),
                        project: scope.submitted}
                    })
                        .then(function successCallback() {
                            $rootScope.removeValue(scope.budgets, id);
                            scope.budgets.loading = false;
                        }, function errorCallback(response) {
                            alert(response.message);
                            scope.budgets.loading = false;
                        });
                })
            }

            scope.saveBudget = function (invalid) {

                scope.budgets.loading = true;
                scope.budgetNew._token= $("body input[name='csrfmiddlewaretoken']").val();
                $http.post('/admin/budgets', scope.budgetNew)
                    .success(function (result) {
                        scope.budgets.push(result);
                        scope.budgetNew = null;
                        scope.budgets.loading = false;
                    })
                    .error(function (err) {
                        scope.errors = err.errors;
                        scope.budgets.loading = false;
                    });
            }

            scope.cancelBudget = function () {
                scope.budgetNew = null;
            }

            scope.openSponsedDate = function (sponsor) {
                sponsor.opened = true;
            }

            scope.addSponsorRow = function () {
                scope.sponsorNew = {id: 0, user: null, quantity: 100, sponsed_at:new Date(), project_id:scope.project.id, opened:false, project:scope.submitted,sponsor:{originalObject:null}};
            }

            scope.switchEditSponsor = function (sponsor, invalid) {
                scope.sponsorNew = null;
                if(!scope.sponsorInEdit || scope.sponsorInEdit.id != sponsor.id)
                    scope.sponsorInEdit = {id: sponsor.id, project_id:sponsor.project_id, quantity:sponsor.quantity, sponsor_name:sponsor.sponsor_name,
                        sponsed_at: new Date(sponsor.sponsed_at), project:scope.submitted, opened:false, sponsor:{originalObject:sponsor.sponsor_name}};
                else if(invalid)
                    return false;
                else
                    scope.saveEditSponsor(sponsor.id);
            }

            scope.saveEditSponsor = function (id) {
                if(scope.sponsorInEdit.sponsor.title){
                    scope.sponsorInEdit.user_id = scope.sponsorInEdit.sponsor.originalObject.id;
                    scope.sponsorInEdit.username = scope.sponsorInEdit.sponsor.title;
                }
                else
                    scope.sponsorInEdit.username = scope.sponsorInEdit.sponsor.originalObject;
                scope.sponsors.loading = true;
                scope.sponsorInEdit._token= $("body input[name='csrfmiddlewaretoken']").val();
                $http.put('/admin/sponsors/' + id, scope.sponsorInEdit)
                    .success(function (result) {
                        $rootScope.setValue(scope.sponsors, result);
                        scope.sponsorInEdit = null;
                        scope.sponsors.loading = false;
                    })
                    .error(function (err) {
                        $log.error("Failed to edit sponsor "+ id, err);
                        scope.sponsors.loading = false;
                    });
            }

            scope.cancelEditSponsor = function (id) {
                if(scope.sponsorInEdit && scope.sponsorInEdit.id == id)
                   scope.sponsorInEdit = null;
                else
                    scope.deleteSponsor(id);
            }

            scope.deleteSponsor = function (id) {
                var modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: 'budget.html',
                    controller: function ($scope) {
                        $scope.confirm = 'confirmS';
                    }
                });

                modalInstance.result.then(function (confirm) {
                    if (!confirm)
                        return;

                    scope.sponsors.loading = true;
                    $http.delete('/admin/sponsors/' + id, {params:{
                        _token: $("body input[name='csrfmiddlewaretoken']").val(),
                        project: scope.submitted}
                    })
                        .then(function successCallback() {
                            $rootScope.removeValue(scope.sponsors, id);
                            scope.sponsors.loading = false;

                        }, function errorCallback(response) {
                            alert(response.message);
                            scope.sponsors.loading = false;
                        });
                });
            }

            scope.saveSponsor = function (){
                if(scope.sponsorNew.sponsor.title){
                    scope.sponsorNew.user_id = scope.sponsorNew.sponsor.originalObject.id;
                    scope.sponsorNew.username = scope.sponsorNew.sponsor.title;
                }
                else
                    scope.sponsorNew.username = scope.sponsorNew.sponsor.originalObject;
                scope.sponsors.loading = true;
                scope.sponsorNew._token= $("body input[name='csrfmiddlewaretoken']").val();
                $http.post('/admin/sponsors', scope.sponsorNew)
                    .success(function (result) {
                        scope.sponsors.push(result);
                        scope.sponsorNew = null;
                        scope.sponsors.loading = false;
                    })
                    .error(function (err) {
                        $log.error('Failed to save sponsor', err);
                    });
            }

            scope.cancelSponsor = function () {
                scope.sponsorNew = null;
            }
        }
    }
})