appZooMov.directive('maker', function ($rootScope, $filter, $http) {
    return {
        restrict: 'A',
        link: function (scope, elm, attrs) {
            if(scope.errors){
                scope.errors.contact = {'name':0};
            }
            else{
                scope.errors = {contact: {'name':0}, filmaker:{}};
            }

            scope.newMaker = {contact:{}};

            if(scope.makers == undefined && scope.credits == undefined){
                $http.get('/archive/' + attrs['film'] + '/' + attrs['maker'])
                    .success(function (data) {
                        scope.makers = data;
                    })
            }

            scope.viewMaker = function (maker) {
                maker.viewed = !maker.viewed;
            }

            scope.cancelMaker = function () {
                scope.makerCopy = null;
            }
            scope.chooseMaker = function () {
                scope.errors.filmaker.another = null;
                scope.makerSelected = [];

                if (!scope.allpersons) {
                    scope.persons = [];
                    $http.get('/archive/makers')
                        .success(function (data) {
                            scope.allperons = data;
                            angular.forEach(data, function (item) {
                                var index = $filter('getById')(scope.persons, item.id) ? 1 : -1;

                                for(var i=0; i<scope.makers.length && index < 0; i++){
                                    if(scope.makers[i].filmaker_id == item.id){
                                        index = i;
                                    }
                                }

                                if(index < 0){
                                    scope.persons.push(item);
                                }
                            });
                            $('#makerListModal').modal('show');
                        })
                        .error(function (err) {
                        })
                }
                else{
                    scope.persons = []
                    angular.forEach(scope.allpersons, function (item) {
                        var index = $filter('getById')(scope.persons, item.id) ? 1 : -1;

                        for(var i=0; i<scope.makers.length && index < 0; i++){
                            if(scope.makers[i].filmaker_id == item.id){
                                index = i;
                            }
                        }

                        if(index < 0){
                            scope.persons.push(item);
                        }
                    });
                    $('#makerListModal').modal('show');
                }
            }
            scope.selectMaker = function (id) {
                var index = scope.makerSelected.indexOf(id);
                if(index < 0)
                    scope.makerSelected.push(id);
                else
                    scope.makerSelected.splice(index,1);
            }

            scope.makerChosen = function (film_id, position) {
                var makerSelected = $filter('filter')(scope.persons, {selected:true});
                $http.post('/archive/maker/' + position, {makers:makerSelected, film_id:film_id})
                    .success(function (data) {
                        angular.forEach(makerSelected, function (maker) {
                            scope.makers.push(angular.copy(maker));
                        })

                        $('#makerListModal').modal('hide');
                    })
                    .error(function (err) {
                        scope.errors.maker.another = err.message;
                    })
            }
            scope.createMaker = function () {
                scope.errors.contact.name = 0;
                if (scope.contacts === undefined) {
                    $http.get('/archive/contacts')
                        .success(function (data) {
                            scope.contacts = data;
                        })
                        .error(function (err) {
                        })
                }
                if(!scope.users){
                    $http.get('/users')
                        .success(function (data) {
                            scope.users = data;
                            $('#newMakerModal').modal('show');
                        })
                        .error(function (err) {

                        })
                }
                else{
                    $('#newMakerModal').modal('show');
                }
            }

            scope.makerCreated = function (id, format) {
                if(scope.allContacts == 2){
                    scope.newMaker.contact.contact_id = null;
                    if(scope.newMaker.contact.name && scope.newMaker.contact.city_id && scope.newMaker.contact.address){
                        var index = -1;
                        for(var i = 0; i < scope.contacts.length && index < 0; i++){
                            if(scope.contacts[i].name ==  scope.newMaker.contact.name){
                                index = i;
                            }
                        }
                        if(index >= 0){
                            scope.errors.contact.name = 1;
                            return;
                        }
                    }
                    else{
                        return;
                    }
                }
                else if(scope.allContacts == 1){
                    var contact_id = scope.newMaker.contact.contact_id;
                    scope.newMaker.contact = {contact_id:contact_id};
                }
                else{
                    scope.newMaker.contact = {}
                }

                if(scope.newMaker.web){
                    scope.newMaker.web = $rootScope.checkUrl(scope.newMaker.web);
                }

                $http.post('/filmaker', {maker:scope.newMaker, film_id:id, position:format})
                    .success(function (maker) {
                        if(maker.related_id){
                            maker.username = $('#newmaker_related option:selected').text();
                        }
                        maker.filmaker_id = maker.id;
                        maker.country = $('#newMaker_nationality option:selected').text();
                        if(maker.contact){
                            if(scope.newMaker.contact.contact_id){
                                var index = -1;
                                for(var i = 0; i < scope.contacts.length && index <0; i++){
                                    if(scope.contacts[i].contact_id == scope.newMaker.contact.contact_id){
                                        index = i;
                                        maker.contact = scope.contacts[i];
                                    }
                                }
                            }
                            else{
                                maker.contact.contact_id = maker.contact.contact_id;
                                maker.contact.country = $('#newmaker_country_id option:selected').text();
                                maker.contact.department = $('#newmaker_depart_id option:selected').text();
                                maker.contact.city = $('#newmaker_city_id option:selected').text();
                                scope.contacts.push(maker.contact);
                            }
                        }

                        scope.makers.push(maker);
                        scope.newMaker = {contact:{}};
                        $('#newMakerModal').modal('hide');
                    })
            }
            scope.updateMaker = function () {
                var maker = scope.makerCopy;
                maker.last_name = maker.last_name.toString().toUpperCase();
                if(maker.web){
                    maker.web = $rootScope.checkUrl(maker.web);
                }
                $http.put('/filmaker/' + scope.makerCopy.filmaker_id, scope.makerCopy)
                    .success(function (result) {
                        if(maker.related_id){
                            var user = $filter('getById')(scope.users, maker.related_id);
                            maker.username = user.username;
                        }
                        maker.country = $('#nationality option:selected').text();
                        var person = $filter('getById')(scope.makers, scope.makerCopy.id);
                        angular.extend(person, maker);
                        scope.makerCopy = null;
                        $('#editMakerModal').modal('hide');
                    })
            }
            scope.editMaker = function (maker) {
                scope.makerCopy = angular.copy(maker);
                if(!scope.makerPool){
                    $http.get('/filmaker')
                        .success(function (data) {
                            scope.makerPool = data;
                        })
                        .error(function (err) {
                        })
                }
                if(!scope.users){
                    $http.get('/users')
                        .success(function (data) {
                            scope.users = data;
                        })
                        .error(function (err) {
                        })
                }
                $('#editMakerModal').modal('show');
            }

            scope.editContact = function (maker) {
                scope.errors.contact.name = 0;
                scope.contactMaker = {id:maker.filmaker_id, name: maker.last_name + ' ' + maker.first_name};
                if(maker.contact){
                    scope.contactCopy = angular.copy(maker.contact);
                    scope.initContact(maker.contact);
                }
                else
                    scope.contactCopy = {name:maker.last_name + ' ' + maker.first_name};
                if (scope.contacts === undefined) {
                    $http.get('/archive/contacts')
                        .success(function (data) {
                            scope.contacts = data;
                            $('#editContactModal').modal('show');
                        })
                        .error(function (err) {
                        })
                }
                else{
                    $('#editContactModal').modal('show');
                }
            }

            scope.selectContact = function (id) {
                angular.forEach(scope.contacts, function (item) {
                    item.selected = item.contact_id == id;
                })
            }

            scope.chooseContact = function (maker) {
                scope.errors.contact.another = null;
                scope.contactSelected = {filmaker_id:maker.filmaker_id};
                if (!scope.contacts) {
                    $http.get('/archive/contacts')
                        .success(function (data) {
                            scope.contacts = data;
                            $('#contactListModal').modal('show');
                        })
                        .error(function (err) {
                        })
                }
                else{
                    $('#contactListModal').modal('show');
                }
            }

            scope.contactChosen = function () {
                var contact = null;
                for(var i = 0; i < scope.contacts.length && !contact; i++){
                    if(scope.contacts[i].selected){
                        contact = scope.contacts[i];
                    }
                }
                if(!contact){
                    return;
                }
                $http.put('/filmaker/contact/' + scope.contactSelected.filmaker_id + '/' + contact.contact_id)
                    .success(function (data) {
                        var index = -1;
                        for(var i = 0; i < scope.makers.length && index < 0; i++){
                            if(scope.makers[i].filmaker_id == scope.contactSelected.filmaker_id){
                                index = i;
                                scope.makers[i].contact = contact
                            }
                        }

                        $('#contactListModal').modal('hide');
                    })
                    .error(function (err) {
                        scope.errors.contact.another = err.message;
                    })
            }

            scope.saveContact = function () {
               var index = -1;
                scope.errors.contact.name = 0;
                for(var i = 0; i < scope.contacts.length && index < 0; i++){
                    if(scope.contacts[i].name ==  scope.contactCopy.name){
                        index = i;
                    }
                }

                if(index < 0){
                    scope.contactCopy.contact_id = null;
                    scope.updateContact();
                }
                else{
                    scope.errors.contact.name = 1;
                    return;
                }
            }

            scope.validContact = function () {
                var index = -1;
                scope.errors.contact.name = 0;
                for(var i = 0; i < scope.contacts.length && index < 0; i++){
                    if(scope.contacts[i].name ==  scope.contactCopy.name && scope.contacts[i].contact_id != scope.contactCopy.contact_id){
                        index = i;
                    }
                }

                if(index < 0){
                    scope.updateContact();
                }
                else{
                    scope.errors.contact.name = 1;
                    return;
                }
            }

            scope.updateContact = function () {
                $http.post('/filmaker/contact/'+scope.contactMaker.id, scope.contactCopy)
                    .success(function (result) {
                       /* maker.username = $('#searchUser option:selected').text();
                        maker.country = $('#nationality_'+maker.id+' option:selected').text();*/
                        scope.contactCopy.country = $('#contact_country_id option:selected').text();
                        scope.contactCopy.department = $('#contact_department_id option:selected').text();
                        scope.contactCopy.city = $('#contact_city_id option:selected').text();
                        var contact = angular.copy(scope.contactCopy);
                        if(!contact.contact_id){
                            contact.contact_id = result;
                            scope.contacts.push(contact);
                        }
                        else{
                            var index = -1;
                            for(var i = 0; i < scope.contacts.length && index < 0; i++) {
                                if (scope.contacts[i].contact_id == contact.contact_id) {
                                    index = i;
                                    scope.contacts[i] = contact;
                                }
                            }
                        }

                        var index = -1;
                        for(var i = 0; i < scope.makers.length && index < 0; i++){
                            if(scope.makers[i].filmaker_id == scope.contactMaker.id){
                                index = i;
                                scope.makers[i].contact = contact;
                            }
                        }
                        scope.contactCopy = null;
                        $('#editContactModal').modal('hide');
                    })
                    .error(function (err) {
                        scope.errors.contact.edit = err.message;
                    })
            }

            scope.deleteContact = function (maker) {
                if(!maker.contact){
                    return;
                }
                scope.contactToDelete = maker.contact;
                scope.contactToDelete.filmaker_id = maker.filmaker_id;
                $('#deleteContactModal').modal('show');
            }
            
            scope.contactDeleted = function () {
                $http.delete('/filmaker/contact/' + scope.contactToDelete.filmaker_id)
                    .success(function () {
                        var index = -1;
                        for(var i = 0; i < scope.makers.length && index < 0; i++){
                            if(scope.makers[i].filmaker_id == scope.contactToDelete.filmaker_id){
                                index = i;
                                scope.makers[i].contact = null;
                            }
                        }

                        $('#deleteContactModal').modal('hide');
                    })
                    .error(function (err) {
                        scope.errors.contact.delete = err.message;
                    })
            }

            scope.deleteMaker = function (d) {
                scope.makerToDelete = d;
                $('#deleteMakerModal').modal('show');
            }

            scope.makerDeleted = function (film_id, position) {
                $http.delete('/archive/' + film_id + '/maker', {params:{position:position, maker_id:scope.makerToDelete.filmaker_id}})
                    .success(function (result) {
                        if(result){
                           $rootScope.removeValue(scope.makers, scope.makerToDelete.filmaker_id, 'filmaker_id');
                        }
                        $('#deleteMakerModal').modal('hide');
                    })
                    .error(function (err) {
                        scope.errors.maker = err.message;
                    })
            }

            scope.initContact = function (contact) {
                if(contact.city_id){
                    $http.get('/locations/' + contact.city_id)
                        .success(function (result) {
                            scope.contactDepartments = result.departments;
                            scope.contactCities = result.cities;
                        });
                }
                else if(contact.department_id){
                    $http.get('/departCities/' + contact.department_id)
                        .success(function (result) {
                            scope.contactDepartments = result.departments;
                            scope.contactCities = result.cities;
                        });
                }
                else if(contact.country_id){
                    $http.get('/departments/' + contact.country_id)
                        .success(function (result) {
                            scope.contactDepartments = result;
                            scope.contactCities = [];
                        });
                }
                else{
                    scope.contactDepartments = [];
                    scope.contactCities = [];
                }
            }

            scope.loadDepartmet = function (contact) {
                if(!contact.country_id){
                    scope.contactDepartments = [];
                }

                $http.get('/departments/' + contact.country_id)
                    .success(function (result) {
                        scope.contactDepartments = result;
                    })
                    .error(function () {
                    });
            }

            scope.loadCity = function (contact) {
                if(!contact.department_id){
                    scope.contactCities = [];
                }

                $http.get('/cities/' + contact.department_id)
                    .success(function (result) {
                        scope.contactCities = result;
                    })
                    .error(function () {
                    });
            }
        }
    }
});