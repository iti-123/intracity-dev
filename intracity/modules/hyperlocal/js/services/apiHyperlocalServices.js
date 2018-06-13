app.service('apiHyperlocalServices', function ($q, $http) {
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
        getMethod: function (url) {
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

        hpCreatePost: function (url, data) {
            var deferred = $q.defer();
            $http({
                url: url,
                method: 'POST',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: data,
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
        hpAddDiscount: function (data) {
            var deferred = $q.defer();
            $http
                .get(data, {
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
        }, sellerPost: function (data) {
            var deferred = $q.defer();
            $http
                .get(data, {
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
        sellerCounts: function (data) {
            var deferred = $q.defer();
            $http
                .get(data, {
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
        sellerListAcdngFilter: function (BASE_URL, data) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "Get",
                data: {"type": data},
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function (response) {
                    console.log(response);
                    deferred.resolve(response);
                },
                error: function (error) {
                    //console.log("error");
                    handleAuthenticationError(error);
                    deferred.reject(error);
                }
            });
            return deferred.promise;
        },
        hpSellerSearchBuyer: function (url, data) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: url,
                type: "POST",
                data: data,
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                contentType: "application/json",
                success: function (response) {
                    //console.log(response);
                    deferred.resolve(response);
                },
                error: function (error) {
                    //console.log("error");
                    handleAuthenticationError(error);
                    deferred.reject(error);
                }
            });
            //console.log('search resultsaa::', deferred.promise);
            return deferred.promise;
        },
        sellerPostDetail: function(BASE_URL,id) {

            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "get",
                data: { "id": id},
                crossDomain:true,
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

                    deferred.resolve(response);
                },
                error: function(err) {
                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        InboundFilter: function(url, type) {
            var deferred = $q.defer();
             $http({
                url: url,
                method: 'POST',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: type,
            })
                .then(function(response) {
                    // console.log(response.data);
                    deferred.resolve(response.data);
                })
                .catch(function(e) {
                    handleAuthenticationError(e);
                    deferred.reject(e);
                });
            return deferred.promise;
        },
        BuyerInboundFilter: function(url, type) {
            var deferred = $q.defer();
                 $http({
                url: url,
                method: 'POST',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: type,
            })
                .then(function(response) {
                    // console.log(response.data);
                    deferred.resolve(response.data);
                })
                .catch(function(e) {
                    deferred.reject(e);
                });
            return deferred.promise;
        },

        BuyerInboundDetails: function(url, str) {
            var deferred = $q.defer();
                 $http({
                url: url,
                method: 'POST',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: str,
            })
                .then(function(response) {
                   
                    deferred.resolve(response.data);
                })
                .catch(function(e) {
                    deferred.reject(e);
                });
            return deferred.promise;
        },
        PostMessage: function (messageObj) {
            $('.loaderGif').show();
            var deferred = $q.defer();
            $http({
                method: 'POST',
                url: BASE_URL + 'messages/send',
                data: messageObj,
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                }
            })
                .then(function (response) {
                    $('.loaderGif').hide();
                    deferred.resolve(response.data);
                })
                .catch(function (e) {
                    $('.loaderGif').hide();
                    deferred.reject(e);
                });
            return deferred.promise;
        },
        uploadDocument: function (messageObj) {
            var deferred = $q.defer();
            $http({
                method: 'POST',
                url: BASE_URL + 'hpUploadfiles/file',
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
        SellerInboundDetails: function(url, str) {
            var deferred = $q.defer();
                 $http({
                url: url,
                method: 'POST',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: str,
            })
                .then(function(response) {
                   
                    deferred.resolve(response.data);
                })
                .catch(function(e) {
                    deferred.reject(e);
                });
            return deferred.promise;
        },



    };

});
