appZooMov.controller("filmCtrl", function($rootScope, $scope) {
    $scope.init = function (production, shooting, dialog) {
        $scope.production = angular.fromJson(production);
        for(var i = 0; i <$scope.production.length; i++)
        {
            $scope.production[i] = $scope.production[i].toString();
        }
        $scope.shooting = angular.fromJson(shooting);
        for(var i = 0; i <$scope.shooting.length; i++)
        {
            $scope.shooting[i] = $scope.shooting[i].toString();
        }
        $scope.dialog = angular.fromJson(dialog);
        for(var i = 0; i <$scope.dialog.length; i++)
        {
            $scope.dialog[i] = $scope.dialog[i].toString();
        }
        $rootScope.loaded();
    }

    $scope.$watch('principal', function (newVal, oldVal) {
        if(oldVal && oldVal != ''){
            $('#block_production select option[value="'+oldVal+'"]').removeAttr('disabled');
            $('#block_shooting select option[value="'+oldVal+'"]').removeAttr('disabled');
        }

        if(newVal && newVal != ''){
            $('#block_production select option[value="'+newVal+'"]').removeAttr('selected');
            $('#block_production select option[value="'+newVal+'"]').attr('disabled', true);
            $('#block_shooting select option[value="'+newVal+'"]').removeAttr('selected');
            $('#block_shooting select option[value="'+newVal+'"]').attr('disabled', true);
        }
    })

  /*  $scope.changeProduction = function (i) {
      $('#production_'+i).siblings('select').find('option').each(function () {
            var val = $(this).val();
            if (val != $scope.principal && $scope.production.indexOf(val) < 0) {
                $(this).removeAttr('disabled');
            }
            else{
                $(this).attr('disabled', true);
            }
        })
    }*/

    $scope.addProduction = function () {
        var count = 0;
        var selectors = $('#block_production select:visible');

        for(;count<selectors.length; count++){
             var val = selectors[count].value;
            if(!val || val == ''){
                return false;
            }
        }

        if(count < 5){
            $('#production_'+ count).show();
        }
        if(count > 3){
            $('#btn_production').hide();
        }
    }

   /* $scope.changeShooting = function (i) {
        $('#shooting_'+i).siblings('select').find('option').each(function () {
            var val = $(this).val();
            if (val != $scope.principal && $scope.shooting.indexOf(val) < 0) {
                $(this).removeAttr('disabled');
            }
            else{
                $(this).attr('disabled', true);
            }
        })
    }*/

    $scope.addShooting = function () {
        var count = 0;
        var selectors = $('#block_shooting select:visible');

        for(;count<selectors.length; count++){
            var val = selectors[count].value;
            if(!val || val == ''){
                return false;
            }
        }

        if(count < 9){
            $('#shooting_'+ count).show();
        }
        if(count > 7){
            $('#btn_shooting').hide();
        }
    }

    $scope.changeSound = function (val) {
        if(val > 0){
            $("#block_lang").show();
        }
        else{
            $("#block_lang").hide();
        }
    }

   /* $scope.changeDialog = function (i) {
        $('#dialog_'+i).siblings('select').find('option').each(function () {
            var val = $(this).val();
            if ($scope.dialog.indexOf(val) < 0) {
                $(this).removeAttr('disabled');
            }
            else{
                $(this).attr('disabled', true);
            }
        })
    }*/
});
