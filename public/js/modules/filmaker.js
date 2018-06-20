appZooMov.factory('Filmaker', function($http) {
        var backendUrl = "http://localhost:3000";
        var service = {
            contacts: [],
            makers:[],
            contactFocus: function () {
                if (!contacts) {
                    scope.searchOn = true;
                    $http.get('/film/contacts')
                        .success(function (data) {
                            scope.contacts = data;
                            scope.searchOn = false;
                        })
                        .error(function (err) {
                            scope.searchOn = false;
                        })
                }
            },
            setEmail: function(newEmail) {
                service.user['email'] = newEmail;
            },
            save: function() {
                return $http.post(backendUrl + '/users', {
                    user: service.user
                });
            }

        };
        return service;
    });