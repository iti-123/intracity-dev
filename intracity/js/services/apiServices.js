app.service('apiServices', function($q, $http) {
    return {
        city: function(url) {
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") }
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
        sellerDetailPage: function(url) {
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") }
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
        getLocationByCity: function(url) {
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") }
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
        vehiclesType: function(url) {
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") }
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
        getMethod: function(url) {
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") }
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
        postMethod: function(url,data) {
            var deferred = $q.defer();
            $('.loading').show();
            $http({
                method: 'POST',
                url: url,
                headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") },
                data: {data: data},
                success: function(msg)
                {
                    /*if($("table").has("sellerPostMasterTable")){
                        sellerPostMastertable.draw();
                    }*/
                    if($("#sellerPostMasterTable").length){
                        sellerPostMastertable.draw(); 
                    }else if($("#buyerPostMasterTable").length){
                        table.draw();
                    }
                    if($("#userNotificationTable").length){
                        userNotificationTable.draw();
                    }
                    $('#settings').scrollTop(0);
                    $(".settings_status_message").html(msg);
                    $('#settings_status_message_flash').fadeIn(1000).delay(100).fadeOut(3000);

                    /*$("#erroralertmodal .modal-body").html(msg);
                    $("#erroralertmodal").modal({
                        show: true
                    }).one('click','.ok-btn',function (e){
                        //location.reload();
                    });*/
                }
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
        sellerPostMethod: function(url,data) {
            var deferred = $q.defer();
            $('.loading').show();
            $http({
                method: 'POST',
                url: url,
                headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") },
                data: {data: data},
                success: function(msg)
                {
                    /*if($("table").has("sellerPostMasterTable")){
                        sellerPostMastertable.draw();
                    }*/
                    if($("#sellerPostMasterTable").length){
                        sellerPostMastertable.draw(); 
                    }else if($("#buyerPostMasterTable").length){
                        table.draw();
                    }
                    if($("#userNotificationTable").length){
                        userNotificationTable.draw();
                    }
                    $('#settings').scrollTop(0);
                    $(".settings_status_message").html(msg);
                    $('#settings_status_message_flash').fadeIn(1000).delay(100).fadeOut(3000);

                    /*$("#erroralertmodal .modal-body").html(msg);
                    $("#erroralertmodal").modal({
                        show: true
                    }).one('click','.ok-btn',function (e){
                        //location.reload();
                    });*/
                }
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
        getAllSellers: function(BASE_URL) {
            var formattedString = "getallseller";
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                type: "GET",
                processData: false,
                dataType: 'json',
                headers: {

                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                url: BASE_URL + formattedString,
                contentType: "application/json",
                success: function(data) {
                    $('.loading').hide();
                    deferred.resolve(data);
                },
                error: function(error) {
                    $('.loading').hide();
                    console.log(error);
                    handleAuthenticationError(error);
                    deferred.reject(error);
                }

            });

            return deferred.promise;
        },
        totalBuyerPost: function(url) {
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") }
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
        listBuyerPost: function(url) {
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") }
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
        sellerSearchByBuyer: function(BASE_URL, data) {
            var deferred = $q.defer();
            $.ajax({
                url: BASE_URL + 'buyer-search',
                type: "POST",
                data: data,
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
                    $('.loaderGif').hide();
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        /// buyer search
        buyerSearchResult: function(url, searchdata) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                type: 'POST',
                url: url,
                data: searchdata,
                crossDomain: true,
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
        searchFilter: function(url, filterData) {
            var deferred = $q.defer();
            $('.loading').show();
            $http({
                    method: 'POST',
                    url: url,
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") },
                    data: filterData,
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
        listBuyerAccordingFilter: function(url, city_id) {
            var deferred = $q.defer();
            $('.loading').show();
            $http
                .get(url + '/' + city_id, {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") }
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
        // Buyer Services
        sellerPostlist: function(BASE_URL, data) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "POST",
                data: { "type": data },
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },

        getallbuyer: function(BASE_URL) {

            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                type: "GET",
                processData: false,
                dataType: 'json',
                headers: {

                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                url: BASE_URL,
                contentType: "application/json",
                success: function(data) {
                    $('.loading').hide();
                    deferred.resolve(data);
                },
                error: function(error) {
                    $('.loading').hide();
                    console.log(error);
                    handleAuthenticationError(error);
                    deferred.reject(error);
                }

            });

            return deferred.promise;
        },
        getMessage: function(url, id) {
            var deferred = $q.defer();
            $http
                .get(url + '/' + id, {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") }
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
        bookNow: function(BASE_URL, data) {
            var deferred = $q.defer();
            $('.loading').show();
            var formattedString = 'intracity/carts';
            $.ajax({
                url: BASE_URL + formattedString,
                type: "POST",
                data: { "data": data },
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                    //console.log(response);
                    deferred.resolve(response);
                },
                error: function(err) {
                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        bookNowLeads: function(BASE_URL, data) {
            var deferred = $q.defer();
            $('.loading').show();
            var formattedString = 'intracity/leadsCarts';
            $.ajax({
                url: BASE_URL + formattedString,
                type: "POST",
                data: { "data": data },
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                    //console.log(response);
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
        BlueCollarbookNow: function(BASE_URL, data) {
            var deferred = $q.defer();
            $('.loading').show();
            var formattedString = 'intracity/carts';
            $.ajax({
                url: BASE_URL + formattedString,
                type: "POST",
                data: { "data": data },
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
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
        getCartItemById: function(BASE_URL) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "GET",
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
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
        updateCartItems: function(BASE_URL, data) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "POST",
                data: { "data": data },
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        getCartCount: function(BASE_URL) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL + 'get-cart-count',
                type: "GET",
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        getSellerById: function(BASE_URL) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "GET",
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        updateCartStatus: function(BASE_URL, cartId) {
            var deferred = $q.defer();
            $('.loading').show();

            $.ajax({
                url: BASE_URL,
                type: "POST",
                data: { "cartId": cartId },
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
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
        getCartItems: function(BASE_URL) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "GET",
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        order: function(BASE_URL) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "POST",
                crossDomain:true,
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        orderConform: function(BASE_URL,orderid) {

            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "POST",
                data: { "orderid": orderid },
                crossDomain:true,
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },


        deleteCartItems: function(BASE_URL,cartId,buyerId) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "POST",
                data: { "Id": cartId,"buyerId": buyerId },
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
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
        clearCartItems: function(BASE_URL,buyerId) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "POST",
                data: { "buyerId": buyerId },
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(error);
                }
            });
            return deferred.promise;
        },getSellerMessage: function(url, id) {
            var deferred = $q.defer();
            $http
                .get(url + '/' + id, {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") }
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
        sellerPostDetail: function(BASE_URL,id) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "get",
                data: { "id": id},
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        buyerPostDetail: function(BASE_URL,id) {

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
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(error);
                }
            });
            return deferred.promise;
        },
        postdiscount: function(BASE_URL,id) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "get",
                data: { "id": id},
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

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
        prediscount: function(BASE_URL,id) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "get",
                data: { "id": id},
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

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
        finaldiscount: function(BASE_URL,id) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "get",
                data: { "id": id},
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

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
        sellerPostDelete: function(BASE_URL,id) {

            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "POST",
                data: { "id": id},
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
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
        productCategory: function(url) {
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") }
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
        BuyerPostDelete: function(BASE_URL,id) {

            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "POST",
                data: { "id": id},
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");
                   handleAuthenticationError(err);
                   deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        orderDetail: function(BASE_URL,orderid) {

            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "get",
                crossDomain: true,
                data: { "orderid": orderid},
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

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
        orderDetails: function(URL,orderParams) {
            
            var deferred = $q.defer();
            $('.loading').show();            
            $http({
                method: 'POST',
                url: URL,
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: orderParams,
            }).then(function (response) {
                //console.log(httpResponse.data.data);
                deferred.resolve(response);
            }, function (response) {
                deferred.reject(response);
            });
            return deferred.promise;
        },
        hpsellerSearchByBuyer: function(BASE_URL, data) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL+'hyperlocal/hp-buyer-search',
                type: "POST",
                crossDomain: true,
                data: data,
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                 contentType: "application/json",
                success: function(response) {
                   
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
         hpbuyerSearchSeller: function(BASE_URL, data) {

            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL+'hyperlocal/hp-seller-buyer-search',
                type: "POST",
                crossDomain: true,
                data: data,
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                 contentType: "application/json",
                success: function(response) {
                   
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },

        hyperpostcount: function(BASE_URL) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "GET",
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },

        validateDigits: function(number, digits){
            var re = new RegExp('^[0-9]{'+digits+'}$');
            var valid = false;
            if(re.test(number)){
            valid = true;
            }
            return valid;
        },
        isValidRoute: function(route) {
            if (!route.length)
                return true;
            return false;
            //console.log("route::",route);
        },
        hyperbuyerPostDetail: function(BASE_URL,id) {

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
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(error);
                }
            });
            return deferred.promise;
        },
        hyperbuyerpostquote: function(BASE_URL,filterData) {
           
            var deferred = $q.defer();
          
            $.ajax({
                url: BASE_URL,
                type: "post",
                data: filterData,
                crossDomain:true,
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

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
        category: function(BASE_URL,id) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "Get",
                data: { "id": id},
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
                    deferred.resolve(response);
                },
                error: function(error) {
                    handleAuthenticationError(error);
                    deferred.reject(error);
                }
            });
            return deferred.promise;
        },
        getMessageNotification: function(BASE_URL) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "GET",
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
        prepaidOrderData: function(BASE_URL,orderid) {
           
            var deferred = $q.defer();
            $.ajax({
                url: BASE_URL,
                type: "POST",
                 data: { "id": orderid},
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {
                   
                    $('.loaderGif').hide();
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");
                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
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
        Termcontract: function (url,Obj) {
            $('.loaderGif').show();
            var deferred = $q.defer();
            $http({
                method: 'POST',
                url: url,
                data: Obj,
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
        Negotiation: function (url,Obj) {
            $('.loaderGif').show();
            var deferred = $q.defer();
            $http({
                method: 'POST',
                url: url,
                data: Obj,
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
        cancelcontract: function (url,Obj) {
            $('.loaderGif').show();
            var deferred = $q.defer();
            $http({
                method: 'POST',
                url: url,
                data: Obj,
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
        sellerfinalQuote: function (url,Obj) {
            $('.loaderGif').show();
            var deferred = $q.defer();
            $http({
                method: 'POST',
                url: url,
                data: Obj,
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
        Documentupload: function(BASE_URL, fd) {
            var formattedString = 'uploadfiles';
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({

                type: 'POST',
                url: BASE_URL + formattedString,
                cache: false,
                contentType: false,
                processData: false,
                data: fd,
                crossDomain: true,
                headers: {

                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },

                success: function(data) {

                    console.log(data);

                    deferred.resolve(data);
                },

                error: function(error) {

                    deferred.reject(error);
                }

            });

            return deferred.promise;
        },
        updateMessage: function (Id) {
            $('.loaderGif').show();
            var deferred = $q.defer();
            $http({
                method: 'POST',
                url: BASE_URL + 'hyperlocal/updateMessage/' + Id,
                data: Id,
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

        acceptPlaceTruckGSA: function (url,value) {
            $('.loaderGif').show();
            var deferred = $q.defer();
                $http({
                    method: 'POST',
                    url: url,
                    data: { data: value },
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
        confirmPlaceTruck: function (url,value) {
            $('.loaderGif').show();
            var deferred = $q.defer();
                $http({
                    method: 'POST',
                    url: url,
                    data: { data: value },
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
        getMessageCount: function(BASE_URL) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "GET",

                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

                   
                    $('.loaderGif').hide();
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");

                    deferred.resolve(response);
                },
                error: function(err) {

                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        getCartValue: function(url) {
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") }
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
        },getTotalLeadCount: function(url) {
            var deferred = $q.defer();
            $http
                .get(url, {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem("access_token") }
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
        updateUserSettings: function(BASE_URL) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "GET",

                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

                   
                    $('.loaderGif').hide();
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");

                    deferred.resolve(response);
                },
                error: function(err) {

                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },    
        // }, totalBuyerPostCount: function(BASE_URL) {
        //     var deferred = $q.defer();
        //     $('.loading').show();
        //     $.ajax({
        //         url: BASE_URL,
        //         type: "GET",

        //         headers: {
        //             'Authorization': 'Bearer ' + localStorage.getItem("access_token")
        //         },
        //         success: function(response) {

                   
        //             $('.loaderGif').hide();
        //             deferred.resolve(response);
        //         },
        //         error: function(err) {
        //             console.log("error");

        //             deferred.resolve(response);
        //         },
        //         error: function(err) {

        //             handleAuthenticationError(err);
        //             deferred.reject(err);
        //         }
        //     });
        //     return deferred.promise;
        // },
        totalCount: function(BASE_URL,serviceId) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "GET",
                data: { data:serviceId },
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

                   
                    $('.loaderGif').hide();
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");

                    deferred.resolve(response);
                },
                error: function(err) {

                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        totalOrderCount: function(BASE_URL,serviceId) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "GET",
                data: { data:serviceId },
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

                   
                    $('.loaderGif').hide();
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");

                    deferred.resolve(response);
                },
                error: function(err) {

                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        leadDetails: function(BASE_URL) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "GET",

                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

                   
                    $('.loaderGif').hide();
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");

                    deferred.resolve(response);
                },
                error: function(err) {

                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        getLeftSideMenu: function(BASE_URL) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "GET",

                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

                   
                    $('.loaderGif').hide();
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");

                    deferred.resolve(response);
                },
                error: function(err) {

                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        getPostPopupMenu: function(BASE_URL) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "GET",

                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

                   
                    $('.loaderGif').hide();
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");

                    deferred.resolve(response);
                },
                error: function(err) {

                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        getTotalLeadCountHp: function(BASE_URL) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "GET",

                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

                   
                    $('.loaderGif').hide();
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");

                    deferred.resolve(response);
                },
                error: function(err) {

                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        getBuyerTotalLeadCount: function(BASE_URL) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "GET",

                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

                   
                    $('.loaderGif').hide();
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");

                    deferred.resolve(response);
                },
                error: function(err) {

                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
         postMasterCounts: function(BASE_URL) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "GET",

                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

                   
                    $('.loaderGif').hide();
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");

                    deferred.resolve(response);
                },
                error: function(err) {

                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        orderMasterCounts: function(BASE_URL) {
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({
                url: BASE_URL,
                type: "GET",

                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                success: function(response) {

                   
                    $('.loaderGif').hide();
                    deferred.resolve(response);
                },
                error: function(err) {
                    console.log("error");

                    deferred.resolve(response);
                },
                error: function(err) {

                    handleAuthenticationError(err);
                    deferred.reject(err);
                }
            });
            return deferred.promise;
        },
        errorHandeler: function(error) {
            console.log("Error Handeler",error);
            handleAuthenticationError(error);
        },
        uploadFile: function(BASE_URL,fd) {            
            var deferred = $q.defer();
            $('.loading').show();
            $.ajax({                   
            type: 'POST',
            url: BASE_URL,
            cache: false,
            contentType: false,
            processData: false,
            data : fd,
            crossDomain : true,
            headers : {                
            'Authorization': 'Bearer ' + Auth.getAccessToken()
            },                   
            success: function(data){
            
                    console.log(data);
                    deferred.resolve(data);
            },                   
            error: function(error){
                handleAuthenticationError(error);
                deferred.reject(error);		
            }
                
            });
         
         return deferred.promise;
     },
     getTodayDate: function() {
        var deferred = $q.defer();
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        
        var yyyy = today.getFullYear();
        if(dd<10){
            dd='0'+dd;
        } 
        if(mm<10){
            mm='0'+mm;
        } 
        deferred.resolve(yyyy+'-'+mm+'-'+dd);
        return deferred.promise;
     }
    };

});
