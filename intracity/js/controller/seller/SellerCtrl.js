app.controller('SellerCtrl', ['$scope', '$http', 'config', 'trackings', 'apiServices', 'discount', '$state', '$dataExchanger', function ($scope, $http, config, trackings, apiServices, discount, $state, $dataExchanger) {

    var serverUrl = config.serverUrl;
    // geting tracking type and discount type from constrants js
    $scope.tracking_type = trackings.type;
    $scope.multipleRates = []

    $scope.discount_list = discount.discount_type;
    $scope.timeSlot = timeSlot;
    $scope.sellerTimeSlot = sellerTimeSlot;
    $scope.transitHour = TRANSIT_HOUR;
    $scope.selectedUser = '';
    $scope.materialType = MATERIAL_TYPE;

    var url = serverUrl + 'getbuyerdetails';
    $scope.getbuyer = function (url) {
        apiServices.getMethod(url).then(function (response) {
            response.push({ 'id': "-1", 'full_name': "All" });
            $scope.users = response;

            //console.log('usersssssssssssssssssssss',$scope.users);
        });
    };
    $scope.getbuyer(url);
    
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


    // get city

    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
        });
    };
    $scope.getCity(url);

    // get vehile type from services
    

    $scope.vehicles = vechileType;
    /**
     * 
     * Get distance hour slabs
     * 
     */
   
    $scope.hourDistanceSlabs = distanceHourSlab;
    /********************************************************
     *
     * add multiple post or seller rate card
     *
     *********************************************************/
    $scope.addroutedata = [];
    $scope.vtype = [] ;
    $scope.addroutes = function (data, discountdata) {
       
        
        if ($scope.rid === '' || $scope.rid == undefined) {
            if (typeof (data.length) != 'undefined' || data.length != '') {
                if($scope.vtype.indexOf(data.vehicle_type.vehicle_type) == -1){
                $scope.vtype.push(data.vehicle_type.vehicle_type);
                var No_adding_restriction = true;
            } else{
                 var No_adding_restriction = false;
            }
            
             if(No_adding_restriction){
                $scope.new = angular.copy(data);
                $scope.addroutedata.push({
                    route: $scope.new,
                    dis: discountdata,
                    multipleRate: $scope.multipleRates
                });
                $scope.data.city = '';
                $scope.data.valid_from_date = '';
                $scope.data.valid_to_date = '';
                $scope.data.base_distance = '';
                $scope.data.rate_base_distance = '';
                $scope.data.tracking_type = '';
                $scope.data.from_time = '';
                $scope.data.from_to = '';
                $scope.data.extra_hour = '';
                $scope.data.vehicle_type = '';
                $scope.data.vehicle_type_wait = '';
                $scope.data.extracost_wait = '';
                $scope.data.vehicle_over_dimension = '';
                $scope.data.dimension_volume = '';
                $scope.data.material_type = '';
                $scope.data.volume_unit_extra = '';
                $scope.data.helpers = '';
                $scope.data.labour_charge = '';
                $scope.data.addition_labour_charge = '';
                $scope.data.toll_charge = '';
                $scope.data.other = '';
                $scope.data.vehicle_type_h = ''
                $scope.data.base_time = ''
                $scope.data.houre_charge = ''
                $scope.data.km_charge = ''
                $scope.data.cost_base_time = '';

                $scope.multipleRates = [];

                $scope.discountdata = [];
                //console.log($scope.addroutedata[0].route);
                
            }
            else{

                $('#addVehiclePopup').modal({ 'show': true });
            }
                

            }
            $scope.rid = '';

        } else {


            if (typeof (data.length) != 'undefined' || data.length != '') {
                console.log('check yaar',data);
                $scope.addroutedata.splice($scope.rid, 1);
                $scope.new = angular.copy(data);
                $scope.addroutedata.push({ route: $scope.new, dis: discountdata });
                $scope.discountdata = [];
                //console.log($scope.addroutedata);
            }
            $scope.rid = '';

        }



    };
    ///for edit
    $scope.editRoute = function (x, index) {
        console.log(x);
        //$scope.discountdata=x.discountdata;
        $scope.data = x.route;
        $scope.discountdata = x.dis;
        $scope.multipleRates = x.multipleRate;
        $scope.rid = index;
    };
    /********************************************
     * 
     * Delete route
     *
     *********************************************/
    $scope.deleteRoute = function (x) {
        $scope.addroutedata.splice(x, 1);
    };

    /*********************************************
     *
     *  Add Pre discount  multiple
     *
     **********************************************
     */

    $scope.discountdata = [];
    $scope.discounts = function (pddata) {
       // console.log('check1', pddata.buyer.full_name);
       /// console.log('check2', $scope.discountarray.indexOf(pddata.buyer.full_name));


        console.log('did', $scope.did);
        if ($scope.did === '' || $scope.did == undefined) {

            if (typeof (pddata.length) != 'undefined' || pddata.length != '') {
                if ($scope.discountarray.indexOf(pddata.buyer.full_name) == -1) {
                    $scope.discountarray.push(pddata.buyer.full_name);
                   // $scope.new = angular.copy($scope.pddata);
                    $scope.discountdata.push($scope.pddata);
                    // $scope.discountarray.length = 0;
                    console.log('discountdata',$scope.discountdata);
                }else{
                    $('#addDiscountBuyerPopup').modal({ 'show': true });
                    //alert('Please selet another buyer') ;
                }
            }
            $scope.did = '';

        } else {

            if (typeof (pddata.length) != 'undefined' || pddata.length != '') {

                $scope.discountdata.splice($scope.did, 1);
                $scope.new = angular.copy($scope.pddata);
                $scope.discountdata.push($scope.new);
                console.log($scope.discountdata);

            }

            $scope.did = '';
        }
        //$scope.edited ='';
        console.log('ssssssssss', $scope.discountdata);


        $scope.pddata = '';
    };


    $scope.closeDiscountBuyerPopup = function () {
        console.log($(".statusText button").attr("data-type"));
        var st = $(".statusText button").attr("data-type");
        $("#addDiscountBuyerPopup").modal("hide");
            setTimeout(function () {
                $state.go("/seller-rate-card");
            }, 1000);
    }
    /********************************************
     *
     *  edit Discount
     *
     *********************************************/
    $scope.editDiscount = function (x, index) {
        console.log(index);

        $scope.pddata = x;
        $scope.did = index;
        console.log($scope.did);
    };
    /********************************************
     * 
     * Delete discount
     *
     *********************************************/
    $scope.deleteDiscount = function (x) {

        $scope.discountdata.splice(x, 1);
    };

    $scope.netprice = function () {


        $scope.pddata.discount_type;
        $scope.data.base_distance;
        $scope.data.rate_base_distance;
        $scope.pddata.buyer_discount;
        //$scope.pddata.netprice;
        if ($scope.data.type == '2') {

            if ($scope.pddata.discount_type == 1) {
                totalprice = $scope.data.base_distance * $scope.data.rate_base_distance;
                disAmount = totalprice * $scope.pddata.buyer_discount / 100;
                $scope.pddata.netprice = totalprice - disAmount;
                $scope.data.netprice = totalprice - disAmount;
                 if ($scope.pddata.buyer_discount > 100) {
                    $('#addpercentageDiscountPopup').modal({ 'show': true });
                    //alert('Please check your discount');
                    $('#buyer_discount').val('');
                    return false;
                }



            } else if ($scope.pddata.discount_type == 2) {

                totalprice = $scope.data.base_distance * $scope.data.rate_base_distance;
                disAmount = totalprice - $scope.pddata.buyer_discount;
                $scope.pddata.netprice = disAmount;
                $scope.data.netprice = disAmount;

                if ($scope.pddata.buyer_discount > totalprice) {
                    $('#addDiscountPopup').modal({ 'show': true });
                    //alert('Please check your discount');
                    $('#buyer_discount').val('');
                    return false;
                }

            }

        } else {

            if ($scope.pddata.discount_type == 1) {
                totalprice = $scope.data.base_time * $scope.data.cost_base_time;
                disAmount = totalprice * $scope.pddata.buyer_discount / 100;
                $scope.pddata.netprice = totalprice - disAmount;
                $scope.data.netprice = totalprice - disAmount;


            } else if ($scope.pddata.discount_type == 2) {
                totalprice = $scope.data.base_time * $scope.data.cost_base_time;
                disAmount = totalprice - $scope.pddata.buyer_discount;
                $scope.pddata.netprice = disAmount;
                $scope.data.netprice = disAmount;
                if ($scope.pddata.buyer_discount > totalprice) {
                    $('#addDiscountPopup').modal({ 'show': true });
                    //alert('Please check your discount');
                    $('#buyer_discount').val('');
                    return false;
                }
            }

        }


    };

    $scope.closeDiscountPopup = function () {
        console.log($(".statusText button").attr("data-type"));
        var st = $(".statusText button").attr("data-type");
        $("#addDiscountPopup").modal("hide");
        if (st) {
            setTimeout(function () {
                $state.go("post-buyer-as-term");
            }, 1000);

        }

    }

    $scope.closepercentageDiscountPopup = function () {
        console.log($(".statusText button").attr("data-type"));
        var st = $(".statusText button").attr("data-type");
        $("#addpercentageDiscountPopup").modal("hide");
        if (st) {
            setTimeout(function () {
                $state.go("post-buyer-as-term");
            }, 1000);

        }

    }

     $scope.closeVehiclePopup = function () {
        console.log($(".statusText button").attr("data-type"));
        var st = $(".statusText button").attr("data-type");
        $("#addVehiclePopup").modal("hide");
        if (st) {
            setTimeout(function () {
                $state.go("post-buyer-as-term");
            }, 1000);

        }

    }


    /*************************************************************
     *
     *    add post discount data
            fd -final discount
     *
     *************************************************************/

    $scope.postDiscountdata = [];
    $scope.addPostdiscount = function (fddata) {
        //console.log(fddata);


        //console.log($scope.pdid);
        if ($scope.pdid === '' || $scope.pdid == undefined) {

            if (typeof (fddata.length) != 'undefined' || fddata.length != '') {
                $scope.new = angular.copy($scope.fddata);
                $scope.postDiscountdata.push($scope.new);
                console.log($scope.postDiscountdata);
            }
            $scope.pdid = '';

        } else {

            if (typeof (fddata.length) != 'undefined' || fddata.length != '') {
                $scope.postDiscountdata.splice($scope.pdid, 1);
                $scope.new = angular.copy($scope.fddata);
                $scope.postDiscountdata.push($scope.new);
                console.log($scope.postDiscountdata);
            }
            $scope.pdid = '';
        }
        //$scope.edited ='';
        //console.log( $scope.discountdata);


        $scope.fddata = '';
    };


    // edit Discount


    $scope.editpostDiscount = function (x, index) {
        console.log(index);

        $scope.fddata = x;
        $scope.pdid = index;
        console.log($scope.pdid);
    };


    //Delete discount

    $scope.deletepostDiscount = function (x) {

        $scope.postDiscountdata.splice(x, 1);
    };




    /***********add post discount data end Here**********************/




    /// add pre disscount validation
    $scope.discountarray = [];
    $scope.validateDiscount = function (pddata, type) {
        console.log('pddata', pddata);
        console.log('type', $scope.discountarray);


        var status = true;


        var base_distance = $.trim($('#base_distance').val());

        if (base_distance == '' && type == 2) {
            $('#base_distance').css('border-color', 'red');
            $('#base_distance').focus();
            status = false;
        } else {
            $('#base_distance').css('border-color', '');
        }
        var rate_base_distance = $.trim($('#rate_base_distance').val());
        if (rate_base_distance == '' && type == 2) {
            $('#rate_base_distance').css('border-color', 'red');
            $('#rate_base_distance').focus();
            status = false;
        } else {
            $('#rate_base_distance').css('border-color', '');
        }

        var base_time = $.trim($('#base_time').val());

        if (base_time == '' && type == 1) {
            $('#base_time').css('border-color', 'red');
            $('#base_time').focus();
            status = false;
        } else {
            $('#base_time').css('border-color', '');
        }

        var cost_base_time = $.trim($('#cost_base_time').val());
        if (cost_base_time == '' && type == 1) {
            $('#cost_base_time').css('border-color', 'red');
            $('#cost_base_time').focus();
            status = false;
        } else {
            $('#cost_base_time').css('border-color', '');
        }

        var buyer = $.trim($('#buyer').val());

        if (buyer == '') {
            $('#buyer').css('border-color', 'red');
            $('#buyer').focus();
            status = false;
        } else {
            $('#buyer').css('border-color', '');
           // $scope.discountarray.push(buyer);

        }
        var obj = pddata.buyer;
        if (!obj.hasOwnProperty("full_name")) {
            $('#buyer').css('border-color', 'red');
            $('#buyer').val('');
            $('#buyer').focus();
            status = false;
        }

        var discount_type = $.trim($('#discount_type').val());
        if (discount_type == '') {
            $('#discount_type').css('border-color', 'red');
            $('#discount_type').focus();
            status = false;
        } else {
            $('#discount_type').css('border-color', '');
        }
        var buyer_discount = $.trim($('#buyer_discount').val());
        if (buyer_discount == '') {
            $('#buyer_discount').css('border-color', 'red');
            $('#buyer_discount').focus();

            status = false;
        } else {
            $('#buyer_discount').css('border-color', '');
        }
        var credit_day = $.trim($('#credit_day').val());
        if (credit_day == '') {
            $('#credit_day').css('border-color', 'red');
            $('#credit_day').focus();

            status = false;
        } else {
            $('#credit_day').css('border-color', '');
        }



        if (status) {
            $scope.discounts(pddata); // call add discount
        }

    };

    /// add post disscount validation
    $scope.postdiscountbuyer=[];
    $scope.validatePostdiscount = function (pddata) {
        // console.log(pddata);
        /**discount amount should be less tha or equal total amout*/
         if ($scope.postdiscountbuyer.indexOf(pddata.buyer.full_name) == -1) {
            $scope.postdiscountbuyer.push(pddata.buyer.full_name);
         }else{
             
             $('#addDiscountBuyerPopup').modal({ 'show': true });
             return false;
         }
       
        console.log('post validate',$scope.postDiscountdata);
        var status = true;

        var buyer = $.trim($('#fdbuyer').val());
        if (buyer == '') {
            $('#fdbuyer').css('border-color', 'red');
            $('#fdbuyer').focus();
            status = false;
        } else {
            $('#fdbuyer').css('border-color', '');
        }
        var obj = pddata.buyer;
        if (!obj.hasOwnProperty("full_name")) {
            $('#fdbuyer').css('border-color', 'red');
            $('#fdbuyer').val('');
            $('#fdbuyer').focus();
            status = false;
        }
        var discount_type = $.trim($('#fddiscount_type').val());
        if (discount_type == '') {
            $('#fddiscount_type').css('border-color', 'red');
            $('#fddiscount_type').focus();
            status = false;
        } else {
            $('#fddiscount_type').css('border-color', '');
        }
        var buyer_discount = $.trim($('#fdbuyer_discount').val());
        if (buyer_discount == '') {
            $('#fdbuyer_discount').css('border-color', 'red');
            $('#fdbuyer_discount').focus();

            status = false;
        } else {
            $('#fdcredit_day').css('border-color', '');
        }
        var credit_day = $.trim($('#fdcredit_day').val());
        if (credit_day == '') {
            $('#fdcredit_day').css('border-color', 'red');
            $('#fdcredit_day').focus();

            status = false;
        } else {
            $('#fdcredit_day').css('border-color', '');
        }



        if (status) {
            $scope.addPostdiscount(pddata); // call add discount
        }

    };
    $scope.finaldiscountprice=function()
    {
        
        $scope.routeprice=[];
        for (let value of $scope.addroutedata) {  
                 
                  var routeprice=value.route.rate_base_distance*value.route.base_distance;
                  $scope.routeprice.push(routeprice);
                }
       
        var minroutevalue = Math.min.apply(Math, $scope.routeprice);
        if($scope.fddata.discount_type==2 && $scope.fddata.buyer_discount>minroutevalue)
            {
               $('#addpercentageDiscountPopup').modal({ 'show': true });
               return false;
            }
        if($scope.fddata.discount_type==1 &&  $scope.fddata.buyer_discount >100)
            {
              $('#fdbuyer_discount').val('');
              $('#addpercentageDiscountPopup').modal({ 'show': true });
              return false;
            }

    }

    $scope.error = {
        title:false,
        city:false,
        validFromDate:false,
        validToDate:false,
        buyerPostValidTo:false,
        trackingType:false,
        fromTime:false,
        fromTo:false,
        baseDistance:false,
        vechileType:false,
        vehicleTypeH:false,
        costBaseTime:false,
        baseTime:false,
        rateBaseDistance:false
    }

    $scope.validateroutes = function (data, discountdata, type) {
        // distance 2 houre 1

        var status = true;
        var title = $.trim($('#title').val());
        if (title == '') {          
            $scope.error.title = true;
            status = false;
        } else {
            $scope.error.title = false;           
        }
        var city = $.trim($('#city').val());
        if (city == '') {          
            $scope.error.city = true;
            status = false;
        } else {
            $scope.error.city = false;           
        }
        var valid_from_date = $.trim($('#valid_from_date').val());
        if (valid_from_date == '') {
            $scope.error.validFromDate = true;
            status = false;
        } else {
            $scope.error.validFromDate = false;
        }

        var valid_to_date = $.trim($('#valid_to_date').val());
        if (valid_from_date == '') {
            $scope.error.validToDate = true;
            status = false;
        } else {
            $scope.error.validToDate = false;
        }

        var buyerPostValidTo = $.trim($('#buyerPostValidTo').val());
        if (buyerPostValidTo == '') {
            $scope.error.buyerPostValidTo = true;

            status = false;
        } else {
            $scope.error.buyerPostValidTo = false;
        }

        var tracking_type = $.trim($('#tracking_type').val());
        if (tracking_type == '' && type == 2) {
            $scope.error.trackingType = true;
            status = false;
        } else {
            $scope.error.trackingType = false;
        }

        var from_time = $.trim($('#from_time').val());
        if (from_time == '' && type == 2) {
            $scope.error.fromTime = true;
            status = false;
        } else {
            $scope.error.fromTime = false;            
        }
        var from_to = $.trim($('#from_to').val());
        if (from_to == '' && type == 2) {
            $scope.error.fromTo = true; 
            status = false;
        } else {
            $scope.error.fromTo = false;
        }
        var base_distance = $.trim($('#base_distance').val());
        if (base_distance == '' && type == 2) {
            $scope.error.baseDistance = true;
            status = false;
        } else {
            $scope.error.baseDistance = false;
        }
        var rate_base_distance = $.trim($('#rate_base_distance').val());
        if (rate_base_distance == '' && type == 2) {
            $scope.error.rateBaseDistance = true;
            status = false;
        } else {
            $scope.error.rateBaseDistance = false;
        }
        var vehicle_type = $.trim($('#vehicle_type').val());
        if (vehicle_type == '' && type == 2) {
            $scope.error.vechileType = true;
            status = false;
        } else {
            $scope.error.vechileType = false;
        }

        var vehicle_type_h = $.trim($('#vehicle_type_h').val());
        if (vehicle_type_h == '' && type == 1) {
            $scope.error.vehicleTypeH = true;
            status = false;
        } else {
            $scope.error.vehicleTypeH = false;
        }
        var base_time = $.trim($('#base_time').val());
        if (base_time == '' && type == 1) {
            $scope.error.baseTime = true;
            status = false;
        } else {
            $scope.error.baseTime = false;
        }
        var cost_base_time = $.trim($('#cost_base_time').val());
        if (cost_base_time == '' && type == 1) {
            $scope.error.costBaseTime = true;
            status = false;
        } else {
            $scope.error.costBaseTime = false;
        }


        if (status) {
            console.log("discountdata::", data);
            $scope.addroutes(data, discountdata);
            // $("#city").prop('disabled', true);
        }
    };


    $scope.createPost = function (data, addroutedata, status) {

        //console.log("DataExchanger::", $dataExchanger.request.serviceName);
        //abort();
        if ($state.params.id != '') {
            $scope.data.uid = $state.params.id; //  
        }

        console.log('data', data);
        if ($scope.data.post_type == 1 && $scope.data.selectseledata == '') {
            alert('Please Select at least one buyer');

            return false;
        }

        if ($scope.addroutedata.length < 1) {
            //alert("Please Create at least one route");
            $('#addRoutePopup').modal({ 'show': true });
            return false;
        }
        if (!$('#Accept').is(':checked')) {
            alert('Accept Terms & Conditions ');
            return false;
        }

        var requestPayload = {
            "data": JSON.stringify(data),
            "addroutedata": JSON.stringify(addroutedata),
            "discount": JSON.stringify($scope.postDiscountdata),
            "status": status,
            "serviceName": $dataExchanger.request.serviceName,
        };
        console.log('total data', requestPayload);

        console.log(addroutedata);
        console.log(requestPayload);
        console.log("url::", serverUrl + 'seller-rate-cart');
        //abort();
        $.ajax({
            url: serverUrl + 'seller-rate-cart',
            type: "POST",

            data: requestPayload,
            headers: {

                'Authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            success: function (response) {
                console.log("Response::", response);
                if (response.status == 'success') {
                    var tran = response.payload.data.post_transaction_id;
                    if(status == 1){
                        str = "Your request for post has been successfully posted to the relevant buyers. Your transacton id is " + tran + " You would be getting the enquiries from the buyers online.";
                    }else if(status == 0){
                        +sessionStorage.setItem('postDraft',true);
                        $state.go('seller-post-list');
                    }
                    $("#responsetext").html(str);
                    $(".waitText button").attr("data-type", "sellerPost");
                    $('#myModal').modal({ 'show': true });

                } else {
                    // $state.go("seller-post-list");
                }
                console.log(response);
            },
            error: function (err) {
                console.log("error");
            }
        });

    };

    $('#Discounts').click(function () {
        //console.log($scope.data.type);
        var status = true;
        if ($scope.data.type == '2') {
            $('#base_distance').css('border-color', '');
            $('#rate_base_distance').css('border-color', '');
            var base_distance = $.trim($('#base_distance').val());
            if (base_distance == '') {
                $('#base_distance').css('border-color', 'red');
                $('#base_distance').focus();
                status = false;
            }
            var rate_base_distance = $.trim($('#rate_base_distance').val());
            if (rate_base_distance == '') {
                $('#rate_base_distance').css('border-color', 'red');
                $('#rate_base_distance').focus();
                status = false;
            }
            if (status) { $("#Discounts_oepn").toggle(); } else { return false; }

        } else {

            $('#base_time').css('border-color', '');
            $('#cost_base_time').css('border-color', '');
            var base_time = $.trim($('#base_time').val());

            if (base_time == '') {
                $('#base_time').css('border-color', 'red');
                $('#base_time').focus();
                status = false;
            }
            var cost_base_time = $.trim($('#cost_base_time').val());
            if (cost_base_time == '') {
                $('#cost_base_time').css('border-color', 'red');
                $('#cost_base_time').focus();
                status = false;
            }
            if (status) { $("#Discounts_oepn").toggle(); } else { return false; }


        }

    }); /// end $('#Discounts').click( function()

    $('#postDiscounts').click(
        function () {
            if ($scope.addroutedata.length < 1) {
                $('#addRoutePopup').modal({ 'show': true });
                //alert("Please Create at least one route");
                return false;
            } else {
                $("#Discounts_show").toggle();
            }
        });

    var url = serverUrl + 'getbuyerdetails';
    $scope.getbuyer = function (url) {
        apiServices.getMethod(url).then(function (response) {

            $scope.sellerList = response;
            //console.log('buyerlisdddddddddddddddd',$scope.sellerList);
            setTimeout(function () {
                $("#sellerList").tokenInput($scope.sellerList, { propertyToSearch: 'full_name' });

                $("#token-input-sellerList").attr("placeholder", "Buyer Name (Auto Search)");
            }, 1000);
            //console.log("$scope.buyer::", $scope.sellerList);
        });

        
    };
    $scope.getbuyer(url);

    /*-----------------------------------------------------------------------------------------*/


    $scope.isSellerVisible = false;
    $scope.showHideBuyer = function (val) {
        if (val == 'private') {
            $scope.data.selectseledata = '';
            $scope.isSellerVisible = true;
        } else {

            $scope.isSellerVisible = false;
            $scope.data.selectseledata = '';
            $scope.dataSpot.visibleToSellers = [];
        }
    };
    /*****show hide form ***********/

    $scope.showHide = function (str) {
        //console.log(str);
        if (str == 1) {

            $scope.addroutedata = [];
            $scope.postDiscountdata = [];
            $scope.discountdata = [];
            $scope.data.vehicle_type = '';
            $scope.data.vehicle_type_wait = '';
            $scope.data.vehicle_over_dimension = '';
            $scope.data.valid_from_date = '';
            $scope.data.valid_to_date = '';
            $scope.data.helpers = '';
            $scope.data.volume_unit_extra = '';

            $scope.data.extracost_wait = '';
            $scope.data.dimension_volume = '';
            $scope.data.dimension_volume = '';
            $scope.data.labour_charge = '';
            $scope.data.addition_labour_charge = '';
            $scope.data.toll_charge = '';
            $scope.data.other = '';

            $('#id2').addClass('activeintra');
            $('#id1').removeClass('activeintra');
            $("#hour_vehicle_type").remove();
            $('#base_time').val('');
            // $('#cost_base_time').val('');
            $scope.postDiscountdata = '';
            // enable disable
            $("#city").prop('disabled', false);
            // $("#valid_from_date").prop('disabled', false);
            $("#valid_to_date").prop('disabled', false);

        } else if (str == 2) {

            $scope.addroutedata = [];
            $scope.postDiscountdata = [];
            $scope.discountdata = [];
            $scope.data.city = '';
            $scope.data.vehicle_type_h = '';
            $scope.data.vehicle_type_wait = '';
            $scope.data.vehicle_over_dimension = '';
            $scope.data.valid_from_date = '';
            $scope.data.valid_to_date = '';
            $scope.data.extracost_wait = '';
            $scope.data.dimension_volume = '';
            $scope.data.dimension_volume = '';
            $scope.data.labour_charge = '';
            $scope.data.helpers = '';
            $scope.data.volume_unit_extra = '';
            $scope.data.addition_labour_charge = '';
            $scope.data.toll_charge = '';
            $scope.data.other = '';
            $('#id1').addClass('activeintra');
            $('#id2').removeClass('activeintra');
            // $('#base_distance').val('');
            // $('#rate_base_distance').val('');
            $scope.postDiscountdata = '';
            // enable disable
            $("#city").prop('disabled', false);
            // $("#valid_from_date").prop('disabled', false);
            $("#valid_to_date").prop('disabled', false);


        }
    };
    /**********change one select box selected another **********/

    $scope.selectbox = function (arg) {

        $scope.data.vehicle_type_wait = arg;
        $scope.data.vehicle_over_dimension = arg;
    };
    /***********************************edit seller Post **********************/
    // var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
    if ($state.params.id != '' && $state.params.id != undefined && $state.params.id != null) {

        var url = serverUrl + 'seller-pre-discout';
        apiServices.prediscount(url + '/' + $state.params.id).then(function (response) {
            console.log('all', response.payload);
            console.log('assign_buyer', response.payload.id);
            $("#Accept").prop("checked", true);
            $scope.addroutedata = JSON.parse(response.payload.routedata);
            $scope.postDiscountdata = JSON.parse(response.payload.discount);
            $scope.data.accept = JSON.parse(response.payload.terms_cond);
            $("textarea#notes").val(response.payload.notes);
            //$scope.data.notes= response.payload.notes;
            $scope.data.post_type = response.payload.is_private_public;
            $scope.data.type = response.payload.rate_cart_type;


            $scope.editRoute($scope.addroutedata[0], 0);
            setTimeout(function () {
                $("#Discounts").trigger('click');
            }, 1000);
            setTimeout(function () {
                $("#postDiscounts").trigger('click');
            }, 1000);



        });
    } else { }
    // {
    //     'vehicle_type_h': $scope.new.vehicle_type_h,
    //     'base_time':$scope.new.base_time,
    //     'cost_base_time':$scope.new.cost_base_time,
    //     'houre_charge':$scope.new.houre_charge,
    //     'km_charge':$scope.new.km_charge
    // }
    $scope.addMultipleRate = function (data) {
        $scope.new = angular.copy(data);
        $scope.multipleRates.push($scope.new);
        console.log("addMultiple Rate ::", data);
    }
    $scope.isUpdate = false;
    $scope.editMultipleRate = function (value) {
        console.log("editMultipleRate", value);
        $scope.data = value;
        $scope.isUpdate = true;
    }

    $scope.updateMultipleRate = function (value) {
        $scope.multipleRates.splice(value.index, 1);
        $scope.multipleRates.push(angular.copy(value));
        $scope.isUpdate = false;
    }

    $scope.deleteMultipleRate = function (index) {
        $scope.multipleRates.splice(index, 1);
    }

    $scope.closeRoutePopup = function () {
        $("#addRoutePopup").modal("hide");
        setTimeout(function () {
            $state.go("seller-rate-card");
        }, 1000);
    }

    $scope.closeMyModalPopup = function () {
        $("#myModal").modal("hide");
        setTimeout(function () {
            $state.go("seller-post-list");
        }, 1000);
    }

    $scope.toTimeSlot = [];
    $scope.toTimeSlot = $scope.sellerTimeSlot;
     
   
    /** THIS IS FOR REMOVING TO TIME FROM PREVIOUS SELECTION */

    $scope.isDisabledToTimeSlot = true;
    $scope.getFromTime = function(fromTime) {  
        $scope.toTimeSlot = angular.copy($scope.sellerTimeSlot);  
        $scope.isDisabledToTimeSlot = false; 
        // get index of selected timeSlot
        let selectedIndex ;

        for (var key in sellerTimeSlot) {
            if (sellerTimeSlot.hasOwnProperty(key)) {
                if(sellerTimeSlot[key].id == fromTime) {
                    selectedIndex = key;
                }   

            }
        }
        $scope.$watch("from_to",function() {

        $("#from_to").selectpicker('refresh'); 
        },true);


        $scope.toTimeSlot.splice(0,selectedIndex); 


    }


    /** THIS IS FOR REMOVING TO TIME FROM PREVIOUS SELECTION */

    // Upload Excel data

    $scope.setTaskFiles = function (element, fileType) {
        $scope.$apply(function (scope) {
            console.log('files:', element.files);
            // Turn the FileList object into an Array
            $scope.taskFile = element.files[0]
        });
    };

    $scope.uploadFileRecord = function () {
        var fd = new FormData();
        fd.append("uploadFile", $scope.taskFile);//file.size > 1024*1024
        var filetype = "task"
        var url = serverUrl + 'intracity/uploadBulkData';
        apiServices.uploadFile(url,fd).then(function (response) { 
            if (response.isSuccessful == true) {
                $('#uploadModalBody').html('<p>File Uploaded Successfully</p>');
                $('#uploadMsgModal').modal('toggle');
                $scope.uploadExcel = true;
            } else {

                htm = '<p style="color:red;">File Upload Failed</p>';

                for(err of response.payload) {
                    htm += err+"<br/>";
                }

                $('#uploadModalBody').html(htm);
                $('#uploadMsgModal').modal('toggle');
                $scope.uploadExcel = false;
            }
        });
    };
}]);