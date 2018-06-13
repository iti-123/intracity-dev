app.filter('getobjectvalue', function () {
    return function (index, object) {

        var returndata = '';
        angular.forEach(object, function (val, key) {

            if (val.id == index) {

                returndata = val.value;
            }

        });
        return returndata;
    }


});

app.filter('limitstring', function () {
    return function (str) {
       
        return str.substring(0, 220);
    };
});

app.filter('getName', function () {
    return function (index, object) {
        var returndata = '';
        angular.forEach(object, function (val, key) {
            if (val.id == index) {
                returndata = val.name;
            }
        });
        return returndata;
    }


});


app.filter('datefilter', function () {
    return function (my_date) {
        my_date = my_date.replace(/-/g, "/");
        var changedate = new Date(my_date);
        return changedate;
    }
});

app.filter('timestampToDate', function () {
    return function (timestamp) {
        var date = new Date(timestamp * 1000);
        var dateObject = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + (date.getDate() - 1)).slice(-2);
        return dateObject;
    };
});

app.filter('removeSpace', function () {
    return function (myStr) {
        return myStr = myStr.replace(/ /g, "-");
    }
});

app.filter('filterMaterial', function () {
    // return function(MATERIAL_TYPE) {
    //   MATERIAL_TYPE.forEach(function(key, value){
    //       if(key == id) {
    //         return value;            
    //       }
    //   });

    // }
    return function (index, object) {
        var returndata = '';
        angular.forEach(object, function (val, key) {

            if (key == index) {

                returndata = val;
            }

        });
        return returndata;
    }

});

app.filter('dateStringFormat', function () {
    return function (x) {
        x = new Date(x);
        let day = x.getDate();
        let month = x.getMonth() + 1;
        let year = x.getFullYear();
        if (day < 10) {
            day = '0' + day;
        }
        if (month < 10) {
            month = '0' + month;
        }
        return day + '-' + month + '-' + year;
    };
});


app.filter('checkDate', function () {
    return function (x) {
        
         var post_moment = moment(x, "YYYY-MM-DD HH:mm:ss" ).unix();
         var current_moment = moment().unix();
      //  console.log( 'post :' + post_moment ) ;
      //  console.log( 'current :' + moment().unix() ) ;
      //  console.log( 'diff :' + (post_moment - current_moment) );         
         return current_moment > post_moment ? 1 : 0;       
    };
});

app.filter('bidelapsed', function () {
    return function (x) {
        // console.log('obj',x);
          var pastdate=x.post.last_date+' '+x.post.last_time;
          var today = new Date();
          var date = today.getFullYear()+'-'+(("0" + (today.getMonth() + 1)).slice(-2))+'-'+(("0" + (today.getDate())).slice(-2));
          var time = today.getHours() + ":" + today.getMinutes() ;
          var dateTime =date+' '+time;
          if (new Date(dateTime) < new Date(pastdate)) {
             return true;
           }else{
             return false;
           }
    };
});

app.filter('isBidElapsed', function () {
    return function (x) {
        // console.log('obj',x);
          var pastdate=x.last_date+' '+x.last_time;
          var today = new Date();
          var date = today.getFullYear()+'-'+(("0" + (today.getMonth() + 1)).slice(-2))+'-'+(("0" + (today.getDate())).slice(-2));
          var time = today.getHours() + ":" + today.getMinutes();
          var dateTime = date+' '+time;
          if (new Date(dateTime) > new Date(pastdate)) {
             
             return true;
             
           }else{
             return false;
           }
    };
});

app.filter('getSlug',function() {
    return function(value) {
        let u = value.username.split(".").join("").toLowerCase().split(" ").join('-')+"-"+value.encryptedId;
        return u;
    }
});

app.filter('getEventSlug',function() {
    return function(value) {
        let u = value.split(".").join("").toLowerCase().split(" ").join('-');
        return u;
    }
});

app.filter('getChar',function() {
    return function(value) {
        return value.charAt(0);
    }
});
app.filter('limitWords',function() {
    return function(value) {
        return  value.substr(0, 200);
    }
});

app.filter('removeSpace',function(){
    return function(StringFormat){
    var str=StringFormat.replace(/\s/g, "-");
     return str;
    }
})