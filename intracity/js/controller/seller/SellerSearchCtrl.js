app.controller('SellerSearchCtrl', ['$scope', '$http', 'config', 'trackings', 'apiServices', '$compile', '$state', '$stateParams', '$dataExchanger', function ($scope, $http, config, trackings, apiServices, $compile, $state, $stateParams, $dataExchanger) {

    $scope.sellersearch = {
        type: "",
        city: "",
        fromLocation: "",
        toLocation: "",
        dispatchDate: "",
        timeSlot: "",
        vehicle_type: ""
    };
    var serverUrl = config.serverUrl;
    $scope.timeslot = TIME_SLOT;

    var specialKeys = new Array();
    specialKeys.push(8); //Backspace
    specialKeys.push(9); //Tab
    specialKeys.push(46); //Delete
    specialKeys.push(36); //Home
    specialKeys.push(35); //End
    specialKeys.push(37); //Left
    specialKeys.push(39); //Right

    $('.cityValidation').keypress(function (e) {
        var keyCode = e.keyCode == 0 ? e.charCode : e.keyCode;
        var ret = ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || 
            (keyCode >= 97 && keyCode <= 122) || (specialKeys.indexOf(e.keyCode) != -1 && e.charCode != e.keyCode));
          return ret;
    });
    /**********************************************************
     *
     *        Get city
     *
     *************************************************************/
    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
        });
    };
    $scope.getCity(url);
    /****************************************************************
     *
     *           get getVehiletype
     *
     *****************************************************************/
    $scope.vehicles = vechileType;
    $scope.hourDistanceSlabs = distanceHourSlab;
    /****************************************************************
     *
     * get city location
     ***************************************************************/
    $scope.selectLocation = function (city_id) {

        url = serverUrl + 'locations/getlocality/' + city_id;
        apiServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;

        });
    };

    $('#searchSeller').click(function () {
        var isValidated = true;
        $('#city').css('border-color', '');
        $('#city').css('border-color', '');
        $('#reporting').css('border-color', '');
        $('#hourcity').css('border-color', '');
        $('#hour_from_calendar').css('border-color', '');
        $('#valid_to_date').css('border-color', '');
        $('#to_location').css('border-color', '');
        $('#valid_to_date').css('border-color', '');
        $('#from_location').css('border-color', '');
        var city = $.trim($('#city').val());
        if (city == '' && $scope.data.type == 2) {
            $('#city').css('border-color', 'red');
            $('#city').focus();
            isValidated = false;
        }
        var valid_to_date = $.trim($('#valid_to_date').val());
        if (valid_to_date == '' && $scope.data.type == 2) {
            $('#valid_to_date').css('border-color', 'red');
            $('#valid_to_date').focus();
            isValidated = false;
        }
        var hourcity = $.trim($('#hourcity').val());
        if (hourcity == '' && $scope.data.type == 1) {
            $('#hourcity').css('border-color', 'red');
            $('#hourcity').focus();
            isValidated = false;
        }
        var hour_from_calendar = $.trim($('#hour_from_calendar').val());
        if (hour_from_calendar == '' && $scope.data.type == 1) {
            $('#hour_from_calendar').css('border-color', 'red');
            $('#hour_from_calendar').focus();
            isValidated = false;
        }
        var reporting = $.trim($('#reporting').val());
        if (reporting == '' && $scope.data.type == 1) {
            $('#reporting').css('border-color', 'red');
            $('#reporting').focus();
            isValidated = false;
        }


        $dataExchanger.$default({request: {serviceId: "", serviceName: "", fullName: "", imagePath: "", data: {}}});

        if (isValidated) {

            var searchdata = $scope.data;

            $scope.sellersearch = {
                reporting: $scope.data.reporting ? $scope.data.reporting : '',
                city: $scope.data.city ? $scope.data.city : $scope.data.hourcity,
                fromLocation: $scope.data.from_location ? $scope.data.from_location : {"id": "", "locality_name": ""},
                toLocation: $scope.data.to_location ? $scope.data.to_location : {"id": "", "locality_name": ""},
                dispatchDate: $scope.data.valid_to_date ? $scope.data.valid_to_date : $scope.data.dispatchdate,
                timeSlot: $scope.data.time_slot ? $scope.data.time_slot : $scope.data.hour_time_slot,
                termPost: $scope.data.termPost ? $scope.data.termPost : '',
                type: $scope.data.type ? $scope.data.type : '',
                vehicle_type: $scope.data.vehicle_type ? $scope.data.vehicle_type : $scope.data.hour_vehicle_type,
                serviceId: _INTRACITY_
            };

            $dataExchanger.request.data = $scope.sellersearch;

            $state.go("seller-search-result");
        }


    });

    /*************find location on change******************/
    $scope.locations = [];
    $scope.onSelect = function (data) {
        var city_id;
        console.log('location', data);

        var city_id = parseInt(data.id);
        if (typeof(city_id) != NaN || city_id != '') {

            url = serverUrl + 'locations/getlocality/' + city_id;
            apiServices.getLocationByCity(url).then(function (response) {
                $scope.locations = response;

            });

        }
    };
    /***************distance slab*************************/
    $scope.hourDistanceSlab = function (url) {
        apiServices.getMethod(url).then(function (response) {
            $scope.hourDistanceSlabs = response;
            // console.log($scope.hourDistanceSlabs);
        });
    };
    $scope.hourDistanceSlab(serverUrl + 'get-hour-distance-labs');

}]);
