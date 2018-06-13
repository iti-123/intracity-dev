app.controller('BuyerSearchCtrl', ['$scope', '$http', 'config', 'trackings', 'apiServices', '$compile', '$state', '$stateParams', '$dataExchanger', 'validateService', function ($scope, $http, config, trackings, apiServices, $compile, $state, $stateParams, $dataExchanger, validateService) {

    var serverUrl = config.serverUrl;
    $scope.timeSlot = TIME_SLOT;
    $scope.transitHour = TRANSIT_HOUR;

    $scope.data = {
        type: "",
        city: "",
        reporting: "",
        base_hour: "",
        date: "",
        hour_city: "",
        fromLocation: "",
        toLocation: "",
        dispatchDate: "",
        timeSlot: "",
        vehiclesType: "",
        unitsOfMeasurement: "",
        errors: {
            type: "",
            city: "",
            hour_city: "",
            date: "",
            base_hour: "",
            reporting: "",
            fromLocation: "",
            toLocation: "",
            dispatchDate: "",
            timeSlot: "",
            vehiclesType: "",
            unitsOfMeasurement: ""
        },
        isValid: false
    };


    $scope.SearchModel = function (data) {
        var isValid = true;
        console.log("Data Validator::", data);

        if (typeof data.city != 'object') {
            isValid = false;
            $scope.data.errors.city = 'Enter valid city ';
        } else {
            //isValid = true;
            $scope.data.errors.city = '';
        }

        if (typeof data.hour_city != 'object') {
            isValid = false;
            $scope.data.errors.hour_city = 'Enter valid city ';
        } else {
            //isValid = true;
            $scope.data.errors.hour_city = '';
        }
         if (typeof data.reporting != 'object') {
            isValid = false;
            $scope.data.errors.reporting = 'Enter valid Location ';
        } else {
            //isValid = true;
            $scope.data.errors.reporting = '';
        }
        if (data.base_hour == '') {
            isValid = false;
            $scope.data.errors.base_hour = 'Enter Base Hour ';
        } else {
            //isValid = true;
            $scope.data.errors.base_hour = '';
        }

        if (typeof data.from_location != 'object') {
            isValid = false;
            $scope.data.errors.fromLocation = 'Enter valid from location';
        } else {
            //isValid = true;
            $scope.data.errors.fromLocation = '';
        }

        if (typeof data.to_location != 'object') {
            isValid = false;
            $scope.data.errors.toLocation = 'Enter valid to location';
        } else {
            //isValid = true;
            $scope.data.errors.toLocation = '';
        }


        if (!(moment(data.valid_to_date, 'YYYY-MM-DD').isValid())) {
            isValid = false;
            $scope.data.errors.dispatchDate = 'Enter valid dispatch date';
        } else {
            //isValid = true;
            $scope.data.errors.dispatchDate = '';
        }
         if (!(moment(data.date, 'YYYY-MM-DD').isValid())) {
            isValid = false;
            $scope.data.errors.date = 'Enter a valid date';
        } else {
            //isValid = true;
            $scope.data.errors.date = '';
        }

        return isValid;
    }

    function isValidDate(dateString) {

    }

   $scope.hourDistanceSlabs = distanceHourSlab;
   $scope.vehicles = vechileType;
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

    var url = serverUrl + 'locations/getVehiletype';
    $scope.getVehiclesType = function (url) {
        apiServices.vehiclesType(url).then(function (response) {
            $scope.vehicles = response;
        });
    };


    $scope.getVehiclesType(url);

    $('#searchSeller').click(function () {

        if (!$scope.SearchModel($scope.data)) {
           // return false;
        }

        $('#date').css('border-color', '');
        $('#city').css('border-color', '');
        $('#valid_to_date').css('border-color', '');
        $('#to_location').css('border-color', '');
        $('#valid_to_date').css('border-color', '');
        $('#from_location').css('border-color', '');
        $('#hour_city').css('border-color', '');
        if ($scope.data.type == 2) {

            var city = $.trim($('#city').val());
            var isValidated = true;

            if (city == '') {
                $('#city').css('border-color', 'red');
                $('#city').focus();
                isValidated = false;
            }

            var from_location = $.trim($('#from_location').val());
            if (from_location == '') {
                $('#from_location').css('border-color', 'red');
                $('#from_location').focus();
                isValidated = false;
            }
            var to_location = $.trim($('#to_location').val());
            if (to_location == '') {
                $('#to_location').css('border-color', 'red');
                $('#to_location').focus();
                isValidated = false;
            }

            var valid_to_date = $.trim($('#valid_to_date').val());
            if (valid_to_date == '') {
                $('#valid_to_date').css('border-color', 'red');
                $('#valid_to_date').focus();
                isValidated = false;
            }
        } else if ($scope.data.type == 1) {
            var hour_city = $.trim($('#hour_city').val());
            var isValidated = true;

            if (hour_city == '') {
                $('#hour_city').css('border-color', 'red');
                $('#hour_city').focus();
                isValidated = false;
            }
            var date = $.trim($('#date').val());
            if (date == '') {
                $('#date').css('border-color', 'red');
                $('#date').focus();
                isValidated = false;
            }

        }


        $dataExchanger.$default({
            request: {
                serviceId: SERVICE_ID,
                serviceName: SERVICE_NAME,
                fullName: "",
                imagePath: "",
                data: {}
            }
        });

        if (isValidated) {
            var searchdata = $scope.data;

            $scope.buyersearch = {
                type: searchdata.type ? searchdata.type : '',
                city: searchdata.city ? searchdata.city : '',
                time_slot: searchdata.timeSlot ? searchdata.timeSlot : '',
                time_slot_to: searchdata.time_slot_to ? searchdata.time_slot_to : '',
                hour_city: searchdata.hour_city ? searchdata.hour_city : '',
                reporting: searchdata.reporting ? searchdata.reporting : '',
                base_hour: searchdata.base_hour ? searchdata.base_hour : '',
                date: searchdata.date ? searchdata.date : '',
                hour_vehicle_type: searchdata.hour_vehicle_type ? searchdata.hour_vehicle_type : '',
                hour_vehicle_type: searchdata.hour_vehicle_type ? searchdata.hour_vehicle_type : '',
                h_timeslot_from: searchdata.h_timeslot_from ? searchdata.h_timeslot_from : '',
                h_timeslot_to: searchdata.h_timeslot_to ? searchdata.h_timeslot_to : '',
                period: searchdata.period ? searchdata.period : 'AM',

                fromLocation: searchdata.from_location ? searchdata.from_location : '',
                toLocation: searchdata.to_location ? searchdata.to_location : '',
                dispatchDate: searchdata.valid_to_date ? searchdata.valid_to_date : searchdata.date,

                vehiclesType: searchdata.vehicle_type ? searchdata.vehicle_type : '',
                unitsOfMeasurement: "",
                serviceId: 3
            };

            // var searchdata = JSON.stringify(searchdata);
            $dataExchanger.request.data = $scope.buyersearch;

            console.log("$scope.buyersearch :: ", $scope.buyersearch);
            console.log("DataExchanger :: ", $dataExchanger.request);

            $state.go("buyer-search-result");
        }
    });
    /*************find location on change******************/
    $scope.locations = [];
    $scope.onSelect = function (data) {
        var city_id;
        console.log('location', data);
        var city_id = parseInt(data.id);
        console.log("OK", isNaN(city_id));
        if (!isNaN(city_id)) {
            url = serverUrl + 'locations/getlocality/' + city_id;
            apiServices.getLocationByCity(url).then(function (response) {
                $scope.locations = response;

            });
        }
    };

    $scope.toTime = angular.copy($scope.timeSlot);
    $scope.removedTime = {};
    $scope.removeToTime = function (selectedFromTime) {
        for (var v in $scope.removedTime) {
            console.log(v);
            $scope.toTime[v] = $scope.removedTime[v];
        }
        console.log('s', $scope.toTime);
        for (var t in $scope.toTime) {
            if ($scope.toTime[t] == selectedFromTime) {
                $scope.removedTime[t] = selectedFromTime;
                console.log($scope.toTime[t]);
                delete $scope.toTime[t];
                break;
            }
        }
        console.log('e', $scope.toTime);
        // for (var i = 0; i < $scope.toTime.length; i++) {
        // if ($scope.toTime[i] == selectedFromTime) {
        //     console.log($scope.toTime[i]);
        //     $scope.toTime.splice(i, 1);
        //     break;
        // }
        // }
        // console.log('TIME SLOT REMOVE',$scope.toTime);
    };

    $scope.showHide = function (id) {

       // $scope.data = "";

       // if (id == 2) {
            $scope.data.hour_city = '';
            $scope.data.reporting = '';
            $scope.data.base_hour = '';
            $scope.data.date = '';
            $scope.data.hour_vehicle_type = '';
            $scope.data.h_timeslot_from = '';
            $scope.data.h_timeslot_to = '';
            $scope.data.errors.hour_city = '';
            $scope.data.errors.reporting = '';
            $scope.data.errors.base_hour = '';
            $scope.data.errors.date = '';
            $scope.data.errors.hour_vehicle_type = '';
            $scope.data.errors.h_timeslot_from = '';
            $scope.data.errors.h_timeslot_to = '';
             $scope.data.errors.dispatchDate = '';
      //  }
      //  else if (id == 1) {
            $scope.data.city = '';
            $scope.data.from_location = '';
            $scope.data.to_location = '';
            $scope.data.valid_to_date = '';
            $scope.data.valid_to_date = '';
            $scope.data.time_slot = '';
            $scope.data.time_slot_to = '';
            $scope.data.vehicle_type = '';
            $scope.data.errors.city = '';
            $scope.data.errors.fromLocation = '';
            $scope.data.errors.toLocation = '';
            $scope.data.errors.valid_to_date = '';
            $scope.data.errors.valid_to_date = '';
            $scope.data.errors.time_slot = '';
            $scope.data.errors.time_slot_to = '';
            $scope.data.errors.vehicle_type = '';

       // }
    }

    //$scope.cityRegex = '/[A-Z]{5}\d{4}[A-Z]{1}/i';






}]);