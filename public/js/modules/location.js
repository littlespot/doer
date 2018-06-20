appZooMov.service('Location', function ($http) {
    var self = this;
    this.countries = [];
    this.departments = [];
    this.cities = [];
    this.disabled = {depart:false, city:false};
    this.country_id = '', this.department_id = '', this.city_id = '';
    this.getCountries = function (countries) {
        self.countries = countries;
    }
    this.setCountry = function (id,  obj) {
        if(obj){
            obj = self;
        }
        self.country_id = id;
        if(!id){
            self.departments = [];
        }
        self.disabled.depart = true;
        var promise = $http({
            method: 'GET',
            url: '/departments/' + id,
            isArray:false
        });

        promise.then(
            function(departments) {
                self.disabled.depart = false;
                self.departments = departments.data;
                return true;
            },
            function() {
                self.disabled.depart = false;
                return false;
            });
    }

    this.setDepartment = function(country_id, department_id) {
        if(!self.setCountry(country_id)) {
            return false;
        }
            self.department_id = department_id;
            if(!id){
                self.cities = [];
            }

        self.disabled.city = true;
            var promise = $http({
                method: 'GET',
                url: '/cities/' + id,
                isArray:false
            });
            promise.then(
                function(departments) {
                    self.disabled.city = false;
                    self.cities = departments.data;
                    return true;
                },
                function(errorPayload) {
                    self.disabled.city = false;
                    return false;
                });
        }

    this.setCity = function(city_id) {
        var promise = $http({
            method: 'GET',
            url: '/locations/' + city_id,
            isArray:false
        });

        promise.then(
            function(result) {
                self.disabled = {depart:false, city:false};
                var data = result.data;
                self.cities = data.cities;
                self.departments = data.departments;
                self.countries = data.countries;
                self.department_id = data.department_id;
                self.country_id = data.country_id;
                return true;
            },
            function() {
                self.disabled.depart = false;
                return false;
            });
    }
})