/**
 * Created by Jieyun on 2016/12/1.
 */
appZooMov.directive('budgetContent', function ($rootScope, $http, $log) {
        return {
        restrict:'A',
        link: function (scope) {
            scope.addBudgetRow = function () {
                scope.budgetNew = {id: 0, type: null, quantity: 100, project_id:scope.project.id, project:scope.submitted};
            }
            scope.budgetRegex = /^[1-9][0-9]*$/;

            scope.switchEditBudget = function (budget, invalid) {
                scope.budgetNew = null;
                if(!scope.budgetEdit || !scope.budgetEdit.id == budget.id){
                    scope.budgetEdit = angular.copy(budget);
                    scope.budgetEdit.project = scope.submitted;
                }
                else if(invalid)
                    return false;
                else
                    scope.saveEditBudget(budget.id);
            }

            scope.saveEditBudget = function (id, invalid) {
                if(invalid)
                    return false;
                scope.budgets.loading = true;
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
                scope.budgetToDelete = id;
                $('#deleteBudgetModal').modal('show');
            }
            scope.budgetDeleted = function (id) {
                    scope.budgets.loading = true;
                    $http.delete('/admin/budgets/' + id, {params:{project: scope.submitted}})
                        .then(function successCallback() {
                            $rootScope.removeValue(scope.budgets, id);
                            scope.budgets.loading = false;
                            $('#deleteBudgetModal').modal('hide');
                        }, function errorCallback(response) {
                            scope.budgets.error = response.message;
                            scope.budgets.loading = false;
                        });
            }

            scope.saveBudget = function (invalid) {
                if(invalid)
                    return false;
                scope.budgets.loading = true;
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

            scope.switchEditSponsor = function (sponsor, invalid){
                scope.sponsorNew = null;
                if(!scope.sponsorInEdit || !scope.sponsorInEdit.id == sponsor.id)
                    scope.sponsorInEdit = {id: sponsor.id, project_id:sponsor.project_id, quantity:sponsor.quantity, sponsor_name:sponsor.sponsor_name,
                        sponsed_at: new Date(sponsor.sponsed_at), project:scope.submitted, opened:false, sponsor:{originalObject:sponsor.sponsor_name}};
                else if(invalid)
                    return false;
                else
                    scope.saveEditSponsor(sponsor.id);
            }

            scope.saveEditSponsor = function (id, invalid) {
                if(invalid)
                    return false;

                if(scope.sponsorInEdit.sponsor.title){
                    scope.sponsorInEdit.user_id = scope.sponsorInEdit.sponsor.originalObject.id;
                    scope.sponsorInEdit.username = scope.sponsorInEdit.sponsor.title;
                }
                else
                    scope.sponsorInEdit.username = scope.sponsorInEdit.sponsor.originalObject;
                scope.sponsors.loading = true;
                $http.put('/api/sponsors/' + id, scope.sponsorInEdit)
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
                scope.sponsorToDelete = id;
                $('#deleteSponsorModal').modal('show');
            }
            scope.sponsorDeleted = function (id) {
                scope.sponsors.loading = true;
                $http.delete('/api/sponsors/' + id, {params:{project: scope.submitted}})
                    .then(function successCallback() {
                        $rootScope.removeValue(scope.sponsors, id);
                        scope.sponsors.loading = false;
                        $('#deleteSponsorModal').modal('hide');
                    }, function errorCallback(response) {
                        scope.sponsors.erro = response.message;
                        scope.sponsors.loading = false;
                    });
            }

            scope.saveSponsor = function (invalid){
                if(invalid)
                    return false;

                if(scope.sponsorNew.sponsor.title){
                    scope.sponsorNew.user_id = scope.sponsorNew.sponsor.originalObject.id;
                    scope.sponsorNew.username = scope.sponsorNew.sponsor.title;
                }
                else
                    scope.sponsorNew.username = scope.sponsorNew.sponsor.originalObject;
                scope.sponsors.loading = true;

                $http.post('/api/sponsors', scope.sponsorNew)
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