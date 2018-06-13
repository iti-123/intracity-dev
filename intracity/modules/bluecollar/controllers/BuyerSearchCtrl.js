app.controller('BuyerSearchCtrl',
    ['$scope', '$http', 'config', '$location', '$q', 'BuyerSearchServices', function ($scope, $http, config, $location, $q, BuyerSearchServices) {
        BuyerSearchServices.checkBuyer();
        var serverUrl = config.serverUrl;
        $scope.profileTypes = [{key: 'DRIVER', value: 'DRIVER'}, {
            key: 'CLEANER',
            value: 'CLEANER'
        }, {key: 'SKILLED', value: 'SKILLED'}, {key: 'SEMISKILLED', value: 'SEMISKILLED'}];
        //$scope.vehicleTypes = ['Vehicle Type', 'BIKE', 'LMV', 'MMV', 'HMV'];
        $scope.vehicleTypes = [];
        $scope.machineTypes = [];
        $scope.employmentTypes = [{
            key: 'FULL TIME',
            value: 'FULL_TIME'
        }, {key: 'PART TIME', value: 'PART_TIME'}, {key: 'CONTRACT', value: 'CONTRACT'}];
        $scope.salaryTypes = [{key: 'PER DAY', value: 'PER_DAY'}, {
            key: 'PER WEEK',
            value: 'PER_WEEK'
        }, {key: 'PER MONTH', value: 'PER_MONTH'}];
        $scope.locationPH = '';
        $scope.formData = {
            profileType: '',
            location: '',
            vehicleType: 'Vehicle Type',
            machineType: 'Machine Type',
            employmentType: '',
            experience1: '',
            salaryType: ''
        };

        $scope.formError = {
            profileType: false,
            location: false,
            vehicleType: false,
            machineType: false,
            employmentType: false,
            experience: false,
            salaryType: false
        };

        $scope.searchResults = [];

        $scope.vehicleTypes = vehType;
        $scope.machineTypes = machineType;

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

        $http({
            method: 'GET',
            url: serverUrl + 'bluecollar/machine-types',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            }
        }).then(function success(response) {
            if (response.data) {
                $scope.machineTypes = response.data.data;
                $scope.machineTypes.unshift({name: "Machine Type", id: ""});
            }
        }, function error(response) {
            //
        });

        $scope.searchLocations = function (location) {
            return BuyerSearchServices.suggestLocation(location);
        };

        $scope.selectedLocation = function ($item, $model, $label) {
            $scope.formData.location = $item;
        };

        $scope.validate = function () {
            // console.log($scope.formData);
            var validated = true;
            if ($scope.formData.profileType == '') {
                $scope.formError.profileType = true;
                validated = false;
            } else {
                $scope.formError.profileType = false;
            }
            if (isNaN($scope.formData.experience1) || $scope.formData.experience1 == '') {
                $scope.formError.experience1 = true;
                validated = false;
            } else {
                $scope.formError.experience1 = false;
            }
            if ($scope.formData.employmentType == '') {
                $scope.formError.employmentType = true;
                validated = false;
            } else {
                $scope.formError.employmentType = false;
            }
            if ($scope.formData.salaryType == '') {
                $scope.formError.salaryType = true;
                validated = false;
            } else {
                $scope.formError.salaryType = false;
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
            return validated;
        };

        $scope.search = function () {
            if ($scope.validate()) {
                BuyerSearchServices.setSearchPageData($scope.formData);
                $location.url('/bluecollar-buyer-search-results')
            }
        };
    }]);
