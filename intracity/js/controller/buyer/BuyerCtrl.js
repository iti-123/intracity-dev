app.controller('BuyerCtrl', ['$scope', '$http', '$state', 'config', 'trackings', 'apiServices', '$compile', 'validateService', 'GoogleMapService', '$dataExchanger', function ($scope, $http, $state, config, trackings, apiServices, $compile, validateService, GoogleMapService, $dataExchanger) {
    
        var serverUrl = config.serverUrl;
        var authToken = config.appAuthToken;
        $scope.data = [];
        $scope.routes = [];
        $scope.setAppTitle($state);
    
        $scope.timeslot = TIME_SLOT;
    
        
        $scope.trackings = trackings.type;
        $scope.priceTypes = PRICE_TYPES;
        $scope.units = ESTIMATED_UNIT;
        $scope.calculateDistance = function (fromLocation, toLocation) {
            console.log("fromLocation", fromLocation);
            GoogleMapService.calculateDistance(fromLocation, toLocation).then(function (response) {
                console.log(response);
                $scope.routes = response;
            });
        }
    
        /* 
        *   Prefilled Buyer post form accourding to search data  
        */

        var searchData = $dataExchanger.request.data;
    
        console.log("isPrefilled", searchData);
    
        if (searchData.isPrefilled) {
            $scope.dataSpot = {
                city: searchData.city,
                d_valid_from: searchData.dispatchDate,
                d_from_location: searchData.fromLocation,
                d_to_location: searchData.toLocation,
                d_vehicle_reporting_time: searchData.time_slot,
                d_vehicle_type_any: searchData.vehiclesType,
                type_basis: 'distance_basis'
            };
        }
    
        var url = serverUrl + 'locations/getCity';
        $scope.getCity = function (url) {
            apiServices.city(url).then(function (response) {
                $scope.cities = response;
                // console.log($scope.cities);
            });
        }
        //  Get city of intracity 
        $scope.getCity(url);
    
        // $scope.materialType = MATERIAL_TYPE;
    
    
        // console.log("Material Type ::", $scope.materialType);
        // Get Location By City 
    
        $scope.selectLocation = function (city_id) {
            $scope.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id);
            // console.log(city_id);
        }
    
        $scope.selectNewFromLocation = function (operator) {
            var num = operator.toString();
            var idx = $scope.NewFromLocation.indexOf(num);
            if (idx > -1)
                $scope.NewFromLocation.splice(idx, 1);
            else
                $scope.NewFromLocation.push(num);
            if ($scope != null)
                $scope.NewFromLocation = $scope.NewFromLocation;
    
        };
    
        $scope.getLocationByCity = function (url) {
            apiServices.getLocationByCity(url).then(function (response) {
                $scope.locations = response;
                // console.log("Location By City Id::", $scope.locations);
            });
        }
    
        /*** Get Location By Id ***/
        $scope.onSelect = function (data) {
            //
            console.log(data);
        }
        $scope.data.city_id = { id: '' };
        $scope.onSelect = function (data) {
            console.log("City Id::", parseInt(data.id));
            var city_id = parseInt(data.id);
            if (typeof (city_id) != NaN || city_id != '') {
                $scope.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id);
                // $scope.getListBuyerAccordingFilter(url, city_id);
            }
        }
    
        $scope.removeToCity = function (selectedFromLocation) {
            $scope.toLocations = angular.copy($scope.locations);
            for (var i = 0; i < $scope.toLocations.length; i++) {
                if ($scope.toLocations[i].id == selectedFromLocation.id) {
                    $scope.toLocations.splice(i, 1);
                    break;
                }
            }
        }
    
    
        // Get Vehicle Type 
    
        
      
    
        $scope.addRout = function () {
            console.log($scope.data);
        }
        /**
         * Show hide Spot Term "hours" and distance
         */
        $scope.showHideSpot = function (type_basis, dataSpot, dataLoads) {
            // console.log(type_basis);
            console.log('guru1', dataSpot.last_date)
            if (type_basis == 'hours') {
                $('#Hour_cont1').addClass('activeintra');
                $('#Distance_cont1').removeClass('activeintra');
            } else if (type_basis == 'distance_basis') {
                $('#Distance_cont1').addClass('activeintra');
                $('#Hour_cont1').removeClass('activeintra');
            } else if (type_basis == 'term_hours') {
                $('#Hour_cont').addClass('activeintra');
                $('#Distance_cont').removeClass('activeintra');
            } else if (type_basis == 'term_distance') {
                $('#Distance_cont').addClass('activeintra');
                $('#Hour_cont').removeClass('activeintra');
            }
    
            $scope.city_validation = false;
            $scope.title_validatoin = false;
            $scope.reporting_location_validation = false;
            $scope.departure_validation = false;
    
            $scope.hd_validation = false;
    
            $scope.no_of_vehicles_validation = false;
    
            $scope.d_no_of_vehicle_validation = false;
    
            $scope.d_no_of_vehicle_validation = false;
    
            $scope.vehicle_reporting_time_validation = false;
    
            $scope.price_type_validation = false;
    
            $scope.form_location_validation = false;
            $scope.d_material_type_validation = false;
            $scope.estimatedQty_validation = false;
            $scope.unit_validation = false;
    
            $scope.d_vehicle_type_any_validation = false;
            $scope.d_valid_from_validation = false;
            $scope.d_valid_from_validation = false;
    
            $scope.d_vehicle_reporting_time_validation = false;
            $scope.d_vehicle_type_any_validation = false;
            $scope.d_to_location_validation = false;
            $scope.term_hour_city_validation = false;
            $scope.term_hour_hd_validation = false;
            $scope.term_hour_d_vehicle_type_term = false;
            $scope.term_hour_valid_from_term = false;
            $scope.term_hour_valid_to_term = false;
            $scope.term_hour_no_of_vehicles_term = false;
            $scope.term_hour_vehicle_reporting_time_term = false;
            $scope.term_hour_vehicle_reporting_location_term = false;
            $scope.term_distance_title = false;
            $scope.term_distance_cityId = false;
            $scope.term_hour_emd_amount = false;

            $scope.term_hour_emd_mode_term = false;
            $scope.term_hour_emd_amount = false;
            $scope.term_hour_award_criteria = false;
            $scope.term_hour_contract_allotment = false;
            $scope.term_hour_payment_term = false;
            $scope.term_hour_no_of_own_truck_term = false;
            $scope.term_hour_average_turn_over_term = false;
            $scope.term_hour_no_of_years_term = false;
            $scope.term_hour_term_last_date_term = false;
            $scope.term_hour_term_last_time_term = false;
            $scope.spot_hour_last_date = false;
            $scope.spot_hour_last_time = false;
            $scope.term_hour_payment_method = false;
            $scope.term_distance_city_id = false;
            $scope.term_distance_vehicle_reporting_time_dTerm = false;
            $scope.term_distance_to_location = false;
            $scope.term_distance_from_location = false;
            $scope.term_distance_valid_from_dTerm = false;
            $scope.term_distance_valid_to_dTerm = false;
            $scope.term_distance_no_of_vehicle_dTerm = false;
            $scope.term_distance_vehicle_type_any_dTerm = false;
            $scope.term_distance_material_type_dTerm = false;
            $scope.estimatedLoads_validation = false;
            dataSpot.term_distance_city_id= '' ;
            // dataLoads.estimatedLoads = '';
    
            dataSpot.last_date = ''
            dataSpot.last_time = ''
            //hello
        }
    
        $scope.showHideTerm = function (type_basis, dataSpot) {
    
        }
    
        $scope.showHideType = function (type, dataSpot,dataLoads) {
            // $scope.showHide('public');
            console.log('guru', dataSpot.last_date)
            if (type == 'spot') {
                // $state.go('post-buyer-as-spot');
                $('#token-input-sellerListTerm').hide();
                $('#token-input-sellerList').hide();
                $('li.token-input-token').remove();
                $scope.dataSpot.post_type_term = 2;
                $scope.dataTerm.is_private_public = 2;
                $('#spot_d').addClass('activeintra');
                $('#term_d').removeClass('activeintra');
            } else if (type == 'term') {
                // $state.go('post-buyer-as-term');
                $('#token-input-sellerListTerm').hide();
                $('#token-input-sellerList').hide();
                $('li.token-input-token').remove();
                $scope.dataSpot.post_type_term = 2;
                $scope.dataTerm.is_private_public = 2;
                $('#term_d').addClass('activeintra');
                $('#spot_d').removeClass('activeintra');
            }
            $scope.city_validation = false;
            $scope.reporting_location_validation = false;
            $scope.departure_validation = false;
    
            $scope.hd_validation = false;
    
            $scope.no_of_vehicles_validation = false;
    
            $scope.d_no_of_vehicle_validation = false;
    
            $scope.d_no_of_vehicle_validation = false;
    
            $scope.vehicle_reporting_time_validation = false;
    
            $scope.price_type_validation = false;
    
            $scope.form_location_validation = false;
            $scope.d_material_type_validation = false;
            $scope.estimatedQty_validation = false;
            $scope.unit_validation = false;
    
            $scope.d_vehicle_type_any_validation = false;
            $scope.d_valid_from_validation = false;
            $scope.d_valid_from_validation = false;
    
            $scope.d_vehicle_reporting_time_validation = false;
            $scope.d_vehicle_type_any_validation = false;
            $scope.d_to_location_validation = false;
            $scope.term_hour_city_validation = false;
            $scope.term_hour_hd_validation = false;
            $scope.term_hour_d_vehicle_type_term = false;
            $scope.term_hour_valid_from_term = false;
            $scope.term_hour_valid_to_term = false;
            $scope.term_hour_no_of_vehicles_term = false;
            $scope.term_hour_vehicle_reporting_time_term = false;
            $scope.term_hour_vehicle_reporting_location_term = false;
            $scope.term_hour_emd_amount = false;
            $scope.term_hour_emd_mode_term = false;
            $scope.term_hour_emd_amount = false;
            $scope.term_hour_award_criteria = false;
            $scope.term_hour_contract_allotment = false;
            $scope.term_hour_payment_term = false;
            $scope.term_hour_no_of_own_truck_term = false;
            $scope.term_hour_average_turn_over_term = false;
            $scope.term_hour_no_of_years_term = false;
            $scope.term_hour_term_last_date_term = false;
            $scope.term_hour_term_last_time_term = false;
            $scope.spot_hour_last_date = false;
            $scope.spot_hour_last_time = false;
            $scope.term_hour_payment_method = false;
            $scope.term_distance_city_id = false;
            $scope.term_distance_vehicle_reporting_time_dTerm = false;
            $scope.term_distance_to_location = false;
            $scope.term_distance_from_location = false;
            $scope.term_distance_valid_from_dTerm = false;
            $scope.term_distance_valid_to_dTerm = false;
            $scope.term_distance_no_of_vehicle_dTerm = false;
            $scope.term_distance_vehicle_type_any_dTerm = false;
            $scope.term_distance_material_type_dTerm = false;
            $scope.estimatedLoads_validation = false;
            // dataLoads.estimatedLoads = '';
            dataSpot.term_distance_city_id= '';
    
            dataSpot.last_date = ''
            dataSpot.last_time = ''
            $scope.multipleLoads = [];
        }
    
        $scope.checkLeadType = function (val) {
            if (val == 'term') {
    
            }
        };
    
        /************************************************
         *
         *  Post Buyer Term Code Start Here
         *
         ************************************************
         */
    
    
        
    
        $scope.hourDistanceSlabs = distanceHourSlab;
        $scope.vehicles = vechileType;
        $scope.materialType =material;
    
    
    
        // List dataSpot 
        $scope.listdataSpot = [];
        $scope.listDistancedataSpot = [];
        $scope.listdataTerm = [];
        $scope.listDistancedataTerm = [];
        // Validate incomming form data  
    
    
        $scope.addMore = function (data, dataloads) {
            // if(data.type == spot && data.type_basis ==  hours){
    
            if (typeof data.city != 'object') {
                //alert('Please select correct city');
                $scope.city_validation = true;
            }
            else {
                $scope.city_validation = false;
            }
            // }
    
            if (typeof data.vehicle_reporting_location != 'object') {
                //alert('Please select correct city');
                $scope.reporting_location_validation = true;
            }
            else {
                $scope.reporting_location_validation = false;
    
            }
            if (!data.departure == '') {
                //alert('Please select correct city');
                $scope.departure_validation = false;
            }
            else {
                $scope.departure_validation = true;
            }
            if (!data.hd_slab == '') {
                //alert('Please select correct city');
                $scope.hd_validation = false;
            }
            else {
                $scope.hd_validation = true;
            }
            if (!data.no_of_vehicles == '') {
                if(data.no_of_vehicles == 0){
                  $scope.zero_no_of_vehicles = true;
                  $scope.no_of_vehicles_validation = false;
                  return false;
                }else{
                  $scope.zero_no_of_vehicles = false;
                }
                $scope.no_of_vehicles_validation = false;
            }
            else {
                $scope.no_of_vehicles_validation = true;
                $scope.zero_no_of_vehicles = false;
            }
            if (!data.d_no_of_vehicle == '') {
                 if(data.d_no_of_vehicle == 0){
                  $scope.zero_d_no_of_vehicle = true;
                  $scope.d_no_of_vehicle_validation = false;
                  return false;
                }else{
                  $scope.zero_d_no_of_vehicle = false;
                }
                $scope.d_no_of_vehicle_validation = false;
            }
            else {
                $scope.d_no_of_vehicle_validation = true;
                $scope.zero_d_no_of_vehicle = false;
            }
    
            if (!data.vehicle_reporting_time == '') {
                //alert('Please select correct city');
                $scope.vehicle_reporting_time_validation = false;
            }
            else {
                $scope.vehicle_reporting_time_validation = true;
            }
            if (!data.price_type == '') {
                //alert('Please select correct city');
                $scope.price_type_validation = false;
            }
            else {
                $scope.price_type_validation = true;
            }
            if (!data.d_from_location == '' && typeof data.d_from_location == 'object') {
                //alert('Please select correct city');
                $scope.form_location_validation = false;
            }
            else {
                $scope.form_location_validation = true;
            }
            if (!data.d_to_location == '' && typeof data.d_to_location == 'object') {
                //alert('Please select correct city');
                $scope.d_to_location_validation = false;
            }
            else {
                $scope.d_to_location_validation = true;
            }
            if (!data.d_material_type == '') {
                //alert('Please select correct city');
                $scope.d_material_type_validation = false;
            }
            else {
                $scope.d_material_type_validation = true;
            }
            //if (data.type == spot || data.type == term ) {
            // if (!dataloads.estimatedQty == '') {
            //     //alert('Please select correct city');
            //     $scope.estimatedQty_validation = false;
            // }
            // else {
            //     $scope.estimatedQty_validation = true;
            // }
            // }
            // if (data.type == spot) {
            // if (!dataloads.unit == '') {
            //     //alert('Please select correct city');
            //     $scope.unit_validation = false;
            // }
            // else {
            //     $scope.unit_validation = true;
            // }
            // }
            if (!data.d_valid_from == '') {
                //alert('Please select correct city');
                $scope.d_valid_from_validation = false;
            }
            else {
                $scope.d_valid_from_validation = true;
            }
            if (!data.d_vehicle_reporting_time == '') {
                //alert('Please select correct city');
                $scope.d_vehicle_reporting_time_validation = false;
            }
            else {
                $scope.d_vehicle_reporting_time_validation = true;
            }
            if (!data.d_vehicle_type_any == '') {
                //alert('Please select correct city');
    
                $scope.d_vehicle_type_any_validation = false;
            }
            else {
                $scope.d_vehicle_type_any_validation = true;
            }
            if (typeof data.term_city_id != 'object') {
                //alert('Please select correct city');
    
                $scope.term_hour_city_validation = true;
            }
            else {
                $scope.term_hour_city_validation = false;
            }
            if (!data.hd_slab_term == '') {
                //alert('Please select correct city');
    
                $scope.term_hour_hd_validation = false;
            }
            else {
                $scope.term_hour_hd_validation = true;
            }
            if (!data.d_vehicle_type_term == '') {
                $scope.term_hour_d_vehicle_type_term = false;
            }
            else {
                $scope.term_hour_d_vehicle_type_term = true;
            }
            if (!data.valid_from_term == '') {
                $scope.term_hour_valid_from_term = false;
            }
            else {
                $scope.term_hour_valid_from_term = true;
            }
            if (!data.valid_to_term == '') {
                $scope.term_hour_valid_to_term = false;
            }
            else {
                $scope.term_hour_valid_to_term = true;
            }
            if (!data.no_of_vehicles_term == '') {
                if(data.no_of_vehicles_term == 0){
                  $scope.zero_no_of_vehicles_term = true;
                  $scope.term_hour_no_of_vehicles_term = false;
                  return false;
                }else{
                  $scope.zero_no_of_vehicles_term = false;
                }
                $scope.term_hour_no_of_vehicles_term = false;
            }
            else {
                $scope.zero_no_of_vehicles_term = false;
                $scope.term_hour_no_of_vehicles_term = true;
            }
            if (!data.vehicle_reporting_time_term == '') {
                $scope.term_hour_vehicle_reporting_time_term = false;
            }
            else {
                $scope.term_hour_vehicle_reporting_time_term = true;
            }
            if (typeof data.vehicle_reporting_location_term != 'object') {
                $scope.term_hour_vehicle_reporting_location_term = true;
            }
            else {
                $scope.term_hour_vehicle_reporting_location_term = false;
            }
            if (data.term_distance_city_id != 'object') {
                $scope.term_distance_city_id = true;
            }
            else {
                $scope.term_distance_city_id = false;
            }
            if (!data.vehicle_reporting_time_dTerm == '') {
                $scope.term_distance_vehicle_reporting_time_dTerm = false;
            }
            else {
                $scope.term_distance_vehicle_reporting_time_dTerm = true;
            }
            if (!data.from_location == '') {
                $scope.term_distance_from_location = false;
            }
            else {
                $scope.term_distance_from_location = true;
            }
            if (!data.to_location == '') {
                $scope.term_distance_to_location = false;
            }
            else {
                $scope.term_distance_to_location = true;
            }
            if (!data.valid_from_dTerm == '') {
                $scope.term_distance_valid_from_dTerm = false;
            }
            else {
                $scope.term_distance_valid_from_dTerm = true;
            }
            if (!data.valid_to_dTerm == '') {
                $scope.term_distance_valid_to_dTerm = false;
            }
            else {
                $scope.term_distance_valid_to_dTerm = true;
            }
            if (!data.no_of_vehicle_dTerm == '') {
                if(data.no_of_vehicle_dTerm == 0){
                  $scope.zero_no_of_vehicle_dTerm = true;
                  $scope.term_distance_no_of_vehicle_dTerm = false;
                  return false;
                }else{
                  $scope.zero_no_of_vehicle_dTerm = false;
                }
                $scope.term_distance_no_of_vehicle_dTerm = false;
            }
            else {
                $scope.zero_no_of_vehicle_dTerm = false;
                $scope.term_distance_no_of_vehicle_dTerm = true;
            }
    
    
    
            if (!data.material_type_dTerm == '') {
                $scope.term_distance_material_type_dTerm = false;
            }
            else {
                $scope.term_distance_material_type_dTerm = true;
            }
            if (!data.vehicle_type_any_dTerm == '') {
                $scope.term_distance_vehicle_type_any_dTerm = false;
            }
            else {
                $scope.term_distance_vehicle_type_any_dTerm = true;
            }
            // if (!dataloads.estimatedLoads == '') {
            //     $scope.estimatedLoads_validation = false;
            // }
            // else {
            //     $scope.estimatedLoads_validation = true;
            // }
    
    
    
    
    
            // }
    
            if (validate(data)) {
                
               
                if (typeof (data.length) != 'undefined' || data.length != '') {
                    if ($scope.update_id === '' || $scope.update_id == undefined) {
                        // Lets Insert
                        if (data.type_basis == 'hours') {
                            $scope.new = angular.copy(data);
                            $scope.listdataSpot.push($scope.new);
                            console.log("$scope.listdataSpot", $scope.listdataSpot);
                        } else if (data.type_basis == 'distance_basis') {
                            $scope.new = angular.copy(data);
                            $scope.listDistancedataSpot.push($scope.new);
                        } else if (data.type_basis == 'term_hours') {
                            $scope.new = angular.copy(data);
                            $scope.listdataTerm.push($scope.new);
                            console.log("term_hours");
                            console.log($scope.listdataTerm);
                        } else if (data.type_basis == 'term_distance') {
                            $scope.new = angular.copy(data);
                            $scope.listDistancedataTerm.push($scope.new);
                        }
    
                        $scope.clearField();
    
                    } else {
                        // console.log("Update");
                        if (data.type_basis == 'hours') {
    
                            $scope.listdataSpot.splice($scope.update_id, 1);
                            $scope.new = angular.copy(data);
                            $scope.listdataSpot.push($scope.new);
    
                        } else if (data.type_basis == 'distance_basis') {
    
                            $scope.listDistancedataSpot.splice($scope.update_id, 1);
                            $scope.new = angular.copy(data);
                            $scope.listDistancedataSpot.push($scope.new);
    
                        } else if (data.type_basis == 'term_distance') {
    
                            $scope.listDistancedataTerm.splice($scope.update_id, 1);
                            $scope.new = angular.copy(data);
                            $scope.listDistancedataTerm.push($scope.new);
                            console.log($scope.listDistancedataTerm);
    
                        } else if (data.type_basis == 'term_hours') {
                            // Remove existing data
                            $scope.listdataTerm.splice($scope.update_id, 1);
                            // Add New items to array
                            $scope.new = angular.copy($scope.dataTerm);
                            $scope.listdataTerm.push($scope.new);
                            console.log($scope.listdataTerm);
    
                        }
                        $scope.clearField();
                        
                    }
                    // Lets Reset Update Id 
                    $scope.update_id = '';
                } 
    
            }   else {
                $('#addRoutePopup').modal({ 'show': true });
            }
    
            // Lets update 
        }
    
        $scope.clearField = function () {
            $scope.dataSpot.departure = '';
            $scope.dataSpot.city = '';
            $scope.dataSpot.d_valid_from = '';
            $scope.dataSpot.d_to_location = '';
            $scope.dataSpot.d_no_of_vehicle = '';
            $scope.dataSpot.d_vehicle_type_any = '';
            $scope.dataSpot.d_material_type = '';
            $scope.dataSpot.price_type = '';
            $scope.dataLoads.unit = '';
            //$scope.dataSpot.title = '';
            $scope.vehicle_reporting_location = '';
            $scope.dataLoads.estimatedLoads = '';
            $scope.dataLoads.estimatedQty = '';
            $scope.dataTerm.term_city_id = '';
            // $scope.dataTerm.term_distance_title = '';
            $scope.dataTerm.hd_slab_term = '';
            $scope.dataTerm.d_vehicle_type_term = '';
            $scope.dataTerm.no_of_vehicles_term = '';
            $scope.dataTerm.vehicle_reporting_location_term = '';
            $scope.dataTerm.term_distance_city_id = '';
            $scope.dataTerm.from_location = '';
            $scope.dataTerm.to_location = '';
            $scope.dataSpot.no_of_vehicles = '';
            $scope.dataSpot.d_vehicle_reporting_time = '';
            $scope.dataSpot.d_from_location = '';
            $scope.dataTerm.valid_from_term = '';
            $scope.dataTerm.valid_to_term = '';
            $scope.dataTerm.vehicle_reporting_time_term = '';
            $scope.dataTerm.vehicle_reporting_time_dTerm = '';
            $scope.dataTerm.valid_from_dTerm = '';
            $scope.dataTerm.valid_to_dTerm = '';
            $scope.dataTerm.vehicle_type_any_dTerm = '';
            $scope.multipleLoads = [];
        }
    
        // Edit term hours
        $scope.editTermHour = function (key, value) {
            $scope.dataTerm = value;
            $scope.update_id = key;
            console.log($scope.dataTerm);
        }
    
        // Edit Term Distance post
        $scope.editTermDistance = function (key, value) {
            
            $scope.dataTerm = value;
            $scope.multipleLoads = value.loads;
            $scope.update_id = key;
            console.log($scope.listDistancedataTerm);
        }
    
        // Delete term hours
        $scope.deleteTermHour = function (key) {
            $scope.listdataTerm.splice(key, 1);
        }
        $scope.deleteTermDistance = function (key) {
            $scope.listDistancedataTerm.splice(key, 1);
        }
    
        $scope.editSpotDistance = function (key, value) {
            console.log("EDIT DATA SPOT::", value);
    
            if ($scope.dataSpot.type_basis == 'hours') {
                $scope.dataSpot = value;
            } else if ($scope.dataSpot.type_basis == 'distance_basis') {
                $scope.multipleLoads = value.loads;
                $scope.dataSpot = value;
            }
            $scope.update_id = key;
            console.log("$scope.multiLoads", $scope.multipleLoads);
        }
        $scope.deleteSpotDistance = function (key) {
            if ($scope.dataSpot.type_basis == 'hours') {
                $scope.listdataSpot.splice(key, 1);
            } else if ($scope.dataSpot.type_basis == 'distance_basis') {
                $scope.listDistancedataSpot.splice(key, 1);
            }
        }
    
        // Edit dataSpot 
    
        $scope.editPostTerm = function (value) {
            $scope.dataSpot = value;
            $scope.multiLoads = value.loads;
        }
    
    
        $scope.counter_i = 1;
        $scope.counter_e = 1;
        $scope.counter_idiv = 1;
        $scope.counter_ediv = 1;
    
        $scope.addMorePriceInclusive = function () {
            // Add more price-inclusive
            if ($scope.counter_idiv <= 4) {
                var html = '<div class="ip_' + $scope.counter_idiv + '"><div class="col-lg-8"> <div class="select_form_intra">  <input type="text" ng-model="dataTerm.price_inclusive[' + $scope.counter_i + ']" class="input_ani" required=""><label class="lbl_text2">Max 5 fields-each field Free Text 20 Char</label> </div> </div> <div class="col-lg-2 col-md-2" style="padding-top: 18px;" > <a style="cursor:pointer" ng-click="removeElementip($event)"> <i class="fa fa-trash-o m-top-15"> </i> </a></div></div></div>';
                el = document.getElementById('price-inclusive');
    
                angular.element(el).append($compile(html)($scope));
                $scope.counter_i++;
                $scope.counter_idiv++;
            }
        }
    
        $scope.addMorePriceExclusive = function () {
            if ($scope.counter_ediv <= 4) {
                var html = '<div class="ep_' + $scope.counter_ediv + '"> <div class="col-lg-8"><div class="select_form_intra"><input type="text" ng-model="dataTerm.price_exclusive[' + $scope.counter_e + ']" class="input_ani" required=""><label class="lbl_text2">Max 5 fields-each field Free Text 20 Char</label></div> </div> <div class="col-lg-2 col-md-2" style="padding-top: 18px;" > <a style="cursor:pointer" ng-click="removeElementep($event)"> <i class="fa fa-trash-o m-top-15"> </i> </a> </div></div></div>';
                el = document.getElementById('price-exclusive');
                angular.element(el).append($compile(html)($scope));
                $scope.counter_e++;
                $scope.counter_ediv++;
            }
    
        }
    
        $scope.removeElementip = function ($event) {
            angular.element($event.currentTarget).parent().parent().remove();
            $scope.counter_idiv = $scope.counter_idiv - 1;
        }
    
        $scope.removeElementep = function ($event) {
            angular.element($event.currentTarget).parent().parent().remove();
            $scope.counter_ediv = $scope.counter_ediv - 1;
        }
    
        $scope.removeElement = function ($event) {
            angular.element($event.currentTarget).parent().parent().remove();
        }
    
        // Add more document upload field 
        $scope.file_counter = 1;
        $scope.addMoreFile = function () {
            var html = '<div> <input id="uploadBtn" ng-model="dataTerm.doc_upload[' + $scope.file_counter + ']" type="file" class="upload" onchange="angular.element(this).scope().setDocument(this)" /> <a style="cursor:pointer" ng-click="removeElement($event)">X</a></div>';
            el = document.getElementById('upload-document');
            angular.element(el).append($compile(html)($scope));
            $scope.file_counter++;
        }
    
        $scope.selectedUser = '';
        $scope.sellerDetails = function (url) {
            apiServices.getMethod(url).then(function (response) {
                $scope.sellerDetails = response;
                // console.log("seller");
                // console.log($scope.sellerDetails);
            });
        }
        $scope.sellerDetails(serverUrl + 'get-seller-details');
        // Auto Select of for private seller 
    
    
        // Handle Submitted data from the form post buyer Spot
    
        $scope.getQuote = function (data, dataSpot) {
            

            // if (dataSpot.type == 'spot' && dataSpot.type_basis == 'hours') {
            if (!dataSpot.last_date == '') {
                $scope.spot_hour_last_date = false;
            }
            else {
                
                $scope.spot_hour_last_date = true;
            }
            if (!dataSpot.last_time == '') {
                
                $scope.spot_hour_last_time = false;
            }
            else {
                
                $scope.spot_hour_last_time = true;
            }
            // }
    
    
            if (data.length) {



                var isValidated = validateQuote($scope.dataSpot);
                if (isValidated) {
                    var requestPayload = {
                        "spotData": JSON.stringify($scope.dataSpot),
                        "attribute": JSON.stringify(data),
                    };
                    console.log("data::", requestPayload);
                    console.log("url::", serverUrl + 'buyer-post-spots');
    
                    if (!$scope.isPrivateSeller($scope.dataSpot.post_type_term, $scope.dataSpot.visibleToSellers))
                        return false;
    
                    $.ajax({
                        url: serverUrl + 'buyer-post-spots',
                        type: "POST",
                        data: requestPayload,
                        // processData: false,
                        // dataType: 'json',
                        headers: {
                            'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                        },
                        success: function (response) {
                            console.log("Response::", response);
                            if (response.isSuccessful) {
    
                                var tran = response.payload.post_transaction_id;  
                                var str = "Your post has been successfully posted to the relevant Seller. Your transacton id is "+" " + tran + " You would be getting lead & enquiries from the seller online."
    
                                $(".statusText button").attr("data-type", response.isSuccessful);
    
                                $("#responsetext").html(str);
    
                                $('#QuoteConfirmationPopup').modal({ 'show': true });
                            } else {
                                $(".statusText button").removeAttr("data-type", response.isSuccessful);
                                $("#responsetext").html("Somthing went wrong please try Again");
    
                                $('#QuoteConfirmationPopup').modal({ 'show': true });
                            }
                        },
                        error: function (err) {
                            console.log("error");
                        }
                    });
                }
    
            } else {
                // $('#addRoutePopup').modal({ 'show': true });
                //alert("Add atleast one Route");
            }
            // console.log("Parent data::", JSON.stringify($scope.dataSpot));
        }
    
    
        /**
         * Handle Submitted data from the form post buyer Term
         */
    
        $scope.storeTerm = function (termRoute, data, postStatus) {
            if($scope.new.valid_from_term || $scope.new.valid_to_term){
                if((data.term_last_date < $scope.new.valid_from_term || data.term_last_date > $scope.new.valid_to_term)){
                    alert('Please select last date in between valid from and valid to date.');
                    return false;
                }
            }
            
            if($scope.new.valid_from_dTerm || $scope.new.valid_to_term){
                if((data.term_last_date < $scope.new.valid_from_dTerm || data.term_last_date > $scope.new.valid_to_dTerm)){
                    alert('Please select last date in between valid from and valid to date.');
                    return false;
                }
            }

            if (!data.emd_amount == '') {
                if(data.emd_amount == 0){
                  $scope.zero_emd_amount = true;
                  $scope.term_hour_emd_amount = false;
                  return false;
                }else{
                  $scope.zero_emd_amount = false;
                }
                $scope.term_hour_emd_amount = false;
            }
            else {
                $scope.term_hour_emd_amount = true;
                $scope.zero_emd_amount = false;
            }
            if (!data.emd_mode == '') {
                $scope.term_hour_emd_mode_term = false;
            }
            else {
                $scope.term_hour_emd_mode_term = true;
            }
            // if (!data.emd_amount == '') {
            //     $scope.term_hour_emd_amount = false;
            // }
            // else {
            //     $scope.term_hour_emd_amount = true;
            // }
            if (!data.award_criteria == '') {
                $scope.term_hour_award_criteria = false;
            }
            else {
                $scope.term_hour_award_criteria = true;
            }
            if (!data.contract_allotment == '') {
                $scope.term_hour_contract_allotment = false;
            }
            else {
                $scope.term_hour_contract_allotment = true;
            }
            if (!data.payment_term == '') {
                $scope.term_hour_payment_term = false;
            }
            else {
                $scope.term_hour_payment_term = true;
            }
            if (!data.payment_method == '') {
                $scope.term_hour_payment_method = false;
            }
            else {
                $scope.term_hour_payment_method = true;
            }
            if (!data.no_of_own_truck == '') {
                if(data.no_of_own_truck == 0){
                  $scope.zero_term_hour_no_of_own_truck_term = true;
                  $scope.term_hour_no_of_own_truck_term = false;
                  return false;
                }else{
                  $scope.zero_term_hour_no_of_own_truck_term = false;
                }
                $scope.term_hour_no_of_own_truck_term = false;
            }
            else {
                $scope.term_hour_no_of_own_truck_term = true;
                $scope.zero_term_hour_no_of_own_truck_term = false;
            }
            if (!data.average_turn_over == '') {
                if(data.average_turn_over == 0){
                  $scope.zero_average_turn_over = true;
                  $scope.term_hour_average_turn_over_term = false;
                  return false;
                }else{
                  $scope.zero_average_turn_over = false;
                }
                $scope.term_hour_average_turn_over_term = false;
            }
            else {
                $scope.term_hour_average_turn_over_term = true;
                $scope.zero_average_turn_over = false;
            }
            if (!data.no_of_years == '') {
                if(data.no_of_years == 0){
                  $scope.zero_no_of_years = true;
                  $scope.term_hour_no_of_years_term = false;
                  return false;
                }else{
                  $scope.zero_no_of_years = false;
                }
                $scope.term_hour_no_of_years_term = false;
            }
            else {
                $scope.term_hour_no_of_years_term = true;
                $scope.zero_no_of_years = false;
            }
            if (!data.term_last_date == '') {
                $scope.term_hour_term_last_date_term = false;
            }
            else {
                $scope.term_hour_term_last_date_term = true;
            }
            if (!data.term_last_time == '') {
                $scope.term_hour_term_last_time_term = false;
            }
            else {
                $scope.term_hour_term_last_time_term = true;
            }
    
    
    
            if (termRoute.length) {
                if (validateQuote(data)) {
    
                    var requestPayload = {
                        "termData": JSON.stringify(data),
                        "attribute": JSON.stringify(termRoute),
                        "postStatus": postStatus
                    };
                    console.log("requestPayload::", requestPayload);
                    if (!$scope.isPrivateSeller(data.is_private_public, data.visibleToSellers))
                        return false;
    
                    $http({
                        method: 'POST',
                        url: serverUrl + 'buyer-post-terms',
                        data: requestPayload,
                        headers: {
                            'Authorization': 'Bearer ' + localStorage.getItem("access_token")
                        }
                    }).then(function (response) {
                        console.log("Response::", response);
                        $scope.uploadBidTermsDocument(response.data.payload.data.id);
                        if (response.data.isSuccessful) {
                            var tran = response.data.payload.data.post_transaction_id;   // raju
                            var str = "Your post has been successfully posted to the relevant sellers. Your transacton id is" + tran + ". You would be getting enquiries & lead from the sellers online."
    
                            $(".statusText button").attr("data-type", response.data.isSuccessful);
    
                            $("#responsetext").html(str);
                            $('#QuoteConfirmationPopup').modal({ 'show': true });
                        } else {
    
                            $("#responsetext").html("Somthing went wrong please try Again");
                            $('#QuoteConfirmationPopup').modal({ 'show': true });
                        }
    
                        console.log("success::", response.data.payload.data.id);
                    });
                }
    
            } else {
                //$('#addRoutePopup').modal({ 'show': true });
                //alert('Add atleast one route');
            }
        }
    
    
        $scope.uploadBidTermsDocument = function (buyerPostTermId) {
            console.log("ID::", $scope.buyerPostTermId);
            var fd = new FormData();
            for (var i = 0; i < $scope.bidDocumentFiles.length; i++) {
                fd.append("uploadFile", $scope.bidDocumentFiles[i]); //file.size > 1024*1024
                fd.append("type", 'bid_term_condition');
                fd.append("buyerPostTermId", buyerPostTermId);
                apiServices.Documentupload(serverUrl, fd).then(function (response) {
                    if (response.isSuccessful) {
                        // $scope.buyerQuoteTerm.attributes.bidTermsAndConditionsDocs.push({ documentId: response.payload.id, documentName: response.payload.file_name });
    
                    } else {
                        clearInterval($scope.checkTimeUpload);
                        $('#validateMsgBody').html('Bid documents upload failed.');
                        $('#alertModalValidateSpot').modal('toggle');
                        i = $scope.bidDocumentFiles.length + 1;
                    }
                });
            }
        }
    
        /*------------------------Get All Sellers--------------------------------*/
    
        apiServices.getAllSellers(serverUrl).then(function (response) {
            $scope.sellerList = response;
            setTimeout(function () {
                $("#sellerList").tokenInput($scope.sellerList, { propertyToSearch: 'username' });
                $("#sellerListTerm").tokenInput($scope.sellerList, { propertyToSearch: 'username' });
            }, 1000);
    
            // console.log("$scope.sellerList::", $scope.sellerList);
        });
    
        /*-----------------------------------------------------------------------------------------*/
    
    
        $scope.isSellerVisible = false;
        $scope.showHide = function (val) {
    
            // console.log($scope.dataSpot);
            // let isValidRoute;
    
            // let isHour = $scope.dataSpot.type_basis;
    
            // if(isHour == 'hours') {
            //     isValidRoute = validateService.isValidRoute($scope.listdataSpot);
            // } else if(isHour == 'distance_basis') {
            //     isValidRoute = validateService.isValidRoute($scope.listDistancedataSpot)
            // } else if(isHour == 'term_hours') {
            //     isValidRoute = validateService.isValidRoute($scope.listdataTerm)
            // } else if(isHour == 'term_distance') {
            //     isValidRoute = validateService.isValidRoute($scope.listDistancedataTerm)
            // } 
    
            // console.log("isValidRoute",isValidRoute);
            // if(isValidRoute) {        
    
            if (val == 'private') {
                $('#token-input-sellerListTerm').show();
                $('#token-input-sellerList').show();
                $scope.isSellerVisible = true;
                $("#sellerList").val('');
                $scope.dataSpot.visibleToSellers = [];
    
            } else {
                $('li.token-input-token').remove();
                $('#token-input-sellerListTerm').hide();
                $('#token-input-sellerList').hide();
                $scope.dataSpot.visibleToSellers = '';
                $scope.dataSpot.visibleToSellers = [];
                $scope.isSellerVisible = false;
                $("#sellerList").val('');
            }
            // } else {
            //     alert("Select atleast on route");
            // }
        };
    
        /*------------------------------------ File Upload Code------------------------------- */
        $scope.setDocument = function (element) {
            // $scope.documentFiles = [];
            $scope.$apply(function (scope) {
                console.log('files:', element.files);
                $scope.documentFiles = element.files[0];
            });
        };
    
        $scope.bidDocumentFiles = [];
        $scope.setBidTermsDocument = function (element) {
            $scope.$apply(function (scope) {
                console.log('files:', element.files);
                $scope.bidDocumentFiles.push(element.files[0])
                console.log($scope.bidDocumentFiles);
            });
    
        };
        /* ----------------------------------*/
        $scope.removeBidDocument = function (index) {
            if(confirm('Are you sure want to delete this item ?')) {
                if ($scope.termUploadDocxs.length == $scope.bidDocumentFiles.length) {
                    $scope.bidDocumentFiles.splice(index, 1);
                    console.log($scope.bidDocumentFiles.length);
                }
                $scope.termUploadDocxs.splice(index, 1);
            }
        }
    
    
        /*------addDocx-------*/
        
        $scope.termUploadDocxs = [{ term_upload_docx: "" }];
        
        $scope.addDocx = function () {
            if ($scope.termUploadDocxs.length <= 4) {
                $scope.termUploadDocxs.push({ term_upload_docx: "" });
            }
        };
    
        /* ------------------validate private and public post ---------*/
    
        $scope.isPrivateSeller = function (isPrivate, visibleToSellers) {
            console.log(isPrivate, visibleToSellers);
    
            if (isPrivate == 1 && visibleToSellers == undefined) {
                return true;
            }
    
            if (isPrivate == 1 && (visibleToSellers == '' || visibleToSellers == undefined)) { // Private Post Seller should 
                alert("Select atleast one seller");
                return false;
            } else {
                return true;
            }
        }
    
    
        /*-------------------- Edit Buyer post data  -------------------------*/
    
        // console.log("$state.params.id::",$state.params.id);
        // console.log("$state.params.id::",typeof $state.params.id);
    
        // $scope.postId = $state.params.id;
    
        // if($scope.postId != '') {
        //     // console.log("Edit Post Content");
        //     apiServices.getMethod(serverUrl+'getPostDataById/'+$scope.postId).then(function(response){
        //         $scope.listdataSpot = JSON.parse(response.payload.attribute);
        //         console.log("BuyerPostData::", JSON.parse(response.payload.attribute));
        //     });
        // }
    
        $scope.closeConfirmationPopup = function () {
            console.log($(".statusText button").attr("data-type"));
            var st = $(".statusText button").attr("data-type");
            $("#QuoteConfirmationPopup").modal("hide");
            if (st) {
                setTimeout(function () {
                    $state.go("buyer-list");
                }, 1000);
    
            }
    
        }
    
        $scope.closeRoutePopup = function () {
            console.log($(".statusText button").attr("data-type"));
            var st = $(".statusText button").attr("data-type");
            $("#addRoutePopup").modal("hide");
            if (st) {
                setTimeout(function () {
                    $state.go("post-buyer-as-term");
                }, 1000);
    
            }
    
        }
    
        $scope.validateReportingLocation = function (data) {
            console.log("Validate Data::", data);
            console.log(data.city);
            console.log("typeof data.vehicle_reporting_location", typeof data.vehicle_reporting_location)
    
            if (typeof data.vehicle_reporting_location !== Object) {
                $scope.data.vehicle_reporting_location = '';
            }
    
            if (typeof data.city !== 'undefined' && data.city !== '' && typeof data.city !== undefined) {
    
                console.log("passed")
    
            } else {
                console.log("failed");
            }
        }
    
        // Add multiple loads
        $scope.dataLoads = {
            'estimatedQty': '',
            'unit': '',
            'estimatedLoads': '',
            'index': ''
        };
    
        $scope.multipleLoads = [];
        $scope.addMultipleLoads = function (loads) {
            console.log("$scope.dataSpot", $scope.dataSpot);
            $scope.dataSpot.loads = $scope.multipleLoads;
            $scope.dataTerm.loads = $scope.multipleLoads;
            $scope.multipleLoads.push(angular.copy(loads));
        }
        // Edit multiple loads
        $scope.isUpdate = false;
        $scope.editLoads = function (key, value) {
            $scope.dataLoads = {
                'estimatedQty': value.estimatedQty,
                'unit': value.unit,
                'estimatedLoads': value.estimatedLoads,
                'index': key
            };
              $("select.selectpicker").selectpicker('refresh');
    
            $scope.isUpdate = true;
        }
        // Update multipleload
        $scope.updateMultipleLoads = function (value) {
            $scope.multipleLoads.splice(value.index, 1);
            $scope.multipleLoads.push(angular.copy(value));
            $scope.isUpdate = false;
        }
        //  Remove multiple load 
        $scope.removeLoads = function (index) {
            $scope.multipleLoads.splice(index, 1);
        }
    
    
    
        $scope.value = '';
        $scope.test = function () {
            alert($scope.value);
        }
    }]);






