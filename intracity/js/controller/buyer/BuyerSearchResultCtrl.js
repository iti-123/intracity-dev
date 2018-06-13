app.controller('BuyerSearchResultCtrl', ['$scope', '$http', 'config', 'trackings', 'apiServices', '$compile', '$state', '$stateParams', '$dataExchanger', 'messageServices','GoogleMapService', function ($scope, $http, config, trackings, apiServices, $compile, $state, $stateParams, $dataExchanger, messageServices,GoogleMapService) {
    var transitHour = [];
    var serverUrl = config.serverUrl;
    $scope.filterdata = [];
    $scope.searchResult = {'len':0};
    console.log('Length:',$scope.searchResult.len);
    $scope.trackings = trackings.type;
    $scope.transitHour = TRANSIT_HOUR;
    $scope.timeslot = TIME_SLOT;
    var i = 0;

    for (var property in TRANSIT_HOUR) {
        transitHour[i] = {"id": property, "value": property};
        i++;
    }

    $scope.TRANSIT_HOUR = transitHour;
    
    apiServices.getMethod(serverUrl + 'get-hour-distance-labs').then(function(response) {
        $scope.hourDistanceSlabs = response;
        // console.log($scope.hourDistanceSlabs);
    });

    /**
     *      Check if data exist or not
     */

    if (angular.equals($scope.buyerSearch, {})) {
        $state.go("buyer-search");
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
    console.log('BUYER SEARCH::',$scope.buyerSearch);
    
    $scope.price = {
          min: 0,
          max: 10000
    };

    apiServices.sellerSearchByBuyer(serverUrl, JSON.stringify($scope.buyerSearch)).then(function (response) {

         // var price = '';
        // if (value.rate_base_distance != '') {
        //     price = 'RS ' + value.base_distance * value.rate_base_distance + ' /- (For ' + value.base_distance + ' KM )';
        // } else if (value.base_time != '') {
        //     price = 'RS ' + value.base_time * value.cost_base_time + ' /- (For ' + value.base_time + ' Hours )';
        // }
        // // console.log("quote::",$scope.searchResult[index]);
        // var confirm1 = confirm("Do you want to book,\n Seller Name: " + value.seller + ", and Price : " + price);
        // console.log("Buyer Quote::", confirm1);
        if(response.payload.length){
            $scope.searchResult = response.payload;
            $scope.searchResult.len = 1;
        }else{
            $scope.searchResult = '';
            $scope.searchResult.len = 2;
        }
        

        var typeBase = $scope.searchResult[0].type_basis;
        var hourCity = $scope.buyerSearch.hour_city.city_name;

        let totalDistance = '';
        let cityName = $scope.buyerSearch.city.city_name; 
        let fromLocation = $scope.buyerSearch.fromLocation.locality_name+', '+cityName;
        let toLocation = $scope.buyerSearch.toLocation.locality_name+', '+cityName;
        
        if(parseInt(typeBase) == 1) {
             fromLocation = $scope.buyerSearch.hour_city.city_name;
              toLocation = $scope.buyerSearch.reporting.locality_name+', '+hourCity;
        } else if(parseInt(typeBase) == 2) {
             fromLocation = $scope.buyerSearch.fromLocation.locality_name+', '+cityName;
            toLocation = $scope.buyerSearch.toLocation.locality_name+', '+cityName;
        }
        // console.log(fromLocation);
        // console.log(toLocation);

        GoogleMapService.calculateDistance(fromLocation,toLocation).then((response) => {
            let calculatedDistance = response;
            console.log('DISTANCE FROM GOOGLE API::',calculatedDistance);
            GoogleMapService.getDistanceInKM(response[0].distance).then((response) => {
                totalDistance = response;
                

                $scope.searchResult.forEach(function(value) { 
                    if(value.rate_base_distance != '' && value.type_basis == 2) {
                        let ratePerKM = value.rate_base_distance; // FOR value.base_distance
                        value.finalPrice = ratePerKM*value.base_distance + value.cost_per_extra_hour*(totalDistance - value.base_distance);                            
                        value.forDistance = totalDistance;                        
                    } else if(value.base_time != '' && value.type_basis == 1) {
                            distance = calculatedDistance[0].distance;
                            time = calculatedDistance[0].duration;

                        GoogleMapService.getDistanceInMinutes(calculatedDistance[0].duration).then((response) => {
                             let durationInMinutes = response;
                             console.log("durationInMinutes API",durationInMinutes);
                             console.log("Distance API",parseInt(distance));

                             console.log("slabTimeDrutaion Slab",value.slabTimeDrutaion.split("_"));

                           
                               
                             let slabTimeDrutaion = value.slabTimeDrutaion.split("_");
                             let postHourToMinute = slabTimeDrutaion[0] * 60;
                             let postDistance = slabTimeDrutaion[1];

                            console.log("postHourToMinute",postHourToMinute);
                            console.log("postDistance SLAB",postDistance);
                                 console.log("*****************************************************************");
                             if(durationInMinutes <=  postHourToMinute && parseInt(distance) <= postDistance) {
                                value.finalPrice = value.cost_base_time;
                                console.log('finalPrice::', value.finalPrice);
                                console.log('skjabdajsk', parseInt(undefined));
                             } else {        
                                console.log('finalPrice API::', value.finalPrice);    
                                value.additionalDistance = parseInt(distance) - parseInt(postDistance)
                                value.additionalHours = (durationInMinutes - parseInt(postHourToMinute))/60;                                
                                value.finalPrice = value.cost_base_time + (value.additionalDistance * value.additional_km_charge) + (value.additionalHours*value.additional_hour_charge);
                             }
                             
                        })   
                                                
                        
                    }                    
                }, this);
            });
        });
        console.log("$scope.searchResult::", response.payload);
    });
    /** Price Slider **/
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

    // {{ !empty(value.rate_base_distance) && value.base_distance*value.rate_base_distance || '' }}
    // {{ !empty(value.base_time) && value.base_time*value.cost_base_time || '' }}

    $scope.bookNow = function (quote, index) {
       
        var value = $scope.searchResult[index];
        // var price = '';
        // if (value.rate_base_distance != '') {
        //     price = 'RS ' + value.base_distance * value.rate_base_distance + ' /- (For ' + value.base_distance + ' KM )';
        // } else if (value.base_time != '') {
        //     price = 'RS ' + value.base_time * value.cost_base_time + ' /- (For ' + value.base_time + ' Hours )';
        // }
        // // console.log("quote::",$scope.searchResult[index]);
        // var confirm1 = confirm("Do you want to book,\n Seller Name: " + value.seller + ", and Price : " + price);
        // console.log("Buyer Quote::", confirm1);
        var confirm1 = true;
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
            apiServices.bookNow(serverUrl, JSON.stringify($scope.bookPostObj)).then(function (response) {
                if (response.isCartValue) {
                    $('#routeAlreadyExistsPopup').modal({ 'show': true });
                   //alert('This route is already booked.');
                } else if (response.isSuccessful && response.payload.id) {
                    $scope.cartId = response.payload.enc_id;
                    $state.go('order-booknow', {serviceId: 3, cartId: $scope.cartId});
                }
            })
        }
    };
    $scope.closeRouteAlreadyExistsPopup = function () {
        console.log($(".statusText button").attr("data-type"));
        var st = $(".statusText button").attr("data-type");
        $("#routeAlreadyExistsPopup").modal("hide");
        if (st) {
            setTimeout(function () {
                $state.go("buyer-search-result");
            }, 1000);

        }

    }

    $scope.showHideDetail = function ($index) {
        $scope.trackVisit($scope.searchResult[$index]);
        $(".toggle-minus-" + $index).toggleClass("detail-minus");
        $("#detail-" + $index).slideToggle();
    };

    $scope.trackVisit = function(value) {
        
        let params = {
            routeId: value.id,
            serviceId:$dataExchanger.request.serviceId,
            roleId:Auth.getUserActiveRole().toLowerCase(),
            type:2 // Buyer Post
        };
        let url = serverUrl+'track';
        
        apiServices.postMethod(url,params).then(response=> {
        }).catch();
    }

    $scope.removefilter = function (index, obj) {


        if (obj.hasOwnProperty('username')) {

            uncheckid = obj.username.replace(/ /g, "-");
            $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1);
            var idx = $scope.Newseller.indexOf(obj.username);
            $scope.Newseller.splice(idx, 1);
        }
        else if (obj.hasOwnProperty('vehicle_type')) {
            $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1);
            var idx = $scope.NewVehicleType.indexOf(obj.vehicle_type);
            $scope.NewVehicleType.splice(idx, 1);
            uncheckid = obj.vehicle_type.replace(/ /g, "-");

        }
        else if (obj.hasOwnProperty('value')) {

            if (obj.value == 'Mile Stone' || obj.value == 'Real Time') {
                $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1);
                uncheckid = obj.value.replace(/ /g, "-");
                var idx = $scope.Newtrackings.indexOf(obj.id);
                $scope.Newtrackings.splice(idx, 1);
            } else {

                $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1);
                var idx = $scope.Newtransit.indexOf(obj.id);
                $scope.Newtransit.splice(idx, 1);
                uncheckid = obj.value;
            }

        }

        $("#" + uncheckid).prop('checked', false);
        setTimeout(function () {

        }, 100);
    };
    $scope.clearall = function () {
        $scope.filterdata = [];
        $scope.Newseller = [];
        $scope.Newtrackings = [];
        $scope.NewVehicleType = [];
        $scope.Newtransit = [];
        $('input:checkbox').prop('checked', false);
    };


    // Lets start sending Message
    $scope.sendMessageModal = function (value) {
        console.log('MESSAGE VALUESSSSSS ID::',value.id);
        $("#id").val("Id:" + value.id)
        $("#from_name").val("From: " + Auth.getUserName());
        $("#to_name").val("To: " + value.seller);
        $("#message_subject").val("Ref:- POST:INTRACITY/2017/" + value.seller_post_id);
        $("#quoteObject").val(JSON.stringify(value));
        

    };



    $scope.MessageObj = {
        "id":"",
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
        "id":"",
        "message_from": "",
        "message_to": "",
        "message_smessage_subjectubject": "",
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
        //console.log("attachedFile", typeof attachedFile);
        var myForm = document.querySelector('#MessageData');
        var formData = new FormData(myForm);

        // Start Upload File
        var postDetails = JSON.parse(formData.get('quoteObject'));

        $scope.MessageObj.message_from = Auth.getUserID();
        $scope.MessageObj.message_to = postDetails.seller_id;
        $scope.MessageObj.message_subject = formData.get('message_subject');
        $scope.MessageObj.message_body = formData.get('message_body');
        $scope.MessageObj.id = postDetails.id;
       
        $scope.MessageObj.lkp_service_id = $dataExchanger.request.serviceId;
        $scope.MessageObj.buyer_quote_item = postDetails.id;
        $scope.MessageObj.buyer_quote = '';
        //console.log("$scope.MessageObj.message_body::", $scope.MessageObj.message_body);
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
                   // console.log("HELLO WITH DOCUMENT", $scope.MessageObj.userMessageId);
                 //   console.log("NEW MESSAGE OBJECT ", $scope.MessageObj);
                    messageServices.uploadDocument($scope.attachedFile).then(function (response) {
                        //console.log(response.payload);
                        $scope.messages = response.payload;
                        $scope.status = true;
                        $("#status").html("Message sent Successfully").addClass("text-success");
                        $("#messageModal").modal('hide');
                        setTimeout(function () {
                            $state.reload();
                        }, 1000);


                    })
                }
            });
        } else {
            //console.log("$scope.MessageObj ::", $scope.MessageObj);
            messageServices.PostMessage($scope.MessageObj).then(function (response) {
                $scope.MessageObj.docId = "";
               // console.log(response.payload);

                $scope.messages = response.payload;
                $("#status").html("Message sent Successfully").addClass("text-success");
                setTimeout(function () {
                    $("#messageModal").modal('hide');
                }, 1000);
            });
        }
    };

    $scope.modifySearch = function (data) {
       // console.log("DATA::",data);
        $scope.data = angular.copy(data);
        $scope.onSelect(data.city);        
        $("#myModal").modal("show");
    };

    $scope.searchModified = function (data) {
        $dataExchanger.request.data = {};
        console.log(data);
        $dataExchanger.request.data = data;

        $("#myModal").modal("hide");
        setTimeout(function () {
            $state.reload();
        }, 1000);

    };

    $scope.showHide = function (type) {
        // Reset for modify form here  
    };

    $scope.onSelect = function (data) {
        var city_id;
        console.log('location', data);
        // $scope.data.fromLocation = '';
        // $scope.data.toLocation = '';
        var city_id = parseInt(data.id);
        if (typeof(city_id) != NaN || city_id != '') {
            url = serverUrl + 'locations/getlocality/' + city_id;
            apiServices.getLocationByCity(url).then(function (response) {
                $scope.locations = response;
            });
        }
    };

    $scope.navigateToBuyerPost = function (searchData) {
        $dataExchanger.request.data.isPrefilled = true;

        $state.go('post-buyer-as-term');

    }
}]);
