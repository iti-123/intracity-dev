app.service('messageServices', function ($q, $http) {
    return {
        MessageType: function (url) {
            var deferred = $q.defer();
            $http
                .get(BASE_URL + 'messageType', {
                    headers: {'Authorization': 'Bearer ' + localStorage.getItem("access_token")}
                })
                .then(function (response) {
                    deferred.resolve(response.data);
                })
                .catch(function (e) {
                    deferred.reject(e);
                });
            return deferred.promise;
        },
        loadAllUsers: function (url) {
            $('.loaderGif').show();
            var deferred = $q.defer();
            $http
                .get(BASE_URL + 'getallusers', {
                    headers: {'Authorization': 'Bearer ' + localStorage.getItem("access_token")}
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
        ReplyMessage: function (messageObj, messageId) {
            $('.loaderGif').show();

            var deferred = $q.defer();
            console.log("messageObj", messageObj);
            $http({
                method: 'POST',
                url: BASE_URL + 'messages/' + messageId + '/reply',
                data: {messageObj},
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
        getMessage: function (getMessage) {
            var deferred = $q.defer();
            var formattedString = "messages/list/sender";
            $('.loaderGif').show();
            $.ajax({
                type: 'POST',
                url: BASE_URL + formattedString,
                data: {"requestData": getMessage},
                crossDomain: true,
                headers: {
                    'Authorization': 'Bearer ' + Auth.getAccessToken()
                },
                success: function (response) {
                    $('.loaderGif').hide();
                    deferred.resolve(response);

                },
                error: function (error) {
                    $('.loaderGif').hide();
                    console.log(error.responseText);
                    if (typeof(error.responseText) == "undefined") {
                        $("div.alert-danger span").html(error.responseText);
                        $("div.alert-danger").fadeIn(300);
                    }
                    deferred.reject(error);
                }
            });
            return deferred.promise;
        },
        getMessageDetailsById: function (url) {
            $('.loaderGif').show();
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: {'Authorization': 'Bearer ' + localStorage.getItem("access_token")}
                })
                .then(function (response) {
                    $('.loaderGif').hide();
                    // console.log(response.data);
                    deferred.resolve(response.data);
                })
                .catch(function (e) {
                    $('.loaderGif').hide();
                    deferred.reject(e);
                });
            return deferred.promise;
        },
        PusherService: function ($pusher, channel, event, callback) {
            var deferred = $q.defer();
            $('.loaderGif').show();
            var client = new Pusher(PUSHER_APP_KEY, PUSHER_OPTIONS);
            var pusher = $pusher(client);

            var channelObj = pusher.subscribe(channel);

            channelObj.bind(event, callback);

            var deferred = $q.defer();
            var formattedString = "broadcastMessage/" + channel + "/" + event;

            $.ajax({
                type: 'GET',
                url: BASE_URL + formattedString,
                crossDomain: true,
                headers: {
                    'Authorization': 'Bearer ' + Auth.getAccessToken()
                },
                success: function (response) {
                    $('.loaderGif').hide();
                    deferred.resolve(response);
                },
                error: function (error) {
                    $('.loaderGif').hide();
                    deferred.reject(error);
                }
            });
            return deferred.promise;

        }
    }
});