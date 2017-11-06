appZooMov.directive('innerEditor', function ($rootScope, $http, $uibModal) {
    return {
        restrict: 'A',
        link: function (scope) {
            scope.fonts = ['宋体','楷体','黑体','仿宋体','Serif', 'Sans', 'Arial', 'Arial Black', 'Courier',
                    'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Lucida Sans', 'Tahoma', 'Times',
                    'Times New Roman', 'Verdana'];
        }
    }
})