app.controller('HpSellerCtrl', ['$scope', '$http', 'config', 'apiHyperlocalServices', 'type_basis', 'discount', '$state', '$location','apiServices', function ($scope, $http, config, apiHyperlocalServices, type_basis, discount, $state, $location,apiServices) {
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

    $scope.data = [];
	$scope.setting_data = {
		seller_spot_enquiries_related : true,
		seller_spot_enquiries_partly_related : false,
		seller_spot_enquiries_un_related: false,
		
		seller_spot_lead_related : true,
		seller_spot_lead_partly_related : false,
		seller_spot_lead_un_related : false,

		seller_term_enquiries_related : true,
		seller_term_enquiries_partly_related : false,
		seller_term_enquiries_un_related : false,

		seller_term_lead_related : true,
		seller_term_lead_partly_related : false,
		seller_term_lead_un_related : false,
		
		user_pk : 8,
		user_id : 99,
		role_id : 99,
		service_id : 99,
		page_type : 88,
		updated_by: 99
	};
	
	$scope.save_setting = function(){
		
        $http({
            url: serverUrl + 'hyperlocal/seller-setting-save',
            method: 'POST',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: $scope.setting_data,
        }).then(function success(response){ 
			//alert(response);
			//alert(response.seller_spot_enquiries_related);
            //console.log('Ajax',response.seller_spot_enquiries_related);  
            //$scope.spotsList = response.data.data;          
            // if (response.isSuccessfull) {
            //    $scope.spotsList = response.data;
            //     $('#loaderGif').hide();
            // }
        }).catch(function(error){
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
    $scope.data.city_id = {id: ''};
    $scope.onSelect = function (data) {
        console.log("City Id::", parseInt(data.id));
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
   

    var url = serverUrl + 'hyperlocal/product-category';
    $scope.getProductCategory = function (url) {
        apiHyperlocalServices.category(url).then(function (response) {
            $scope.categories = response;
           // console.log('Product Category:', $scope.categories);
        });
    }
    $scope.getProductCategory(url);
    var isValidated = true;

    $('#save ,#draft').click(function () {
        isValidated = true;
        $('#product_category').css('border-color', '');
        $('#service').css('border-color', '');
        $('#product').css('border-color', '');
        $('#departing').css('border-color', '');
        
        $('#departing').css('border-color', '');
        $('#returning1').css('border-color', '');
        $('#city').css('border-color', '');
        $('#line_items').css('border-color', '');
        $('#service_type').css('border-color', '');
        $('#base_price').css('border-color', '');
        $('#distance_included').css('border-color', '');
        $('#rate_per_ext_km').css('border-color', '');
        $('#weight_included').css('border-color', '');
        $('#rate_per_ext_kg').css('border-color', '');
       
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

        if (product_category == '') {
            $('#product').css('border-bottom', '1px solid red');
            $('#product_category').focus();
            isValidated = false;
        }

        if (departing == '') {
            $('#departing').css('border-color', 'red');
            $('#departing').focus();
            isValidated = false;
        }

        if (returning1 == '') {
            $('#returning1').css('border-color', 'red');
            $('#returning1').focus();
            isValidated = false;
        }

        if (city == '') {
            $('#city').css('border-color', 'red');
            $('#city').focus();
            isValidated = false;
        }

        if (line_items == '') {
            $('#line_items').css('border-color', 'red');
            $('#line_items').focus();
            isValidated = false;
        }

        if (service_type == '') {
            $('#service').css('border-bottom', '1px solid red');
            $('#service_type').focus();
            isValidated = false;
        }
        console.log('service_type:', service_type);

        if (base_price == '') {
            $('#base_price').css('border-color', 'red');
            $('#base_price').focus();
            isValidated = false;
        }

        if (distance_included == '') {
            $('#distance_included').css('border-color', 'red');
            $('#distance_included').focus();
            isValidated = false;
        }

        if (rate_per_ext_km == '') {
            $('#rate_per_ext_km').css('border-color', 'red');
            $('#rate_per_ext_km').focus();
            isValidated = false;
        }

        if (weight_included == '') {
            $('#weight_included').css('border-color', 'red');
            $('#weight_included').focus();
            isValidated = false;
        }

        if (rate_per_ext_kg == '') {
            $('#rate_per_ext_kg').css('border-color', 'red');
            $('#rate_per_ext_kg').focus();
            isValidated = false;
        }


        // else {
        //     isValidated = true;

        // }

        if (isValidated) {

            if ($('#PostPrivate').is(':checked')) {
                if (seller == '') {
                    $('#seller').css('border-bottom', '1px solid red');
                    $('#seller').focus();
                    isValidated = false;
                    alert("Please select atleast one buyer");
                }
            }


            if (!$('#Accept').is(':checked')) {
                alert('Accept Terms & Conditions');
                //return false;
            }
        }
        else {
            alert('Please fill all the mandatory(*) field');
        }


        //console.log('check:', isValidated);
        //console.log("bug:", $scope.formData.selectseledata);

    });
    console.log('check2:', isValidated);

    // $('#item_add_discounts').click(function() {


    // });


    $scope.isSellerVisible = false;
    $scope.showHideBuyer = function (val) {
        if (val == 'private') {
            $scope.data.selectseledata = '';
            $scope.isSellerVisible = true;
        } else {

            $scope.isSellerVisible = false;
            $scope.data.selectseledata = '';
            //$scope.dataSpot.visibleToSellers = [];
        }
    };

    var url = serverUrl + 'getbuyerdetails';
    $scope.getbuyer = function (url) {
        apiHyperlocalServices.getMethod(url).then(function (response) {

            $scope.sellerList = response;
            setTimeout(function () {
                $("#sellerList").tokenInput($scope.sellerList, {propertyToSearch: 'full_name'});

                $("#token-input-sellerList").attr("placeholder", "Buyer Name (Auto Search)");
            }, 1000);
        });
    }
    $scope.getbuyer(url);


    /*** For Inserting data seller rate card */
    $scope.createPost = function (formData, isOpen) {
        
        $scope.finalData = {
            'rateCartData': formData,
            'discountData': $scope.discountLists,
            'isOpen': isOpen
        };

        // if (!$('#Accept').is(':checked')) {
        //     //alert('Accept Terms & Conditions');
        //     return false;
        // }
        var url = serverUrl + 'hyperlocal/seller-rate-card-post';
        var data = formData;
        // console.log("$scope.formData::",formData);
        apiHyperlocalServices.hpCreatePost(url, JSON.stringify($scope.finalData)).then(function (response) {
            // var tran =  response.finalData.post_transaction_id;
            if ($("#Accept").prop("checked", true)) {
                str = "Your request for post has been successfully posted to the relevant buyers. You would be getting the enquiries from the buyers online.";
                $("#responsetext").html(str);
                $(".waitText button").attr("data-type", "hpsellerPost");
                $('#myRateCardModal').modal({'show': true});
            }
        });

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
        console.log('gurudev:', $scope.formData.discountType);
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
            console.log('Discount Data:', fddata);
            $("#table_heading").show();
        }

        else {
            alert("Please fill all mandatory(*) fields of discount")
        }


    }


    //$scope.showDiscountType = false;
    $scope.showDiscountType = function (type) {
       //alert(type);
        console.log(type);
        $scope.formData.buyer_discount='';
        $scope.formData.credit_day='';
        
        //$scope.showDiscountType = true;

    }

    var url = serverUrl + 'getbuyerdetails';
    $scope.getbuyer = function (url) {
        apiHyperlocalServices.getMethod(url).then(function (response) {
            response.push({'id': "-1", 'full_name': "All"});
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
    /**Seller post count */

    
    $scope.search_filter = function (type) {
      
        $scope.filterdata=type;
      console.log($scope.filterdata);
        if($scope.filterdata=='Inbound')
        {

            var url = serverUrl + 'hyperlocal/hp-get-all-records-inbound';
            // $scope.filterType = 'all';
            apiHyperlocalServices.InboundFilter(url, type).then(function (response) {
            $scope.spotsList = response.data;
            console.log('Ajax1',response.data);
           
        });

        }else{
        $scope.show = '';
        var url = serverUrl + 'hyperlocal/seller-search-filters';
        apiHyperlocalServices.sellerListAcdngFilter(url, type).then(function (response) {
        $scope.results = response.payload;
            $scope.changeClass(type);
            //console.log('seller-post-listasasasas',$scope.potsList);


        });
            
        }
    }
    $scope.search_filter('all');
  

    /************************For Active Class On Click Filters************/
    $scope.changeClass = function (type) {
        $('.all,.public,.private,.Inbound,.outbound').removeClass('search_nav_active');
        $('.' + type).addClass("search_nav_active");
        if (type == 'inbound' || type == 'outbound') {
            $scope.show = type;
        } else {
            $scope.show = '';
        }
    };
    /************************For Active Class On Click Filters************/





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
    $scope.selectBuyer = function (operator) {
        // alert(operator);
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
        serviceId: _HYPERLOCAL_
    };


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
    
    $scope.$watch('filterData', function (newValue, oldValue) {
        // $('#loaderGif').show();
        $http({
            url: serverUrl + 'hyperlocal/hp-get-all-records-inbound/inbound',
            method: 'POST',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: $scope.filterData,
        }).then(function success(response) { 

            console.log('Ajax',response.data.data);  
            $scope.spotsList = response.data.data;          
            // if (response.isSuccessfull) {
            //    $scope.spotsList = response.data;
            //     $('#loaderGif').hide();
            // }
        }).catch(function(error) {
            apiServices.errorHandeler(error);    
            $('#loaderGif').hide();        
        });
    }, true);
   

}]);