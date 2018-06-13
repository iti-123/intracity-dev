app.service('apiCommunityServices', function ($q, $http) {
    return {
        city: function (url) {
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: {'Authorization': 'Bearer ' + localStorage.getItem("access_token")}
                })
                .then(function (response) {
                    // console.log(response.data);
                    deferred.resolve(response.data);
                })
                .catch(function (e) {
                    handleAuthenticationError(e);
                    deferred.reject(e);
                });
            return deferred.promise;
        },
        getLocationByCity: function (url) {
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: {'Authorization': 'Bearer ' + localStorage.getItem("access_token")}
                })
                .then(function (response) {
                    // console.log(response.data);
                    deferred.resolve(response.data);
                })
                .catch(function (e) {
                    handleAuthenticationError(e);
                    deferred.reject(e);
                });
            return deferred.promise;
        },

        category: function (url) {
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: {'Authorization': 'Bearer ' + localStorage.getItem("access_token")}
                })
                .then(function (response) {
                    // console.log(response.data);
                    deferred.resolve(response.data);
                })
                .catch(function (e) {
                    handleAuthenticationError(e);
                    deferred.reject(e);
                });
            return deferred.promise;
        },
        articleList: function (url) {
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: {'Authorization': 'Bearer ' + localStorage.getItem("access_token")}
                })
                .then(function (response) {
                    // console.log(response.data);
                    deferred.resolve(response.data);
                })
                .catch(function (e) {
                    handleAuthenticationError(e);
                    deferred.reject(e);
                });
            return deferred.promise;
        },
         viewarticle: function (url) {
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: {'Authorization': 'Bearer ' + localStorage.getItem("access_token")}
                })
                .then(function (response) {
                    // console.log(response.data);
                    deferred.resolve(response.data);
                })
                .catch(function (e) {
                    handleAuthenticationError(e);
                    deferred.reject(e);
                });
            return deferred.promise;
        },
        addArticle: function(BASE_URL, data) {
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
                    deferred.reject(error);
                }
            });
            return deferred.promise;
        },
        postComment: function(BASE_URL, data) {
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
                    deferred.reject(error);
                }
            });
            return deferred.promise;
        },
        postCommentReply: function(BASE_URL, data) {
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
                    deferred.reject(error);
                }
            });
            return deferred.promise;
        },
        
        uploadDocument: function (messageObj) {
            var deferred = $q.defer();
            $http({
                method: 'POST',
                url: BASE_URL + 'uploadfiles/file',
                data: {'uploadFile': messageObj},
                crossDomain: true,
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                }
            })
                .then(function (response) {
                    deferred.resolve(response.data);
                })
                .catch(function (e) {
                    deferred.reject(e);
                });
            return deferred.promise;
        },
        postMethod: function(url,data) {
            var deferred = $q.defer();
            $('.loading').show();
            $http({
                method: 'POST',
                url: url,
                headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") },
                data: {data: data},
            })
            .then(function(response) {
                $('.loading').hide();
                deferred.resolve(response.data);
            })
            .catch(function(e) {
                handleAuthenticationError(e);
                $('.loading').hide();
                deferred.reject(e);
            });
            return deferred.promise;
        },
        addEventsRegisterUsers: function(BASE_URL, data) {
            var deferred = $q.defer();
            $('.loading').show();
            $http({
                method: 'POST',
                url: BASE_URL,
                headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") },
                data: data,
            })
            .then(function(response) {
                $('.loading').hide();
                deferred.resolve(response.data);
            })
            .catch(function(e) {
                handleAuthenticationError(e);
                $('.loading').hide();
                deferred.reject(e);
            });
            return deferred.promise;
        },eventDetails: function (url) {
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: {'Authorization': 'Bearer ' + localStorage.getItem("access_token")}
                })
                .then(function (response) {
                    // console.log(response.data);
                    deferred.resolve(response.data);
                })
                .catch(function (e) {
                    handleAuthenticationError(e);
                    deferred.reject(e);
                });
            return deferred.promise;
        },


    };

});
