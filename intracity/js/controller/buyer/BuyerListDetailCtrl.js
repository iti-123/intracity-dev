app.controller('BuyerListDetailCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state','$dataExchanger', function ($scope, $http, config, apiServices, type_basis, $state,$dataExchanger) {
  
    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.data = [];
    $scope.type_basis = type_basis.type_basis;
    $scope.post_Status = POST_STATUS;
    $scope.show = '';
    $scope.boundList = [];
    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;            
        });
    };
    //  Get city of intracity
    $scope.getCity(url);
    

    $scope.getVehiclesType = function (url) {
        apiServices.vehiclesType(url).then(function (response) {
            $scope.vehicles = response;            
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
        fromDate:[],
        toDate:[],
        type: 'settings',
        title:($state.params.title).split('-').join(" "),
        isInbound: 'inbound',//default
        serviceId: $dataExchanger.request.serviceId
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


    $scope.search_filter = function (type) {   
        
        $scope.filterData.type = type;
        
        var url = serverUrl + 'get-all-records';
        apiServices.searchFilter(url,$scope.filterData).then(function (response) {
            $scope.spotsList = response;     
            
            for (var key in $scope.spotsList.payload) {
                if ($scope.spotsList.payload.hasOwnProperty(key)) {                    
                    $scope.spotsList.payload[key].title = $scope.filterData.title; 
                    for(var k in $scope.spotsList.payload[key].routes) {
                        $scope.spotsList.payload[key].routes[k].seller_id = $scope.spotsList.payload[key].seller.id;
                    }                   
                }
            }
           
            console.log('All List', $scope.spotsList);
        });

        console.log($scope.filterData);
        
    };
    $scope.search_filter('all'); 
  
    $scope.$watch("filterData",function() {
        $scope.search_filter($scope.filterData.type);
    },true);
    /*** Get Filtered Record ***/


    /*** Get Location By Id ***/
    $scope.onSelect = function (data) {
        //
        console.log(data);
    };
    $scope.data.city_id = {id: ''};
    $scope.onSelect = function (data) {
        console.log("City Id::", parseInt(data.id));
        var city_id = parseInt(data.id);
        if (typeof(city_id) != NaN || city_id != '') {
            $scope.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id);
            // $scope.getListBuyerAccordingFilter(url, city_id);
        }
    };


    $scope.getLocationByCity = function (url) {
        apiServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            // console.log('Locations:', $scope.locations);
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




    
    /************************For Active Class On Click Filters************/
    $scope.changeClass = function (type) {
        $('.all,.spot,.term,.public,.private').removeClass('search_nav_active');
        $('.' + type).addClass("search_nav_active");
       
    };
    /************************For Active Class On Click Filters************/


    /**
     * Submit counter offer
     *
     */
    $scope.submitCounterOffer = function (buyerData, id) {
        console.log("buyerData", buyerData);
    };


  

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
            $(".detail-data-form-"+$index).hide();
            $("#detail-" + $index).hide();
        } else {
            $(".detail-data-form-"+$index).show();
            $("#detail-" + $index).show();
        }        
        
    };

    $scope.viewFormNow = function(quote, index) {
        $(".detail-data-form-"+index).toggle();       
    }

    $scope.bookNow = function (quote, index) {
        console.log(quote);
        // debugger;
        // var value = $scope.searchResult[index];
        // var price = '';
        // if (value.rate_base_distance != '') {
        //     price = 'RS ' + value.base_distance * value.rate_base_distance + ' /- (For ' + value.base_distance + ' KM )';
        // } else if (value.base_time != '') {
        //     price = 'RS ' + value.base_time * value.cost_base_time + ' /- (For ' + value.base_time + ' Hours )';
        // }
        // // console.log("quote::",$scope.searchResult[index]);
        // var confirm1 = confirm("Do you want to book,\n Seller Name: " + value.seller + ", and Price : " + price);
        // console.log("Buyer Quote::", confirm1);
        
        quote.price = quote.base_distance * quote.rate_base_distance;
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


    

}]);
