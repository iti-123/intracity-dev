app.controller('sellerListCtrl', ['$scope', '$http', 'config','$location','apiServices', 'type_basis', '$state','$window', function ($scope, $http, config,$location, apiServices, type_basis, $state,$window) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.data = [];
    $scope.type_basis = type_basis.type_basis;
    $scope.post_Status = POST_STATUS;
    $scope.boundList = [];
    $scope.filterdata = [];

    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
            // console.log($scope.cities);
        });
    };
    //  Get city of intracity
    $scope.getCity(url);

    $scope.postDraft = $window.sessionStorage.getItem('postDraft');

    setTimeout(function () {
      $scope.$apply(function() {
        $scope.postDraft = false;
        sessionStorage.removeItem('postDraft');
       });
    },8000);
    /*** Get Filtered Record ***/

    $scope.getLocationByCity = function (url) {
        apiServices.getLocationByCity(url).then(function (response) {
            $scope.cities = response;
            console.log('city', $scope.cities);
        });
    };
    /*** Get Location By Id ***/


    /*** Get Vehicle Type ***/
    var url = serverUrl + 'locations/getVehiletype';
    $scope.getVehicleType = function (url) {
        apiServices.vehiclesType(url).then(function (response) {
            $scope.vehicles = response;
            //console.log($scope.vehicles);
        });
    };
    $scope.getVehicleType(url);
    /*** Get Vehicle Type ***/


    /*** Get All Seller List ***/
    apiServices.getAllSellers(serverUrl).then(function (response) {
        $scope.sellerList = response;
        setTimeout(function () {
            $("#sellerList").tokenInput($scope.sellerList, { propertyToSearch: 'username' });
        }, 1000);

        // console.log("$scope.sellerList::", $scope.sellerList);
    });
    /*** Get All Seller List ***/


    /*** Get Total Numbers of seller Post  ***/
    var url = serverUrl + 'count-seller-post';
    $scope.getTotalBuyerPost = function (url) {
        apiServices.totalBuyerPost(url).then(function (response) {
            $scope.postCount = response.payload;
            console.log('count', $scope.postCount);
        });
    };
    $scope.getTotalBuyerPost(url);

    /*** Get buyer detils ***/
    var buyerurl = serverUrl + 'getallbuyer';

    apiServices.getMethod(buyerurl).then(function (response) {
        $scope.buyers = response.payload;
        console.log('buyers', $scope.buyers);

    });
    
    $scope.filterData = {
        valid_from: '',
        valid_to: '',
        type: [],
        city: [],
        vehicleTypes: [],
        post_type: [],
        status: [],
        username: [],
        pageLoader : 5,
        pageNextValueCount : 1
    };
    
    $scope.clearAll = function () {
        $scope.filClearValue = false;
        $scope.filterData = {
            valid_from: '',
            valid_to: '',
            type: [],
            city: [],
            vehicleTypes: [],
            post_type: [],
            status: [],
            username: [],
            pageLoader : 5,
            pageNextValueCount : 1
        };
    }

    $scope.search_filter = function (type) {
        $scope.show = '';
        $scope.filterData.pageLoader = 5;
        $scope.filterData.pageNextValueCount = 1;
        if(type == 'all'){
           $scope.filterData.type = 'all';
        } else if(type == 'distance'){
           $scope.filterData.type = 'distance';
        } else if(type == 'hour'){
          $scope.filterData.type = 'hour';  
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
        } else if(type == 'distance'){
           $scope.filterData.type = 'distance';
        } else if(type == 'hour'){
          $scope.filterData.type = 'hour';  
        } else if(type == 'public'){
           $scope.filterData.type = 'public'; 
        } else if(type == 'private'){
           $scope.filterData.type = 'private'; 
        }
    };

    $scope.$watch('filterData', function (newValue, oldValue) {
        $http({
            url: serverUrl + 'seller-post-list',
            method: 'POST',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: $scope.filterData,
        }).then(function success(response) {
            if (response.data.status == 'success') {
                $scope.potsList = response.data.data.data;
                console.log('potsList',$scope.potsList);
            }
        }, function error(response) {

        });
    }, true);

    $(window).scroll(function () {
       if ($(window).scrollTop()) {
           $scope.filterData.pageLoader = $scope.filterData.pageLoader + $scope.filterData.pageNextValueCount;
           $scope.search_filters($scope.filterData.type);
           $scope.$apply();
       }
    });

    $scope.goToLink = function(value) {
        if(value.post_status == 1){
            $location.path('/sellerDetails/' + value.enc_id);
        }else if(value.post_status == 0){
            $location.path('/seller-rate-draft-card/' + value.enc_id);
        }
    };

    $scope.addFilterData = function (arr,value) {
        $scope.filClearValue = true;
        var index = arr.indexOf(value);
        if (index == -1) {
            arr.push(value);
        } else {
           arr.splice(index,1);
           if($scope.filterData.vehicleTypes.length < 1 && $scope.filterData.username.length < 1 && $scope.filterData.city == '' && $scope.filterData.post_type.length < 1 
            && $scope.filterData.valid_from == '' && $scope.filterData.valid_to == ''){
                $scope.filClearValue = false;
           }
        }
        console.log('Filter Datas',$scope.filterData);
    }

    $scope.removeElem = function (arr, index) {
        arr.splice(index, 1);
        if($scope.filterData.vehicleTypes.length < 1 && $scope.filterData.username.length < 1 && $scope.filterData.city == '' 
            && $scope.filterData.post_type.length < 1 && $scope.filterData.valid_from == '' && $scope.filterData.valid_to == ''){
            $scope.filClearValue = false;
        }
    };
    
    $scope.checkedFilterData = function (arr, value) {
        var index = arr.indexOf(value);
        if (index == -1) {
            return false;
        }
        return true;
    }

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
      for (let v of $scope.buyers) {
            if (v.id == id) {
                return v.username;
            }
        }
    }

    /************************Buyer filter **********/

    $scope.NewBuyers = [];
    $scope.NewBuyersFilter = function (item) {

        if (item != null) {
            if ($scope.NewBuyers.length == 0)
                return item;
            else {

                return $scope.NewBuyers.indexOf(item.assign_buyer) !== -1;
            }
        }

    };
    $scope.selectBuyer = function (operator, buyer) {
        if ($scope.filterdata.indexOf(buyer) == -1) {
            $scope.filterdata.push(buyer);
        }
        else {
            $scope.filterdata.splice($scope.filterdata.indexOf(buyer), 1);
        }
        //alert(operator);
        var idx = $scope.NewBuyers.indexOf(operator);
        console.log('indexof', idx);
        if (idx > -1)
            $scope.NewBuyers.splice(idx, 1);
        else
            $scope.NewBuyers.push(operator);
        if ($scope != null)
            $scope.NewBuyers = $scope.NewBuyers;

    };

    /************************ Vehicle type filter **********/
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
    $scope.selectNewVehicleType = function (operator, vehicle) {
        console.log('operator',operator);
        console.log('vehicle',vehicle);
       console.log('indexOf', $scope.filterdata.indexOf(vehicle));


        if ($scope.filterdata.indexOf(vehicle) == -1) {
            $scope.filterdata.push(vehicle);
        }
        else {
            $scope.filterdata.splice($scope.filterdata.indexOf(vehicle) ,1);
        }

        var idx = $scope.NewVehicleType.indexOf(operator);
        if (idx > -1)
            $scope.NewVehicleType.splice(idx, 1);
        else
            $scope.NewVehicleType.push(operator);
        if ($scope != null)
            $scope.NewVehicleType = $scope.NewVehicleType;

    };

    /************************ Vehicle type filter end here **********/

    /************************filter for post type type_basis**********/
    $scope.NewPostType = [];
    $scope.NewTypebasisFilter = function (item) {

        if (item != null) {
            if ($scope.NewPostType.length == 0)
                return item;
            else {
                return $scope.NewPostType.indexOf(item.type_basis) !== -1;
            }
        }

    };
    $scope.selectNewPostType = function (operator, type) {
        if ($scope.filterdata.indexOf(type) == -1) {
            $scope.filterdata.push(type);
        }
        else {
            $scope.filterdata.splice($scope.filterdata.indexOf(type), 1);
        }

        var num = operator.toString();
        var idx = $scope.NewPostType.indexOf(num);
        if (idx > -1)
            $scope.NewPostType.splice(idx, 1);
        else
            $scope.NewPostType.push(num);
        if ($scope != null)
            $scope.NewPostType = $scope.NewPostType;

    };

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
    $scope.postStatus = function (operator, status) {
        if ($scope.filterdata.indexOf(status) == -1) {
            $scope.filterdata.push(status);
        } else {
            $scope.filterdata.splice($scope.filterdata.indexOf(status), 1);
        }

        var num = operator.toString();
        var idx = $scope.NewPostStatus.indexOf(num);
        if (idx > -1)
            $scope.NewPostStatus.splice(idx, 1);
        else
            $scope.NewPostStatus.push(num);
        if ($scope != null)
            $scope.NewPostStatus = $scope.NewPostStatus;

    };

    /************************city filter **********/
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
    $scope.selectCity = function (operator, city) {
        if ($scope.filterdata.indexOf(city) == -1) {
            $scope.filterdata.push(city);
        }
        else {
            $scope.filterdata.splice($scope.filterdata.indexOf(city), 1);
        }
        

        var idx = $scope.Newcity.indexOf(operator);
        
        if (idx > -1)
            $scope.Newcity.splice(idx, 1);
        else
            $scope.Newcity.push(operator);
        if ($scope != null)
            $scope.Newcity = $scope.Newcity;

    };


    /************Get Number of Message Seller ************/
    var url = serverUrl + 'seller-post-message';
    $scope.getSellerPostMessage = function (url) {
        apiServices.getSellerMessage(url + '/' + $state.params.id).then(function (response) {
            $scope.getSellerMessage = response.payload;
            console.log("Seller Message ::", $scope.getSellerMessage);
        });
    };
    $scope.getSellerPostMessage(url);
    /************Get Number of Message Seller ************/

    /************ Remove particular filters ************/
    $scope.removefilter = function (index, obj) {
        console.log('obj',obj);
        uncheckid = obj.id;

        $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1);

        $("#vl" + uncheckid).prop('checked', false);
        $("#s" + uncheckid).prop('checked', false);
        $("#vls" + uncheckid).prop('checked', false);
        $("#type" + uncheckid).prop('checked', false);
        $("#status" + uncheckid).prop('checked', false);
    }
    /************ Remove particular filters ************/

    /************ Remove all  filters ************/
    $scope.clearall = function () {
        $scope.filterdata = [];
        $('input:checkbox').prop('checked', false);

    }
    /************ Remove all filters ************/

    /*** Inbound Outbound start ***/
    $scope.bound = { in_bound: 0, out_bound: 0 };
    $http({
        method: 'GET',
        url: config.serverUrl + 'seller-in-out-bound-count',
        headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
        }
    }).then(function (response) {
        $scope.bound = response.data.payload;
    }, function (response) {
        console.log(response);
    });
    $scope.show = '';
    $scope.inboundList = [];
    
    $scope.settingData = {
        settings: [],
        userId: Auth.getUserID(),
        serviceId: _INTRACITY_,
        role: Auth.getUserRole(),   
    }; 
    
    $scope.fetchInbound = function () {
        // $scope.changeClass('inbound');
        $scope.show = 'inbound';
        if ($scope.inboundList.length == 0) {
            $http({
                method: 'POST',
                url: config.serverUrl + 'seller-inbound-list',
                data:$scope.settingData,
                
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                }
            }).then(function (response) {
                $scope.inboundList = response.data.payload;
                $scope.boundList = $scope.inboundList;
                $scope.inboundList.totalLeads = 0;
                $scope.inboundList.forEach(function(element) {
                    $scope.inboundList.totalLeads = $scope.inboundList.totalLeads + element.value.length;
                }, this);

                console.log("RRESSPONSEEEE", $scope.inboundList);

            }, function (response) {
                console.log(response);
            });
        }
        $scope.boundList = $scope.inboundList;
    };
    
    $scope.showHideInboundDetail = function (routeKey) {
        console.log(routeKey);
        $('#detail-'+routeKey).toggle();
    };
    $scope.redirectToListDetail = function(value) {
        var title = (value.title).split(" ").join("-").toLowerCase();
        $state.go("sellerinbounddetail",{"title":title});
    }

    $scope.outboundList = [];
    $scope.fetchOutbound = function () {
        // $scope.changeClass('outbound');
        console.log('outbound');
        $scope.show = 'outbound';
        if ($scope.outboundList.length == 0) {
            $http({
                method: 'GET',
                url: config.serverUrl + 'seller-outbound-list',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                }
            }).then(function (response) {
                $scope.outboundList = response.data.payload;
                $scope.boundList = $scope.outboundList;
                console.log("$scope.outboundList::", $scope.outboundList);
            }, function (response) {
                console.log(response);
            });

        }
        $scope.boundList = $scope.outboundList;
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
            quote: $scope.inboundList[index].id,
            sellerAction: action,
            isSeller: true
        };
        $http({
            method: 'POST',
            url: config.serverUrl + 'accept-quote',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: data,
        }).then(function (response) {
            $scope.inboundList[index].seller_status = action;
        }, function (response) {
            console.log(response);
        });
    };

    $scope.validate = function (data) {
        if (isNaN(parseInt(data.transit_days)) || isNaN(parseInt(data.seller_quote))) {
            return false;
        } else {
            return true;
        }
    };

    $scope.competitiveQuoteAction = function (index, action) {
        var data = {
            quote: $scope.inboundList[index].id,
            sellerAction: action,
            transit_days: $scope.inboundList[index].counter_transit_days,
            seller_quote: $scope.inboundList[index].seller_quote
        };
        console.log(data);
        if ($scope.validate(data)) {
            $http({
                method: 'POST',
                url: config.serverUrl + 'seller-quote-counter-action',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: data,
            }).then(function (response) {
                $scope.inboundList[index].seller_status = action;
                if (response.data.isSuccessfull) {
                    $("#sellerCounterQuote").hide(1000);
                }
                console.log(response);
            }, function (response) {
                console.log(response);
            });
        }
    };


    $scope.declineQuoteAction = function (index, action) {
        var data = {
            quote: $scope.boundList[index].id,
            buyerAction: action,
            isSeller: true
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


    /*** Inbound Outbound end ***/

    /** */
    $scope.settings = [];
    console.log('SETTINGSS DATA::',$scope.settings = []);
    $scope.getSettings = [];
    

    $scope.getSetting = function(isChecked, key) {
        if(isChecked) {
            if($scope.settingData.settings.indexOf(key) == -1) {
                $scope.settingData.settings.push(key);
            }            
        } else {
           if($scope.settingData.settings.indexOf(key) > -1) {
                $scope.settingData.settings.splice($scope.settings.indexOf(key),1);
            } 
        }
    }

    $scope.$watch('settingData', function() {
        let url = serverUrl + 'settings/update';

        apiServices.sellerPostMethod(url,$scope.settingData).then((response) => {

                $('#mysettingbtn').scrollTop(0);
                $(".settings_status_message").html(response);
                $('#settings_status_message_flash').fadeIn(1000).delay(100).fadeOut(3000);
                
        }).catch();

    },true);

    $('.reloadSettings').click(function(){
        location.reload();
    });
    
}]);
