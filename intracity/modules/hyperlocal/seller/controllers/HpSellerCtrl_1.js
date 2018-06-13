app.controller('HpSellerCtrl', ['$scope', '$http', 'config', 'apiHyperlocalServices', 'type_basis', 'discount', '$state', '$location', 'apiServices','$dataExchanger','trackings' ,function ($scope, $http, config, apiHyperlocalServices, type_basis, discount, $state, $location, apiServices,$dataExchanger,trackings) {
    $scope.searchFormSubmit = "Search";

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.servicetype = SERVICE_TYPE;
    $scope.materialtype = HYPERLOCAL_MATERIAL_TYPE;
    $scope.transitHour = HP_TRANSIT_HOUR;
    $scope.weight = HYPERLOCAL_WEIGHT;
    $scope.discount_list = discount.discount_type;
    $scope.servicetype = SERVICE_TYPE;
    $scope.post_Status = POST_STATUS;

    $scope.tracking_type = trackings.type;
    $scope.categories = hyerCatergories;
    // download sample excel file path
    $scope.excel_download_path = $location.host()+'/intracity-dev/intracity';
    // download sample excel file method
    $scope.downloadTemplate = function(){
        var anchor = angular.element('<a/>');
        anchor.attr({
            href: 'data:attachment/csv;charset=utf-8,',
            download: 'hyperlocal_seller_ratecard_sample.csv'
        })[0].click();        
    }
    
    
    // console.log( 'localhost :' + $location.host()+'/intracity-dev/intracity' );

    $scope.data = [];
    $scope.setting_data = {
        seller_spot_enquiries_related: true,
        seller_spot_enquiries_partly_related: false,
        seller_spot_enquiries_un_related: false,

        seller_spot_lead_related: true,
        seller_spot_lead_partly_related: false,
        seller_spot_lead_un_related: false,

        seller_term_enquiries_related: true,
        seller_term_enquiries_partly_related: false,
        seller_term_enquiries_un_related: false,

        seller_term_lead_related: true,
        seller_term_lead_partly_related: false,
        seller_term_lead_un_related: false,

        user_pk: Auth.getUserID(),
        user_id: Auth.getUserID(),
        user_type: 2, // flag ( 2 = 'seller', 1 = 'buyer' )
        role_id: 2,
        service_id: _HYPERLOCAL_,
        page_type: 88,
        setting_type: 0, // flag ( 0 = 'post_master_setting', 1 = 'notification_setting' )
        updated_by: Auth.getUserID()
    };
    
    var specialKeys = new Array();
    specialKeys.push(8); //Backspace
    specialKeys.push(9); //Tab
    specialKeys.push(46); //Delete
    specialKeys.push(36); //Home
    specialKeys.push(35); //End
    specialKeys.push(37); //Left
    specialKeys.push(39); //Right

    $('.cityLocation').keypress(function (e) {
        var keyCode = e.keyCode == 0 ? e.charCode : e.keyCode;
        var ret = ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || 
            (keyCode >= 97 && keyCode <= 122) || (specialKeys.indexOf(e.keyCode) != -1 && e.charCode != e.keyCode));
          return ret;
    });

    $scope.formData = {
        title: '',
        service_type: '',
        city: '',
        fromdate: '',
        todate: '',
        category: '',
        line_items: '',
        base_price: '',
        fragile_addtnl_charges: '',
        distance_included: '',
        rate_per_extra_km: '',
        weight_included: '',
        rate_pre_extra_kg: '',
        num_of_parcel: '',
        addtn_cost_per_ext_parcel: '',
        time_for_selected_services: '',
        extra_time_per_km: '',
        pricing: '',
        excel_data: {},
        selectseledata: [],
    };

    $scope.formError = {
        lineItems : false,
        basePrice: false,
        fragileAddtnlCharges: false,
        ratePerExtraKm: false,
        ratePerExtkg: false,
        numOfParcel: false,
        addtnCostExtParcel: false,
        addtnCostExtParcel: false,
        sellerListPH: false,
    };


    $scope.title1=$state.params.id;
    let title1 = $scope.title1 !=undefined?$scope.title1.split("-").join(" "):'';
    $scope.filterData = {
        category: [],
        service: [],
        postStatus: [],       
        date: [],
        sellerName: [],
        serviceId: _HYPERLOCAL_,
        is_private_public:0,
        title:title1,
        offset:0,
        totalRow:10,
       
       
    };
   // console.log('xxxxx',$scope.filterData);
    /*********inbound detials ********************/
    //console.log("PPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPP",$scope.filterData.title);
       
    $scope.inboundResult = function () {        
        var url = serverUrl + 'hyperlocal/hp-get-seller-inbound-details';
        $scope.filterData.title = title1;
        apiHyperlocalServices.BuyerInboundDetails(url, $scope.filterData).then(function (response) {
            $scope.filterData.totalRow = response.data.length;
            //console.log('row',response);
            $scope.listdata = response.data;
            for (let key in $scope.listdata) {
                $scope.listdata[key].title=$scope.filterData.title;                
            } 
            
            //console.log("$scope.listdata",$scope.listdata);
        })
    }

    $scope.$watch('filterData', function (newValue, oldValue) {      
        $scope.inboundResult();   
    }, true);



    $scope.save_setting = function () {

        $http({
            url: serverUrl + 'hyperlocal/seller-setting-save',
            method: 'POST',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: $scope.setting_data,
        }).then(function success(response) {
            //alert(response);
            //alert(response.seller_spot_enquiries_related);
            //console.log('Ajax',response.seller_spot_enquiries_related);  
            //$scope.spotsList = response.data.data;          
            // if (response.isSuccessfull) {
            //    $scope.spotsList = response.data;
            //     $('#loaderGif').hide();
            // }
        }).catch(function (error) {
            apiServices.errorHandeler(error);
            $('#loaderGif').hide();
        });
    }

    $scope.type_basis = type_basis.type_basis;

    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiHyperlocalServices.city(url).then(function (response) {
            $scope.cities = response;

        });
    }
    //  Get city of intracity 
    $scope.getCity(url);


    /*** Get Location By Id ***/
    $scope.onSelect = function (data) {
        // 
        console.log(data);
    }
    $scope.data.city_id = { id: '' };
    $scope.onSelect = function (data) {
        //console.log("City Id::", parseInt(data.id));
        var city_id = parseInt(data.id);
        if (typeof (city_id) != NaN || city_id != '') {
            $scope.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id);
            // $scope.getListBuyerAccordingFilter(url, city_id);
        }
    }


    $scope.getLocationByCity = function (url) {
        apiHyperlocalServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            //console.log('Locations:', $scope.locations);
        });
    }
    /*** Get Location By Id ***/


    $scope.deleteDiscount = function (x) {

        $scope.discountLists.splice(x, 1);
        // console.log("aish:",x);
    }

    $scope.editDiscount = function (x, index) {
        //console.log(index);

        $scope.formData = x;
        $scope.pdid = index;
        $('#fdbuyer_discount').focus();
        //console.log("pdid:",$scope.pdid);
        //console.log("formdata:",$scope.formData);
    }
     
    // $scope.categories = hyerCatergories;

    
   

    $scope.isValidated = function () {
        var isValidated = true;
        var title = $.trim($('#title').val());
        var product_category = $.trim($('#product_category').val());
        var returning1 = $.trim($('#returning1').val());
        var departing = $.trim($('#departing').val());
        var line_items = $.trim($('#line_items').val());
        var service_type = $.trim($('#service_type').val());
        var city = $.trim($('#city').val());
        var base_price = $.trim($('#base_price').val());
        var distance_included = $.trim($('#distance_included').val());
        var rate_per_ext_km = $.trim($('#rate_per_ext_km').val());
        var weight_included = $.trim($('#weight_included').val());
        var rate_per_ext_kg = $.trim($('#rate_per_ext_kg').val());
        var seller = $.trim($('#sellerList').val());

        if ($scope.formData.title == '') {
            $('#title').css('border-bottom', '1px solid red');
            $('#title').focus();
            isValidated = false;
        }else{
            $('#title').css('border-bottom', '');
        }
        
        if ($scope.formData.category == '' || $scope.formData.category == null) {
            $('#product').css('border-bottom', '1px solid red');
            isValidated = false;
        }else{
            $('#product').css('border-bottom', '');
        }
        
        if ($scope.formData.fromdate == '') {
            $('#departing').css('border-bottom', '1px solid red');
            $('#departing').focus();
            isValidated = false;
        }else{
            $('#departing').css('border-bottom', '');
        }
        
        if ($scope.formData.todate == '') {
            $('#returning1').css('border-bottom', '1px solid red');
            $('#returning1').focus();
            isValidated = false;
        }else{
            $('#returning1').css('border-bottom', '');
        }

        if ($scope.formData.city == '') {
            $('#city').css('border-bottom', '1px solid red');
            $('#city').focus();
            isValidated = false;
        }else{
            $('#city').css('border-bottom', '');
        }
        
        if ($scope.formData.line_items == '' || typeof($scope.formData.line_items) == 'undefined') {
            $('#line_items').css('border-bottom', '1px solid red');
            $('#line_items').focus();
            $scope.formError.lineItems = false;
            isValidated = false;
        }else{
            if($scope.formData.line_items == 0){
               $scope.formError.lineItems = true;
               $('#line_items').css('border-bottom', '1px solid red');
               $('#line_items').focus();
               isValidated = false;
            }else{
               $scope.formError.lineItems = false;
            }
            $('#line_items').css('border-bottom', '');
        }

        if ($scope.formData.service_type == '' || $scope.formData.service_type == null) {
            $('#service').css('border-bottom', '1px solid red');
            isValidated = false;
        }else{
            $('#service').css('border-bottom', '');
        }

        if ($scope.formData.base_price == ''|| typeof($scope.formData.base_price) == 'undefined') {
            $('#base_price').css('border-bottom', '1px solid red');
            $('#base_price').focus();
            $scope.formError.basePrice = false;
            isValidated = false;
        }else{
            if($scope.formData.base_price == 0){
               $scope.formError.basePrice = true;
               $('#base_price').css('border-bottom', '1px solid red');
               $('#base_price').focus();
               isValidated = false;
            }else{
               $scope.formError.basePrice = false;
            }
            $('#base_price').css('border-bottom', '');
        }
        
        if ($scope.formData.fragile_addtnl_charges == ''|| typeof($scope.formData.fragile_addtnl_charges) == 'undefined') {
            $('#fragile').css('border-bottom', '1px solid red');
            $scope.formError.fragileAddtnlCharges = false;
            isValidated = false;
        }else{
            if($scope.formData.fragile_addtnl_charges == 0){
               $scope.formError.fragileAddtnlCharges = true;
               $('#fragile').css('border-bottom', '1px solid red');
               $('#fragile').focus();
               isValidated = false;
            }else{
               $scope.formError.fragileAddtnlCharges = false;
            }
            $('#fragile').css('border-bottom', '');
        }

        if ($scope.formData.distance_included == '' || typeof($scope.formData.distance_included) == 'undefined') {
            $('#distance_included').css('border-bottom', '1px solid red');
            $('#distance_included').focus();
            $scope.formError.distanceIncluded = false;
            isValidated = false;
        }else{
            if($scope.formData.distance_included == 0){
               $scope.formError.distanceIncluded = true;
               $('#distance_included').css('border-bottom', '1px solid red');
               $('#distance_included').focus();
               isValidated = false;
            }else{
               $scope.formError.distanceIncluded = false;
            }
            $('#distance_included').css('border-bottom', '');
        }

        if ($scope.formData.rate_per_extra_km == '' || typeof($scope.formData.rate_per_extra_km) == 'undefined') {
            $('#rate_per_ext_km').css('border-bottom', '1px solid red');
            $('#rate_per_ext_km').focus();
            $scope.formError.ratePerExtraKm = false;
            isValidated = false;
        }else{
            if($scope.formData.rate_per_extra_km == 0){
               $scope.formError.ratePerExtraKm = true;
               $('#rate_per_ext_km').css('border-bottom', '1px solid red');
               $('#rate_per_ext_km').focus();
               isValidated = false;
            }else{
               $scope.formError.ratePerExtraKm = false;
            }
            $('#rate_per_ext_km').css('border-bottom', '');
        }

        if ($scope.formData.weight_included == '' || typeof($scope.formData.weight_included) == 'undefined') {
            $('#weight_included').css('border-bottom', '1px solid red');
            $('#weight_included').focus();
            $scope.formError.weightIncluded = false;
            isValidated = false;
        }else{
            if($scope.formData.weight_included == 0){
               $scope.formError.weightIncluded = true;
               $('#weight_included').css('border-bottom', '1px solid red');
               $('#weight_included').focus();
               $scope.formError.weightIncluded = true;
               isValidated = false;
            }else{
               $scope.formError.weightIncluded = false;
            }
            $('#weight_included').css('border-bottom', '');
        }

        if ($scope.formData.rate_pre_extra_kg == '' || typeof($scope.formData.rate_pre_extra_kg) == 'undefined') {
            $('#rate_per_ext_kg').css('border-bottom', '1px solid red');
            $('#rate_per_ext_kg').focus();
            $scope.formError.ratePerExtkg = false;
            isValidated = false;
        }else{
            if($scope.formData.rate_pre_extra_kg == 0){
               $scope.formError.ratePerExtkg = true;
               $('#rate_per_ext_kg').css('border-bottom', '1px solid red');
               $('#rate_per_ext_kg').focus();
               isValidated = false;
            }else{
               $scope.formError.ratePerExtkg = false;
            }
            $('#rate_per_ext_kg').css('border-bottom', '');
        } 

        if ($scope.formData.num_of_parcel == '' || typeof($scope.formData.num_of_parcel) == 'undefined') {
            $('#no_of_parcel').css('border-bottom', '1px solid red');
            $('#no_of_parcel').focus();
            $scope.formError.numOfParcel = false;
            isValidated = false;
        }else{
            if($scope.formData.num_of_parcel == 0){
               $scope.formError.numOfParcel = true;
               $('#no_of_parcel').css('border-bottom', '1px solid red');
               $('#no_of_parcel').focus();
               isValidated = false;
            }else{
               $scope.formError.numOfParcel = false;
            }
            $('#no_of_parcel').css('border-bottom', '');
        } 

        if ($scope.formData.addtn_cost_per_ext_parcel == '' || typeof($scope.formData.addtn_cost_per_ext_parcel) == 'undefined') {
            $('#addtn_cost_per_ext_parcel').css('border-bottom', '1px solid red');
            $('#addtn_cost_per_ext_parcel').focus();
            $scope.formError.addtnCostExtParcel = false;
            isValidated = false;
        }else{
            if($scope.formData.addtn_cost_per_ext_parcel == 0){
               $scope.formError.addtnCostExtParcel = true;
               $('#addtn_cost_per_ext_parcel').css('border-bottom', '1px solid red');
               $('#addtn_cost_per_ext_parcel').focus();
               isValidated = false;
            }else{
               $scope.formError.addtnCostExtParcel = false;
            }
            $('#addtn_cost_per_ext_parcel').css('border-bottom', '');
        } 

        if ($scope.formData.pricing == '' || typeof($scope.formData.pricing) == 'undefined') {
            $('#pricing').css('border-bottom', '1px solid red');
            $('#pricing').focus();
            $scope.formError.addtnPricing = false;
            isValidated = false;
        }else{
            if($scope.formData.pricing == 0){
               $scope.formError.addtnPricing = true;
               $('#pricing').css('border-bottom', '1px solid red');
               $('#pricing').focus();
               isValidated = false;
            }else{
               $scope.formError.addtnPricing = false;
            }
            $('#pricing').css('border-bottom', '');
        } 

        if($('#PostPrivate').is(':checked')) {
            if (seller == '') {
                $('#seller').css('border-bottom', '1px solid red');
                $('#seller').focus();
                isValidated = false;
                alert("Please select atleast one buyer");
                return false;
            }
        }

        return isValidated;
    }
   
    $scope.isSellerVisible = false;
    $scope.showHideBuyer = function (val) {
        if (val == 'private') {
            $scope.isSellerVisible = true;
        } else {
            $scope.isSellerVisible = false;
            $scope.formError.sellerListPH = false;
            $scope.sellerList = [];
            //console.log('Lists',$scope.sellerList);
        }
    };

    var url = serverUrl + 'getbuyerdetails';
    $scope.getbuyer = function (url) {
        apiHyperlocalServices.getMethod(url).then(function (response) {

            $scope.sellerList = response;
            setTimeout(function () {
                $("#sellerList").tokenInput($scope.sellerList, { propertyToSearch: 'full_name' });

                $("#token-input-sellerList").attr("placeholder", "Buyer Name (Auto Search)");
            }, 1000);
        });
    }
    $scope.getbuyer(url);


    /*** For Inserting data seller rate card */
    $scope.createPost = function (formData, isOpen) {
        var valid = $scope.isValidated();
        //alert($('#PostPrivate').is(':checked'));
        if(valid){
            if ($('#PostPrivate').is(':checked')) {
                var seller = $.trim($('#sellerList').val());
                if (seller == '') {
                    $('#seller').css('border-bottom', '1px solid red');
                    $('#seller').focus();
                    alert("Please select atleast one buyer");
                    return false;
                }
            }
            if (!$('#Accept').is(':checked')) {
                alert('Accept Terms & Conditions');
                return false;
            }
           
           

            $scope.finalData = {
                    'rateCartData': $scope.formData,
                    'discountData': $scope.discountLists,
                    'isOpen': isOpen
            };

       
            var url = serverUrl + 'hyperlocal/seller-rate-card-post';
            var data = formData;

            apiHyperlocalServices.hpCreatePost(url, JSON.stringify($scope.finalData)).then(function (response) {
                var tran = response.post_transaction_id;
                var postStatus = response.post_status;

                if(postStatus == 0){
                    sessionStorage.setItem("post-status",postStatus);
                    $state.go('hp-seller-list');

                } else if ($("#Accept").prop("checked", true)) {
                    str = "Your request for post has been successfully posted to the relevant buyers.Your transacton id is " + tran + " You would be getting the enquiries from the buyers online.";
                    $("#responsetext").html(str);
                    $(".waitText button").attr("data-type", "hpsellerPost");
                    sessionStorage.setItem("post-status",1);
                    $('#myRateCardModal').modal({ 'show': true });
                }
            });

        }
        
    }
    /*** For Inserting data seller rate card */

    $scope.discountLists = [];
    $scope.addDiscount = function (fddata) {
        isValidated = true;
        $('#fdbuyer_discount').css('border-color', '');
        $('#fdcredit_day').css('border-color', '');
        $('#discount_type').css('border-color', '');
        $('#name').css('border-color', '');


        var fdbuyer_discount = $scope.formData.buyer_discount;
        var fdcredit_day = $scope.formData.credit_day;
        var discount_type = $scope.formData.discount_type;
        var name = $scope.formData.buyer;
        //console.log('gurudev:', $scope.formData.discountType);
        //console.log('CREDIT', fdbuyer_discount);
        if (fdbuyer_discount == '' || fdbuyer_discount == undefined) {
            $('#fdbuyer_discount').css('border-color', 'red');
            $('#fdbuyer_discount').focus();
            isValidated = false;
        }


        if (fdcredit_day == '' || fdcredit_day == undefined) {
            $('#fdcredit_day').css('border-color', 'red');
            $('#fdcredit_day').focus();
            isValidated = false;
        }
        if (discount_type == '' || discount_type == undefined) {
            $('#discount_type').css('border-color', 'red');
            $('#discount_type').focus();
            isValidated = false;
        }
        if ($scope.formData.discountType == '2') {
            if (name == '' || name == undefined) {
                $('#name').css('border-color', 'red');
                $('#name').focus();
                isValidated = false;
            }
        }

        if (isValidated) {
            $scope.discountLists.push(angular.copy(fddata));
            //console.log('Discount Data:', fddata);
            $("#table_heading").show();
        }

        else {
            alert("Please fill all mandatory(*) fields of discount")
        }


        $scope.formData.credit_day = ""
        $scope.formData.buyer_discount = ""

    }


    //$scope.showDiscountType = false;
    $scope.showDiscountType = function (type) {
        //alert(type);
        //console.log(type);
        $scope.formData.buyer_discount = '';
        $scope.formData.credit_day = '';

        //$scope.showDiscountType = true;

    }

    var url = serverUrl + 'getbuyerdetails';
    $scope.getbuyer = function (url) {
        apiHyperlocalServices.getMethod(url).then(function (response) {
            response.push({ 'id': "-1", 'full_name': "All" });
            $scope.users = response;
            //console.log('Buyersssssss:', $scope.users);
        });
    }
    $scope.getbuyer(url);


    /**Seller post List */
    var url = serverUrl + 'hyperlocal/seller-list';
    $scope.getSellerPostList = function (url) {
        apiHyperlocalServices.sellerPost(url).then(function (response) {
            $scope.results = response;

        });
    }
    $scope.getSellerPostList(url);

    $scope.closeSellerCard = function () {
        $('#myRateCardModal').modal('hide');
        $('.modal-backdrop').hide();
        $('body').removeClass('modal-open');
        $location.url('/hp-seller-list');
    }
    /**Seller post List */


    /**Seller post count */
    var url = serverUrl + 'hyperlocal/seller-list-counts';
    $scope.getSellerPostCount = function (url) {
        apiHyperlocalServices.sellerCounts(url).then(function (response) {
            $scope.postCount = response.payload;
        });
    }
    $scope.getSellerPostCount(url);

    /************************For Active Class On Click Filters************/
    $scope.changeClass = function (type) {
        $('.all,.public,.private,.Inbound,.Outbound').removeClass('search_nav_active');
        $('.' + type.post_type).addClass("search_nav_active");
        $('.' + type.bound).addClass("search_nav_active");

    };
    /************************For Active Class On Click Filters************/


    /**Seller post count */
    $scope.selectdata = { post_type: 'public', bound: 'Outbound' };

    $scope.getInboundData = function(data) {
        var url = serverUrl + 'hyperlocal/hp-get-all-records-inbound';
        apiHyperlocalServices.InboundFilter(url, data).then(function (response) {
            $scope.spotsList = response.data;
            $scope.spotsList.totalLeadCount = 0;
            $scope.spotsList.forEach(function(element) {
                $scope.spotsList.totalLeadCount = $scope.spotsList.totalLeadCount+ element.data.length;
            }, this);
        });
        $scope.changeClass($scope.selectdata);
    }

    $scope.search_filter = function (type) {

        switch (type) {
            case 'all':
                $scope.selectdata.post_type = 'all'
                break;
            case 'public':
                $scope.selectdata.post_type = 'public'
                break;
            case 'private':
                $scope.selectdata.post_type = 'private'
                break;
            case 'Inbound':
                $scope.selectdata.bound = 'Inbound'
                break;
            case 'Outbound':
                $scope.selectdata.bound = 'Outbound'
                break;
            default:
                $scope.selectdata = { post_type: 'public', bound: 'Outbound' };
        }


        $scope.filterdata = type;
        if ($scope.selectdata.bound == 'Inbound') {
            $scope.getInboundData($scope.selectdata);
        }

        if ($scope.selectdata.bound == 'Outbound') {
            $scope.show = '';
            var url = serverUrl + 'hyperlocal/seller-search-filters';
            apiHyperlocalServices.sellerListAcdngFilter(url, $scope.selectdata).then(function (response) {
                $scope.results = response.payload;
                $scope.changeClass($scope.selectdata);
            });
        }
    }
    $scope.getInboundData($scope.selectdata);
    $scope.search_filter('all');

    /**
     * filter accoriding city
     */
    /************************city filter **********/
    $scope.Newcity = [];
    $scope.NewCityFilter = function (item) {
        if (item != null) {
            if ($scope.Newcity.length == 0)
                return item;
            else {

                return $scope.Newcity.indexOf(item.city_id) !== -1;
            }
        }

    };
    // to use 1
    $scope.selectCity = function (operator) {
        var num = operator.toString();
        var idx = $scope.Newcity.indexOf(num);
        if (idx > -1)
            $scope.Newcity.splice(idx, 1);
        else
            $scope.Newcity.push(num);
        if ($scope != null)
            $scope.Newcity = $scope.Newcity;

    };

    /**
     * filter accoriding city
     */

    /************************ Vehicle type filter **********/
    $scope.NewServiceType = [];
    $scope.NewServiceTypeFilter = function (item) {
        if (item != null) {
            if ($scope.NewServiceType.length == 0)
                return item;
            else {

                return $scope.NewServiceType.indexOf(item.service_type) !== -1;
            }
        }

    };
    // to do 2
    $scope.selectNewServiceType = function (operator) {

        var num = operator.toString();
        var idx = $scope.NewServiceType.indexOf(num);
        if (idx > -1)
            $scope.NewServiceType.splice(idx, 1);
        else
            $scope.NewServiceType.push(num);
        if ($scope != null)
            $scope.NewServiceType = $scope.NewServiceType;

    };

    /************************Buyer filter **********/

    $scope.NewBuyers = [];
    $scope.NewBuyersFilter = function (item) {

        if (item != null) {
            if ($scope.NewBuyers.length == 0)
                return item;
            else {
                //console.log('FILTERSSSSSS', item.assign_buyer);
                return $scope.NewBuyers.indexOf(item.assign_buyer) !== -1;

            }
        }

    };
    
    $scope.clearAll = function (){
        
        $scope.NewBuyers.length = 0;
        $('.sellers_det .admin_input').find('input:checkbox').prop('checked', false);
        $scope.NewServiceType.length = 0;
        $scope.Newcity.length = 0;
        
        $scope.data.city = '';
        $scope.data.service_type = '';
        $scope.data.name = '';
    }
    
    // to do 3
    $scope.selectBuyer = function (operator) {
         
         console.log('operator', operator);
         console.log('NewBuyers', $scope.NewBuyers);         
         
        var idx = $scope.NewBuyers.indexOf(operator);
        console.log('indexof', idx);
        if (idx > -1)
            $scope.NewBuyers.splice(idx, 1);
        else
            $scope.NewBuyers.push(operator);
        if ($scope != null)
            $scope.NewBuyers = $scope.NewBuyers;

    };
    /************************filter for post category**********/

    $scope.NewPostCategory = [];
    $scope.NewCategoryFilter = function (item) {

        if (item != null) {
            if ($scope.NewPostCategory.length == 0)
                return item;
            else {
                return $scope.NewPostCategory.indexOf(item.category.toString()) !== -1;
            }
        }

    };
    $scope.selectNewCategory = function (operator) {

        var num = operator.toString();
        var idx = $scope.NewPostCategory.indexOf(num);
        if (idx > -1)
            $scope.NewPostCategory.splice(idx, 1);
        else
            $scope.NewPostCategory.push(num);
        if ($scope != null)
            $scope.NewPostCategory = $scope.NewPostCategory;

    };


    /**
     * 
     * Filter inbound with ajax
     * 
     */

    $scope.filterData = {
        category: [],
        service: [],
        postStatus: [],
        date: [],
        sellerName: [],
        serviceId: _HYPERLOCAL_,
        post_type: $scope.selectdata.post_type
    };
    
    $scope.getData = function (value) {

        console.log('shiv',value);
        $dataExchanger.request.data = value;
    }
    

    $scope.addFilterData = function (arr, value) {
        console.log(arr);
        var index = arr.indexOf(value);
        if (index == -1) {
            arr.push(value);
        } else {
            arr.splice(index, 1);
        }

        console.log($scope.filterData);
    };

    // $scope.$watch('filterData', function (newValue, oldValue) {
    //     // $('#loaderGif').show();
    //     $http({
    //         url: serverUrl + 'hyperlocal/hp-get-all-records-inbound/inbound',
    //         method: 'POST',
    //         headers: {
    //             'authorization': 'Bearer ' + localStorage.getItem("access_token")
    //         },
    //         data: $scope.filterData,
    //     }).then(function success(response) {

    //         console.log('Ajax', response.data.data);
    //         $scope.spotsList = response.data.data;
    //         $scope.postDraft =  +sessionStorage.getItem("post-status");
    //         // if (response.isSuccessfull) {
    //         //    $scope.spotsList = response.data;
    //         //     $('#loaderGif').hide();
    //         // }
    //     }).catch(function (error) {
    //         apiServices.errorHandeler(error);
    //         $('#loaderGif').hide();
    //     });
    // }, true);
    $scope.paymentMethod = function (key,listKey, method) {
        if($scope.listdata[listKey].get_all_route[key].payment_method==method){
            $scope.listdata[listKey].get_all_route[key].payment_method = '';

        }else {
            $scope.listdata[listKey].get_all_route[key].payment_method = method;
        }
    }

    $scope.validated = function(listKey,key){
        var valid = true;
        var data = $scope.listdata[listKey].get_all_route[key];
        console.log("data",data);
        if(data.tracking_type==""||data.tracking_type==null){
          data.tracking_type_error = true;
          valid=false;
        }else{
          data.tracking_type_error = false;
        }
 
        if(data.payment_term==""||data.payment_term==null){
          data.payment_term_error = true;
          valid=false;
        }else{
          data.payment_term_error = false;
        }
 
        if(data.payment_term=='CREDIT'&&(data.credit_days==""||data.credit_days==null)){
          data.credit_days_error = true;
          valid=false;
        }else{
          data.credit_days_error = false;
        }
 
        if(data.payment_method==""||data.payment_method==null){
          data.payment_method_error = true;
          valid=false;
        }else{
          data.payment_method_error = false;
        }
        return valid;
      };
 
      $scope.submitFinalQuotation = function(key,listKey,status) {
         var finalQuote = $scope.listdata[listKey].get_all_route[key].quote;
         console.log(finalQuote);
         let url = serverUrl+'sellerQuoteAction';
         let params = {
             sellerFinalQuotePrice : finalQuote.sellerFinalQuotePrice,
             sellerFinalTransitDay: '',
             action: status,
             id: finalQuote.id
         };
 
         apiServices.postMethod(url,params).then(response => {
             console.log(response);
             location.reload();
         }).catch();
         
         console.log(params);
         console.log(finalQuote);
         
     }

     $scope.sellerQuoteAction = function(key,listKey,action) {
        
        console.log($scope.listdata[listKey].get_all_route[key].quote);
        let quote =$scope.listdata[listKey].get_all_route[key].quote;
        
        let url = serverUrl+'sellerQuoteAction';
        let data = {
            action: action,
            id: quote.id
        }
        apiServices.postMethod(url,data).then(response => {
            console.log(response);
            location.reload();
        }).catch();
    }

    $scope.submitQuotation = function(key,listKey){
        if($scope.validated(listKey,key)){
           var data = $scope.getQuoteData(listKey,key);
           $http({
             method: 'POST',
             url: config.serverUrl+'seller-quote-submission',
             headers: {
               'authorization': 'Bearer ' + localStorage.getItem("access_token")
             },
             data: data,
           }).then(function(response){
            $scope.listdata[listKey].get_all_route[key].quote = response.data.intra_hp_post_quotation;
           }, function(response){
             console.log(response);
           });
        }
      };

      $scope.getQuoteData = function(listKey,key){
        var data = $scope.listdata[listKey].get_all_route[key];
        console.log(data);
        var paymentMethods = ['', 'NEFT_RTGS', 'CREDIT_CARD', 'DEBIT_CARD'];
        var formData = {
          route_id: data.id,
          post_id: data.fk_buyer_seller_post_id,
          buyer_id: Auth.getUserID(),
          lkp_service_id: _HYPERLOCAL_,
          quotation_type: data.price_type,
          transit_day: data.transit_days==''?'':data.transit_days,
          tracking_type: data.tracking_type==''? 2:data.tracking_type,
          payment_term: data.payment_term,
          credit_days: data.credit_days,
          firm_price: data.price_type == 2? data.firm_price: data.quotedPrice,
         
          payment_method: paymentMethods.indexOf(data.payment_method)
        };
        
        console.log("formData::",formData);
        
        return formData;
      }; 
      
        $scope.postExcelData = function() {  
            
            console.log(serverUrl+'hyperlocal/sellerExcelUpload');
            console.log('$scope.formData.excel_data' + $scope.formData.excel_data);
            
            let url = serverUrl+'hyperlocal/sellerExcelUpload';
            var data = {
                action: null,
                id: null,
                csv: $scope.formData.excel_data
            };           
            apiServices.postMethodExcelUpload(url, data).then(response => {
                console.log(response);
            }).catch();
        };
        
        $scope.setTaskFiles = function (element, fileType) {
            $scope.$apply(function (scope) {
                console.log('files:', element.files);
                // Turn the FileList object into an Array
                $scope.taskFile = element.files[0]

            });
        };  
        
        $scope.uploadExcel = false;
        $scope.uploadFileRecord = function () {
            $('.loaderGif').show();
            var fd = new FormData();
            fd.append("uploadFile", $scope.taskFile);//file.size > 1024*1024

            var filetype = "task"
            
//            let url = serverUrl+'hyperlocal/sellerExcelUpload';
//            var data = {
//                action: null,
//                id: null,
//                csv: $scope.formData.excel_data
//            };           
//            apiServices.postMethodExcelUpload(url, data).then(response => {
//                console.log(response);
//            }).catch();
            
            
            
            apiServices.uploadFile(fd).then(function (response) {
                console.log(response)
                $('.loaderGif').hide();
                if (response.isSuccessful == true) {
                    $('#uploadModalBody').html('<p>File Uploaded Successfully</p>');
                    $('#uploadMsgModal').modal('toggle');
                    $scope.populateRateCardXLS(response);
                    $scope.uploadExcel = true;
                } else {
                    /*$('#uploadModalBody').html('<p style="color:red;">File Upload Failed</p>');
                    $('#uploadMsgModal').modal('toggle');*/
                    $scope.uploadExcel = false;
                    $scope.validationMsg = response
                    $('#generateErrorMessage').modal('toggle');
                }
            });

        };         
        
        

}]);
