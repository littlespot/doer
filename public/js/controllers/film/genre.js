appZooMov.controller("filmCtrl", function($rootScope, $scope) {
    $scope.init = function () {
    }

    $scope.change = function (name) {
        if ($("#block_" + name + " input:checked").length > 1) {
            $("#block_" + name + " input:not(:checked)").attr('disabled', true);
        }
        else {
            $("#block_" + name + " input:not(:checked)").removeAttr('disabled');
        }
    }
});
