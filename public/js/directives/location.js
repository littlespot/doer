appZooMov.directive('location', function ($http) {
    return {
        restrict: 'A',
        link: function(scope, elem, attr, log) {
            var self = scope;
            var opt = attr['location'];


            scope.disabled = {depart:false, city:false};
            scope.loadDepart = function (id) {
                if(!id){
                    scope[opt].departments = [];
                }
                scope.disabled.depart = true;
                $http.get('/departments/' + id)
                    .success(function (departments) {
                        scope[opt].departments = departments;
                    })
                    .error(function (err) {
                        return [];
                    })

            }

            scope.loadCity = function (id) {
                if(!id){
                    scope[opt].cities = [];
                }

                scope.disabled.city = true;
                var promise = $http({
                    method: 'GET',
                    url: '/cities/' + id,
                    isArray:false
                });
                promise.then(
                    function(departments) {
                        scope.disabled.city = false;
                        scope[opt].cities = departments.data;
                    },
                    function(errorPayload) {
                        scope.disabled.city = false;
                        log.error('failure loading movie', errorPayload);
                    });
            }

            scope.setDepartment = function(department_id) {
                if(!department_id){
                    scope[opt].cities = [];
                }

                scope[opt].department_id = department_id;
                self.disabled.city = true;
                var promise = $http({
                    method: 'GET',
                    url: '/cities/' + department_id,
                    isArray:false
                });
                promise.then(
                    function(cities) {
                        self.disabled.city = false;
                        scope[opt].cities = cities.data;
                        return true;
                    },
                    function(errorPayload) {
                        self.disabled.city = false;
                        return false;
                    });
            }

            scope.setCity = function(city_id) {
                var promise = $http({
                    method: 'GET',
                    url: '/locations/' + city_id,
                    isArray:false
                });

                promise.then(
                    function(result) {
                        self.disabled = {depart:false, city:false};
                        return result.data;
 /*                       var data = result.data;
                        self.cities = data.cities;
                        self.departments = data.departments;
                        self.countries = data.countries;
                        self.department_id = data.department_id;
                        self.country_id = data.country_id;
                        return true;*/
                    },
                    function() {
                        self.disabled.depart = false;
                        return false;
                    });
            }
        }
    };
});