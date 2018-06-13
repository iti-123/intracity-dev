app.controller('postDetailLeadCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', 'trackings','$dataExchanger','GoogleMapService', function ($scope, $http, config, apiServices, type_basis, $state, trackings,$dataExchanger,GoogleMapService) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.data = [];
    $scope.type_basis = type_basis.type_basis;
    $scope.post_Status = POST_STATUS;
    $scope.trackings = trackings.type;
    var transitHour = [];
    $scope.transitHour = TRANSIT_HOUR;

   
    $scope.hourDistanceSlabs = distanceHourSlab;
    console.log('Distance Slabs',$scope.hourDistanceSlabs);

    var i = 0;
    for (var property in TRANSIT_HOUR) {


        transitHour[i] = {"id": property, "value": property};
        i++;
    }
    $scope.TRANSIT_HOUR = transitHour;
    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
            //console.log($scope.cities);
        });
    };
    //  Get city of intracity 

    $scope.getCity(url);

    $scope.filterData = {
        vehicleType: [],
        sellerType: [],
    };

    $scope.clearAll = function () {
        $scope.filClearValue = false;
        $scope.filterData = {
           vehicleType: [],
           sellerType: [],
        };
    }
    
    $scope.checkedFilterData = function (arr, value) {
        var index = arr.indexOf(value);
        if (index == -1) {
            return false;
        }
        return true;
    }

    $scope.addFilterData = function (arr, value) {
        $scope.filClearValue = true;
        var index = arr.indexOf(value);
        if(index == -1){
            arr.push(value);
        }else{
            arr.splice(index,1);
            if($scope.filterData.vehicleType.length < 1 && $scope.filterData.sellerType.length < 1){
                $scope.filClearValue = false;
            }
        }
        console.log('Filter Data',$scope.filterData);
    };

    $scope.getVehicle = function (id) {
        for (let v of $scope.vehicles) {
            if (v.id == id) {
                return v.vehicle_type;
            }
        }
    }

    $scope.getUsername = function (id) {
      for (let v of $scope.sellerList) {
            if (v.id == id) {
                return v.username;
            }
        }
    }

    $scope.removeElem = function (arr,index) {
        arr.splice(index,1);
        if($scope.filterData.vehicleType.length < 1 && $scope.filterData.sellerType.length < 1){
                $scope.filClearValue = false;
        }
    };

    $scope.$watch('filterData', function(newValue, oldValue){
        $scope.filterData.ids = $state.params.id;
        $http({
          url: serverUrl + 'buyer-post-lead-list',
          method: 'POST',
          headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
          },
          data: $scope.filterData,
        }).then(function success(response){
          if(response.data.success){
            $scope.postLeads = response.data.data;
          }else{
            $scope.postLeads = response.data.data;
          }
        },function error(response){
           
        });
    },true);

    $http({
        method: 'POST',
        url: serverUrl+'buyer-post-lead-details',
        dataType: 'json',
        data: {'id':$state.params.id},
        headers: {
          'authorization': 'Bearer ' + localStorage.getItem("access_token")
        }
    }).then(function success(response){
        if(response.data.success){
          $scope.postDetails = response.data.dataPostDetails;
          $scope.getLocations($scope.postDetails[0]['city_id']);
          console.log('Post Details',$scope.postDetails);
        }
    }, function error(response){
        //
    });

    $http({
        method: 'POST',
        url: serverUrl+'intra-private-post',
        dataType: 'json',
        data: {'id':$state.params.id},
        headers: {
          'authorization': 'Bearer ' + localStorage.getItem("access_token")
        }
    }).then(function success(response){
        if(response.data.success){
          $scope.privateSellers = response.data.data;
        }
    }, function error(response){
        //
    });

    $scope.getLocations = function (city_id) {
        apiServices.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id).then(function (response) {
            $scope.locations = response;
            console.log('Locations Intracity:', $scope.locations);
        });
    };

    $scope.openDetails = function (index) {
        $scope.postLeads[index].showDetails = !$scope.postLeads[index].showDetails;
    };

    $scope.isDetailsHidden = function (index) {
        if ($scope.postLeads[index].showDetails == undefined) {
            $scope.postLeads[index].showDetails = false;
        }
        return $scope.postLeads[index].showDetails;
    };

    var buyerurl = serverUrl + 'getallbuyer';
    apiServices.getallbuyer(buyerurl).then(function (response) {

        $scope.sellerList = response.payload;

    });

    /*** Get Vehicle Type ***/
    var url = serverUrl + 'locations/getVehiletype';
    $scope.getVehicleType = function (url) {
        apiServices.vehiclesType(url).then(function (response) {
            $scope.vehicles = response;
            //console.log($scope.vehicles);
        });
    };
    $scope.getVehicleType(url);

  
    /*** Get All Seller List ***/
    apiServices.getAllSellers(serverUrl).then(function (response) {
        $scope.sellerList = response;
        setTimeout(function () {
            $("#sellerList").tokenInput($scope.sellerList, {propertyToSearch: 'username'});
        }, 1000);

        //console.log("$scope.sellerList::", $scope.sellerList);
    });
    /*** Get Location By Id ***/
  
    $scope.data.city_id = {id: ''};
    $scope.onSelect = function (data) {
        var city_id = parseInt(data.id);
        if (typeof(city_id) != NaN || city_id != '') {
            $scope.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id);
            // $scope.getListBuyerAccordingFilter(url, city_id);
        }
    };


    $scope.getLocationByCity = function (url) {
        apiServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            //console.log($scope.locations);
        });
    };
    /*** Get Location By Id ***/
 
 

    $scope.displayFirmQuoteAction = function(index, action) {
        $("#quote-action-"+index).toggle(1000);
    }

    $scope.bookNowForm = function(value,index) {
        console.log("Quotes::",value);
        $scope.onSelect(value.city);
        $("#quote-locality-"+index).toggle(1000);

    }

    $scope.onSelect = function (data) {
        var city_id;
        console.log('location', data);
        var city_id = parseInt(data.id);
        console.log("OK",isNaN(city_id));
        if (!isNaN(city_id)) {
            url = serverUrl + 'locations/getlocality/' + city_id;
            apiServices.getLocationByCity(url).then(function (response) {
                $scope.locations = response;

            });
        }
    };

    $scope.validateQuotation = function (post,index) {
        var valid = true;
        if($scope.postLeads[index].type_basis == 2){
            if ($scope.postLeads[index].fromLocation == undefined || $scope.postLeads[index].fromLocation == '') {
                valid = false;
                $scope.postLeads[index].from_location = true;
            } else {
                $scope.postLeads[index].from_location = false;
            }

            if ($scope.postLeads[index].tolocation == undefined || $scope.postLeads[index].tolocation == '') {
                $scope.postLeads[index].to_location = true;
                valid = false;
            } else {
                $scope.postLeads[index].to_location = false;
            }
        }

        if($scope.postLeads[index].type_basis == 1){
            if ($scope.postLeads[index].reporting_location == undefined || $scope.postLeads[index].reporting_location == '') {
                $scope.postLeads[index].reportings = true;
                valid = false;
            } else {
                $scope.postLeads[index].reportings = false;
            }

            if ($scope.postLeads[index].base_hour == undefined || $scope.postLeads[index].base_hour == '') {
                $scope.postLeads[index].base_hours = true;
                valid = false;
            } else {
                $scope.postLeads[index].base_hours = false;
            }    
        }
        return valid;
    }

    $scope.bookNowLeads = function (post,index) {
        if ($scope.validateQuotation(post,index)) {
            var value = $scope.postLeads[index];

            var fromLocations;
            var toLocations;
            var slabTimeDrutaion;

            if($scope.postLeads[index].type_basis == 2) {
               fromLocations = $scope.postLeads[index].fromLocation.locality_name+', '+$scope.postLeads[index].city_name;
               toLocations = $scope.postLeads[index].tolocation.locality_name+', '+$scope.postLeads[index].city_name;
            } else if($scope.postLeads[index].type_basis == 1) {
                fromLocations = $scope.postLeads[index].city_name;
                toLocations = $scope.postLeads[index].reporting_location.locality_name+', '+$scope.postLeads[index].city_name;
            }
            console.log("slabTimeDrutaion Slab",slabTimeDrutaion);
            GoogleMapService.calculateDistance(fromLocations,toLocations).then((response) => {
                let calculatedDistance = response;
                console.log('DISTANCE FROM GOOGLE API::',calculatedDistance);
                GoogleMapService.getDistanceInKM(response[0].distance).then((response) => {
                    totalDistance = response;
                    console.log('Total Distance',totalDistance);
                    if(value.rate_base_distance != '' && value.type_basis == 2) {
                        let ratePerKM = value.rate_base_distance; // FOR value.base_distance
                        value.price = ratePerKM*value.base_distance + value.cost_per_extra_hour*(totalDistance - value.base_distance);                            
                        value.forDistance = totalDistance; 

                        
                        value.data = { 'dispatchDate': value.valid_from,
                                       'fromLocation': {'locality_name':$scope.postLeads[index].fromLocation.locality_name},
                                       'toLocation': {'locality_name':$scope.postLeads[index].tolocation.locality_name}  };
                        console.log('value',value);           
                        var booknowSerachObj = value;
                        $scope.bookPostObj = {
                              initialDetails: {
                                serviceId: value.lkp_service_id,
                                serviceType: '',
                                sellerId: value.posted_by,
                                buyerId: Auth.getUserID(),
                                postType: "BP",
                                searchData: booknowSerachObj,
                                post: post
                              }
                        };
                        apiServices.bookNowLeads(serverUrl, JSON.stringify($scope.bookPostObj)).then(function (response) {
                            if(response.isSuccessful && response.payload.id) {
                                  $scope.cartId = response.payload.enc_id;
                                  console.log('Cart Id',$scope.cartId);
                                  $state.go('order-booknow', {serviceId: _INTRACITY_,cartId: $scope.cartId});
                            }
                        })                       
                    } else if(value.base_time != '' && value.type_basis == 1) { 
                        distance = calculatedDistance[0].distance;
                        time = calculatedDistance[0].duration;

                        GoogleMapService.getDistanceInMinutes(calculatedDistance[0].duration).then((response) => {
                             let durationInMinutes = response;
                             console.log("durationInMinutes API",durationInMinutes);
                             console.log("Distance API",parseInt(distance));

                              $http({
                                    method: 'POST',
                                    url: serverUrl + 'get-intra-slab',
                                    dataType: 'json',
                                    data: $scope.postLeads[index].base_hour,
                                    headers: {
                                      'authorization': 'Bearer ' + localStorage.getItem("access_token")
                                    }
                                }).then(function success(response){
                                    slabTimeDrutaion = response.data;
                                    console.log('Slab Time Duration',slabTimeDrutaion);

                                    let postHourToMinute = slabTimeDrutaion[0].hour * 60;
                                    let postDistance = slabTimeDrutaion[0].distance;

                                    console.log("postHourToMinute",postHourToMinute);
                                    console.log("postDistance SLAB",postDistance);
                                         console.log("*****************************************************************");
                                     if(durationInMinutes <=  postHourToMinute && parseInt(distance) <= postDistance) {
                                        value.price = value.cost_base_time;
                                        console.log('finalPrice::', value.price);
                                     } else {        
                                        console.log('finalPrice API::', value.price);    
                                        value.additionalDistance = parseInt(distance) - parseInt(postDistance);
                                        value.additionalHours = (durationInMinutes - parseInt(postHourToMinute))/60;                                
                                        value.price = value.cost_base_time + (value.additionalDistance * value.additional_km_charge) + (value.additionalHours*value.additional_hour_charge);
                                     }

                                    
                                    value.data = { 'dispatchDate': value.valid_from,
                                       'type': 1,
                                       'reporting': {'locality_name':$scope.postLeads[index].reporting_location.locality_name}};
                        

                                    console.log('Final Price',value);    
                                    console.log('From Location',$scope.postLeads[index].fromLocation);
                                    console.log('To Location',$scope.postLeads[index].tolocation);
                                   // return false;
                                        
                                       // value.data = { 'departingDate':value.valid_from };
                                        console.log('value.data',value);           
                                        var booknowSerachObj = value;
                                        $scope.bookPostObj = {
                                              initialDetails: {
                                                serviceId: value.lkp_service_id,
                                                serviceType: '',
                                                sellerId: value.posted_by,
                                                buyerId: Auth.getUserID(),
                                                postType: "BP",
                                                searchData: booknowSerachObj,
                                                post: post
                                              }
                                        };
                                        apiServices.bookNowLeads(serverUrl, JSON.stringify($scope.bookPostObj)).then(function (response) {
                                            if(response.isSuccessful && response.payload.id) {
                                                  $scope.cartId = response.payload.enc_id;
                                                  console.log('Cart Id',$scope.cartId);
                                                  $state.go('order-booknow', {serviceId: _INTRACITY_,cartId: $scope.cartId});
                                            }
                                        })  
                                }, function error(response){
                                    
                             });
                        })   
                    }    
                    
                });
            });
            
        }
    };

    $scope.buyerQuoteAction = function(index, action) {

        console.log($scope.posts.quotes[index]);
        $scope.posts.quotes[index].action = action;

        let url = serverUrl+'negotation/buyerQuoteAction';
        apiServices.postMethod(url,$scope.posts.quotes[index]).then(response => {
            console.log(response);
            location.reload();
        }).catch();
    }


    $scope.validateQuote = function(index) {
        if($scope.posts[index].action == 'COUNTER_BY_BUYER') {
            if ($scope.posts[index].buyerPrice == '') {
                $scope.posts[index].buyerPriceError = 'Enter counter price ';
            } else {
                $scope.posts[index].buyerPriceError = '';
            }
        }
    }

    $scope.viewDetails = function(index) {
        $("#quote-detail-"+index).toggle(); 
        $scope.posts.quotes[index].isActive = !$scope.posts.quotes[index].isActive; 
              
    }

    /* $scope.viewDetails  = function(index)
    { 
        $scope.posts.quotes[index].isActive = !$scope.posts.quotes[index].isActive;
    }*/



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

        alert(obj);

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


}]);
