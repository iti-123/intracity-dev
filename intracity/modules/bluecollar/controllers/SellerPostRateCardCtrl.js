app.controller('SellerPostRateCardCtrl', ['$scope', '$http', 'config', '$location', 'BuyerSearchServices','$state','SellerSearchServices', function ($scope, $http, config, $location, BuyerSearchServices,$state,SellerSearchServices) {

    var serverUrl = config.serverUrl;
    SellerSearchServices.checkSeller();
    $scope.multiplePosts = [];

    $scope.profileTypes = [{key: 'DRIVER', value: 'DRIVER'}, {
        key: 'CLEANER',
        value: 'CLEANER'
    }, {key: 'SKILLED', value: 'SKILLED'}, {key: 'SEMISKILLED', value: 'SEMISKILLED'}];
    //$scope.vehicleTypes = ['Vehicle Type', 'BIKE', 'LMV', 'MMV', 'HMV'];
    //$scope.vehicleTypes = [{key: 'BIKE', value: 'BIKE'},{key: 'LMV', value: 'LMV'},{key: 'MMV', value: 'MMV'},{key: 'HMV', value: 'HMV'}];
    $scope.employmentTypes = [{key: 'FULL TIME', value: 'FULL_TIME'},{
        key: 'PART TIME',value: 'PART_TIME'}, {key: 'CONTRACT', value: 'CONTRACT'}];
    $scope.salaryTypes = [{key: 'PER DAY', value: 'PER_DAY'}, {
        key: 'PER WEEK',
        value: 'PER_WEEK'
    }, {key: 'PER MONTH', value: 'PER_MONTH'}];
    $scope.qualificationTypes = [{key: 'SSLC', value: 'SSLC'}, {
        key: 'Intermediate',value: 'INTERMEDIATE'}, {key: 'Graduate', value: 'GRADUATE'}, {key: 'Post Graduate', value: 'POST_GRADUATE'}];
    //$scope.locationPH = '';
    $scope.vehicleTypePH = 'Vehicle Type';
    $scope.machineTypePH = 'Machine Type';
    $scope.vehicleTypes = [];
    $scope.machineTypes = [];
    $scope.first = true;
    
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

    $scope.formData = {
        profileType: '',
        location: '',
        vehicleTypePH: '',
        machineTypePH: '',
        locationPH: '',
        vehicleTypes: [],
        machineTypes: [],
        employmentType: '',
        experience: '',
        salaryType: '',
        salary: '',
        qualification: ''
    };

    $scope.formError = {
        profileType: false,
        location: false,
        locationPH: false,
        vehicleTypes: false,
        machineTypes: false,
        employmentType: false,
        experience: false,
        salaryType: false,
        salary: false,
        qualification: false
    };
    $scope.defaultForm = angular.copy($scope.formData);
    $scope.searchResults = [];
     
    $scope.addMultiplePosts = function (data) {
        if($scope.validate()){
          $scope.new = angular.copy(data);
          $scope.multiplePosts.push($scope.new);
          $scope.formData = angular.copy($scope.defaultForm);
          $scope.locationPH = '';
          console.log("AddMultiple Posts ::", $scope.multiplePosts);
        }
    }
  
    $scope.deleteMultiplePosts = function(index) {
        $scope.multiplePosts.splice(index,1);
    }
  
    $scope.editMultipleRate = function(value,$index) {
        console.log("editMultipleRate",$scope.multiplePosts);
        $scope.multiplePosts.splice($index,1);
        $scope.formData = value;
        $scope.locationPH = value.location;
        $scope.isUpdate = true;
    }

    $scope.selectedLocation = function($item, $model, $label){
        $scope.formData.location = $item;
    };
    
   /* $http({
        method: 'GET',
        url: serverUrl + 'bluecollar/vehicle-types',
        headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
        }
    }).then(function success(response) {
        if (response.data) {
            $scope.vehicleTypes = response.data.data;
            $scope.vehicleTypes.unshift({name: "Vehicle Type", id: ""});

            console.log($scope.vehicleTypes);
        }
    }, function error(response) {
        //
    });*/
      
      $scope.vehicleTypes = vehType;
      $scope.machineTypes = machineType;

   /* $http({
        method: 'GET',
        url: serverUrl + 'bluecollar/machine-types',
        headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
        }
    }).then(function success(response) {
        if (response.data) {
            $scope.machineTypes = response.data.data;
            $scope.machineTypes.unshift({name: "Machine Type", id: ""});
            console.log($scope.machineTypes);
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

   
    $scope.validate = function () {
        // console.log($scope.formData);
        var validated = true;
        if ($scope.formData.profileType == '') {
            $scope.formError.profileType = true;
            validated = false;
        } else {
            $scope.formError.profileType = false;
        }
        if (isNaN($scope.formData.experience) || $scope.formData.experience == '') {
            $scope.formError.experience = true;
            validated = false;
        } else {
            $scope.formError.experience = false;
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
        if ($scope.formData.salary == '') {
            $scope.formError.salary = true;
            validated = false;
        } else {
            $scope.formError.salary = false;
        }
        if ($scope.formData.profileType == 'DRIVER' && $scope.formData.vehicleTypePH == '') {
            $scope.formError.vehicleTypes = true;
            validated = false;
        } else {
            $scope.formError.vehicleTypes = false;
        }

        if($scope.formData.profileType=='SKILLED' && $scope.formData.machineTypePH == ''){
          $scope.formError.machineTypes = true;
          validated = false;
        }else{
          $scope.formError.machineTypes = false;
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

        if ($scope.formData.qualification == '') {
            $scope.formError.qualification = true;
            validated = false;
        } else {
            $scope.formError.qualification = false;
        }

        if ($scope.formData.salary == '' || $scope.formData.salary == undefined) {
            $scope.formError.salary = true;
            $scope.formError.salaryDigits = false;
            validated = false;
        } else {
            if($scope.formData.salary < 100){
                $scope.formError.salaryDigits = true;
                validated = false;
            }else{
                $scope.formError.salaryDigits = false;
            }
            $scope.formError.salary = false;
        }
        return validated;
    };

    $scope.addVehicleType = function () {
        var vehicleType = JSON.parse($scope.vehicleTypePH);
        var index = -1;
        if (vehicleType != '') {
            let exists = false;
            for (let v in $scope.formData.vehicleTypes) {
                if ($scope.formData.vehicleTypes[v].name == vehicleType.name) {
                    exists = true;
                    index = v;
                }
            }
            if (!exists) {
                $scope.formData.vehicleTypes.push(vehicleType);
            } else {
                $scope.formData.vehicleTypes.splice(index, 1);
            }
        }
    };

    $scope.addMachineType = function () {
        var vehicleType = JSON.parse($scope.machineTypePH);
        var index = -1;
        if (vehicleType != '') {
            if (vehicleType.name != 'Machine Type' && vehicleType.name != '') {
                let exists = false;
                for (let v in $scope.formData.machineTypes) {
                    if ($scope.formData.machineTypes[v].name == vehicleType.name) {
                        exists = true;
                        index = v;
                    }
                }
                if (!exists) {
                    $scope.formData.machineTypes.push(vehicleType);
                } else {
                    $scope.formData.machineTypes.splice(index, 1);
                }
            }
        }
    };
    
    $scope.closeConfirmationPopup = function () {
        $("#postConfirmationModal").modal("hide");
        setTimeout(function () {
                $state.go('bluecollar-seller-post-list');
            }, 1000);
    }

    $scope.post = function (multiplePosts,status) {

            if($scope.multiplePosts.length < 1) {
               alert("Please Create at least one post");
               return false;
            }

            if(!$('#Accept').is(':checked')) {
               alert('Accept Terms & Conditions ');
               return false;
            }

            if(status == 1){
               //+sessionStorage.setItem('postDraft',false);
            }else if(status == 0){
               +sessionStorage.setItem('postDraft',true);
            }
             
            var str = "Your request for post has been successfully posted to the relevant buyers."
           // $('.loaderGif').show();
            $scope.first = false;
            $http({
                url: serverUrl + 'bluecollar/seller-post/' + status,
                method: 'POST',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: JSON.stringify(multiplePosts),
            }).then(function success(response) {
                if (response.data.success) {
                    $scope.formData = angular.copy($scope.defaultForm);
                    $("#responsetext").html(str);

                    if(status == 1){
                        $('body #postConfirmationModal').modal('show');
                    }else{
                        $state.go('bluecollar-seller-post-list');
                    }
                   // $('body #postConfirmationModal').modal('show');
                }
            }, function error(response) {
                $('.loaderGif').hide();

            });
        };
}]);
