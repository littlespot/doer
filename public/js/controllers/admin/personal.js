/**
 * Created by Jieyun on 06/07/2016.
 */

appZooMov.controller("personalCtrl", function($rootScope, $scope, $http, $filter,$window, $timeout) {
    $scope.disabled = {depart:false, city:false};

    $scope.init = function (username, birthday, sex, city_id, occupations) {
        var location = window.location.href;
        var anchor = location.lastIndexOf('#');
        $scope.anchor = '#information';
        if (anchor > 0){
            $scope.anchor = location.substr(anchor);
        }
        $scope.location = {};
        $scope.user = {username:username, sex:sex?sex.toString():'s', opened:false, edit:{city:false, info:false, presentation: false}};
        if(city_id){
            $scope.user.city_id =  parseInt(city_id)
        }

        if(birthday){
            $scope.user.birthday = new Date(birthday);
            $scope.user.birthdayFormat = $scope.user.birthday.toISOString().split('T')[0];
        }
        $scope.occupations = angular.fromJson(occupations);
        $scope.contact = {};
        $scope.error = {username:null, presentation:null};

        $scope.chosenSns = {id:'', sns_name:''};
        $rootScope.loaded();
    }

    $scope.initContact = function (contact) {
        $scope.contact = angular.fromJson(contact);
    }

    $scope.loadLocation = function (city_id, opt) {
        if(!city_id || city_id == 0)
            return;
        $http.get('/locations/' + city_id)
            .success(function (data) {
                angular.extend($scope[opt], data);
            })
    }

    $scope.changeCountry = function (country_id, opt) {
        if(!country_id){
            $scope[opt].departments =  [];
        }
        $http.get('/departments/' + country_id)
            .success(function (departments) {
                $scope[opt].departments =  departments;
            })
            .error(function (err) {
                $scope[opt].departments =  [];
            })
    }
    
    $scope.changeDepartment = function (department_id, opt) {
        if(!department_id){
            $scope[opt].cities =  [];
        }
        $http.get('/cities/' + department_id)
            .success(function (cities) {
                $scope[opt].cities =  cities;
            })
            .error(function (err) {
                $scope[opt].cities =  [];
            })
    }

    $scope.saveLocation = function (city_id, opt, subject) {
        $http.put('/locations/' + city_id + '?subject=' + subject)
            .success(function (result) {
                $('#'+opt+'_location').text(result.city.name + '(' + result.country.name  +')')
                $scope[opt].city_id =  city_id;
            })
            .error(function (err) {
                $scope[opt].cities =  [];
            })
    }

    $scope.editInfo = function () {
        $scope.userCopy = {username:$scope.user.username,  sex:$scope.user.sex};
        $scope.user.edit.info = true;
    }

    $scope.cancelInfo = function () {
        angular.extend($scope.user, $scope.userCopy);
        $scope.user.edit.info = false;
    }
    $scope.cancelPresentation = function () {
        $scope.user.edit.presentation = false;
    }
    $scope.editPresentation = function () {
        $scope.user.edit.presentation = true;
        $scope.presentation = $('#presentationDiv').html();
    }
    $scope.savePresentation = function (invalid) {
        if(invalid){
            $scope.submitted = true;
            return;
        }
        var presentation = $('#presentation').val();

        $http.post('/accountPresentation', {presentation:presentation})
            .success(function (result) {
                $scope.user.edit.presentation = false;
                $('#presentationDiv').html(result);

            })
            .error(function (err) {
            })
    }
    $scope.saveInfo = function () {
        $scope.user.sex = $('#sexOption option:selected').val();

        $http({
            method:'post',
            url:'/accountInfo',
            data:$scope.user
        })
            .success(function (result) {
                $('#userSexe').text($('#sexOption option:selected').text());
                $scope.user.birthdayFormat = result;
                $scope.user.edit.info = false;
                $scope.userCopy = null;
            })
            .error(function (err) {
            })

    }

    $scope.openCalendar = function () {
        $scope.user.opened = true;
    }

    $scope.selectTab = function () {
        if(!$scope.sns){
            $http.get('/sns')
                .success(function (data) {
                    $scope.sns = data;
                })
        }
    }

    $scope.removeTalent = function(occupation){
        $http.delete('/accountOccupation/'+occupation.id)
            .success(function () {
                occupation.old = 0;
            })
            .error(function (err) {
            })
    }

    $scope.saveTalents = function(){
        var occupations = $filter('filter')($scope.occupations, {old:1});
        if(occupations.length == 0){
            $scope.submitted = 1;
            return;
        }
        var roles = [];
        angular.forEach(occupations, function (item) {
            roles.push(item.id);
        })
        $http.post('/accountOccupation', {roles:roles})
            .success(function (result) {
                if(result){
                    $scope.submitted = true;
                    angular.forEach($scope.occupations, function (item) {
                        if(result.indexOf(item.id) > 0){
                            item.old = 1;
                        }
                        else{
                            item.old = 0;
                        }
                    })
                }
                else{
                    $scope.user.edit.occupation = false;
                }
            })
            .error(function (err) {
            })
       /* if(newTalent)
            newTalent.old = 1;*/
    }

    $scope.addSns = function(type){
        $scope.chosenSns.type = type;
        var index = -1;
        for(var i = 0; index < 0 && i < $scope.sns[type].length; i++){
            if(!$scope.sns[type][i].sns_id){
                index = i;
                $scope.chosenSns.id  = $scope.sns[type][i].id;
                $scope.chosenSns.name = $scope.sns[type][i].name;
            }
        }
      /*  else{
            $http.post('/sns', $scope.chosenSns)
                .success(function (id) {
                    $scope.chosenSns.sns_id = id;
                    $scope.chosenSns = null;
                })
                .error(function(err){
                    alert(err);
                })
        }*/
    }

    $scope.snsSaved = function (type) {
        if(!$scope.chosenSns.sns_name || $scope.chosenSns.sns_name.length > 40){
            return;
        }
        $http.post('/sns', $scope.chosenSns)
            .success(function (id) {
                var index = -1;
                for(var i = 0; index < 0 && i < $scope.sns[type].length; i++){
                    if($scope.sns[type][i].id == $scope.chosenSns.id){
                        index = i;
                        $scope.sns[type][i].sns_id = id;
                        $scope.sns[type][i].sns_name = $scope.chosenSns.sns_name;
                    }
                }

                $scope.cancelSns(type);
            })
            .error(function(err){
                $scope.errors = err.message;
            })
    }

    $scope.cancelSns = function (type) {
        if($scope.chosenSns.type == type){
            $scope.chosenSns = {id:'', name:'', sns_name:'', type:''}
        }
    }

    $scope.updateSns = function(sns){
        if(!$scope.editedSns || !$scope.editedSns.sns_id == sns.sns_id){
            $scope.editedSns = sns;
        }
        else{
            $http.put('/sns/' + sns.sns_id, $scope.editedSns)
                .success(function (data) {
                    $scope.editedSns = null;
                })
                .error(function (err) {
                    $scope.errors = err.message;
                });
        }
    }

    $scope.deleteSns = function(sns){
        if($scope.editedSns && $scope.editedSns.sns_id == sns.sns_id){
            $scope.editedSns = null;
        }
        else{
            $scope.snsToDelete = sns;
            $('#deleteSnsModal').modal('show');
        }
    }

    $scope.snsDeleted = function () {

        $http.delete('/sns/' + $scope.snsToDelete.sns_id)
            .success(function () {
                angular.forEach($scope.sns, function (value) {
                    angular.forEach(value, function (sns) {
                        if(sns.sns_id == $scope.snsToDelete.sns_id){
                            sns.sns_id = null;
                            sns.sns_name = null;
                        }
                    })
                });
                $('#deleteSnsModal').modal('hide');
            })
            .error(function (err) {
                alert(err);
            })
    }

    $scope.save = function(){
        $scope.submitted = true;
        var count = $("#roles input[name^='role']").length;
        if(!count)
            return;

        if($("#poster_image").attr('src').indexOf('default.png') > 0)
        {
            $("#informationErrorModal").modal('show');
            return;
        }

       /* var username = $("#username").val();
        if(username.length < 2){
            $scope.error.username = 'i';
            return;
        }
        else if(username.length > 40){
            $scope.error.username = 'm';
            return;
        }
        else{
            $scope.error.username = null;
        }

        var presentation = $("#presentation").val();*/
        /*if(presentation.length < 10){
            $scope.error.presentation = 'i';
            return;
        }
        else*/
       /* if(presentation.length > 800){
            $scope.error.presentation = 'm';
            return;
        }
        else{
            $scope.error.presentation = null;
        }
        $http({
            method: 'POST',
            url: '/account',
            data:  new FormData($('#usrform')[0]),
            headers: {
                'Content-Type': undefined
            }
        }).
            success(function() {
                $('#informationChangedModal').modal('show');
                $scope.pageJump = $timeout(function(){
                    window.location.href = '/';
                },6000)
            })
            .error(function (err) {
                $scope.errors = err;
                $('#informationErrorModal').modal('show');
            })*/
       $('#usrform').submit();
    }

    $scope.confirmPageJump = function () {
        window.location.href = $('#previous').val();
    }

    $scope.cancelPageJump = function () {
        $timeout.cancel($scope.pageJump);

        $('.modal').modal('hide');
    }

    $scope.$watch('contact.fix_code', function (newVal) {
        $('#label_fix_code').text($('#fix_code option[value=' + newVal + ']').attr('title'));
    })
    $scope.$watch('contact.mobile_code', function (newVal) {
        $('#label_mobile_code').text($('#mobile_code option[value=' + newVal + ']').attr('title'));
    })
    $scope.changeContact = function () {
        $http({
            method: 'POST',
            url: '/contact',
            data:  new FormData($('#contactForm')[0]),
            headers: {
                'Content-Type': undefined
            }
        }).
        success(function() {
            $('#contactChangedModal').modal('show');
            $scope.contact.edited = false;
            $scope.pageJump = $timeout(function(){
                window.location.href = $('#previous').val();
            },6000)
        })
            .error(function (err) {
                $scope.errors = err.errors;
            })
    }
    $scope.changePwd = function(pwd){
        if(pwd.old == pwd.new){
            $scope.result = 'samepwd';
            $scope.openModal(true);
        }
        else {
            $http.put('/account/'+pwd.old, pwd)
                .success(function (data) {
                    $('#pwdChangedModal').modal('show');
                    $scope.pageJump = $timeout(function(){
                        window.location.href = '/';
                    },6000)
                }).error(function (err) {
                    $scope.errors = err;
                    $('#pwdErrorModal').modal('show');
                }
            );
        }
    }

    $scope.choseSns = function (s) {
        $scope.sns[s.type].viewed = false;
        $scope.chosenSns.id = s.id;
        $scope.chosenSns.name = s.name;
    }
    $scope.regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]{6,16}$/;
    //$scope.regex = /^(?=.*[\d])(?=.*[!@#$%^&*-])[\w!@#$%^&*-]{6,16}$/;
})