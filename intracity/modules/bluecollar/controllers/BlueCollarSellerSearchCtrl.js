app.controller('BlueCollarSellerSearchCtrl',
    ['$scope', '$http', 'config', 'trackings', 'apiServices',
        '$compile', '$state', '$stateParams', '$location', '$window',
        'SellerSearchServices',
        function ($scope, $http, config, trackings, apiServices,
                  $compile, $state, $stateParams, $location, $window, SellerSearchServices) {
            SellerSearchServices.checkSeller();
            var serverUrl = config.serverUrl;

            $scope.profileTypes = [{key: 'DRIVER', value: 'DRIVER'},
                {key: 'CLEANER', value: 'CLEANER'}, {key: 'SKILLED', value: 'SKILLED'}, {
                    key: 'SEMISKILLED',
                    value: 'SEMISKILLED'
                }];

            $scope.vehicleTypes = [];
            $scope.machineTypes = [];
            $scope.employmentType = [{
                key: 'FULL TIME',
                value: 'FULL_TIME'
            }, {key: 'PART TIME', value: 'PART_TIME'},
                {key: 'CONTRACT', value: 'CONTRACT'}];
            $scope.salaryTypes = [{key: 'PER DAY', value: 'PER_DAY'},
                {key: 'PER WEEK', value: 'PER_WEEK'}, {key: 'PER MONTH', value: 'PER_MONTH'}];
            $scope.locationPH = '';

            $scope.formData = {
                profileType: '',
                location: '',
                vehicleType: 'Vehicle Type',
                machineType: 'Machine Type',
                employmentType: '',
                experience1: '',
                salaryType: '',
            };

            $scope.formError = {
                profileType: false,
                employmentType: false,
                vehicleType: false,
                machineType: false,
                experience1: false,
                location: false,
                salaryType: false,
            };

            var url = serverUrl + 'locations/getCity';
            $scope.searchResults = [];
            
            var specialKeys = new Array();
            specialKeys.push(8); //Backspace
            specialKeys.push(9); //Tab
            specialKeys.push(46); //Delete
            specialKeys.push(36); //Home
            specialKeys.push(35); //End
            specialKeys.push(37); //Left
            specialKeys.push(39); //Right

            $('#location').keypress(function (e) {
                var keyCode = e.keyCode == 0 ? e.charCode : e.keyCode;
                var ret = ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || 
                    (keyCode >= 97 && keyCode <= 122) || (specialKeys.indexOf(e.keyCode) != -1 && e.charCode != e.keyCode));
                  return ret;
            });

            /*$http({
                method: 'GET',
                url: serverUrl + 'bluecollar/vehicle-types',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                }
            }).then(function success(response) {
                if (response.data) {
                    $scope.vehicleTypes = response.data.data;
                    $scope.vehicleTypes.unshift({name: "Vehicle Type", id: ""});
                    //console.log('vehicleTypes',JSON.stringify($scope.vehicleTypes));
                }
            }, function error(response) {
                //
            });*/

            $scope.vehicleTypes = vehType;

           $scope.machineTypes = machineType;

            /*$http({
                method: 'GET',
                url: serverUrl + 'bluecollar/machine-types',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                }
            }).then(function success(response) {
                if (response.data) {
                    $scope.machineTypes = response.data.data;
                    $scope.machineTypes.unshift({name: "Machine Type", id: ""});
                    console.log('machineTypes',JSON.stringify($scope.machineTypes));
                }
            }, function error(response) {
                //
            });*/



            $scope.searchLocations = function () {
                //$scope.searchResults = [{'cur_state_or_city': 'PUNED'}, {'cur_state_or_city': 'hydra'}];
                $http({
                    method: 'POST',
                    url: config.serverUrl + 'bluecollar/buyer-location-search',
                    headers: {
                        'authorization': 'Bearer ' + localStorage.getItem("access_token")
                    },
                    data: {'search': $scope.formData.locationPH},
                }).then(function (response) {
                    if(response.data.data.length){
                       $scope.searchResults = response.data.data;
                    }else{
                       $scope.searchResults = '';
                    }
                }, function (response) {

                });
            };

            $scope.selectedLocation = function ($item, $model, $label) {
                $scope.formData.location = $item;
            };

            $scope.validate = function () {
                var validated = true;
                if ($scope.formData.profileType == '' || typeof($scope.formData.profileType) == 'undefined') {
                    $scope.formError.profileType = true;
                    validated = false;
                } else {
                    $scope.formError.profileType = false;
                }

                if ($scope.formData.locationPH == '' || typeof($scope.formData.locationPH) == 'undefined') {
                    $scope.formError.location = true;
                    validated = false;
                } else {
                    $scope.formError.location = false;
                    if ($scope.formData.locationPH.indexOf(',') > -1)
                    {
                      var array = $scope.formData.locationPH.split(','); 
                      if ($scope.formData.location.city_name != array[0] || $scope.formData.location.state_name != array[1].trim()) {
                        console.log('Str',array[0]);
                        console.log('String',array[1]);
                        $scope.formError.location = true;
                       }
                    }else{
                      if ($scope.formData.location.city_name != $scope.formData.locationPH || $scope.formData.location.state_name != $scope.formData.locationPH) {
                        $scope.formError.location = true;
                       }
                    }
                   
                    if ($scope.formError.location) {
                        validated = false;
                    }
                }

                if ($scope.formData.profileType == 'DRIVER' && $scope.formData.vehicleType == '') {
                    $scope.formError.vehicleType = true;
                    validated = false;
                } else {
                    $scope.formError.vehicleType = false;
                }

                if ($scope.formData.profileType == 'SKILLED' && $scope.formData.machineType == '') {
                    $scope.formError.machineType = true;
                    validated = false;
                } else {
                    $scope.formError.machineType = false;
                }

                if ($scope.formData.salaryType == '' || typeof($scope.formData.salaryType) == 'undefined') {
                    $scope.formError.salaryType = true;
                    validated = false;
                } else {
                    $scope.formError.salaryType = false;
                }

                if ($scope.formData.employmentType == '' || typeof($scope.formData.employmentType) == 'undefined') {
                    $scope.formError.employmentType = true;
                    validated = false;
                } else {
                    $scope.formError.employmentType = false;
                }

                if ($scope.formData.experience1 == '' || typeof($scope.formData.experience1) == 'undefined') {
                    $scope.formError.experience1 = true;
                    validated = false;
                } else {
                    $scope.formError.experience1 = false;
                }
                return validated;
            };

            $scope.searchSellers = function () {
                console.log($scope.formData);
                if ($scope.validate()) {
                    console.log($scope.formData);
                    SellerSearchServices.setSearchPageData($scope.formData);
                    $location.url('/bluecollar-seller-search-results');
                }
            };
        }]);
