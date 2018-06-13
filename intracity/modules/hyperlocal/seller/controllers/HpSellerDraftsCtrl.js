app.controller('HpSellerDraftsCtrl', ['$scope', '$http', 'config', 'apiHyperlocalServices', 'type_basis', 'discount', '$state', '$location', 'apiServices','$dataExchanger','trackings' ,function ($scope, $http, config, apiHyperlocalServices, type_basis, discount, $state, $location, apiServices,$dataExchanger,trackings) {
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
     /*   var anchor = angular.element('<a/>');
        anchor.attr({
            href: 'data:attachment/csv;charset=utf-8,',
            download: 'SellerRateCardWithDiscount.xlsx'
        })[0].click();*/        
    }
    
    $scope.postdetails = function () {
         $http({
          method: 'POST',
          url: config.serverUrl + 'hyperlocal/seller-post-draft-detail',
          headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
          },
          data: {'id': $state.params.id},
        }).then(function(response){
          $scope.postDetails = response.data.payload;
          console.log('Post Details',$scope.postDetails);
                
          $scope.formData.title = $scope.postDetails[0].title;
          $scope.formData.service_type = parseInt($scope.postDetails[0].service_type);

          $scope.formData.city_id = $scope.postDetails[0].hp_sellercity.id;
          $scope.formData.city = $scope.postDetails[0].hp_sellercity.city_name;

          console.log('Cities',$scope.cities);
          console.log('Category',$scope.categories);
          console.log('Service Type',$scope.servicetype);
          console.log('Transit Hour',$scope.transitHour);

          $from_date = ($scope.postDetails[0].from_date).split("-");
          $scope.formData.fromdate = $from_date[2]+'/'+$from_date[1]+'/'+$from_date[0];

          $to_date = ($scope.postDetails[0].to_date).split("-");
          $scope.formData.todate = $to_date[2]+'/'+$to_date[1]+'/'+$to_date[0];

          if($scope.postDetails[0].discount.length){
            $scope.discountLists = JSON.parse($scope.postDetails[0].discount);
          }
          
          $scope.formData.category = parseInt($scope.postDetails[0].product_category);
          $scope.formData.line_items = $scope.postDetails[0].line_items;
          $scope.formData.base_price = $scope.postDetails[0].base_price;
          $scope.formData.fragile_addtnl_charges = $scope.postDetails[0].additional_charges;
          $scope.formData.distance_included = $scope.postDetails[0].dist_included_per_base;

          $scope.formData.rate_per_extra_km = $scope.postDetails[0].rate_per_extra_kms;
          $scope.formData.weight_included = $scope.postDetails[0].weight_included;
          $scope.formData.rate_pre_extra_kg = $scope.postDetails[0].rate_per_extra_kgs;
          $scope.formData.num_of_parcel = $scope.postDetails[0].num_parcels_included;
          $scope.formData.addtn_cost_per_ext_parcel = $scope.postDetails[0].additional_cost_per_ext_parcel;

          $scope.formData.time_for_selected_services = $scope.postDetails[0].time_for_selected_service;
          $scope.formData.extra_time_per_km = $scope.postDetails[0].extra_time_per_km;
          $scope.formData.pricing = $scope.postDetails[0].pricing_service_types;

          $scope.formData.post_type = $scope.postDetails[0].is_private_public;
          $scope.formData.terms_condition = parseInt($scope.postDetails[0].terms_cond);
          
        }, function(response){

        });
    };
    $scope.postdetails();

    $scope.getCity = function (id) {
       $http({
            method: 'POST',
            url: serverUrl + 'hyperlocal/getLocationCity',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            }
        }).then(function success(response) {
            if (response.data) {
                $scope.vehicleTypes = response.data.data;
            }
        }, function error(response) {
            //
        });
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


    $scope.onSelect = function($item, $model, $label){
        $scope.formData.city_id = $item.id;
        console.log('City Id',$item);
    };
    
    $scope.deleteDiscount = function (x) {
        $scope.discountLists.splice(x, 1);
    }

    $scope.editDiscount = function (x,index) {
        console.log('Discount Values',x);
        $scope.fddata = x;
        $scope.discountLists.splice(index,1);
    }

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
        console.log('Category',$scope.formData.category);
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
                'discountData': JSON.stringify($scope.discountLists),
                'id': $state.params.id,
                'isOpen': isOpen
            };

       
            var url = serverUrl + 'hyperlocal/seller-rate-card-drafts-post';
            var data = formData;

            apiHyperlocalServices.hpCreatePost(url, JSON.stringify($scope.finalData)).then(function (response) {
                var tran = response.post_transaction_id;
                var postStatus = response.post_status;

                if(postStatus == 0){
                    +sessionStorage.setItem('postDraft',true);
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


        var fdbuyer_discount = $scope.fddata.buyer_discount;
        var fdcredit_day = $scope.fddata.credit_day;
        var discount_type = $scope.fddata.discount_type;
        var name = $scope.fddata.buyer;
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
        if ($scope.fddata.discountType == '2') {
            if (name == '' || name == undefined) {
                $('#name').css('border-color', 'red');
                $('#name').focus();
                isValidated = false;
            }
        }

        if (isValidated) {
            $scope.discountLists.push(angular.copy(fddata));
        } else {
            alert("Please fill all mandatory(*) fields of discount")
        }


        $scope.fddata.credit_day = ""
        $scope.fddata.buyer_discount = ""

    }


    //$scope.showDiscountType = false;
    $scope.showDiscountType = function (type) {
        $scope.fddata.buyer_discount = '';
        $scope.fddata.credit_day = '';
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
            
            let url = serverUrl+'hyperlocal/sellerExcelUpload';
            
            apiServices.uploadFile(url, fd).then(function (response) {
                console.log(response);
                $('.loaderGif').hide();
				
                if (response.isSuccessful) {

                    $('#uploadModalBody').html('<p>File Uploaded Successfully</p>');
                    $('#modal-footer').html('');
                    $('#uploadMsgModal').modal('toggle');
                    $scope.uploadExcel = true;
                } else {

                    var htm = '<p style="color:red;">File Upload Failed</p>';
                    var error_report = JSON.parse(response.payload);

                    function loopEachRow(idx, param) {
                        var arr = tempjson = [];
                        var param = Object.values(param);

                        if(idx ==  1){
                                param.map(function(current_value, index, element) {
                                        for(var index in current_value) {

                                                arr.push(current_value[index]);
                                        }
                                });
                                        arr.forEach(function(el,key){

                                                htm += '<p>Row no.' + index + ' : ' +  el + ' </p>';	
                                        });									


                        }
                        if(idx ==  2 || idx ==  3){
                                param.map(function(current_value, index, element) {	

                                        var arr = Object.values(current_value);
                                          for(var index in current_value) { }
                                         
                                            arr.forEach(function(el,key){  

                                                    htm += '<p>Row no.' + index + ' : ' +  el + ' </p>';	
                                                });
                                });									
                        }

                    }

                    error_report.map(function(current_value, index, element) {
//                        console.log(current_value);
               for(var index in current_value) {

                    if(index==1){

                            htm += '<p style="color:red;"> MASTER PAGE </p>';

                                            loopEachRow(index, current_value);
                    }

                    if(index==2){

                            htm += '<p style="color:red;"> RATE CARD PAGE </p>';

                                            loopEachRow(index, current_value);
                    }

                    if(index==3){

                            htm += '<p style="color:red;"> DISCOUNT PAGE </p>';

                                            loopEachRow(index, current_value);
                            break;							
                    }}

                    });						


                    $('#uploadModalBody').html(htm);
                    //$('#btnConfirmOK').hide();                    
                    $('#uploadMsgModal').modal('toggle');
                    $scope.uploadExcel = false;
                }                                

            });

        };         
        
        

}]);
