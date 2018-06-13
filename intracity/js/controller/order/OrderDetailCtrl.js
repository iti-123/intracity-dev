app.controller('OrderDetailCtrl', ['$scope', '$http', 'config', 'consignment', 'apiServices', '$state', 'trackings', '$dataExchanger', function ($scope, $http, config, consignment, apiServices, $state, trackings, $dataExchanger) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.trackings = trackings.type;

    $scope._HYPERLOCAL_ = _HYPERLOCAL_;
    $scope._INTRACITY_ = _INTRACITY_;
    $scope._BLUECOLLAR_ = _BLUECOLLAR_;
    
    $scope.post_type = POST_TYPE;

    $scope.storagePath = STORAGE_PATH;

    $scope.setBreadCrumb = ($dataExchanger.request.serviceName).toLowerCase();

    /**********************
    * 
    * prefilled filter section   
    * 
    */

    //  Get city of intracity

    apiServices.city(serverUrl + 'locations/getCity').then(function (response) {
        $scope.cities = response;
    });


    apiServices.vehiclesType(serverUrl + 'locations/getVehiletype').then(function (response) {
        $scope.vehicles = response;
    });

    /**
     * Get order number
     */
    apiServices.getMethod(serverUrl + 'getOrderNumber/' + $dataExchanger.request.serviceId).then(function (response) {
        $scope.orderNumber = response.payload.orderNo;
        $scope.dispatchDate = response.payload.dispatchDate;
    });

    /*** Get All Seller List ***/
    apiServices.getAllSellers(serverUrl).then(function (response) {
        $scope.sellerList = response;
    });
    /*** Get All Seller List ***/

    $scope.data = [
        {
            city: {
                id: '',
                name: ''
            }
        }
    ];

    $scope.getDate = function () {
        var date = new Date();
        $scope.FromDate = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
        return $scope.FromDate;
    }


    $scope.onSelect = function (data) {
        console.log("City Id::", parseInt(data.id));
        var city_id = parseInt(data.id);
        if (typeof (city_id) != NaN || city_id != '') {
            $scope.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id);
            // $scope.getListBuyerAccordingFilter(url, city_id);
        }
    }

    $scope.getLocationByCity = function (url) {
        apiServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            console.log('Locations:', $scope.locations);
        });
    }

    /**
     * display order details 
     * 
     */
    $scope.filterData = {
        fromLocation: [],
        toLocation: [],
        postType: [],
        vehicleType: [],
        orderDate: [],
        orderNumber: [],
        sellerType: [],
        orderId: $state.params.id,
        serviceType: $dataExchanger.request.serviceId
    };

    $scope.showIntraHyper = false;
    $scope.showBlueCollar = false;
    if ($scope.filterData.serviceType == _INTRACITY_ || $scope.filterData.serviceType == _HYPERLOCAL_) {
        $scope.showIntraHyper = true;
    } else if ($scope.filterData.serviceType == _BLUECOLLAR_) {
        $scope.showBlueCollar = true;
    }


    $scope.addFilterData = function (arr, value) {
        console.log(arr);
        var index = arr.indexOf(value);
        if (index == -1) {
            arr.push(value);
        } else {
            arr.splice(index, 1);
        }

        
    };
    $scope.section = 'indent';
    $scope.$watch('filterData', function (newValue, oldValue) {
        $('#loaderGif').show();
        $http({
            url: serverUrl + 'orderDetails',
            method: 'POST',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: $scope.filterData,
        }).then(function success(response) {
            if (response.data.isSuccessfull) {
                $scope.orderlists = response.data.payload;
                $scope.order = response.data.payload[0];

                $scope.countIndent = [];

                $scope.countStatus = [];
                $scope.billingStatus = [];


                for (let key in $scope.orderlists) {
                    if($scope.orderlists[key].consignmentPickupDetails){
                        $scope.orderlists[key].consignmentPickupDetails = JSON.parse($scope.orderlists[key].consignmentPickupDetails);
                    }
                    
                    if($scope.orderlists[key].transitDetail){
                        $scope.orderlists[key].transitDetails = JSON.parse($scope.orderlists[key].transitDetail);
                    }

                    if($scope.orderlists[key].deliveryDetail){
                        $scope.orderlists[key].deliveryDetail = JSON.parse($scope.orderlists[key].deliveryDetail);
                    }

                    if (parseInt($scope.orderlists[key].orderItemStatus) == 1) {
                        $scope.section = 'indent';
                        $scope.countIndent.push($scope.orderlists[key].itemId);
                    } else if (parseInt($scope.orderlists[key].orderItemStatus) >= 2 && parseInt($scope.orderlists[key].orderItemStatus) <= 4) {
                        $scope.section = 'status';
                        $scope.countStatus.push($scope.orderlists[key].itemId);
                    } else if (parseInt($scope.orderlists[key].orderItemStatus) >= 5 && parseInt($scope.orderlists[key].orderItemStatus) <= 6) {
                        $scope.billingStatus.push($scope.orderlists[key].itemId);
                        $scope.section = 'billing';
                    }

                    console.log("$scope.orderlists", $scope.orderlists[key]);
                }

                console.log("$scope.countIndent", $scope.countIndent.length + '=' + $scope.countStatus.length);
                $('#loaderGif').hide();
            }
        }).catch(function (error) {
            apiServices.errorHandeler(error);
            $('#loaderGif').hide();
        });
    }, true);


    


    $scope.acceptPlaceTruckGSA = function (value, index) {
        var isChecked = document.getElementById("gsa-" + index).checked;
        console.log(isChecked);

        if (isChecked) {
            $("#routeDetail").val(JSON.stringify(value));
            $scope.populateBookNowModel(value);
            $("#booknow-popup").modal("show");
        }
       // console.log("GSA Values::", value);
    }
    $scope.addVehicle = '';
    $scope.placeTruck = function (value, index) {
        if(value.truckAttribute){
           $scope.orderlists[index].multipleVehicle = JSON.parse(value.truckAttribute);
        }
        
        $(".displayMultipleVehicle-" + index).toggle();
        $(".addVehicle-" + index).toggle();
    }

    $scope.placeIndent = function (quote, index) {
       quote.from_date = quote.valid_from;
       quote.to_date = quote.valid_to;
       quote.posted_by = quote.seller_id;
       quote.vendor = quote.seller_name;
       quote.orderId = quote.id;
       quote.id = quote.routeId;
       quote.isIndent = quote.is_indent;
       console.log('Order', quote);
        var location =  [{
            "fromLocation": {
                "locality_name": quote.from_location,
            },
            "tolocation": {
                "locality_name": quote.to_location,
            }
        }];
        
        $scope.searchData = {data:{location}};
        console.log('Search Data',$scope.searchData);
        var booknowSerachObj = $dataExchanger.request;
        $scope.bookPostObj = {
            initialDetails: {
                serviceType: '',
                sellerId: quote.seller_id,
                serviceId: _HYPERLOCAL_,
                buyerId: Auth.getUserID(),
                postType: "BP",
                sellerQuoteId: quote.id, //need to discuss
                searchData: $scope.searchData ,
                carrierIndex: index,
                quote: quote
            }
        };
        console.log("$scope.bookPostObj::", JSON.stringify($scope.bookPostObj));
        apiServices.bookNow(serverUrl, JSON.stringify($scope.bookPostObj)).then(function (response) {
            if (response.isCartValue) {
                    $('#routeAlreadyExistsPopup').modal({ 'show': true });
                   //alert('This route is already booked.');
                } else if (response.isSuccessful && response.payload.id) {
                    $scope.cartId = response.payload.enc_id;
                    $state.go('order-booknow', {serviceId: _HYPERLOCAL_, cartId: $scope.cartId});
                }
            
        })
    };

    $scope.getTodayDate = function() {
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
        return  (yyyy+'-'+mm+'-'+dd);
    }
    
    $scope.placeIndentIntracity = function (quote, index) {
        console.log('Order', quote);
       quote.from_date = quote.valid_from;
       quote.to_date = quote.valid_to;
       quote.posted_by = quote.seller_id;
       quote.vendor = quote.seller_name;
       quote.orderId = quote.id;
       quote.id = quote.routeId;
       quote.isIndent = quote.is_indent;

       
       console.log('Order', quote);
        var location =  {
            "fromLocation": {
                "locality_name": quote.from_location,
            },
            "toLocation": {
                "locality_name": quote.to_location,
            },
            "dispatchDate":$scope.getTodayDate(),   
                     
        };
        
        
        $scope.searchData = {
            data:location,
            "serviceId": quote.lkp_service_id
        };
        console.log('Search Data',$scope.searchData);
        var booknowSerachObj = $dataExchanger.request;
        $scope.bookPostObj = {
            initialDetails: {
                serviceType: '',
                sellerId: quote.seller_id,
                serviceId: _INTRACITY_,
                buyerId: Auth.getUserID(),
                postType: "BP",
                sellerQuoteId: quote.id, //need to discuss
                searchData: $scope.searchData ,
                carrierIndex: index,
                quote: quote
            }
        };
        console.log("$scope.bookPostObj::", JSON.stringify($scope.bookPostObj));
        apiServices.bookNow(serverUrl, JSON.stringify($scope.bookPostObj)).then(function (response) {
            if (response.isCartValue) {
                    $('#routeAlreadyExistsPopup').modal({ 'show': true });
                   //alert('This route is already booked.');
                } else if (response.isSuccessful && response.payload.id) {
                    $scope.cartId = response.payload.enc_id;
                    $state.go('order-booknow', {serviceId: _INTRACITY_, cartId: $scope.cartId});
                }
            
        })
    };

    $scope.showHideDetail = function (value, index) {
        if(value.lkp_service_id == _BLUECOLLAR_) {
            // $(".showBlueCollerDetail-"+index).toggle();
        } else {
            $scope.placeTruck(value, index);
        }    

    }
    $scope.detailhide  = function(index)
    { 
        $scope.orderlists[index].isActive = !$scope.orderlists[index].isActive;
    }

    //************* Place Truck Form validation *************//
    $scope.PlaceTruckValidation = function (order) {


        isValidated = true;
        var driverMobileNo = order.driverMobileNo;
        var vehicleNumber = order.vehicleNumber;
        var driverName = order.driverName
        if (driverMobileNo == undefined) {
            isValidated = false;
            order.driverMob_validation = true;
        }
        else {
            order.driverMob_validation = false;
        }
        if (driverName == undefined) {
            isValidated = false;
            order.driverName_validation = true;
        }
        else {
            order.driverName_validation = false;
        }
        if (vehicleNumber == undefined) {
            isValidated = false;
            order.vehicleNumber_validation = true;
        }
        else {
            order.vehicleNumber_validation = false;
        }

        return isValidated;
    }
    //************* Place Truck Form validation *************//


    $scope.confirmPlaceTruck = function (order, multipleVehicle) {

        // $scope.PlaceTruckValidation(order);
        console.log("multipleVehicle",multipleVehicle);
        if(typeof multipleVehicle === undefined || typeof multipleVehicle === 'undefined') {
            alert("Add atleast one Vehicle");
            return false;
        } else {
            if (multipleVehicle.length) {
                order.truck_attribute = multipleVehicle;
                apiServices.confirmPlaceTruck(serverUrl + 'intracity/confirmPlaceTruck', order)
                    .then(function (response) {
                        setTimeout(function () { $state.reload(); }, 2000);
                        console.log("Request Data", response);
                    });
            }
        }        

    }

    // confirm transit detail 



    $scope.confirmTransitDetail = function (order, transitDetail) {
        order.transit_detail = transitDetail;
        apiServices.postMethod(serverUrl + 'intracity/confirmTransitDetail', order)
            .then(function (response) {
                setTimeout(function () { $state.reload(); }, 2000);
            });
    }

    // consignmentDeliveryDetails

    $scope.consignmentDeliveryDetails = function (order, deliveryDetail) {
        isValid = true;
        console.log('Order',order);
        if(deliveryDetail === null || deliveryDetail === '') {
            alert('Please fill mandatory(*) field');
            return false;
        } else {
            var date = deliveryDetail.date;
            var recipientName = deliveryDetail.recipientName;
            var recipientMobileNumber = deliveryDetail.recipientMobileNumber;
            
            if (date == undefined || date == '') {
                isValid = false;
                deliveryDetail.transitDate = true;
            }else {
                deliveryDetail.transitDate = false;
            }

            if (recipientName == undefined || recipientName == '') {
                isValid = false;
                deliveryDetail.reciName = true;
            }else {
                deliveryDetail.reciName = false;
            }

            if (recipientMobileNumber == undefined || recipientMobileNumber == '') {
                isValid = false;
                deliveryDetail.reciMobile = true;
            }else {
                deliveryDetail.reciMobile = false;
            }
        }
        
        if(isValid){
            order.delivery_detail = deliveryDetail;
            apiServices.postMethod(serverUrl + 'intracity/consignmentDeliveryDetails', order)
                .then(function (response) {
                    setTimeout(function () { $state.reload(); }, 2000);
            });
        }
        
    }


    $("#acceptTruckGSA").on("click", function () {
       apiServices.acceptPlaceTruckGSA(serverUrl + 'intracity/acceptPlaceTruckGSA', $("#routeDetail").val())
            .then(function (response) {
                $("#booknow-popup").modal('hide');
                setTimeout(function () {
                    $state.reload();
                }, 2000);
            });
    });

    // Add multiple Vehicle 
    var elementArray = [];
    $scope.AddVehicle = function (order, key) {

        let isValid = $scope.PlaceTruckValidation(order);

        if(!isValid) {
            return false;
        }

        if ($scope.orderlists[key].multipleVehicle === undefined || typeof $scope.orderlists[key].multipleVehicle == undefined || $scope.orderlists[key].multipleVehicle == '' || $scope.orderlists[key].multipleVehicle == null) {
            $scope.orderlists[key].multipleVehicle = [];
        }

        $scope.vehicle = {
            vehicleNumber: order.vehicleNumber,
            driverName: order.driverName,
            driverMobileNo: order.driverMobileNo
        }

        $scope.orderlists[key].multipleVehicle.forEach(function(element) {
            var b = elementArray.indexOf(element.vehicleNumber);
            if(b == -1){
              elementArray.push(element.vehicleNumber);
            }    
        });

        var a = elementArray.indexOf($scope.vehicle.vehicleNumber);
        if(a == -1){
           $scope.orderlists[key].multipleVehicle.push($scope.vehicle);
        }

        //console.log('Element Array',elementArray);
    }

    // Add transit detail 

    $scope.addTransitDetail = function (value, key) {
        isValid = true;
        console.log('Value',value);
        if(value === null || value === '') {
            alert('Please fill mandatory(*) field');
            return false;
        } else {
            var location1 = value.location;
            var date1 = value.date;
            if (location1 == 'undefined' || location1 == '') {
                isValid = false;
                value.loc = true;
            }else {
                value.loc = false;
            }

            if (date1 == undefined || date1 == '') {
                isValid = false;
                value.transitDate = true;
            }else {
                value.transitDate = false;
            }
        }
               
        if(isValid){
            if($scope.orderlists[key].transitDetails === undefined || typeof $scope.orderlists[key].transitDetails == undefined || $scope.orderlists[key].transitDetails == '' || $scope.orderlists[key].transitDetails == null) {
              $scope.orderlists[key].transitDetails = [];
            }
            
            $scope.orderlists[key].transitDetails.push(angular.copy(value));
        }
       
    }
    
   
    $scope.confirmConsignmentPickup = function (consignmentDetail, key) {
     
       if(typeof consignmentDetail == 'undefined') {
           alert('Please fill mandatory(*) field');
           return false;
       } else {
        isValid = true;
        var consignmentPickupDate = consignmentDetail.consignmentPickupDate;
        var lrDate = consignmentDetail.lrDate;
        var lrNo = consignmentDetail.lrNo;
        var billNo = consignmentDetail.billNumber;
        if (consignmentPickupDate == 'undefined' || consignmentPickupDate == '') {
            console.log('isValid',isValid);
            isValid = false;
            $scope.consignmentPickupDate_validation = true;
        }
        else {
            $scope.consignmentPickupDate_validation = false;
        }
        if (lrDate == undefined || lrDate == '') {
            isValid = false;
            consignmentDetail.lrDate_validation = true;
        }
        else {
            consignmentDetail.lrDate_validation = false;
        }
        if (lrNo == undefined || lrNo == '') {
            isValid = false;
            consignmentDetail.lrNo_validation = true;
        }
        else {
            consignmentDetail.lrNo_validation = false;
        }
        if (billNo == undefined || billNo == '') {
            isValid = false;
            consignmentDetail.consignmentTransBill_validation = true;
        }
        else {
            consignmentDetail.consignmentTransBill_validation = false;
        }
       }
        

       if (isValid) {
            apiServices.postMethod(serverUrl + 'intracity/confirmConsignmentPickup', $scope.orderlists[key])
                .then(function (response) {
                    setTimeout(function () { $state.reload(); }, 2000);
                    console.log("ConfirmConsignmentPickup Data", response);
                });
       }
    }

    // Confirm delivery detail by buyer

    $scope.confirmDelivery = function (order, key) {
        apiServices.postMethod(serverUrl + 'intracity/confirmDelivery', $scope.orderlists[key])
            .then(function (response) {
                $state.reload();
            });
    }

    // Navigate to 
     // by default and then 
    // console.log("$scope.countIndent.length",$scope.countIndent.length);
    $scope.navigateTo = function (section) {
        $scope.section = section;   
        if(section == 'messages') {
            $scope.getMessage();
        }     
    }

    $scope.getMessage = function() {
        //var url = serverUrl + 'order-messages';
        var params = {
            userId:Auth.getUserID(),
            serviceId:$scope.order.lkp_service_id,
            routeId:$scope.order.routeId
        };
//console.log("sfd :: ", serverUrl);
        apiServices.postMethod(serverUrl + 'order-messages', params)
            .then(function (response) {
                $scope.OrderMessages = response;
                console.log('Message details::', $scope.OrderMessages);
            });
    }

    $scope.displayDocumentUpload = function (key) {
        console.log("displayDocumentUpload::", key);

        if ($scope.orderlists[key].orderDocuments === undefined || typeof $scope.orderlists[key].orderDocuments == undefined || $scope.orderlists[key].orderDocuments == '' || $scope.orderlists[key].orderDocuments == null) {
            apiServices.postMethod(serverUrl + 'fetch/order-document', $scope.orderlists[key].itemId).then(response => {
                console.log("response", response);
                $scope.orderlists[key].orderDocuments = response.payload;
                console.log($scope.orderlists[key].orderDocuments);
            }).catch(err => {
                console.log("ERROR");
            });
        }

        $("#displayDocumentUpload-" + key).toggle();
    }

    $scope.uploadOrderDocument = function (document, key) {
        document.orderItemId = $scope.orderlists[key].itemId;
        document.type = 'OrderDocument';
        apiServices.uploadDocument(document).then(response => {
            if (response.isSuccessfull == true) {
                if ($scope.orderlists[key].orderDocuments === undefined || typeof $scope.orderlists[key].orderDocuments == undefined || $scope.orderlists[key].orderDocuments == '' || $scope.orderlists[key].orderDocuments == null) {
                    $scope.orderlists[key].orderDocuments = [];
                }
                $scope.orderlists[key].orderDocuments.splice(0, 0, response.payload);

                $scope.orderlists[key].successMessage = 'Document uploaded successfull';
                $scope.orderlists[key].errClass = 'text-success';
                console.log($scope.orderlists[key].successMessage);
            }
            console.log(response);
        }).catch(err => {
            $scope.orderlists[key].successMessage = 'Document not uploaded please try again';
            $scope.orderlists[key].errClass = 'text-danger';
        });
    }

    $scope.downloadDocument = function (attachment) {
        apiServices.postMethod(serverUrl + 'download/order-document', attachment).then(response => {
            console.log("response", response);

        }).catch(err => {
            console.log("ERROR");
        });
    }



    $scope.emailDocument = function (document, order) {
        console.log("document::", order);
        document.serviceId = order.lkp_service_id;
        document.orderNo = order.order_no;
        document.postType = order.postType;
        document.postId = order.routeId;
        document.orderId = order.itemId;
        apiServices.postMethod(serverUrl + 'email/order-document', document).then(response => {
            if (response.isSuccessfull) {
                $("#responseText").html("Document Emailed Successfully");
                $("#DocumentPopup").modal("show");
            }
        }).catch(err => {
            console.log("ERROR");
        });
    }

    $scope.closeDocumentPopup = function () {
        $("#DocumentPopup").modal("hide");
    }




    $scope.populateBookNowModel = function (data) {
        $scope.values = data;
        // console.log('GSA VALUESSS::', $scope.values);
        // console.log('xsxsxsxs', data);
        var order = data;
        console.log("orderData::", order.buyer_id);


        $("#buyer_user").html(order.buyer_name);


        $("#consignor,#seller-name").html(order.seller_name);

        $("#consignor_mobile").html(order.consignor_mobile);
        $("#consignor_adddress,#seller-address").html(order.consignor_address1 + ', ' + order.consignor_address2);

        $("#consignee_name").html(order.consignee_name);
        $("#consignee_mobile").html(order.consignee_mobile);
        $("#consignee_address").html(order.consignor_address1);

        $("#consignment_type").html('');
        $("#pickup_con_date").html(order.consignment_pickup_date);

        if (order.lkp_service_id == _HYPERLOCAL_) {
            // $("#source_location").html(data.source_locations.location_type_name);
            // $("#destination_locations").html(data.destination_locations.location_type_name);
             try{
                $("#from_location").html(data.data.fromLocation);
            }catch(e){
              console.log("From Location",e)
            }

            try{
                $("#to_location").html(data.data.toLocation);
            }catch(e){
              console.log("To Location",e)
            }
           // $("#from_location").html(data.data.fromLocation);
           // $("#to_location").html(data.data.toLocation);
            $("#seller-year-of-estd").html(data.seller.established_in);
            $("#seller-gta").html(data.seller.gta);
            $("#seller-tin-number").html(data.seller.tin);
            $("#seller-place-of-business").html(data.seller.principal_place);
            $("#seller-landline").html(data.seller.contact_landline);
            $("#seller-stn-number").html(data.seller.service_tax_number);
        } else if (order.lkp_service_id == _INTRACITY_) {
           // $("#source_location").html(data.source_locations.location_type_name);
            //$("#destination_locations").html(data.destination_locations.location_type_name);
            $("#from_location").html(order.from_location);
            $("#to_location").html(order.to_location);
            $("#seller-year-of-estd").html(order.established_in);
            $("#seller-gta").html(data.seller.gta);
            $("#seller-tin-number").html(data.seller.tin);
            $("#seller-place-of-business").html(data.seller.principal_place);
            $("#seller-landline").html(data.seller.contact_landline);
            $("#seller-stn-number").html(data.seller.service_tax_number);
        } else if (order.lkp_service_id == _BLUECOLLAR_) {
            $("#bluecollar_vehicle_type").html($("#veh_type_list").html());
            $("#bluecollar_experience").html($("#experience").html());
            $("#bluecollar_qualification").html($("#qualification").html());
            $("#bluecollar_seller_salary").html($("#seller_salary").html());
        }
        // Seller Detail 
        $("#seller-mobile").html(order.mobile);
        $("#seller-email").html(order.email);


    }


}]);