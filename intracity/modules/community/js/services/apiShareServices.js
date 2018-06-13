app.service('apiShareServices', function ($q, $http) {
    return {
       insertPost: function(BASE_URL, data) {
            var deferred = $q.defer();
            $('.loading').show();

            $.ajax({
                url: BASE_URL,
                type: "POST",
                data: data,
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                    console.log(response);
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        }
        


    };

});
