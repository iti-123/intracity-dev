app.controller('BuyerSearchCtrl', ['$scope', '$http', 'config', 'trackings', 'apiServices', '$compile', '$state', '$stateParams', '$dataExchanger', function ($scope, $http, config, trackings, apiServices, $compile, $state, $stateParams, $dataExchanger) {
    $scope.searchFormSubmit = "Search";
    $scope.date = new Date();

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.servicetype = SERVICE_TYPE;
    $scope.materialtype = HYPERLOCAL_MATERIAL_TYPE;
    $scope.weight = HYPERLOCAL_WEIGHT;
    $scope.data = [];


    console.log("URL ::", serverUrl);

    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
            console.log($scope.cities);
        });
    }
    //  Get city of intracity 
    $scope.getCity(url);


    var url = serverUrl + 'hyperlocal/product-category';
    $scope.getProductCategory = function (url) {
        apiServices.category(url).then(function (response) {
            $scope.categories = response;
            //console.log('Product Category:', $scope.categories);
        });
    }
    $scope.getProductCategory(url);


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
        apiServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            console.log('Locations:', $scope.locations);
        });
    }
    /*** Get Location By Id ***/

    $scope.multipleLocation = [];
    $scope.addLocation = function (locationData) {

        if (!$("#searchForm").data('bootstrapValidator').validate().isValid()) {

            return false;
        }


        console.log("locationData::", locationData);
        $scope.new = angular.copy(locationData);

        $scope.multipleLocation.push($scope.new);

        console.log("multipleLocation::", $scope.multipleLocation);

        $scope.removeItem = function (x) {

            $scope.multipleLocation.splice(x, 1);
        }
        //  locationData.tolocation = "";
        //  locationData.NoParcel ="" ;
        //  locationData.parcelweight ="" ;


    }

    $dataExchanger.$default({
        request: {
            serviceId: _HYPERLOCAL_,
            serviceName: 'HYPERLOCAL',
            fullName: "",
            imagePath: "",
            data: {}
        }
    });


    $scope.StartSearch = function () {

        // if (!$("#searchForm").data('bootstrapValidator').validate().isValid()) {
        //   return false;
        //  }
        var searchdata = $scope.data;
        $scope.buyersearch = {
            city: searchdata.city ? searchdata.city : '',
            departingDate: searchdata.departingDate ? searchdata.departingDate : '',
            service_type: searchdata.service_type ? searchdata.service_type : '',
            fragile: searchdata.fragile ? searchdata.fragile : '',
            material_type: searchdata.material_type ? searchdata.material_type : '',
            location: $scope.multipleLocation ? $scope.multipleLocation : '',

        };

        $dataExchanger.request.data = $scope.buyersearch;

        $state.go("hp-buyer-search-result");
    };
}]);
