appZooMov.directive('email', function($http, $q, $timeout) {
    return {
        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {
            ctrl.$asyncValidators.unique = function(modelValue) {

                if (ctrl.$isEmpty(modelValue)) {
                    // consider empty model valid
                    return $q.when();
                }

                var def = $q.defer();

                $timeout(function() {
                    $http.get("/users/" + modelValue)
                        .success(function(data){
                            if(data && data.id){
                                def.reject();
                            }
                            else{
                                def.resolve();
                            }
                        })
                        .error(function () {
                            def.reject();
                        })

                }, 2000);

                return def.promise;
            };
        }
    };
})
    .directive('pemail', function($http, $q, $timeout) {
    return {
        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {
            ctrl.$asyncValidators.unique = function(modelValue) {

                if (ctrl.$isEmpty(modelValue)) {
                    // consider empty model valid
                    return $q.when();
                }

                var def = $q.defer();

                $timeout(function() {
                    $http.get("/potential/" + modelValue)
                        .success(function(data){
                            if(data == "T"){
                                def.resolve();
                            }
                            else{
                                def.reject();
                            }
                        })
                        .error(function () {
                            def.reject();
                        })

                }, 2000);

                return def.promise;
            };
        }
    };
})
    .directive('pwd', function($http, $q, $timeout) {
        return {
            require: 'ngModel',
            link: function(scope, elm, attrs, ctrl) {
                ctrl.$asyncValidators.confirm = function(modelValue) {

                    if (ctrl.$isEmpty(modelValue)) {
                        // consider empty model valid
                        return $q.when();
                    }

                    var def = $q.defer();

                    $timeout(function() {
                        $http.get("/users/" + modelValue)
                            .success(function(data){
                                if(data == "T"){
                                    def.resolve();
                                }
                                else{
                                    def.reject();
                                }
                            })
                            .error(function () {
                                def.reject();
                            })

                    }, 2000);

                    return def.promise;
                };
            }
        };
    })
    .directive('pwCheck', [function () {
    return {
        require: 'ngModel',
        link: function (scope, elem, attrs, ctrl) {
            var firstPassword = '#' + attrs.pwCheck;
            elem.add(firstPassword).on('keyup', function () {
                scope.$apply(function () {
                    var v = elem.val()===$(firstPassword).val();
                    ctrl.$setValidity('pwmatch', v);
                });
            });
        }
    }
}]);

