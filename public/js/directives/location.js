appZooMov.directive('location', function ($http) {
    return {
        restrict: 'A',
        link: function(scope, elem, attr, log) {
            scope.departments = [];
            scope.cities = [];
            scope.disabled = {depart:false, city:false};

            scope.country_id = attr["country"];

            scope.loadDepart = function (id) {
                if(!id){
                    scope.departments = [];
                }
                scope.disabled.depart = true;
                var promise = $http({
                    method: 'GET',
                    url: '/locations/' + id,
                    isArray:false
                });

                promise.then(
                    function(departments) {
                        scope.disabled.depart = false;
                        scope.departments = departments.data;
                    },
                    function() {
                        scope.disabled.depart = false;
                    });
            }

            scope.loadCity = function (id) {
                if(!id){
                    scope.cities = [];
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
                        scope.cities = departments.data;
                    },
                    function(errorPayload) {
                        scope.disabled.city = false;
                        log.error('failure loading movie', errorPayload);
                    });
            }


            if(scope.country_id && scope.country_id > 0){
                scope.departments = scope.loadDepart(scope.country_id);
                scope.department_id = parseInt(attr["department"]);

                if(scope.department_id && scope.department_id > 0){
                    scope.cities = scope.loadCity(scope.department_id);
                }
            }
        }
    };
});