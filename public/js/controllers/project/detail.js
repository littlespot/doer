/**
 * Created by Jieyun on 2016/12/1.
 */
appZooMov.controller("projectDetailCtrl", function($rootScope, $scope, $http, $filter, $log, $uibModal)
{
    $scope.roles = [];

    $scope.tabMenu = ['Container', 'Team', 'Updates', 'Comments', 'qna'];
    $scope.selectedTab = 0;

    $scope.changeData = function (tab) {
        if($scope.selectedTab == 1){
            $http.get('/api/teams/' + $scope.id + (tab ? '': '?page=' + $scope.pagination.currentPage))
                .success(function (result) {
                    $scope.team = result.data;
                    if(tab){
                        $scope.pagination = $rootScope.setPage(result);
                    }

                    $scope.loading = false;
                })
                .error(function (err) {
                    $log.error('failure loading team for project ' + $scope.id + tab ? '': '?page ' + $scope.pagination.currentPage, err);
                })
        }
        else if($scope.selectedTab == 3){

            $http.get('/api/comments/' + $scope.id + (tab ? '': '?page=' + $scope.pagination.currentPage))
                .success(function (result) {
                    $scope.comments = result.data;
                    if(tab){
                        $scope.pagination = $rootScope.setPage(result);
                    }
                    $scope.loading = false;
                })
                .error(function (err) {
                    $log.error('failure loading comments for project ' + $scope.id + tab ? '' : '?page ' + $scope.pagination.currentPage, err);
                })
        }
        else if($scope.selectedTab == 4){
            if(tab)
                $http.get('/api/questions/'+ $scope.id)
                    .success(function (result) {
                        $scope.questions = result.data;
                        if(tab){
                            $('#sup_questions').text(result.total > 0 ? result.total: '');
                            $scope.pagination = $rootScope.setPage(result);
                        }
                        $scope.loading = false;
                    })
                    .error(function (err) {
                        $log.error('failure loading question for project ' + $scope.id, err);
                    });
            else
                $http.get('/api/questions/' + $scope.id, {params:{page:$scope.pagination.currentPage}})
                    .success(function (result) {
                        $scope.questions = result.data;
                        $scope.loading = false;
                    })
                    .error(function (err) {
                        $log.error('failure loading question for project ' + $scope.id, err);
                    });
        }
    }
    $scope.loadData = function (selectedTab) {
        if(selectedTab == 0){
            $scope.loading = false;
        }
        else if (selectedTab == 2){
            $http.get('/api/events/' + $scope.id)
                .success(function (result) {
                   $scope.loadEvents(result);
                   $scope.loading = false;
                })
                .error(function (err) {
                    alert("error");
                    $scope.loading = false;
                })
        }
        else
            $scope.changeData(true);
    }

    $scope.selectTab = function (index) {
        $scope.selectedTab = index;
        $scope.loading = true;
        $scope.loadData(index);
    }

    $scope.init = function (id, user, comments_cnt, tab) {
        $scope.id = id;
        $scope.user = user;

        if(tab){
            $scope.selectTab(tab);
        }

        var cnt = parseInt(comments_cnt);
        $scope.comments_cnt = cnt ? cnt : 0;

        $rootScope.loaded();
    }

    $scope.pageChanged = function () {
        $scope.loading = true;
        $scope.changeData(false);
    }

    $scope.finish = function () {
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'confirm.html',
            controller: function($scope) {
                $scope.confirm = 'confirmO';
            }
        });

        modalInstance.result.then(function (confirm) {
            if (!confirm)
                return;
            $http.post('/admin/finish', {id:$scope.id})
                .success(function (data) {
                    if(data == 2){
                        $("#project_status").html('<div class="alert alert-success">' + $filter("translate")("notification.wait") + '</div>');
                    }
                });
        })
    }

    $scope.followQuestion = function(question){
        if(question.mine)
            return;
        question.following = true;
        if(question.myfollow){
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'confirm.html',
                controller: function($scope) {
                    $scope.confirm = 'confirmF';
                }
            });

            modalInstance.result.then(function (confirm) {
                if (!confirm)
                    return;
                $scope.followConfirmed(question);
            })
        }
        else{
            $scope.followConfirmed(question);
        }
    }

    $scope.followConfirmed = function (question) {
        $http.put('/admin/questions/'+ question.id + '?_token=' + $("body input[name='csrfmiddlewaretoken']").val())
            .success(function(result){
                if(!result)
                    return;

                question.myfollow = result.myfollow;
                question.followers_cnt += result.cnt;
                question.following = false;
            })
            .error(function (err) {
                $log.error('failure follow question for question ' + question.id, err);
            });
    }

    $scope.addEvent = function (monthEvent, type, content) {
        var month = content.created_at.substr(0,7);
        var day = content.created_at.substr(8,2);
        var index = -1;
        if(type == 't')
            devent = {user_id:content.user_id, username:content.username, roles:[content.name]};
        else
            devent = content;
        for(var i = 0; i < monthEvent.length && index < 0; i++){
            var mevent = monthEvent[i];

            if(mevent.month == month){

                index = i;
                var eventDays = mevent.events;
                var dayIndex = -1;

                for(var j = 0; j < eventDays.length && dayIndex < 0; j++){
                    var eventDay = eventDays[j];

                    if(eventDay.day == day){
                        dayIndex = j;
                        var found = -1;
                        for(var z = 0; z < eventDay.events.length && found < 0; z++){
                            if(eventDay.events[z].type == type){
                                found = z;
                                if(type == 't'){
                                    var p = -1;
                                    for(var t=0; t <  eventDay.events[z].events.length && p<0; t++){
                                        var person = eventDay.events[z].events[t];
                                        if(person.user_id == content.user_id){
                                            p = t;
                                            if(person.roles.indexOf(content.name) < 0)
                                                person.roles.push(content.name)
                                        }
                                    }

                                    if(p < 0){
                                        eventDay.events[z].events.push(devent)
                                    }
                                }
                                else
                                    eventDay.events[z].events.push(devent);
                            }
                        }

                        if(found < 0){
                            eventDay.events.push({type:type, events:[devent]});
                        }
                    }
                    else if(eventDay.day < day){
                        dayIndex = j;
                        eventDay.splice(j,0, {day:day, events:[{type:type, events:[devent]}]});
                    }
                }

                if(dayIndex < 0){
                    eventDays.push({day:day, events:[{type:type, events:[devent]}]});
                }
            }
            else if(mevent.month < month){
                index = i;
                monthEvent.splice(i, 0, {month:month, events:[{day:day, events:[{type:type, events:[devent]}]}]});
            }
        }

        if(index < 0){
            monthEvent.push({month:month, events:[{day:day, events:[{type:type, events:[devent]}]}]});
        }
    }
    $scope.loadEvents = function (result) {
        var months = [];
        var monthEvent = [];
        angular.forEach(result.reports,function(event){
            event.roles = event.occupations.split(',');
            $scope.addEvent(monthEvent, 'r', event)
        });

        angular.forEach(result.teams,function(event) {
            $scope.addEvent(monthEvent, 't', event)
        });

        angular.forEach(result.teams,function(event) {
            $scope.addEvent(monthEvent, event.type, event)
        });

        $scope.months = months;
        $scope.events = monthEvent;

    }

    $scope.deleteEvent = function (event, month) {
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'confirm.html',
            controller: function($scope) {
                $scope.confirm = 'confirmE';
            }
        });

        modalInstance.result.then(function (confirm) {
            if (!confirm)
                return;

            event.deleting = true;

            $http.delete('/api/events/' + event.id, {
                params: {_token: $("body input[name='csrfmiddlewaretoken']").val()}
            })
                .success(function (result) {
                    if (!result)
                        return;

                    var index = $scope.months.indexOf(month);

                    if($scope.events[index].length == 1){
                        $scope.months.splice(index,1);
                        $scope.events.splice(index,1);
                    }
                    else{
                        var events = $scope.events[index];
                        index = -1;
                        for(var i = 0; i < events.length && index < 0; i++){
                            if(events[i].id == event.id){
                                index = i;
                                events.splice(index, 1);
                            }
                        }
                    }

                    event.deleting = false;
                })
                .error(function (err) {
                    $log.error('failure delete question ' + question.id, err);
                });
        });
    }
    $scope.deleteQuestion = function (question) {
        if(!question.mine)
            return;
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'confirm.html',
            controller: function($scope) {
                $scope.confirm = 'confirmQ';
            }
        });

        modalInstance.result.then(function (confirm) {
            if (!confirm)
                return;
            question.deleting = true;

            var page = 0;

            if($scope.pagination.show){
                if($scope.pagination.currentPage == $scope.pagination.lastPage)
                {
                    page = $scope.questions.length == 1 ? $scope.pagination.currentPage -1 : 0;
                }
                else{
                    page = $scope.pagination.currentPage;
                }
            }

            $http.delete('/admin/questions/' + question.id, {params:
                {_token: $("body input[name='csrfmiddlewaretoken']").val(), page:page}
            })
                .success(function (result) {
                    if (!result)
                        return;

                    if(page){
                        $scope.questions = result.data;
                        $('#sup_questions').text(result.total);
                        $scope.pagination = $rootScope.setPage(result);
                    }
                    else{
                        var index = -1;

                        for(var i = 0; i < $scope.questions.length && index < 0; i++){
                            if($scope.questions[i].id == question.id){
                                index = i;
                                $scope.questions.splice(index,1);
                                var count = $('#sup_questions');
                                var cnt = parseInt(count.text().replace(/[\D]*/,''));

                                if(cnt){
                                    count.text(cnt-1);
                                }

                                $scope.pagination.total -= 1;
                            }
                        }
                    }

                    question.deleting = false;
                })
                .error(function (err) {
                    $log.error('failure delete question ' + question.id, err);
                });
        });
    }

    $scope.followProject = function(){
        var div = $('#followers_cnt');

        var contents = $('div', div);
        var icon = contents[0];
        var count =contents[1];
        var mine = $(icon).hasClass('fa-bookmark');
        var cnt = parseInt(count.innerText.replace(/[\D]*/,''), 0);
        if(!cnt)
            cnt = 0;
        $scope.following = true;
        $http.put("/api/project/followers/" + $scope.id + '?_token=' + $("body input[name='csrfmiddlewaretoken']").val())
            .success(function() {
                if(mine == 1){
                    $(count).text(cnt - 1);
                    $(icon).removeClass('fa-bookmark').addClass('fa-bookmark-o');
                }
                else{
                    $(count).text(cnt + 1);
                    $(icon).removeClass('fa-bookmark-o').addClass('fa-bookmark');
                }
                $scope.following  = false
            })
            .error(function (err){
                $log.error('failure to update followshipt of project ' + $scope.id + ' for user +' + $scope.user, err);
            });
    }

    $scope.loveProject = function(){
        var div = $('#lovers_cnt');

        var contents = $('div', div);
        var icon = contents[0];
        var count =contents[1];
        var mine = $(icon).hasClass('fa-heart');
        var cnt = parseInt(count.innerText.replace(/[\D]*/,''), 0);
        if(!cnt)
            cnt = 0;

        $scope.loving = true;
        $http.put("/api/project/lovers/" + $scope.id + '?_token=' + $("body input[name='csrfmiddlewaretoken']").val())
            .success(function() {
                if(mine == 1){
                    $(count).text(cnt - 1);
                    $(icon).removeClass('fa-heart').addClass('fa-heart-o');
                }
                else{
                    $(count).text(cnt + 1);
                    $(icon).removeClass('fa-heart-o').addClass('fa-heart');
                }

                $scope.loving = false;
            })
            .error(function (err){
                $log.error('failure to update loveship of project ' + $scope.id + ' for user +' + $scope.user, err);
            });
    }

    $scope.slideUp = function(index){
        var div = $("#timeline-" + index);
        if($(".timeline-header", div).hasClass('closed')){
            $(".timeline-header", div).removeClass('closed');
            $("ul", div).show( "slow" );
        }
        else{
            $(".timeline-header", div).addClass('closed');
            $("ul", div).slideUp();
        }
    }

    $scope.cancelApplication = function(){
        $scope.myapplication = null;
    }

    $scope.openApplication = function(id, name){
        $scope.myapplication = {recruit_id:id, motivation:'', occupation:name, _token: $("body input[name='csrfmiddlewaretoken']").val()};
    }

    $scope.sendApplication = function (receiver) {
        if($scope.myapplication.motivation.length < 15 || $scope.myapplication.motivation.length > 2000)
            return;
        $scope.myapplication.receiver_id = receiver;
        $scope.myapplication.sending = true;
        $http.post('/admin/applications',  $scope.myapplication)
            .then(function successCallback() {
                var application = $('#recruit_' + $scope.myapplication.recruit_id);
                $('.recruit-block', application).addClass('recruit-applied');
                $('.title>.pull-right', application).show();
                $('.overlay', application).remove();
                $scope.myapplication = null;
            }, function errorCallback(response) {
                $log.error('failure send application to recuit ' + $scope.myapplication.recruit_id, response);
            });
    }

    $scope.share = [];

    $scope.timelineFilter = '!!';

    $scope.setTimelineFilter = function(id, sFilter, step, fFilter, final){
        var div = $("#timeline-" + id);
        if(div.hasClass("chosen")){
            var span = $("span", div);
            if(span.hasClass("fa-"+step)){
                span.removeClass("fa-"+step);
                span.addClass("fa-"+final);
                $scope.timelineFilter = fFilter;
            }
            else{
                if(span.hasClass("fa-"+final)){
                    span.removeClass("fa-"+final);
                    span.addClass("fa-"+step);
                }

                $scope.timelineFilter = '!!';
                div.removeClass("chosen")
            }
        }
        else{
            div.siblings('.timeline-btn').removeClass("chosen");
            div.addClass("chosen");
            if(sFilter){
                $scope.timelineFilter = sFilter;
            }
            else{
                $scope.timelineFilter = '!!';
            }
        }
    }
});