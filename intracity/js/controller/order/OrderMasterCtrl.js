app.controller('OrderMasterCtrl', ['$scope', '$http', 'config', 'consignment', 'apiServices', '$state', 'trackings', '$dataExchanger', function ($scope, $http, config, consignment, apiServices, $state, trackings, $dataExchanger) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.trackings = trackings.type;
    $scope.post_type = POST_TYPE;
    
    $scope.setBreadCrumb = $state.params.serviceName;
    
    /**********************
     *
     * prefilled filter section
     *
     * 
     */
     //  Get city of intracity

    apiServices.city(serverUrl + 'locations/getCity').then(function (response) {
        $scope.cities = response;       
    });

    apiServices.vehiclesType(serverUrl + 'locations/getVehiletype').then(function (response) {
        $scope.vehicles = response;
        // console.log($scope.vehicle);
    });

    /**
     * Get order number
     */
    apiServices.getMethod(serverUrl + 'getOrderNumber/' + $dataExchanger.request.serviceId).then(function (response) {
        $scope.orderNumber = response.payload.orderNo;
        $scope.dispatchDate = response.payload.dispatchDate;
        // console.log($scope.vehicle);
    });


    /*** Get All Seller List ***/
    apiServices.getAllSellers(serverUrl).then(function (response) {
        $scope.sellerList = response;
    });
    /*** Get All Seller List ***/

    $scope.data = [
        {
            city: {
                id: '',
                name: ''
            }
        }
    ];


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




    /**
     * display order details 
     * 
     */

    $scope.orderDetails = function (value) {
        if (value != null || value != '' || typeof value !== undefined) {
            console.log("orderId::", value);
            $state.go("orderDetails", { "id": value.id });
        }
    }

    /**
     * 
     * Filter Order master 
     * 
     */

    $scope.filterData = {
        fromLocation: [],
        toLocation: [],
        postType: [],
        vehicleType: [],
        orderDate: [],
        orderNumber: [],
        sellerType: [],
        serviceType: $dataExchanger.request.serviceId
    };

    $scope.showIntraHyper = false;
    $scope.showBlueCollar = false;
    $scope.showHyperlocal = false;
    if($scope.filterData.serviceType == _INTRACITY_) {
        $scope.showIntraHyper = true;
        $scope.showHyperlocal = true;
    } else if($scope.filterData.serviceType == _HYPERLOCAL_) {
        $scope.showHyperlocal = false;
        $scope.showIntraHyper = true;
    } else if($scope.filterData.serviceType == _BLUECOLLAR_) {
        $scope.showBlueCollar = true;
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
    
    $scope.$watch('filterData', function (newValue, oldValue) {
        $('#loaderGif').show();
        $http({
            url: serverUrl + 'orderMaster/filter',
        method: 'POST',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: $scope.filterData,
        }).then(function success(response) {             
            if (response.data.isSuccessfull) {
                console.log(response.data.payload);
                $scope.orderlists = response.data.payload;
                $('#loaderGif').hide();
            }
        }).catch(function(error) {
            apiServices.errorHandeler(error);    
            $('#loaderGif').hide();        
        });
    }, true);


}]);