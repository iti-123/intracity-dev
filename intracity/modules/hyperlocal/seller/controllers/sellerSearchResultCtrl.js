app.controller('BuyerSearchResultCtrl', ['$scope', '$http', 'config', 'trackings', 'apiServices', '$compile', '$state', '$stateParams', '$dataExchanger', function ($scope, $http, config, trackings, apiServices, $compile, $state, $stateParams, $dataExchanger) {
    var transitHour = [];
    var serverUrl = config.serverUrl;
    $scope.filterdata = [];
    $scope.servicetype = SERVICE_TYPE;
    $scope.materialtype = HYPERLOCAL_MATERIAL_TYPE;
    $scope.weight = HYPERLOCAL_WEIGHT;

    var url = serverUrl + 'hyperlocal/product-category';
    $scope.getProductCategory = function (url) {
        apiServices.category(url).then(function (response) {
            $scope.categories = response.data;
            //console.log('Product Category:', $scope.categories);
        });
    }
    $scope.getProductCategory(url);

    /**
     *      Check if data exist or not
     */

    if (angular.equals($scope.buyerSearch, {})) {
        $state.go("hp-seller-search");
    }

    /**********************************************************
     *
     *        Get city
     *
     *************************************************************/
    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
        });
    };
    $scope.getCity(url);
    /****************************************************************
     *
     *           get getVehiletype
     *
     *****************************************************************/

    var url = serverUrl + 'locations/getVehiletype';
    $scope.getVehiclesType = function (url) {
        apiServices.vehiclesType(url).then(function (response) {
            $scope.vehicles = response;
        });
    };
    $scope.getVehiclesType(url);

    /*------------------------Get All Sellers--------------------------------*/
    var buyerurl = serverUrl + 'getallbuyer';
    apiServices.getallbuyer(buyerurl).then(function (response) {
        //console.log('buyerrrrrrrrrrrrr',response);
        $scope.sellerList = response.payload;
    });

    /*-----------------------------------------------------------------------------------------*/

    $scope.buyerSearch = $dataExchanger.request.data;


    apiServices.hpbuyerSearchSeller(serverUrl, JSON.stringify($scope.buyerSearch)).then(function (response) {
        $scope.searchResult = response.payload;
        console.log("$scope.searchResult::", response.payload);
    });


    console.log("DataExchanger::", $dataExchanger.request.data);

    // Price Slider

    $scope.value = 150;

    $scope.slider = {
        min: 100,
        max: 180,
        options: {
            floor: 0,
            ceil: 450
        }
    };
    /*********************data filter base on vechile ******************/

    $scope.NewVehicleType = [];
    $scope.NewVehicleTypeFilter = function (item) {

        if (item != null) {
            if ($scope.NewVehicleType.length == 0)
                return item;
            else {
                return $scope.NewVehicleType.indexOf(item.vehicle) !== -1;
            }
        }

    };
    $scope.selectNewVehicleType = function (operator, obj) {


        if ($scope.filterdata.indexOf(obj) !== -1) {

            $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1)
        }
        else {
            $scope.filterdata.push(obj);

        }

        var idx = $scope.NewVehicleType.indexOf(operator);
        if (idx > -1)
            $scope.NewVehicleType.splice(idx, 1);
        else

            $scope.NewVehicleType.push(operator);
        if ($scope != null)
            $scope.NewVehicleType = $scope.NewVehicleType;

    };
    /*********************data filter base on tracking ******************/

    $scope.Newtrackings = [];
    $scope.NewtrackingFilter = function (item) {

        if (item != null) {
            if ($scope.Newtrackings.length == 0)
                return item;
            else {
                tracking = item.tracking;
                tracking = tracking.toString();
                return $scope.Newtrackings.indexOf(tracking) !== -1;
            }
        }

    };
    $scope.selecttrackingType = function (operator, obj) {
        console.log(operator);
        if ($scope.filterdata.indexOf(obj) !== -1) {
            $scope.filterdata.splice($scope.filterdata.indexOf(obj, 1))
        }
        else {
            $scope.filterdata.push(obj);
        }

        operator = operator.toString();
        var idx = $scope.Newtrackings.indexOf(operator);
        if (idx > -1)
            $scope.Newtrackings.splice(idx, 1);
        else
            $scope.Newtrackings.push(operator);

        if ($scope != null)
            $scope.Newtrackings = $scope.Newtrackings;

    };

    /*********************data filter base on transit ******************/

    $scope.Newtransit = [];
    $scope.NewtrnsitFilter = function (item) {
        //console.log('transit',item.transit_hour);
        if (item != null) {
            if ($scope.Newtransit.length == 0)
                return item;
            else {

                transit = item.transit_hour;

                return $scope.Newtransit.indexOf(transit) !== -1;
            }
        }

    };
    $scope.transitSelect = function (operator, obj) {

        if ($scope.filterdata.indexOf(obj) !== -1) {
            $scope.filterdata.splice($scope.filterdata.indexOf(obj, 1))
        }
        else {
            $scope.filterdata.push(obj);
        }

        //operator = operator.toString();
        var idx = $scope.Newtransit.indexOf(operator);
        if (idx > -1)
            $scope.Newtransit.splice(idx, 1);
        else
            $scope.Newtransit.push(operator);

        if ($scope != null)
            $scope.Newtransit = $scope.Newtransit;

    };
    /************************filter base on seller**************/

    $scope.Newseller = [];
    $scope.NewSellerFilter = function (item) {

        if (item != null) {
            if ($scope.Newseller.length == 0)
                return item;
            else {


                return $scope.Newseller.indexOf(item.seller) !== -1;
            }
        }

    };

    $scope.selectSeller = function (operator, obj) {


        if ($scope.filterdata.indexOf(obj) !== -1) {

            $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1)
        }
        else {
            $scope.filterdata.push(obj);
            //$scope.filterdata.push({ 'id': obj.id,'filtername': obj.username,type:"seller",filterid:obj.id+obj.username });

        }

        var idx = $scope.Newseller.indexOf(operator);
        if (idx > -1)
            $scope.Newseller.splice(idx, 1);
        //console.log($scope.filterdata);
        //$scope.filterdata.splice(obj,1);
        else

            $scope.Newseller.push(operator);

        if ($scope != null)
            $scope.Newseller = $scope.Newseller;

    };


    $scope.bookNow = function (quote, index) {
        var confirm1 = confirm("Do you want to book");

        //console.log("Buyer Quote::", confirm1);
        if (confirm1) {
            var booknowSerachObj = $dataExchanger.request;
            $scope.bookPostObj = {
                initialDetails: {
                    serviceId: $dataExchanger.request.serviceId,
                    serviceType: '',
                    sellerId: quote.seller_id,
                    buyerId: Auth.getUserID(),
                    postType: "BP",
                    sellerQuoteId: quote.seller_post_id, //need to discuss
                    searchData: booknowSerachObj,
                    carrierIndex: index,
                    quote: quote
                }
            };
            //console.log("$scope.bookPostObj::", JSON.stringify($scope.bookPostObj));
            apiServices.bookNow(serverUrl, JSON.stringify($scope.bookPostObj)).then(function (response) {
                if (response.isSuccessful && response.payload.id) {
                    $scope.cartId = response.payload.enc_id;
                    $state.go('order-booknow', {cartId: $scope.cartId});
                }
            })
        }
    };


    // Lets start sending Message
    $scope.sendMessageModal = function (value) {
        console.log(value);
        $("#from_name").val("From: " + Auth.getUserName());
        $("#to_name").val("To: " + value.seller);
        $("#message_subject").val("Ref:- POST:INTRACITY/2017/" + value.seller_post_id);
        $("#quoteObject").val(JSON.stringify(value));

    };

    $scope.MessageObj = {
        "message_from": "",
        "message_to": "",
        "message_subject": "",
        "message_body": "",
        "buyer_quote": "",
        "seller_post": "",
        "buyer_quote_item": "",
        "buyer_quote_item_leads": "",
        "buyer_quote_item_seller": "",
        "buyer_quote_item_seller_leads": "",
        "order_id_for_model": "",
        "is_term": "",
        "buyer_quote_item_for_search": "",
        "buyer_quote_item_for_search_seller": "",
        "order_id_for_model_seller": "",
        "message_id": "",
        "lkp_service_id": ""

    };
    $scope.MessageReplyObj = {
        "message_from": "",
        "message_to": "",
        "message_subject": "",
        "message_body": "",
        "buyer_quote": "",
        "seller_post": "",
        "buyer_quote_item": "",
        "buyer_quote_item_leads": "",
        "buyer_quote_item_seller": "",
        "buyer_quote_item_seller_leads": "",
        "order_id_for_model": "",
        "is_term": "",
        "buyer_quote_item_for_search": "",
        "buyer_quote_item_for_search_seller": "",
        "order_id_for_model_seller": "",
        "message_id": "",
        "files": ""

    };
    $scope.MessageSearchObj = {
        "message_services": "22",
        "message_type": "",
        "message_keywords": "",
        "from_date": "",
        "to_date": ""
    };
    $scope.message_attachment = '';
    $scope.attachedFile = {
        "file": "",
        "userMessageId": ""
    };

    $scope.sendMessage = function (attachedFile) {
        console.log("attachedFile", typeof attachedFile);
        var myForm = document.querySelector('#MessageData');
        var formData = new FormData(myForm);

        // Start Upload File
        var postDetails = JSON.parse(formData.get('quoteObject'));

        $scope.MessageObj.message_from = Auth.getUserID();
        $scope.MessageObj.message_to = postDetails.seller_id;
        $scope.MessageObj.message_subject = formData.get('message_subject');
        $scope.MessageObj.message_body = formData.get('message_body');

        $scope.MessageObj.lkp_service_id = $dataExchanger.request.serviceId;
        $scope.MessageObj.buyer_quote_item = postDetails.id;
        $scope.MessageObj.buyer_quote = postDetails.seller_post_id;
        console.log("$scope.MessageObj.message_body::", $scope.MessageObj.message_body);
        $("#message_body").css("border-color", "#e2e2e2");
        if ($scope.MessageObj.message_body == '') {
            $("#message_body").css("border-color", "red");
            return false;
        }
        $scope.status = false;
        if (attachedFile != '') {
            $scope.attachedFile.file = attachedFile;
            messageServices.PostMessage($scope.MessageObj).then(function (response) {

                if (response.isSuccessful == true) {
                    // $scope.files = {documentId: response.payload.id, documentName: response.payload.file_name};
                    $scope.attachedFile.userMessageId = response.payload.id;
                    console.log("HELLO WITH DOCUMENT", $scope.MessageObj.userMessageId);
                    console.log("NEW MESSAGE OBJECT ", $scope.MessageObj);
                    messageServices.uploadDocument($scope.attachedFile).then(function (response) {
                        console.log(response.payload);
                        $scope.messages = response.payload;
                        $scope.status = true;
                        $("#status").html("Message sent Successfully").addClass("text-success");
                        setTimeout(function () {
                            $("#messageModal").modal('hide');
                        }, 1000);

                        $state.reload();

                    })
                }
            });
        } else {
            console.log("$scope.MessageObj ::", $scope.MessageObj);
            messageServices.PostMessage($scope.MessageObj).then(function (response) {
                $scope.MessageObj.docId = "";
                console.log(response.payload);

                $scope.messages = response.payload;
                $("#status").html("Message sent Successfully").addClass("text-success");
                setTimeout(function () {
                    $("#messageModal").modal('hide');
                }, 1000);
            });
        }
    };

    $scope.modifySearch = function (data) {
        $scope.timeslot = TIME_SLOT;
        $scope.data = angular.copy(data);
        // $scope.data.timeSlot = data.timeSlot.split(' ')[0];
        // $scope.data.period = data.timeSlot.split(' ')[1];
        console.log("modifySearch::", data);
        console.log("modifySearch::", $scope.data.timeSlot);
        $("#myModal").modal("show");
    };

    $scope.searchModified = function (data) {
        $dataExchanger.request.data = data;
        $dataExchanger.request.data.timeSlot = data.timeSlot + ' ' + data.period;
        console.log($dataExchanger.request.data);

        $("#myModal").modal("hide");
        setTimeout(function () {
            $state.reload();
        }, 1000);

    };

    $scope.onSelect = function (data) {
        var city_id;
        console.log('location', data);
        $scope.data.fromLocation = '';
        $scope.data.toLocation = '';
        var city_id = parseInt(data.id);
        if (typeof(city_id) != NaN || city_id != '') {
            url = serverUrl + 'locations/getlocality/' + city_id;
            apiServices.getLocationByCity(url).then(function (response) {
                $scope.locations = response;
            });
        }
    }


}]);
