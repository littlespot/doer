appZooMov.controller("profileCtrl", function($filter, $rootScope, $scope, $timeout, $http, $uibModal, $log, Projects) {

    $scope.setLocalPage = function (total, perPage) {
        var totalPage = Math.ceil(total/perPage);
        return {
            show:totalPage>1,
            currentPage: 1,
            perPage: perPage,
            total:total};
    }

    $scope.loadProjects = function () {
        var promise = $http({
            method: 'GET',
            url: '/api/profile/'+  $scope.catalogueChosen + '/' + $scope.profile.id,
            isArray:true,
            cache:true
        });

        promise.then(
            function(projects) {
                $scope.projects = projects.data;
                $scope.pagination = $scope.setLocalPage($scope.projects.length, 9);
                $('#catalogues li[data-bind=catalogues]:hidden').show();
                $('#catalogue_'+ $scope.catalogueChosen).hide();
                $rootScope.loaded();
            },
            function(error) {
                $log.error('failure loading projects for user ' + $scope.profile.id, error);
            });
    }

    $scope.loadRelations = function (page, callback) {
        $http.get('/api/' + $scope.selectedTopTab + '/' + $scope.profile.id + (page ? '?page=' +$scope.rpagination.currentPage : ''))
            .success(function(relations){
                $scope.relations = relations.data;
                $scope.rpagination = $rootScope.setPage(relations);
                if(callback){
                    $scope.resetNumber(relations);
                }
            })
            .error(function (error) {
                $log.error('failure loading ' + $scope.selectedTopTab + ' for user ' + $scope.profile.id, error);
            });
    }

    $scope.selectTopTab = function (index) {
        $scope.selectedTopTab = $scope.relationTabs[index];
        $scope.loadRelations(null, true);
    }

    $scope.selectTab = function (index) {
        if( $scope.selectedTab.equals(index)){
            $scope.selectedTab = 0;
            return;
        }

        $scope.selectedTab = index;

        if(index == 2 && !$scope.sns)
            $http.get('/sns/' +  $scope.profile.id)
                .success(function (data) {
                    $scope.sns = data;
                })
                .error(function (error) {
                    $log.error('failure loading sns for user ' + $scope.profile.id, error);
                });
    }

    $scope.resetNumber = function (relations) {
        var relation = $('#relation_' + $scope.profile.id);
        var number = relations.total;
        $("#sup_" + $scope.selectedTopTab).text(number> 0  ? number : '');
        var fans = $('.ifollow', relation);
        var fans_cnt = parseInt(fans.text().replace(/[\D]*/,''), 0);
        var idols = $('.followme', relation);
        var idols_cnt = parseInt(idols.text().replace(/[\D]*/,''), 0);

        switch ($scope.selectedTopTab){
            case 'fans':
                if(!number.equals(fans_cnt)){
                    fans.text(number);
                }
                $("#sup_idols").text(idols_cnt > 0 ? idols_cnt: '')
                $("#sup_friends").text($scope.friends_cnt > 0  ? $scope.friends_cnt : '');;
                break;
            case 'friends':
                if(!number.equals($scope.friends_cnt))
                    $scope.friends_cnt = number;
                $("#sup_idols").text(idols_cnt > 0 ? idols_cnt: '');
                $("#sup_fans").text(fans_cnt > 0 ? fans_cnt: '');
                break;
            case 'idols':
                if(!number.equals(idols_cnt)){
                    idols.text(number);
                }
                $("#sup_fans").text(fans_cnt > 0 ? fans_cnt: '');
                $("#sup_friends").text($scope.friends_cnt > 0  ? $scope.friends_cnt : '');;
                break;
        }
    }

    $scope.pageChanged = function () {
        $scope.loadRelations(true, false);
    }

    $scope.changeLocation = function(path)
    {
        window.location = path;
    }

    $scope.setTabs = function (relation, fans_cnt) {
        $("#sup_fans").text(fans_cnt > 0 ? fans_cnt : '');
        var friends = parseInt($('#sup_friends').text(), 0);
        if(!friends)
            friends = 0;

        if(relation ===  'Friend'){
            $("#sup_friends").text(friends+1);
        }
        else if(relation === 'Fan'){
            friends -= 1;
            $("#sup_friends").text(friends > 0 ? friends : '');
        }
        if($scope.selectedTopTab ===  'idols'){
            return;
        }
        if(relation === 'Friend' || ($scope.selectedTopTab === 'fans' && relation === 'Idol')){
            var projects = $http({
                method: 'GET',
                url: '/api/myrelation',
                isArray:false,
                cache:true
            });

            projects.then(
                function(relation) {
                    $scope.relations.splice(0, 0, relation.data);
                },
                function(error) {
                    $log.error('failure loading myrelation', error);
                });
        }
        else if(relation === 'Fan' || ($scope.selectedTopTab === 'fans' && relation === 'Stranger')){
            var index = -1;
            for(var i = 0; i < $scope.relations.length && index < 0; i++){
                if($scope.relations[i].id.equals($scope.profile.id))
                    index = i;
            }

            if(index >= 0){
                $scope.relations.splice(index, 1);
            }
        }
    }

    $scope.changeRelation = function(id, username, callback){
        var relation = $('#relation_' + id);
        var oldClass = relation.attr('class').split(' ')[1];
        if(oldClass.equals('mySelf'))
            return;
        else if(oldClass.equals('myIdol') || oldClass.equals('myFriend')){
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'confirm.html',
                controller: function($scope) {
                    $scope.selectedUser = username;
                }
            });

            modalInstance.result.then(function (confirm) {
                if (confirm)
                    $scope.relation(id,callback,relation, oldClass);
            })
        }
        else
            $scope.relation(id,callback,relation, oldClass);
    }

    $scope.relation = function (id, callback, relation, oldClass) {
        var fans = $('.ifollow', relation);
        var fans_cnt = parseInt(fans.text().replace(/[\D]*/,''), 0);

        $http.put('/admin/relations/' + id)
            .success(function (result) {
                if('my'+result.relation === oldClass)
                    return;

                var counter = -1;
                if (result.relation === 'Friend' || result.relation === 'Idol')
                    counter = 1;

                fans_cnt += counter;
                fans.text(fans_cnt);

                if(callback == 2) {
                    relation.removeClass(oldClass).addClass('my' + result.relation);
                    if ($scope.selectedTab == 1) {
                        $scope.setTabs(result.relation, fans_cnt);
                    }

                    return;
                }
                else if(callback == 1){
                    var idol = $('#profileIdol');
                    var idol_cnt = parseInt(idol.text().replace(/[\D]*/,''), 0);

                    idol_cnt += counter;
                    idol.text(idol_cnt);

                    $("#sup_idols").text(idol_cnt > 0 ? idol_cnt : '');

                    var friends = $("#sup_friends");
                    var friends_cnt = parseInt(friends.text().replace(/[\D]*/,''), 0);
                    if(!friends_cnt)
                        friends_cnt = 0;
                    if(result.relation == 'Friend'){
                        friends.text(friends_cnt+1);
                    }
                    else if(result.relation == 'Fan'){
                        friends_cnt -= 1;
                        friends.text(friends_cnt> 0 ? friends_cnt : '');
                    }

                    if($scope.selectedTopTab != 'fans'){
                        $scope.loadRelations(true, true);
                        return;
                    }
                }

                var rela = $filter('getById')($scope.relations, id);

                rela.fans_cnt = fans_cnt;
                rela.relation = result.relation;
            })
            .error(function (error) {
                $log.error('failure chang relation for user ' + $scope.profile.id, error);
            });
    }

    //$scope.categories = Projects.categories;

    $scope.openCatalogue = function(){
        $scope.overlay = true;
    }

    $scope.setFilter = function(filter){
        $scope.filterChosen = filter;
        $scope.overlay = false;
    }

    $scope.optionChosen =  'Plan';


    $scope.catalogueChosen = 'creator';

    $scope.chooseCatalogue = function(key, value){
        $scope.catalogueChosen = key;
        $scope.loadProjects();
        $('#catalogue_name').text(value);
    }

    $scope.orders = Projects.orders;
    $scope.orderChosen = Projects.orders[0];

    $scope.openOrder = function(){
        $scope.overlayOrder = true;
    }

    $scope.setOrder = function(order){
        $scope.orderChosen = order;
        $scope.overlayOrder = false;
    }

    $scope.openSns = function(){
        $scope.overlaySns = true;
    }

    $scope.callback = function (opt) {
        $scope.selectTab(0);
    }

    $scope.init = function (id, username, admin) {
        $scope.snsMenu = ["m", "v", "t", "c", "p", "s"];
        $scope.overlaySns = false;

        $scope.tabMenu = ['Projects', 'Relations'];
        $scope.selectedTab = 0;
        $scope.relationTabs = ['fans', 'idols', 'friends'];

        $scope.selectedTopTab = $scope.relationTabs[0];
        $scope.profile = {id:id, username:username} ;
        $scope.filters =  admin ? [{id: '!!', name:'All'},{id:1, name:"Inprogress"},{id:2,name:"Completed"},{id:3,name:"Out"}] : [{id: '!!', name:'All'},{id:1, name:"Inprogress"},{id:2,name:"Completed"}] ;
        $scope.filterChosen = Projects.status[0];
        $scope.invitation = {message:"", receiver:$scope.profile};
        $scope.loadProjects();
    }
});