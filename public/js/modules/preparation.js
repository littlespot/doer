
appZooMov.factory('Preparations', ['$http', function($http) {
    var preparations = {};

    preparations.setDate = function () {
        var today = new Date();
        var tYear = today.getYear();
        var tMonth = today.getMonth() + 1;
        var tDate = today.getDate();

        if(tMonth === 12 && tDate > 25){
            today.setYear(tYear + 1);
            today.setMonth(0);
            today.setDate(26 - tDate);

            return today;
        }

        var rest = 24;
        if((tMonth < 8 && tMonth%2 === 1 ) || (tMonth > 7 && tMonth%2 === 0 )){
            rest = 25;
        }
        else if(tMonth === 2){
            rest = tYear%4 === 0 ? 23 : 22;
        }

        if(tDate > rest){
            today.setMonth(tMonth);
            today.setDate(rest + 1 - tDate);
        }
        else{
            today.setDate(tDate + 7)
        }

        return today;
    }

    preparations.compareSetDate = function (finish_at) {
        var dateString = finish_at.replace('T',' ');
        var arr1 = dateString.split(" ");
        var sdate = arr1[0].split('-');
        finish_at = new Date(sdate[0], sdate[1]-1, sdate[2]);
        if(preparations.compareDate){
            return finish_at;
        }
        else{
            return preparations.setDate();
        }
    }

    preparations.compareDate = function (finish_at) {
        var today = new Date();
        var finish = typeof (finish_at) == "string" ? new Date(finish_at) : finish_at;
        var tYear = today.getYear();
        var fYear = finish.getYear();
        var tMonth = today.getMonth() + 1;
        var fMonth = finish.getMonth() + 1;
        var tDate = today.getDate();
        var fDate = finish.getDate();

        if(tYear > fYear)
            return false;
        else if(tYear === fYear){
            if(tMonth > fMonth)
                return false;
            else if (tMonth === fMonth){
                return fDate - tDate > 6
            }
            else if(fMonth - tMonth === 1 && fDate < 7){
                if(tDate > 22){
                    var rest = 30;
                    if((tMonth < 8 && tMonth%2 === 1 ) || (tMonth > 7 && tMonth%2 === 0 ))
                        rest = 31;
                    else if(tMonth === 2 && tYear%4 === 0)
                        rest = 28;

                    return rest - tDate + fDate > 6;
                }
            }
        }
        else if(fYear - tYear === 1 && fMonth === 1 && tMonth === 12){
            return (30 - tDate + fDate > 6);
        }

        return true;
    }

    return preparations;
}]);
