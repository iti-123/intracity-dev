app.controller('buyerSearchResultCtrl', ['$scope', '$http', 'config', 'trackings', 'apiServices', '$compile', '$state', '$stateParams', '$dataExchanger', function ($scope, $http, config, trackings, apiServices, $compile, $state, $stateParams, $dataExchanger) {

    var transitHour = [];
    var serverUrl = config.serverUrl;
    $scope.filterdata = [];
    $scope.servicetype = SERVICE_TYPE;
    $scope.materialtype = HYPERLOCAL_MATERIAL_TYPE;
    $scope.weight = HYPERLOCAL_WEIGHT;


    var url = serverUrl + 'hyperlocal/product-category';
    $scope.getProductCategory = function (url) {
        apiServices.category(url).then(function (response) {
            $scope.categories = response;
            //console.log('Product Category:', $scope.categories);
        });
    }
    $scope.getProductCategory(url);

    /**
     *      Check if data exist or not
     */

    if (angular.equals($scope.buyerSearch, {})) { $state.go("hp-search-buyer"); }

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
    }
    $scope.getCity(url);
    
    var specialKeys = new Array();
    specialKeys.push(8); //Backspace
    specialKeys.push(9); //Tab
    specialKeys.push(46); //Delete
    specialKeys.push(36); //Home
    specialKeys.push(35); //End
    specialKeys.push(37); //Left
    specialKeys.push(39); //Right

    $('#city').keypress(function (e) {
        var keyCode = e.keyCode == 0 ? e.charCode : e.keyCode;
        var ret = ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || 
            (keyCode >= 97 && keyCode <= 122) || (specialKeys.indexOf(e.keyCode) != -1 && e.charCode != e.keyCode));
          return ret;
    });
    
    $('#from_location').keypress(function (e) {
        var keyCode = e.keyCode == 0 ? e.charCode : e.keyCode;
        var ret = ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || 
            (keyCode >= 97 && keyCode <= 122) || (specialKeys.indexOf(e.keyCode) != -1 && e.charCode != e.keyCode));
          return ret;
    });

    $('#to_location').keypress(function (e) {
        var keyCode = e.keyCode == 0 ? e.charCode : e.keyCode;
        var ret = ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || 
            (keyCode >= 97 && keyCode <= 122) || (specialKeys.indexOf(e.keyCode) != -1 && e.charCode != e.keyCode));
          return ret;
    });



    /*** Get Location By Id ***/
    $scope.onSelect = function (data) {
        // 
        console.log(data);
    }
    // $scope.data.city_id = { id: '' };
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
    /*** Get Location By Id ***/
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
    }
    $scope.getVehiclesType(url);

    /*------------------------Get All Sellers--------------------------------*/
    var buyerurl = serverUrl + 'getallbuyer';
    apiServices.getallbuyer(buyerurl).then(function (response) {
        $scope.sellerList = response.payload;
    });

    /*-----------------------------------------------------------------------------------------*/

    $scope.buyerSearch = $dataExchanger.request.data;

    $scope.items = '';
    apiServices.hpbuyerSearchSeller(serverUrl, JSON.stringify($scope.buyerSearch)).then(function (response) {

        console.log("$scope.searchResult::", response.payload);
        $scope.items = response.payload;
        var calculatedprice;
        var distance = parseInt($scope.buyerSearch.location[0].distance);
        console.log('Buyer Search',$scope.buyerSearch);
        console.log('Distance',distance);
        for (let objectdata of $scope.items) {
            var baseprice = parseInt(objectdata.base_price);
            var dist_included_per_base = parseInt(objectdata.dist_included_per_base);
            var rate_per_extra_kms = parseInt(objectdata.rate_per_extra_kms);

            if (distance > dist_included_per_base) {
                extradistance = distance - dist_included_per_base;
                extraprice = extradistance * rate_per_extra_kms;
                calculatedprice = baseprice + extraprice;


            } else {
                calculatedprice = baseprice;
            }
            objectdata.price = calculatedprice;
        }
        $scope.searchResult = $scope.items;

        //console.log("$scope.searchResult2::", $scope.searchResult);

    });
    
    console.log("DataExchanger::", $dataExchanger.request.data);

    // Price Slider

    $scope.value = 150;

    $scope.slider = {
        min: 1,
        max: 100000,
        options: {
            floor: 0,
            ceil: 450
        }
    };

    $scope.price = {
        min: 1,
        max: 100000
    };

    /************************filter base on seller**************/

    $scope.NewSellerName = [];
    $scope.NewSellerNameFilter = function (item) {


        if (item != null) {
            if ($scope.NewSellerName.length == 0)
                return item;
            else {
                return $scope.NewSellerName.indexOf(item.posted_by) !== -1;
            }
        }

    };
    $scope.selectSeller = function (operator,value) {

        if ($scope.filterdata.indexOf(value) !== -1) {

            $scope.filterdata.splice($scope.filterdata.indexOf(value), 1)
        }
        else {
            $scope.filterdata.push(value);

        }
        var num = operator.toString()
        var idx = $scope.NewSellerName.indexOf(num);
        if (idx > -1)
            $scope.NewSellerName.splice(idx, 1);
        else
            $scope.NewSellerName.push(num);
        if ($scope != null)
            $scope.NewSellerName = $scope.NewSellerName;

    };
     $scope.removefilter = function (index, obj) {
        if (obj.hasOwnProperty('username')) {

            uncheckid = obj.id;
            $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1);
            
        }
        // else if (obj.hasOwnProperty('name')) {
        //     $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1);
           
        //     uncheckid = obj.id

        // }
        // else if (obj.hasOwnProperty('value')) {

        //    $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1);
           
        //     uncheckid = obj.id


        // }

        $("#v1" + uncheckid).prop('checked', false);
        setTimeout(function () {

        }, 100);
         
    };
      $scope.clearall = function () {
        $scope.filterdata = [];
        $('input:checkbox').prop('checked', false);
       
      }

    /*****************************filter price*************/

    $scope.priceRange = function (item) {

        return (parseInt(item.price) >= $scope.price.min && parseInt(item.price) <= $scope.price.max);
    };

    $scope.bookNow = function (quote, index) {
        var value = $scope.searchResult[index];
        console.log('valueeeeeeeeee', quote);
        var booknowSerachObj = $dataExchanger.request;
        $scope.bookPostObj = {
            initialDetails: {
                serviceType: '',
                sellerId: quote.posted_by,
                serviceId: _HYPERLOCAL_,
                buyerId: Auth.getUserID(),
                postType: "BP",
                sellerQuoteId: quote.id, //need to discuss
                searchData: booknowSerachObj,
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





    // Lets start sending Message
    $scope.sendMessageModal = function (val) {
        console.log('values for MESSAGESSS::',val);
        $("#from_name").val("From: " + Auth.getUserName());
        $("#to_name").val("To: " + val.vendor);
        $("#message_subject").val("Ref:- POST:" + val.post_transaction_id);
        $("#quoteObject").val(JSON.stringify(val));

        $scope.MessageObj.id = val.id; 
    }

    $scope.MessageObj = {
        "id": "",
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
        "id": "",
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
        //console.log("attachedFile", typeof attachedFile);
        var myForm = document.querySelector('#MessageData');
        var formData = new FormData(myForm);

        // Start Upload File
        var postDetails = JSON.parse(formData.get('quoteObject'));

        $scope.MessageObj.message_from = Auth.getUserID();
        $scope.MessageObj.message_to = postDetails.posted_by;
        $scope.MessageObj.message_subject = formData.get('message_subject');
        $scope.MessageObj.message_body = formData.get('message_body');

        $scope.MessageObj.lkp_service_id = $dataExchanger.request.serviceId;
        //$scope.MessageObj.buyer_quote_item = postDetails.id;
       // $scope.MessageObj.buyer_quote = postDetails.seller_post_id;
        //console.log("$scope.MessageObj.message_body::", $scope.MessageObj.message_body);
        $("#message_body").css("border-color", "#e2e2e2");
        if ($scope.MessageObj.message_body == '') {
            $("#message_body").css("border-color", "red");
            return false;
        }
        $scope.status = false;
        if (attachedFile != '') {
            $scope.attachedFile.file = attachedFile;
            apiServices.PostMessage($scope.MessageObj).then(function (response) {

                if (response.isSuccessful == true) {
                    // $scope.files = {documentId: response.payload.id, documentName: response.payload.file_name};
                    $scope.attachedFile.userMessageId = response.payload.id;
                    //console.log("HELLO WITH DOCUMENT", $scope.MessageObj.userMessageId);
                    //console.log("NEW MESSAGE OBJECT ", $scope.MessageObj);
                    apiServices.uploadDocument($scope.attachedFile).then(function (response) {
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
            apiServices.PostMessage($scope.MessageObj).then(function (response) {
                $scope.MessageObj.docId = "";
                //console.log(response.payload);

                $scope.messages = response.payload;
                $("#status").html("Message sent Successfully").addClass("text-success");
                setTimeout(function () {
                    $("#messageModal").modal('hide');
                }, 1000);
            });
        }
    };

    $scope.modifySearch = function (data) {

       
        // $scope.timeslot=TIME_SLOT;
        // $scope.data = angular.copy(data);
        // // $scope.data.timeSlot = data.timeSlot.split(' ')[0];
        // // $scope.data.period = data.timeSlot.split(' ')[1];
        // console.log("modifySearch::",data);
        //console.log("modifySearch::",$scope.data.timeSlot);
        console.log("City extra Id::", parseInt(data.city.id));
        var city_id = parseInt(data.city.id);
        if (typeof (city_id) != NaN || city_id != '') {
            $scope.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id);
        }

        $("#myModal").modal("show");
        
    }
    $scope.form_show = function (value, buyerSearch) {
        $scope.edited = true;
      

        console.log('object:', value);
        console.log('object2:', buyerSearch);
        $scope.show_form = true;
        $scope.editData = value;
        $scope.Editdata = buyerSearch;
        $scope.hide_add_button = true;
    }
    $scope.edit_form = function (buyerSearch) {
        $scope.show_form = true;
        $scope.editData = '';
        //$scope.Editdata = '';

        $scope.hide_add_button = false;
        $scope.edited = false;

    }
    $scope.dismiss_form = function () {

        $scope.show_form = false;

    }

    $scope.update_location = function (editData, Editdata) {
        console.log('editing1:', editData);
        console.log('editing2:', Editdata);

    }
    $scope.multipleLocation = [];
    $scope.addLocation = function (locationData) {
        $('#city').css('border-color', '');
        $('#departingDate').css('border-color', '');
        $('#from_location').css('border-color', '');
        $('#to_location').css('border-color', '');
        $('#ServiceType').css('border-color', '');
        $('#product_category').css('border-color', '');
        $('#weight').css('border-color', '');
        $('#parcel').css('border-color', '');
        console.log("locationData::", locationData);
        var isValidated = true;
        var weight = $.trim($('#weight').val());
     //if ($scope.multipleLocation == '') {
        if (weight == '') {
            $('#weight').css('border-color', 'red');
            $('#weight').focus();
            isValidated = false;
            //alert("Please fillup all the field");
        }
        // }
       // var isValidated = true;
        var to_location = $.trim($('#to_location').val());
       //  if ($scope.multipleLocation == '') {
        if (to_location == '') {
            $('#to_location').css('border-color', 'red');
            $('#to_location').focus();
            isValidated = false;
            //alert("Please fillup all the field");
        }
       // }
       //  if ($scope.multipleLocation == '') {
        var parcel = $.trim($('#parcel').val());
        if (parcel == '') {
            $('#parcel').css('border-color', 'red');
            $('#parcel').focus();
            isValidated = false;
            // alert("Please fillup all the field");
        }
        // }
        if (isValidated) {

            $scope.new = angular.copy(locationData);

            $scope.buyerSearch.location.push($scope.new);
            //console.log('ohh', $scope.multipleLocation);
            // alert('data');
             $scope.editData.tolocation = "";
             $scope.editData.parcelweight = "";
             $scope.editData.NoParcel = "";
        }
        else{
            alert('Must be fill To location, weight and No. of parcel');
        }
    }
    $scope.removeItem = function (x) {

        $scope.multipleLocation.splice(x, 1);
    }
    $scope.removeItem1 = function (x) {

        $scope.buyerSearch.location.splice(x, 1);
    }


    $scope.searchModified = function (data) {
       if( $scope.buyerSearch.location ==''){
           alert('Please add atleast one location') ;
           return;
       }

        $('#city').css('border-color', '');
        $('#departingDate').css('border-color', '');
        $('#from_location').css('border-color', '');
        $('#to_location').css('border-color', '');
        $('#ServiceType').css('border-color', '');
        $('#product_category').css('border-color', '');
        $('#weight').css('border-color', '');
        $('#parcel').css('border-color', '');

        var isValidated = true;
        var city = $.trim($('#city').val());
        if (city == '') {
            $('#city').css('border-color', 'red');
            $('#city').focus();
            isValidated = false;
            // alert("Please fillup all the field");
        }
        var departingDate = $('#departingDate').val();
        if (departingDate == '') {
            $('#departingDate').css('border-color', 'red');
            $('#departingDate').focus();
            isValidated = false;
            //alert("Please fillup all the field");
        }
        var from_location = $.trim($('#from_location').val());
        if (from_location == '') {
            $('#from_location').css('border-color', 'red');
            $('#from_location').focus();
            isValidated = false;
            //alert("Please fillup all the field");
        }
        var to_location = $.trim($('#to_location').val());
        if ($scope.buyerSearch.location == '' || $scope.edited) {
            if (to_location == '') {
                $('#to_location').css('border-color', 'red');
                $('#to_location').focus();
                isValidated = false;
                //alert("Please fillup all the field");
            }
        }
        
        var ServiceType = $.trim($('#ServiceType').val());
        if (ServiceType == '') {
            $('#ServiceType').css('border-bottom', '1px solid red');
            $('#ServiceType').focus();
            isValidated = false;
            // alert("Please fillup all the field");
        }
       
        var product_category = $.trim($('#product_category').val());
        if (product_category == '') {
            $('#product_category').css('border-bottom', '1px solid red');
            $('#product_category').focus();
            isValidated = false;
            // alert("Please fillup all the field");
        }
        
        var weight = $.trim($('#weight').val());
        if ($scope.buyerSearch.location == ''  || $scope.edited) {
            if (weight == '') {
                $('#weight').css('border-color', 'red');
                $('#weight').focus();
                isValidated = false;
                // alert("Please fillup all the field");
            }
        }
        
        if ($scope.buyerSearch.location == ''  || $scope.edited) {
            var parcel = $.trim($('#parcel').val());
            if (parcel == '') {
                $('#parcel').css('border-color', 'red');
                $('#parcel').focus();
                isValidated = false;
                // alert("Please fillup all the field");
            }
        }
               if (isValidated) {
        $dataExchanger.request.data = data;
        //$dataExchanger.request.data.timeSlot = data.timeSlot + ' ' + data.period;

        console.log('mylocalstore', $dataExchanger.request.data)

        $("#myModal").modal("hide");
        setTimeout(function () {
            $state.reload();
        }, 1000);
               }else{
                   alert('Please filled all the field');
               }
               
    }


    $scope.showHideDetail = function ($index) {

        $(".toggle-minus-" + $index).toggleClass("detail-minus");
        $("#detail-" + $index).slideToggle();
    };


}]);


// app.filter('priceRange', function() {
//         return function( items, price ) {
//             var filtered = [];
//             //here you will have your desired input
//             console.log(fromDate, toDate);
//             var from_date = Date.parse(fromDate);
//             var to_date = Date.parse(toDate);
//             angular.forEach(items, function(item) {
//                 if(item.base_price > price.min && item.base_price < price.max) {
//                     filtered.push(item);
//                 }
//             });
//             return filtered;
//         };
//     });