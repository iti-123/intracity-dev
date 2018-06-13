app.controller('BuyerListCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state','$dataExchanger', function ($scope, $http, config, apiServices, type_basis, $state,$dataExchanger) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.data = [];
    $scope.type_basis = type_basis.type_basis;
    $scope.post_Status = POST_STATUS;
    $scope.show = '';
    $scope.boundList = [];
    $scope.filterdata = [];
    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
            console.log($scope.cities);
        });
    };
    //  Get city of intracity
    $scope.getCity(url);
	
	$scope.buyer_setting_data = {
		seller_spot_enquiries_related : true,
		seller_spot_enquiries_partly_related : false,
		seller_spot_enquiries_un_related: false,
		
		seller_spot_lead_related : true,
		seller_spot_lead_partly_related : false,
		seller_spot_lead_un_related : false,
		
		user_id : 99,
		user_type : 1, // flag ( 0 = 'seller', 1 = 'buyer' )
		role_id : 99,
		service_id : 99,
		page_type : 88,
		setting_type : 0, // flag ( 0 = 'post_master_setting', 1 = 'notification_setting' )
		updated_by: 1023
	};
	
	$scope.buyer_save_setting = function(){
		
        $http({
            url: serverUrl + 'hyperlocal/buyer-setting-save',
            method: 'POST',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: $scope.buyer_setting_data,
        }).then(function success(response){ 
			alert(response);

        }).catch(function(error){
            apiServices.errorHandeler(error);    
            $('#loaderGif').hide();        
        });
	}


    $scope.getVehiclesType = function (url) {
        apiServices.vehiclesType(url).then(function (response) {
            $scope.vehicles = response;
            console.log("$scope.vehicle::", $scope.vehicle);
        });
    };

    $scope.getMaterialType = function () {
        apiServices.getMethod(serverUrl + 'getLoadType').then(function (response) {
            $scope.materialType = response;
            // console.log($scope.cities);
        });
    }
    $scope.getMaterialType();

    /*** Get Filtered Record ***/

    $scope.filterData = {
        fromLocation: [],
        toLocation: [],
        postType: [],
        postStatus:[],
        vehicleType: [],
        orderDate: [],
        orderNumber: [],
        sellerType: [],
        fromDate:'',
        toDate:'',
        type: '',
        postId:'',
        isInbound: 'outbound',//default
        serviceId: 3,
        loc : [],
        pageLoader : 5,
        pageNextValueCount : 1
    };

    $scope.addFilterData = function (arr, value) {
        $scope.filClearValue = true;
        var index = arr.indexOf(value);
        if (index == -1) {
            arr.push(value);
        } else {
            arr.splice(index,1);
            if($scope.filterData.vehicleType.length < 1 && $scope.filterData.sellerType.length < 1 && $scope.filterData.postType.length < 1 
                && $scope.filterData.fromLocation.length < 1 && $scope.filterData.toLocation.length < 1){
                $scope.filClearValue = false;
            }
        }
        console.log($scope.filterData);
    };

    $scope.checkedFilterData = function (arr, value) {
        var index = arr.indexOf(value);
        if (index == -1) {
            return false;
        }
        return true;
    }

    $scope.isInbound = function(isInbound) {
        $scope.filterData.isInbound = isInbound;
        $scope.changeClass(isInbound);
        $scope.search_filter($scope.filterData.type);
       
        $('.inbound,.outbound').removeClass('search_nav_active');
        if(isInbound == 'inbound') {
             $('.inbound').addClass("search_nav_active");
        } else {
             $('.outbound').addClass("search_nav_active");
        }
    }
    
    $scope.newList= [];
    $scope.search_filter1 = function (type) {   
        $scope.filterData.type = type;
        var url = serverUrl + 'get-all-records';
        apiServices.searchFilter(url,$scope.filterData).then(function (response) {
            $scope.spotsList = response; 
            console.log('FILETERDDDDD DATAAAAA::', $scope.spotsList);
            $scope.changeClass($scope.filterData.type);
            $scope.filterData.totalRow = response.payload.length
            // console.log('FILETERDDDDD DATAAAAA TOTAL ROWWWW::', $scope.filterData.totalRow);
            $(".page-data-loader").fadeOut(1000);
        });
    };

    // $scope.search_filter1('all'); 

    $scope.search_filter = function (type) {
        console.log('Scroll',$scope.filterData.pageLoader);
        $scope.show = '';
        $scope.filterData.pageLoader = 5;
        $scope.filterData.pageNextValueCount = 1;
        if(type == 'all'){
           $scope.filterData.type = 'all';
        } else if(type == 'spot'){
           $scope.filterData.type = 'spot';
        } else if(type == 'term'){
          $scope.filterData.type = 'term';  
        } else if(type == 'public'){
           $scope.filterData.type = 'public'; 
        } else if(type == 'private'){
           $scope.filterData.type = 'private'; 
        }
    };

    $scope.search_filter('all');

    $scope.search_filters = function (type) {
        $scope.show = '';
        if(type == 'all'){
           $scope.filterData.type = 'all';
        } else if(type == 'spot'){
           $scope.filterData.type = 'spot';
        } else if(type == 'term'){
          $scope.filterData.type = 'term';  
        } else if(type == 'public'){
           $scope.filterData.type = 'public'; 
        } else if(type == 'private'){
           $scope.filterData.type = 'private'; 
        }
    };

    $(window).scroll(function () {
       if ($(window).scrollTop()) {
           $scope.filterData.pageLoader = $scope.filterData.pageLoader + $scope.filterData.pageNextValueCount;
           $scope.search_filters($scope.filterData.type);
           $scope.$apply();
       }
    });

    $scope.$watch('filterData', function (newValue, oldValue) {
        // $scope.spinnerOperator = true;
        $http({
            url: serverUrl + 'get-all-records',
            method: 'POST',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: $scope.filterData,
        }).then(function success(response) {
            if (response.data.isSuccessful == true) {
               $scope.spotsList = response.data.data.data;
               // $scope.spinnerOperator = false;
               console.log('Spots List',$scope.spotsList);
            }
            if($scope.filterData.isInbound == 'inbound'){
               $scope.spotsList = response.data.payload;
               // $scope.spinnerOperator = false;
               console.log('Spots',$scope.spotsList);
            }
        }, function error(response) {

        });
    }, true);

    $scope.removeElem = function (arr,index) {
        arr.splice(index,1);
        if($scope.filterData.vehicleType.length < 1 && $scope.filterData.sellerType.length < 1 && $scope.filterData.postType.length < 1 
            && $scope.filterData.fromLocation.length < 1 && $scope.filterData.toLocation.length < 1){
            $scope.filClearValue = false;
        }
    };

    $scope.getCity = function (id) {
        for (let v of $scope.cities) {
            if (v.id == id) {
                return v.city_name;
            }
        }
    }

    $scope.getVehicle = function (id) {
        for (let v of $scope.vehicles) {
            if (v.id == id) {
                return v.vehicle_type;
            }
        }
    }

    $scope.getPostType = function (id) {
       if(id == 2){
          return 'Distance';
       }else if(id == 1){
          return 'Hour';
       }
    }

    $scope.getUsername = function (id) {
      for (let v of $scope.sellerList) {
            if (v.id == id) {
                return v.username;
            }
        }
    }

    $scope.clearAll = function () {
        $scope.filClearValue = false;
        $scope.filterData = {
            fromLocation: [],
            toLocation: [],
            postType: [],
            postStatus:[],
            vehicleType: [],
            orderDate: [],
            orderNumber: [],
            sellerType: [],
            fromDate:'',
            toDate:'',
            type: '',
            postId:'',
            isInbound: 'outbound',//default
            serviceId: 3,
            location : [],
            pageLoader : 5,
            pageNextValueCount : 1
        };
    }

    // $(window).scroll(function() {                
    //     $scope.filterData.offset = 2;               
    //     if ($(window).scrollTop() == $(document).height() - $(window).height()) {                
    //         if($scope.filterData.isInbound === 'outbound') {
    //             $(".page-data-loader").fadeIn();                       
    //             $scope.search_filter($scope.filterData.type);  
    //         }           
    //     }        
    // });
    
    
        
    apiServices.getMethod(serverUrl + 'count-inbound').then(function (response) {
        $scope.inboundCount = response.payload.count;   
        $scope.inboundCount.total = $scope.inboundCount.public + $scope.inboundCount.private;         
        $scope.changeClass($scope.filterData.type);
        console.log('$scope.inbound', $scope.inboundCount);
    });
    
    /*** Get Filtered Record ***/

    $scope.selectedLocation = function($item, $model, $label){
        console.log('Item',$item);
        $scope.filterData.location = $item;
    };

    $scope.data.city_id = {id: ''};
    $scope.onSelect = function ($item,$model,$label) {
        $scope.filterData.location = $item;
        $scope.filterData.loc = $item.id;
        $scope.filterData.locationPH = $item.city_name;

        console.log('Item',$item);
        var city_id = parseInt($item.id);
        if (typeof(city_id) != NaN || city_id != '') {
            $scope.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id);
        }
    };

    $scope.removeLocation = function () {
        $scope.filterData.location = '';
        $scope.filterData.locationPH = '';
        if($scope.filterData.profileType.length < 1 && $scope.filterData.vehicleType.length < 1 && $scope.filterData.status.length < 1
            && $scope.filterData.machineType.length < 1 && $scope.filterData.employmentType.length < 1 && $scope.filterData.salaryType.length < 1 && $scope.filterData.qualification.length < 1){
            $scope.filClearValue = false;
        }
    }

    $scope.getLocationByCity = function (url) {
        apiServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            console.log('Locations:', $scope.locations);
        });
    };
    /*** Get Location By Id ***/


    /*** Get Vehicle Type ***/
    var url = serverUrl + 'locations/getVehiletype';
    $scope.getVehicleType = function (url) {
        apiServices.vehiclesType(url).then(function (response) {
            $scope.vehicles = response;
            console.log($scope.vehicles);
        });
    };
    $scope.getVehicleType(url);
    /*** Get Vehicle Type ***/


    /*** Get All Seller List ***/
    apiServices.getAllSellers(serverUrl).then(function (response) {
        $scope.sellerList = response;
        setTimeout(function () {
            $("#sellerList").tokenInput($scope.sellerList, {propertyToSearch: 'username'});
        }, 1000);

        console.log("$scope.sellerList::", $scope.sellerList);
    });
    /*** Get All Seller List ***/


    /*** Get Total Numbers of Buyer Post Spots ***/
    var url = serverUrl + 'count-buyerpost-spots';
    $scope.getTotalBuyerPost = function (url) {
        apiServices.totalBuyerPost(url).then(function (response) {
            $scope.postCount = response;
            console.log('BUYER POST COUNTS:', $scope.postCount);
            //$scope.postCount.total = response.total_buyerpost_spots + response.get_total_terms;
            //console.log("postCount.total", $scope.postCount);
        });
    };
    $scope.getTotalBuyerPost(url);
    /*** Get Total Numbers of Buyer Post Spots ***/

    /*** Get List of Buyer Post Spots ***/
    var url = serverUrl + 'buyer-post-list';
    $scope.getListBuyerPost = function (url) {
        apiServices.listBuyerPost(url).then(function (response) {
            $scope.spotsList = response;
            console.log("SPOOOTTT LISSSSTTTTT ::", $scope.spotsList);
        });
    };
    // $scope.getListBuyerPost(url);
    /*** Get List of Buyer Post Spots ***/


    /*** Get List of Buyer Post According Filters ***/
    var url = serverUrl + 'buyer-listing-according-filters';
    $scope.getListBuyerAccordingFilter = function (url, city_id) {
        apiServices.listBuyerAccordingFilter(url, city_id).then(function (response) {
            $scope.spotsList = response;
            console.log("Post List ::", $scope.spotsList);
        });
    };

    /*** Get List of Buyer Post According Filters ***/
    /************************filter for post Status**********/
    $scope.NewPostStatus = [];
    $scope.NewPostStatusFilter = function (item) {
        if (item != null) {
            if ($scope.NewPostStatus.length == 0)
                return item;
            else {
                return $scope.NewPostStatus.indexOf(item.post_status.toString()) !== -1;
            }
        }

    };
    $scope.postStatus = function (operator,status) {
        if ($scope.filterdata.indexOf(status) !== -1) {

            $scope.filterdata.splice($scope.filterdata.indexOf(status), 1)
        }
        else {
            $scope.filterdata.push(status);

        }
      //  $scope.filterdata.push(status);

        var num = operator.toString();
        var idx = $scope.NewPostStatus.indexOf(num);
        if (idx > -1)
            $scope.NewPostStatus.splice(idx, 1);
        else
            $scope.NewPostStatus.push(num);
        if ($scope != null)
            $scope.NewPostStatus = $scope.NewPostStatus;

    };


    /************************filter for post type city**********/
    $scope.Newcity = [];
    $scope.NewCityFilter = function (item) {

        if (item != null) {
            if ($scope.Newcity.length == 0)
                return item;
            else {

                return $scope.Newcity.indexOf(item.city_name) !== -1;
            }
        }

    };
    $scope.selectCity = function (operator) {

        var idx = $scope.Newcity.indexOf(operator);
        console.log('indexof', idx);
        if (idx > -1)
            $scope.Newcity.splice(idx, 1);
        else
            $scope.Newcity.push(operator);
        if ($scope != null)
            $scope.Newcity = $scope.Newcity;

    };
    /************************filter for post type city**********/


    /************************filter for post vehicle**********/
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
    $scope.selectNewVehicleType = function (operator,all) {
          if ($scope.filterdata.indexOf(all) !== -1) {

            $scope.filterdata.splice($scope.filterdata.indexOf(all), 1)
        }
        else {
            $scope.filterdata.push(all);

        }
        //$scope.filterdata.push(all);
        var idx = $scope.NewVehicleType.indexOf(operator);
        if (idx > -1)
            $scope.NewVehicleType.splice(idx, 1);
        else
            $scope.NewVehicleType.push(operator);
        if ($scope != null)
            $scope.NewVehicleType = $scope.NewVehicleType;

    };

    /************************filter for post vehicle**********/

    /************************filter for post seller name**********/
    $scope.NewSellerName = [];
    $scope.NewSellerNameFilter = function (item) {


        if (item != null) {
            if ($scope.NewSellerName.length == 0)
                return item;
            else {
                return $scope.NewSellerName.indexOf(item.assign_seller) !== -1;
            }
        }

    };
    $scope.selectNewSellerName = function (operator,value) {
        if ($scope.filterdata.indexOf(value) !== -1) {

            $scope.filterdata.splice($scope.filterdata.indexOf(value), 1)
        }
        else {
            $scope.filterdata.push(value);

        }
       // $scope.filterdata.push(value);
        var idx = $scope.NewSellerName.indexOf(operator);
        if (idx > -1)
            $scope.NewSellerName.splice(idx, 1);
        else
            $scope.NewSellerName.push(operator);
        if ($scope != null)
            $scope.NewSellerName = $scope.NewSellerName;

    };
    /************************filter for post seller name**********/

    /************************filter for post type_basis**********/

    $scope.NewPostType = [];
    $scope.NewTypebasisFilter = function (item) {

        if (item != null) {
            if ($scope.NewPostType.length == 0)
                return item;
            else {
                return $scope.NewPostType.indexOf(item.type_basis.toString()) !== -1;
            }
        }

    };
    $scope.selectNewPostType = function (operator,type) {
        if ($scope.filterdata.indexOf(type) !== -1) {

            $scope.filterdata.splice($scope.filterdata.indexOf(type), 1)
        }
        else {
            $scope.filterdata.push(type);

        }
        //$scope.filterdata.push(type)
        var num = operator.toString();
        var idx = $scope.NewPostType.indexOf(num);
        if (idx > -1)
            $scope.NewPostType.splice(idx, 1);
        else
            $scope.NewPostType.push(num);
        if ($scope != null)
            $scope.NewPostType = $scope.NewPostType;

    };
    /************************filter for post type_basis**********/

    /************************filter for From Location**********/

    $scope.NewFromLocation = [];
    $scope.NewFromLocationFilter = function (item) {
        if (item != null) {
            if ($scope.NewFromLocation.length == 0)
                return item;
            else {
                return $scope.NewFromLocation.indexOf(item.from_location) !== -1;
            }
        }

    };
    $scope.selectNewFromLocation = function (operator,value) {
        if ($scope.filterdata.indexOf(value) !== -1) {

            $scope.filterdata.splice($scope.filterdata.indexOf(value), 1)
        }
        else {
            $scope.filterdata.push(value);

        }
       // $scope.filterdata.push(value);
        var num = operator.toString();
        var idx = $scope.NewFromLocation.indexOf(num);
        if (idx > -1)
            $scope.NewFromLocation.splice(idx, 1);
        else
            $scope.NewFromLocation.push(num);
        if ($scope != null)
            $scope.NewFromLocation = $scope.NewFromLocation;

    };
    /************************filter for From Location**********/

    /************************filter for To Location**********/

    $scope.NewToLocation = [];
    $scope.NewToLocationFilter = function (item) {
        if (item != null) {
            if ($scope.NewToLocation.length == 0)
                return item;
            else {
                return $scope.NewToLocation.indexOf(item.to_location) !== -1;
            }
        }

    };
    $scope.selectNewToLocation = function (operator,value) {
        if ($scope.filterdata.indexOf(value) !== -1) {

            $scope.filterdata.splice($scope.filterdata.indexOf(value), 1)
        }
        else {
            $scope.filterdata.push(value);

        }
       // $scope.filterdata.push(value)
        var num = operator.toString();
        var idx = $scope.NewToLocation.indexOf(num);
        if (idx > -1)
            $scope.NewToLocation.splice(idx, 1);
        else
            $scope.NewToLocation.push(num);
        if ($scope != null)
            $scope.NewToLocation = $scope.NewToLocation;

    };
    /************************filter for To Location**********/

    /************************For Active Class On Click Filters************/
    $scope.changeClass = function (type) {
        $('.all,.spot,.term,.public,.private').removeClass('search_nav_active');
        $('.' + type).addClass("search_nav_active");
        console.log(type);
        // if (type == 'inbound' || type == 'outbound') {
        //     $('.' + type).addClass("search_nav_active");
        //     $scope.show = type;
        // } else {
        //     $scope.show = '';
        // }
    };
    /************************For Active Class On Click Filters************/

    /*** Get Number of Messages Posted ***/
    var url = serverUrl + 'buyer-post-message';
    $scope.getPostMessage = function (url) {
        if($state.params.id != 'undefined' ||  typeof $state.params.id != undefined || $state.params.id != '') {
            apiServices.getMessage(url + '/' + $state.params.id).then(function (response) {
                $scope.getMessage = response.payload;
                console.log( typeof $state.params.id);
            });
        }
    };
    $scope.getPostMessage(url);
    /*** Get Number of Messages Posted ***/


    /*** Get Buyer Details By ID ***/
    var url = serverUrl + 'get-buyer-routes-details';

    $scope.getBuyerRoutesDetails = function (url, id) {
        apiServices.getRoutesDetails(url).then(function (response) {
            $scope.buyerDetails = response;
            console.log("Post List ::", $scope.buyerDetails);
        });
    };
    /*** Get Buyer Details By ID ***/

    /*** Inbound Outbound start ***/
    $scope.bound = {in_bound: 0, out_bound: 0};
    $http({
        method: 'GET',
        url: config.serverUrl + 'buyer-in-out-bound-count',
        headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
        }
    }).then(function (response) {
        $scope.bound = response.data.payload;
    }, function (response) {
        console.log(response);
    });

    $scope.inboundList = [];
    $scope.fetchInbound = function () {
        $scope.changeClass('inbound');
        if ($scope.inboundList.length == 0) {
            $http({
                method: 'GET',
                url: config.serverUrl + 'buyer-inbound-list',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                }
            }).then(function (response) {
                $scope.inboundList = response.data.payload;
                $scope.boundList = $scope.inboundList;
            }, function (response) {
                console.log(response);
            });
        }
        $scope.boundList = $scope.inboundList;
    };
    console.log($scope.boundList);
    $scope.outboundList = [];
    $scope.fetchOutbound = function () {
        $scope.changeClass('outbound');
        if ($scope.outboundList.length == 0) {
            $http({
                method: 'GET',
                url: config.serverUrl + 'buyer-outbound-list',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                }
            }).then(function (response) {
                $scope.outboundList = response.data.payload;
                $scope.boundList = $scope.outboundList;
            }, function (response) {
                console.log(response);
            });
        }
        $scope.boundList = $scope.outboundList;
        console.log($scope.boundList);
    };

    $scope.showHideInboundDetail = function (index) {
        console.log(index);
        $('#detail-' + index).toggle(1000);
    };

    $scope.typeBasis = function (id) {
        var types = ['', 'Hour Basis', 'Distance Basis'];
        return types[parseInt(id)];
    };

    $scope.leadBasis = function (id) {
        var types = ['', 'Spot lead', 'Term Lead'];
        return types[parseInt(id)];
    };

    $scope.trackingType = function (id) {
        var types = ['', 'Mile Stone', 'Real Time'];
        return types[parseInt(id)];
    };

    $scope.firmQuoteAction = function (index, action) {
        var data = {
            quote: $scope.boundList[index].id,
            buyerAction: action,
            isSeller: false
        };
        $http({
            method: 'POST',
            url: config.serverUrl + 'accept-quote',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: data,
        }).then(function (response) {
            $scope.boundList[index].buyer_status = action;
        }, function (response) {
            console.log(response);
        });
    };

    $scope.validate = function (data) {
        if (isNaN(parseInt(data.transit_days)) || isNaN(parseInt(data.buyer_quote))) {
            return false;
        } else {
            return true;
        }
    };

    $scope.competitiveQuoteAction = function (index, action) {
        var data = {
            quote: $scope.inboundList[index].id,
            buyerAction: action,
            transit_days: $scope.inboundList[index].counter_transit_days,
            buyer_quote: $scope.inboundList[index].buyer_quote
        };
        console.log(data);
        if ($scope.validate(data)) {
            $http({
                method: 'POST',
                url: config.serverUrl + 'buyer-competitive-quote-action',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: data,
            }).then(function (response) {
                $scope.inboundList[index].buyer_status = action;
                setTimeout(function () {
                    $state.reload();
                }, 1000);
            }, function (response) {
                console.log(response);
            });
        }
    };

    $scope.declineQuoteAction = function (index, action) {
        var data = {
            quote: $scope.boundList[index].id,
            buyerAction: action,
            isSeller: false
        };
        console.log(data);

        $http({
            method: 'POST',
            url: config.serverUrl + 'quote-decline-action',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: data,
        }).then(function (response) {
            $scope.boundList[index].buyer_status = action;
        }, function (response) {
            console.log(response);
        });

    };

    $scope.finalQuoteAction = function (index, action) {
        var data = {
            quote: $scope.outboundList[index].id,
            buyerAction: action
        };
        $http({
            method: 'POST',
            url: config.serverUrl + 'buyer-final-quote-action',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: data,
        }).then(function (response) {
            $scope.outboundList[index].buyer_status = action;
        }, function (response) {
            console.log(response);
        });
    };
    /*** Inbound Outbound end ***/

    /**
     * Submit counter offer
     *
     */
    $scope.submitCounterOffer = function (buyerData, id) {
        console.log("buyerData", buyerData);
    };


    $scope.declineQuoteAction = function (index, action) {
        var data = {
            quote: $scope.boundList[index].id,
            buyerAction: action,
            isSeller: false
        };
        console.log(data);

        $http({
            method: 'POST',
            url: config.serverUrl + 'quote-decline-action',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: data,
        }).then(function (response) {
            $scope.boundList[index].buyer_status = action;
        }, function (response) {
            console.log(response);
        });
    }

    $scope.redirectToDetail = function(id) {
        $state.go("buyerDetails",{"id":id});
    }


    $scope.redirectToListDetail = function(value) {
        console.log("value::",(value.title).split(" ").join("-").toLowerCase());
        var title = (value.title).split(" ").join("-").toLowerCase();
        $state.go("buyerlistdetail",{"title":title});
    }

    $scope.showHideDetail = function ($index) {
        $(".toggle-minus-" + $index).toggleClass("detail-minus");
        if($("#detail-" + $index).css("display") == "block") {
            $(".detail-data-form-"+$index).hide(1000);
            $("#detail-" + $index).hide(1000);
        } else {
            $(".detail-data-form-"+$index).show(1000);
            $("#detail-" + $index).show(1000);
        }        
        
    };


    // Book Now 

    $scope.viewFormNow = function(value,quote, index) {
        console.log(index);
        $(".detail-data-form-"+index).toggle();
       
    }

    $scope.bookNow = function (value,quote, index) {
        console.log(value);
       
        quote.price = quote.base_distance * quote.rate_base_distance;
        var confirm1 = true;
        if (confirm1) {
            var booknowSerachObj = $dataExchanger.request;

            $dataExchanger.request.data.fromLocation = {};
            $dataExchanger.request.data.toLocation = {};
            $dataExchanger.request.data.city = quote.city;
            $scope.bookPostObj = {
                initialDetails: {
                    serviceId: $dataExchanger.request.serviceId,
                    serviceType: '',
                    sellerId: value.seller.id,
                    buyerId: Auth.getUserID(),
                    postType: "BP",
                    sellerQuoteId: quote.id, //need to discuss
                    searchData: booknowSerachObj,
                    carrierIndex: index,
                    quote: quote
                }
            };
            quote.id = quote.encId;
            // console.log("$scope.bookPostObj::", $scope.bookPostObj);
            apiServices.bookNow(serverUrl, JSON.stringify($scope.bookPostObj)).then(function (response) {
                if (response.isSuccessful && response.payload.id) {
                    $scope.cartId = response.payload.enc_id;
                    $state.go('order-booknow', {serviceId: 3, cartId: $scope.cartId});
                }
            })
        }
    };

//removefilter all filter

$scope.clearall = function () {
        $scope.filterdata = [];
        $('input:checkbox').prop('checked', false);
         $("#city").val("");
          setTimeout(function () {

        }, 100);
       
      }


//removefilter particular filter 
$scope.removefilter  = function(index, obj){
    console.log('index',index);
    console.log('obj',obj);
     uncheckid = obj.id;
     $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1);
      $("#vl" + uncheckid).prop('checked', false);
        setTimeout(function () {

        }, 100);
         $("#type" + uncheckid).prop('checked', false);
        setTimeout(function () {

        }, 100);
         $("#status" + uncheckid).prop('checked', false);
        setTimeout(function () {

        }, 100);
         $("#s" + uncheckid).prop('checked', false);
        setTimeout(function () {

        }, 100);

        
       
        $("#from" + uncheckid).prop('checked', false);
        setTimeout(function () {

        }, 100);
         $("#to" + uncheckid).prop('checked', false);
        setTimeout(function () {

        }, 100);

}

    

/*** Get User Setting Add Details ***/

    $scope.settings = [];
    $scope.getSettings = [];
    $scope.settingData = {
        settings: [],
        userId: Auth.getUserID(),
        serviceId: $dataExchanger.request.serviceId,
        role: Auth.getUserRole()      
        
       
    };
    $scope.buyer = Auth.getUserID();
    $scope.getSetting = function(isChecked,key) {

        if(isChecked) {
            if($scope.settingData.settings.indexOf(key) == -1) {
                $scope.settingData.settings.push(key);
            }            
        } else {
           if($scope.settingData.settings.indexOf(key) > -1) {
                $scope.settingData.settings.splice($scope.settings.indexOf(key),1);
            } 
        }
       // console.log($scope.settingData);
    }

    $scope.$watch('settingData', function() {
        console.log($scope.settingData);
        let url = serverUrl + 'settings/update';
        apiServices.postMethod(url,$scope.settingData).then((response) => {
            $('#settings').scrollTop(0);
            $(".settings_status_message").html(response);
            $('#settings_status_message_flash').fadeIn(1000).delay(100).fadeOut(3000);
        }).catch();
    },true);

    $('.reloadSettings').click(function(){
        location.reload();
    });
    
  
  
    $scope.updateUserSettings = function () {
        var url = serverUrl + 'settings/update';
        apiServices.updateUserSettings(url).then(function (response) {
            //$scope.userSettingUpdate = response;
            var usersettingObj = $scope.userSettingUpdate = response;

            console.log("userSettingUpdate ::", $scope.userSettingUpdate);
        });
    };

    $scope.updateUserSettings();
    /*** Get User Setting Add Details ***/

}]);


app.filter('reverse', function() {
    return function(items) {
      return items.slice().reverse();
    };
  });