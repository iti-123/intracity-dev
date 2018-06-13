angular.module('lgApp')
    .factory('homeServices', function ($q, $http) {


        function loadShippingServiceJson() {

            var formattedString = "menu/" + Auth.getUserID();
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                type: "GET",
                processData: false,
                dataType: 'json',
                headers: {
                    'Authorization': 'Bearer ' + Auth.getAccessToken()
                },
                url: BASE_URL + formattedString,
                contentType: "application/json",
                success: function (data) {
                    console.log(data);
                    $('.loading').hide();
                    deferred.resolve(data);
                },
                error: function (error) {
                    $('.loading').hide();
                    alert(error.responseText);
                    deferred.reject(error);
                }

            });

            return deferred.promise;

        }

        return {
            loadShippingServiceJson: loadShippingServiceJson
        };

    });