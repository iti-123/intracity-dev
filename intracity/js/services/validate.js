app.service('validateService', function ($q, $http) {
    return {
        validateDigits: function (number, digits) {
            var re = new RegExp('^[0-9]{' + digits + '}$');
            var valid = false;
            if (re.test(number)) {
                valid = true;
            }
            return valid;
        },
        isValidRoute: function (route) {
            console.log("route::", route.length);
            if (route.length)
                return true;
            return false;
        },
        isValidDate: function(date) {
            var m = moment(date, 'YYYY-MM-DD');           
            return m.isValid();
        }
    };

});
