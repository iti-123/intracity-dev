app.filter('stringFormat', function () {
    return function (x) {
        x = x.split('_').join(' ');
        return x;
    };
});

app.filter('arrayToBackSlashString', function () {
    return function (x) {
        var s = "";
        for (let i = 0; i < x.length; i++) {
            if (i > 0) {
                s += " ";
            }
            s += x[i];
        }
        return s;
    };
});

app.filter('arrayToString', function () {
    return function (x) {
        var s = "";
        for (let i = 0; i < x.length; i++) {
            if (i > 0) {
                s += ",";
            }
            s += x[i];
        }
        return s;
    };
});

// app.filter('languageList', function() {
//     return function(x) {
//         console.log(x);
//         var j = JSON.parse(eval('('+x+')'));
//         console.log(j);
//         var s = "";
//         for(let i in j){
//           console.log(i.language);
//           // if(s != ""){
//           //   s += ","
//           // }
//           // s += i.language;
//         }
//         return s;
//     };
// });
