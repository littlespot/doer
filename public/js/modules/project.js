appZooMov.factory('Projects', ['$resource', '$http', function($resource, $http) {
    var project = $resource('/api/projects/:id', null,
        {
            'all':{method:'GET',params:{id:null}, isArray:true},
            'show':{method:'GET',params:{id:'@id'}, isArray:false},
            'update': { method:'PUT' }
        });
    project.status = [{id: '!!', name:'All'},{id:1, name:"Inprogress"},{id:2,name:"Completed"},{id:3,name:"Out"}];
    project.orders = [{id:'updated_at',name:"updated_at"},
        {id:'created_at',name:"created_at"},
        {id:'finish_at',name:"finish_at"},
        {id:'views_cnt', name:"Popularity"},
        {id: 'comments_cnt', name:'Comments'},
        {id: 'followers_cnt', name:'Followers'}];

    project.sendApplication = function (recruit) {
        $http.post('/api/applications', {
                recruit: recruit.id,
                motivation: recruit.motivation,
                admin: $scope.project.user.id
            })
            .then(function successCallback(result) {

            }, function errorCallback(response) {
                alert(response.message);
            });
    }

    return project
}]);