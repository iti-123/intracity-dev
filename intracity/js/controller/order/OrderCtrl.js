app.controller('OrderCtrl', ['$scope', '$http', 'config', 'consignment', 'apiServices', '$state', 'trackings', '$q','$dataExchanger', function ($scope, $http, config, consignment, apiServices, $state, trackings, $q,$dataExchanger) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.trackings = trackings.type;
    $scope.data = {
        'consignor': {},
        'consignee': {},
    };

    $scope._HYPERLOCAL_ = _HYPERLOCAL_;
    $scope._INTRACITY_ = _INTRACITY_;
    $scope._BLUECOLLAR_ = _BLUECOLLAR_;

    var serviceId = $state.params.serviceId;
    $scope.serviceIdIf = $state.params.serviceId;
    $scope.blueCollarServiceId = false;
    $scope.intracityServiceId = false;

 
    if (serviceId == _INTRACITY_) {
        $scope.intracityServiceId = true;
        $scope.lkp_intracity_service_id = _INTRACITY_;
    }
    else if (serviceId == _HYPERLOCAL_) {
        $scope.intracityServiceId = true;
        $scope.lkp_hyperlocal_service_id = _HYPERLOCAL_;
    }
    else if (serviceId == _BLUECOLLAR_) {
        $scope.intracityServiceId = true;
        $scope.lkp_bluecollar_service_id = _BLUECOLLAR_;
    }

    var cartId = $state.params.cartId;
    $scope.consignment = consignment.consignment_type;

    //Location Type for Source
 
    $scope.locations = Locationtype;
 
    //Packaging Type
    
    $scope.packagingtypes = Packagetyp;

    

    // Get Cart Item By Id
    apiServices.getCartItemById(serverUrl + 'intracity/carts/' + cartId).then(function (response) {
        $scope.searchData = JSON.parse(response.payload.search_data);

        if(JSON.parse(response.payload.search_data).serviceId == _BLUECOLLAR_)
        {
            console.log(response.payload);
            console.log("cartId",cartId);

            $scope.data.consignor = {
                city: response.payload.bc_seller_data.cur_city.city_name,
                address1: response.payload.bc_seller_data.cur_locality,
                address2: response.payload.bc_seller_data.cur_street,
                address3: response.payload.bc_seller_data.per_house_no,
                email: response.payload.bc_seller_data.email,
                mobile: response.payload.bc_seller_data.cur_mobile,
                name: response.payload.bc_seller_data.first_name,
                pin_code: response.payload.bc_seller_data.cur_pincode,
                state: response.payload.bc_seller_data.cur_state.state_name,
            }
        } else {

            $scope.data = $scope.searchData;
            $scope.cartData = response.payload;
            console.log("Cart Data", $scope.cartData);

            apiServices.getSellerById(serverUrl + 'get-seller-details-by-id/' + $scope.cartData.seller_id).then(function (sellerResponse) {
                $scope.data.seller = sellerResponse.payload;
                console.log("Seller Details::", $scope.data.seller);
                if ($scope.data.seller !== 0) {
                    //if()
                    $scope.data.consignor = {
                        city: $scope.data.seller.principal_place,
                        address1: $scope.data.seller.address1,
                        address2: $scope.data.seller.address2,
                        address3: $scope.data.seller.address3,
                        email: $scope.data.seller.contact_email,
                        mobile: $scope.data.seller.contact_mobile,
                        name: $scope.data.seller.name,
                        pin_code: $scope.data.seller.pincode,
                        state: $scope.data.seller.state_name,
                    }
                }

                $scope.data.consignee = {
                    city: "",
                    landmark: "",
                    address1: $scope.cartData.buyer_consignee_address,
                    address2: $scope.cartData.buyer_consignee_address2,
                    address3: $scope.cartData.buyer_consignee_address3,
                    email: $scope.cartData.buyer_consignee_email,
                    mobile: $scope.cartData.buyer_consignee_mobile,
                    name: $scope.cartData.buyer_consignee_name,
                    pin_code: $scope.cartData.buyer_consignee_pincode,
                    state: "",
                    additional_details: $scope.cartData.buyer_additional_details
                }

                // $scope.data.consignment_pickup_date = $scope.cartData.buyer_consignment_pick_up_date;
                // $scope.data.consignment_value = $scope.cartData.buyer_consignment_value
                // $scope.data.consignment_type = $scope.cartData.consignment_type

                $scope.data.source_locations = $scope.cartData.lkp_src_location_type_id;
                $scope.data.destination_locations = $scope.cartData.lkp_dest_location_type_id;
                $scope.data.packings = $scope.cartData.lkp_packaging_type_id;

            });
        }
    });


    $scope.submitBookNow = function (data, type) {
        console.log("Data", data,type);
        //return false;
        $scope.data.type = type;
        $scope.data.cartId = cartId;

        apiServices.updateCartItems(serverUrl + 'intracity/updateCartItem', JSON.stringify($scope.data))
            .then(function (response) {
                //console.log(response);
               // return false;
                $scope.populateBookNowModel($scope.data);
                $("#booknow-popup").modal('show');
                $scope.cartPrice = response;
                console.log("Response Data", $scope.cartPrice);
            });

        console.log("Payment Data::", $scope.data);

    }

    $("#acceptterms").on("click", function () {
        apiServices.updateCartStatus(serverUrl + 'intracity/updateCartStatus', $scope.data.cartId)
            .then(function (response) {
                if ($scope.data.type == 'add_to_cart') {
                    $("#cartModal").modal('show');
                } else {
                    $("#booknow-popup").modal('hide');
                    setTimeout(function () {
                        $state.go("cart");
                    }, 2000);
                }

                console.log("Request Data", response);
            });
    });

    $scope.closeCartModel = function () {
        $("#cartModal").modal('hide');
        $("#booknow-popup").modal('hide');

        setTimeout(function () {
            $state.go("cart");
        }, 2000);

    }
    // Get Cart Count
    var url = serverUrl + 'get-cart-count';
    $scope.cartCount = function (url) {
        apiServices.getCartCount(url).then(function (response) {
            $scope.cartCount = response;
            console.log('Cart Count::', $scope.cartCount)
        });
        $scope.cartCount(url);
    }

    $scope.populateBookNowModel = function (data) {
        $scope.values = data;
        console.log('Value data on booknows::', $scope.values);
        console.log('xsxsxsxs',$("#veh_type_list").html());
        var consignor = data.consignor;
        var consignee = data.consignee;

        $("#buyer_user").html(consignee.name);


        $("#consignor,#seller-name").html(consignor.name);

        $("#consignor_mobile").html(consignor.mobile);
        $("#consignor_adddress,#seller-address").html(consignor.address1 + ', ' + consignor.address2);

        $("#consignee_name").html(consignee.name);
        $("#consignee_mobile").html(consignee.mobile);
        $("#consignee_address").html(consignee.address1);

        $("#consignment_type").html(data.consignment_type);
        $("#pickup_con_date").html(data.consignment_pickup_date);

        if (serviceId == _HYPERLOCAL_) {
            $("#source_location").html(data.source_locations.location_type_name);
            $("#destination_locations").html(data.destination_locations.location_type_name);

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
        }else if(serviceId == _INTRACITY_){
            $("#source_location").html(data.source_locations.location_type_name);
            $("#destination_locations").html(data.destination_locations.location_type_name);
            $("#from_location").html(data.data.fromLocation);
            $("#to_location").html(data.data.toLocation);
            $("#seller-year-of-estd").html(data.seller.established_in);
            $("#seller-gta").html(data.seller.gta);
            $("#seller-tin-number").html(data.seller.tin);
            $("#seller-place-of-business").html(data.seller.principal_place);
            $("#seller-landline").html(data.seller.contact_landline);
            $("#seller-stn-number").html(data.seller.service_tax_number);
        }else if(serviceId == _BLUECOLLAR_)
        {
            $("#bluecollar_vehicle_type").html($("#veh_type_list").html());
            $("#bluecollar_experience").html($("#experience").html());
            $("#bluecollar_qualification").html($("#qualification").html());
            $("#bluecollar_seller_salary").html($("#seller_salary").html());
        }
        // Seller Detail 
        $("#seller-mobile").html(consignor.mobile);
        $("#seller-email").html(consignor.email);


    }


    $scope.filterValue = function ($event) {
        if (isNaN(String.fromCharCode($event.keyCode))) {
            $event.preventDefault();
        }
    };

    $scope.pincodeKeyup = function (type, Pincode) {
        var status = false;
        var pin = '';
        if (type == 'consignor') {
            status = apiServices.validateDigits(Pincode, 6);
            pin = Pincode;
        } else {
            status = apiServices.validateDigits(Pincode, 6);
            pin = Pincode;
        }

        if (status) {
            return status;
        }
    }

    $scope.autocompCity = function (type, pin) {
        console.log("$scope.data::",$scope.data);
        console.log(" $scope.data.seller", $scope.data.seller);
        var cityId ;
        if($scope.cartData.get_route) {
            cityId = $scope.cartData.get_route.city_id;
        } else if($dataExchanger.request.data.city) {
            cityId = $dataExchanger.request.data.city.id;
        } else {
            cityId = 1263;
        }
       
        var deferred = $q.defer();
        var isTrue = $scope.pincodeKeyup(type, pin);
        if (isTrue) {
            $http({
                method: 'POST',
                url: serverUrl + 'bluecollar/city-suggestion',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: {
                    'text': pin,
                    'cityId':cityId
                }
            }).then(function success(response) {
                let filteredCity = [];   
                if (response.data.data.length != 0) {
                    let pinCode = response.data.data; 
                   
                    // console.log( JSON.stringify(pinCode));
                    console.log("cityId::",cityId);
                    pinCode.forEach(element => {
                        if(element.city_id == cityId) {
                            filteredCity.push(element);
                        }
                    });
                    console.log("filteredCity",filteredCity);
                    if (type == 'consignor') {
                        $scope.consignorCitiesList = response.data.data;
                    } else {
                        $scope.consigneeCitiesList = response.data.data;
                    }

                    // if(filteredCity.length == 0) {
                    //     alert("Pincode not in To Location district. Click Cancel to re enter pincode or Ok to skip this validation.");
                    // }

                } else {
                    alert("Pincode not in To Location district. Click Cancel to re enter pincode or Ok to skip this validation.");
                }
                deferred.resolve(response.data.data);
            }, function error(response) {
                deferred.reject(response);
            });
            return deferred.promise;
        }
    };

    $scope.selectedCity = function ($item, $model, $label, type) {
        console.log($model);
        console.log($item);

        if (type == 'consignor') {
            $scope.data.consignor.pin_code = $item.pincode;
            $scope.data.consignor.city = $item.city_name;
            $scope.data.consignor.state = $item.statename;
        } else {
            $scope.data.consignee.pin_code = $item.pincode;
            $scope.data.consignee.city = $item.city_name;
            $scope.data.consignee.state = $item.statename;
        }
          $scope.activeClass="active";
    }

     
}]);