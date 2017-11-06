/**
 * Created by Jieyun on 2016/12/1.
 */
appZooMov.directive('questionsContent', function ($http) {
    return {
        transclude:true,
        restrict:'E',
        templateUrl:'views/templates/questions-content.html',
        link:function (scope, elm) {
            scope.supportQuestion = function(question, user){
                if(user)
                    return;
                $http.post('/api/questionFollow/', {id: question.id, _token:$("body input[name='csrfmiddlewaretoken']").val()})
                    .success(function(result){
                        if(result == 0){
                            question.followers = [question.followers[0]-1, false];
                        }
                        else{
                            question.followers = [question.followers[0]+1, true];
                        }
                    })
                    .error(function (err) {
                        alert(err.data);
                    });
            }
        }
    };
});
