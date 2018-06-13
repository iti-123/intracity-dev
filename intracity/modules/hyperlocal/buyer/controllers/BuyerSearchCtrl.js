app.controller('BuyerSearchCtrl', ['$scope', '$http', 'config', 'trackings', 'apiServices', '$compile', '$state', '$stateParams', '$dataExchanger', 'GoogleMapService', function ($scope, $http, config, trackings, apiServices, $compile, $state, $stateParams, $dataExchanger, GoogleMapService) {
    $scope.searchFormSubmit = "Search";
    $scope.addloc = 'Add Location';
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
    };
    //  Get city of intracity 
    $scope.getCity(url);
    
    $scope.categories = Buyerserchcat;
    
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
    
    $('#from_location').keypress(function (e) {
        var keyCode = e.keyCode == 0 ? e.charCode : e.keyCode;
        var ret = ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || 
            (keyCode >= 97 && keyCode <= 122) || (specialKeys.indexOf(e.keyCode) != -1 && e.charCode != e.keyCode));
          return ret;
    });

    $('#to_location').keypress(function (e) {
        var keyCode = e.keyCode == 0 ? e.charCode : e.keyCode;
        var ret = ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || 
            (keyCode >= 97 && keyCode <= 122) || (specialKeys.indexOf(e.keyCode) != -1 && e.charCode != e.keyCode));
          return ret;
    });

    /*** Get Location By Id ***/
    $scope.onSelect = function (data) {
        // 
        console.log(data);
    };
    $scope.data.city_id = { id: '' };
    $scope.onSelect = function (data) {
        console.log("City Id::", parseInt(data.id));
        var city_id = parseInt(data.id);
        if (typeof (city_id) != NaN || city_id != '') {
            $scope.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id);
            // $scope.getListBuyerAccordingFilter(url, city_id);
        }
    };

    $scope.getLocationByCity = function (url) {
        apiServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            console.log('Locations:', $scope.locations);
        });
    };

    $scope.multipleLocation = [];
    $scope.addLocation = function (locationData,data) {
        
        if (typeof data.city != 'object') {
            
            $scope.hp_buyer_city_validation = true;
            isValidated = false;
        }
        else {
            $scope.hp_buyer_city_validation = false;
        }
        if (!data.departingDate == '') {
            $scope.hp_buyer_departingDate_validation = false;
        }
        else {
            $scope.hp_buyer_departingDate_validation = true;
            isValidated = false;
        }
         if (!data.service_type == '') {
            $scope.hp_buyer_departingDatservice_type_validation = false;
        }
        else {
            $scope.hp_buyer_departingDatservice_type_validation = true;
            isValidated = false;
        }
        if (!data.category == '') {
            $scope.hp_buyer_category_validation = false;
        }
        else {
            $scope.hp_buyer_category_validation = true;
            isValidated = false;
        }
         if (typeof locationData.fromLocation != 'object') {
            $scope.hp_buyer_fromLocation_validation = true;
        }
        else {
            $scope.hp_buyer_fromLocation_validation = false;
            isValidated = false;
        }
        if (typeof locationData.tolocation != 'object') {
            $scope.hp_buyer_tolocation_validation = true;
        }
        else {
            $scope.hp_buyer_tolocation_validation = false;
            isValidated = false;
        }
          if (!data.parcelweight == '') {
            $scope.hp_buyer_parcelweight_validation = false;
        }
        else {
            $scope.hp_buyer_parcelweight_validation = true;
            isValidated = false;
        }
        if (!locationData.NoParcel == '') {
            $scope.hp_buyer_NoParcel_validation = false;
        }
        else {
            $scope.hp_buyer_NoParcel_validation = true;
            isValidated = false;
        }
        var isValidated = true;
        var city = $.trim($('#city').val());
        if (city == '') {
            $('#city').css('border-color', 'red');
            $('#city').focus();
            isValidated = false;
            
        }
        var departingDate = $('#departingDate').val();
        if (departingDate == '') {
            $('#departingDate').css('border-color', 'red');
            $('#departingDate').focus();
           return false;
        }
        var from_location = $.trim($('#from_location').val());
        if (from_location == '') {
            $('#from_location').css('border-color', 'red');
            $('#from_location').focus();
           return false;
        }
        var to_location = $.trim($('#to_location').val());
        if (to_location == '') {
            $('#to_location').css('border-color', 'red');
            $('#to_location').focus();
        return flase;
        }
        
        var ServiceType = $.trim($('#ServiceType').val());
        if (ServiceType == '') {
            $('#ServiceType').css('border-bottom', '1px solid red');
            $('#ServiceType').focus();
         return flase;
        }
        var isValidated = true;
        var product_category = $.trim($('#product_category').val());
        if (product_category == '') {
            $('#product_category').css('border-bottom', '1px solid red');
            $('#product_category').focus();
           return false;
        }
        var isValidated = true;
        var weight = $.trim($('#weight').val());
        if (weight == '') {
            $('#weight').css('border-color', 'red');
            $('#weight').focus();
           return false;
        }
        var isValidated = true;
        var parcel = $.trim($('#parcel').val());
        if (parcel == '') {
            $('#parcel').css('border-color', 'red');
            $('#parcel').focus();
            return false;
        }

        // $scope.distance=''; 
        // GoogleMapService.calculateDistance(from_location, to_location).then(function(response){
        //     console.log('distance',response);
        //     $scope.routes = response[0].distance;
        //     $scope.distance = parseInt($scope.routes);
        //     console.log('routes',parseInt($scope.routes));
        // });                


        if (isValidated) {
            $scope.addloc = 'Processing...'
            $scope.distance = '';
            GoogleMapService.calculateDistance(from_location, to_location).then(function (response) {
                console.log('distance', response);
                $scope.routes = response[0].distance;
                $scope.distance = parseInt($scope.routes);
                console.log('routes', parseInt($scope.routes));
                $('#Search').removeAttr('disabled');
                // locationData=locationData.push({'distance':$scope.distance})
                console.log('dist1', $scope.distance);
                locationData.distance = $scope.distance;
                console.log("locationData1::", locationData);
                $scope.new = angular.copy(locationData);
                if (locationData != '') {
                    $scope.multipleLocation.push($scope.new);
                }
                $scope.data.location.tolocation = "";
                $scope.data.location.parcelweight = "";
                $scope.data.location.NoParcel = "";
                console.log("multipleLocation::", $scope.multipleLocation);
                $scope.addloc = 'Add Location';
            });
         
        }
       
    }

    $scope.removeItem = function (x) {
        $scope.multipleLocation.splice(x, 1);
    }

    $dataExchanger.$default({ request: { serviceId: _HYPERLOCAL_, serviceName: 'HYPERLOCAL', fullName: "", imagePath: "", data: {} } });
    $scope.StartSearch = function (locationData) {
        $('#city').css('border-color', '');
        $('#departingDate').css('border-color', '');
        $('#from_location').css('border-color', '');
        $('#to_location').css('border-color', '');
        $('#ServiceType').css('border-color', '');
        $('#product_category').css('border-color', '');
        $('#weight').css('border-color', '');
        $('#parcel').css('border-color', '');
        var isValidated = true;
        var city = $.trim($('#city').val());
        if (city == '') {
            $('#city').css('border-color', 'red');
            $('#city').focus();
            $('#mcity').css('color','red');
             return false;
         
        }
        var departingDate = $('#departingDate').val();
        if (departingDate == '') {
            $('#mdepartingDate').css('color', 'red');
            $('#departingDate').focus();
            return false;
                       
        }
        var from_location = $.trim($('#from_location').val());
        if (from_location == '') {

            $('#mfrom_location').css('color', 'red');
            $('#from_location').focus();
           return  false;
       
        }
        var to_location = $.trim($('#to_location').val());
        if ($scope.multipleLocation == '') {
            if (to_location == '') {
                $('#mto_location').css('color', 'red');
                $('#to_location').focus();
               return false;
        
            }
        }
        
        var ServiceType = $.trim($('#ServiceType').val());
        if (ServiceType == '') {
            $('#ServiceType').css('border-bottom', '1px solid red');
            $('#ServiceType').focus();
             return false;         
        }
        
        var product_category = $.trim($('#product_category').val());
        if (product_category == '') {
            $('#mproduct_category').css('color', 'red');
            $('#product_category').focus();
          return false;
            
        }
       
        var weight = $.trim($('#weight').val());
        if ($scope.multipleLocation == '') {
            if (weight == '') {
                $('#weight').css('border-color', 'red');
                $('#weight').focus();
                isValidated = false;
                // alert("Please fillup all the field");
            }
        }
        var isValidated = true;
        if ($scope.multipleLocation == '') {
            var parcel = $.trim($('#parcel').val());
            if (parcel == '') {
                $('#parcel').css('border-color', 'red');
                $('#parcel').focus();
                isValidated = false;
                // alert("Please fillup all the field");
            }
        }

        if (isValidated) {

            // $scope.multipleLocation = [];
            $scope.new = angular.copy(locationData);
            if (locationData.tolocation != '') {

                $scope.multipleLocation.push($scope.new);
            }

            var searchdata = $scope.data;
            $scope.buyersearch = {
                city: searchdata.city ? searchdata.city : '',
                departingDate: searchdata.departingDate ? searchdata.departingDate : '',
                service_type: searchdata.service_type ? searchdata.service_type : '',
                fragile: searchdata.fragile ? searchdata.fragile : '0',
                category: searchdata.category ? searchdata.category : '',
                from_location: searchdata.location.fromLocation ? searchdata.location.fromLocation : '',
                // to_location:searchdata.location.fromLocation ? searchdata.location.fromLocation : '',
                // weight:searchdata.location.parcelweight ? searchdata.location.parcelweight : '',
                // no_parcel:searchdata.location.NoParcel ? searchdata.location.NoParcel : '',
                location: $scope.multipleLocation ? $scope.multipleLocation : '',
                serviceId: _HYPERLOCAL_
            };
            $dataExchanger.request.data = $scope.buyersearch;
            $state.go("hp-buyer-search-list");
        }
    };
}]);
