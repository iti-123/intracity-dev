app.controller('BuyerSearchResultsCtrl', ['$scope', '$http', '$location', 'config', 'BuyerSearchServices', '$state','$dataExchanger','apiServices', function ($scope, $http, $location, config, BuyerSearchServices, $state,$dataExchanger,apiServices) {
    BuyerSearchServices.checkBuyer();
    var serverUrl = config.serverUrl;

    $scope.showFilters = false;
    $scope.prevData = BuyerSearchServices.getSearchPageData();
    $scope.searchResults = [];

    $scope.vehicleTypes = [];
    $scope.machineTypes = [];

    $scope.searchData = {
        experience: {
            min: 0,
            max: 50
        },
        salary: {
            min: 0,
            max: 100000
        },
        experience1:'',
        profileType: '',
        location: '',
        locationPH: '',
        salaryType: '',
        vehicleType: [],
        machineType: [],
        employmentType: [],
        qualification: [],
        rating: [],
        salaryType: []
    };
    
    $scope.filterData = {
        experience: {
          min: 0,
          max: 50
        },
        salary: {
          min: 0,
          max: 100000
        },
        experience1:'',
        profileType : '',
        location: '',
        vehicleType: [],
        machineType: [],
        employmentType: [],
        qualification: [],
        rating: [],
        salaryType: []
    };

    var initializeFilters = function(){
        for (d in $scope.prevData) {
          if (d != 'experience') {
              if (d == 'machineType' || d == 'vehicleType' || d == 'employmentType' || d == 'salaryType') {
                  if ($scope.prevData[d] != "" && $scope.prevData[d] != "Vehicle Type" && $scope.prevData[d] != "Machine Type") {
                    $scope.filterData[d] = [];
                    if($scope.filterData.profileType=='DRIVER'&&d=='vehicleType'||$scope.filterData.profileType=='SKILLED'&&d=='machineType'){
                      $scope.filterData[d].push($scope.prevData[d]);
                    }else if(d!='vehicleType'&&d!='machineType'){
                      $scope.filterData[d].push($scope.prevData[d]);
                    }
                  }
              } else {
                  $scope.filterData[d] = $scope.prevData[d];
              }
          }
        }

        for (d in $scope.prevData) {
          if (d != 'experience') {
            if (d == 'machineType' || d == 'vehicleType' || d == 'employmentType' || d == 'salaryType') {
                if ($scope.prevData[d] != "" && $scope.prevData[d] != "Vehicle Type" && $scope.prevData[d] != "Machine Type") {
                  $scope.searchData[d] = [];
                  if($scope.searchData.profileType=='DRIVER'&&d=='vehicleType'||$scope.searchData.profileType=='SKILLED'&&d=='machineType'){
                    $scope.searchData[d].push($scope.prevData[d]);
                  }else if(d!='vehicleType'&&d!='machineType'){
                    $scope.searchData[d].push($scope.prevData[d]);
                  }
                }
            } else {
                $scope.searchData[d] = $scope.prevData[d];
            }
         }
       }

    };
    
    var initializeClearFilters = function(){
        $scope.searchData['rating'] = [];
        $scope.searchData['qualification'] = [];
        for (d in $scope.prevData) {
          if (d != 'experience') {
              if (d == 'machineType' || d == 'vehicleType' || d == 'employmentType' || d == 'salaryType') {
                  if ($scope.prevData[d] != "" && $scope.prevData[d] != "Vehicle Type" && $scope.prevData[d] != "Machine Type") {
                    $scope.searchData[d] = [];
                    if($scope.searchData.profileType=='DRIVER'&&d=='vehicleType'||$scope.searchData.profileType=='SKILLED'&&d=='machineType'){
                      $scope.searchData[d].push($scope.prevData[d]);
                    }else if(d!='vehicleType'&&d!='machineType'){
                      $scope.searchData[d].push($scope.prevData[d]);
                    }
                  }
              } else {
                  $scope.searchData[d] = $scope.prevData[d];
                  $scope.searchData.experience.min = 0;
                  $scope.searchData.experience.max = 50;
                  $scope.searchData.salary.min = 0;
                  $scope.searchData.salary.max = 100000;
              }
          }
        }
        console.log('SearchData Rating',$scope.searchData);
        console.log('prevData',$scope.prevData);
    };

    initializeFilters();
    $scope.filClearValue = true;

    $scope.clearAll = function(){
     $scope.filClearValue = false;
     $scope.filterData = {
        experience: {
            min: 0,
            max: 50
        },
        salary: {
            min: 0,
            max: 100000
        },
        profileType: '',
        location: '',
        locationPH: '',
        salaryType: '',
        vehicleType: [],
        machineType: [],
        employmentType: [],
        qualification: [],
        rating: [],
        salaryType: []
    };
     
    //console.log('Abc',$scope.filterData);
    initializeClearFilters();
    }

    $http({
        method: 'GET',
        url: serverUrl + 'bluecollar/vehicle-types',
        headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
        }
    }).then(function success(response) {
        if (response.data) {
            $scope.vehicleTypes = response.data.data;
        }
    }, function error(response) {
        //
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
        }
    }, function error(response) {
        //
    });

    $scope.toggleFilter = function () {
        if ($scope.showFilters) {
            $scope.showFilters = false;
        } else {
            $scope.showFilters = true;
        }
    };

    $scope.setRating = function (type) {
       $scope.filClearValue = true;
        let indexFilter = $scope.filterData.rating.indexOf(type);
        if(indexFilter==-1){
          $scope.filterData.rating.push(type);
        }else{
          $scope.filterData.rating.splice(indexFilter, 1);
          if($scope.searchData.experience.min == 0 && $scope.searchData.experience.max == 50 && $scope.searchData.salary.min == 0 && $scope.searchData.salary.max == 100000 
            && $scope.filterData.rating.length < 1 && $scope.filterData.vehicleType.length < 1 && $scope.filterData.machineType.length < 1
            && $scope.filterData.employmentType.length < 1 && $scope.filterData.qualification.length < 1){
             $scope.filClearValue = false;
          }
        }

        let index = $scope.searchData.rating.indexOf(type);
        if(index==-1){
          $scope.searchData.rating.push(type);
        }else{
          $scope.searchData.rating.splice(index, 1);
        }
    };

    $scope.setVehicleType = function(type){
        $scope.filClearValue = true;
        let indexVehType = $scope.filterData.vehicleType.indexOf(type);
        if(indexVehType==-1){
          $scope.filterData.vehicleType.push(type);
        }else{
          $scope.filterData.vehicleType.splice(indexVehType, 1);
          if($scope.searchData.experience.min == 0 && $scope.searchData.experience.max == 50 && $scope.searchData.salary.min == 0 && $scope.searchData.salary.max == 100000 && $scope.filterData.rating.length < 1 && $scope.filterData.vehicleType.length < 1 && $scope.filterData.machineType.length < 1
            && $scope.filterData.employmentType.length < 1 && $scope.filterData.qualification.length < 1){
             $scope.filClearValue = false;
          }
        }

        let index = $scope.searchData.vehicleType.indexOf(type);
        if(index==-1){
          $scope.searchData.vehicleType.push(type);
        }else{
          if(indexVehType!=-1){
            $scope.searchData.vehicleType.splice(index, 1);
          }
        }
    };

    $scope.setMachineType = function(type){
        $scope.filClearValue = true;
        let indexMacType = $scope.filterData.machineType.indexOf(type);
        if(indexMacType==-1){
          $scope.filterData.machineType.push(type);
        }else{
          $scope.filterData.machineType.splice(indexMacType, 1);
          if($scope.searchData.experience.min == 0 && $scope.searchData.experience.max == 50 && $scope.searchData.salary.min == 0 && $scope.searchData.salary.max == 100000 && $scope.filterData.rating.length < 1 && $scope.filterData.vehicleType.length < 1 && $scope.filterData.machineType.length < 1
            && $scope.filterData.employmentType.length < 1 && $scope.filterData.qualification.length < 1){
             $scope.filClearValue = false;
          }
        }

        let index = $scope.searchData.machineType.indexOf(type);
        if(index==-1){
            $scope.searchData.machineType.push(type);
        }else{
          if(indexMacType!=-1){
            $scope.searchData.machineType.splice(index, 1);
          }
        }
    };

   $scope.setEmploymentType = function(type){
        let indexEmpType = $scope.filterData.employmentType.indexOf(type);
        if(indexEmpType==-1){
          $scope.filterData.employmentType.push(type);
          $scope.filClearValue = true;
        }else{
          $scope.filterData.employmentType.splice(indexEmpType, 1);
          if($scope.searchData.experience.min == 0 && $scope.searchData.experience.max == 50 && $scope.searchData.salary.min == 0 && $scope.searchData.salary.max == 100000 && $scope.filterData.rating.length < 1 && $scope.filterData.vehicleType.length < 1 && $scope.filterData.machineType.length < 1
            && $scope.filterData.employmentType.length < 1 && $scope.filterData.qualification.length < 1){
             $scope.filClearValue = false;
          }
        }

        let index = $scope.searchData.employmentType.indexOf(type);
        if(index==-1){
          $scope.searchData.employmentType.push(type);
        }else{
          if(indexEmpType!=-1){
             $scope.searchData.employmentType.splice(index, 1);
          }
        }
   };

    $scope.setQualification = function(type){
        let indexQuaType = $scope.filterData.qualification.indexOf(type);
        if(indexQuaType==-1){
          $scope.filterData.qualification.push(type);
          $scope.filClearValue = true;
        }else{
          $scope.filterData.qualification.splice(indexQuaType, 1);
          if($scope.searchData.experience.min == 0 && $scope.searchData.experience.max == 50 && $scope.searchData.salary.min == 0 && $scope.searchData.salary.max == 100000 && 
            $scope.filterData.rating.length < 1 && $scope.filterData.vehicleType.length < 1 && $scope.filterData.machineType.length < 1
            && $scope.filterData.employmentType.length < 1 && $scope.filterData.qualification.length < 1){
             $scope.filClearValue = false;
          }
        }

        let index = $scope.searchData.qualification.indexOf(type);
        if(index==-1){
           $scope.searchData.qualification.push(type);
        }else{
          if(indexQuaType!=-1){
            $scope.searchData.qualification.splice(index, 1);
          }
        }
   };
    
    $scope.$watchGroup(['searchData.experience.min','searchData.experience.max'], function(newValue, oldValue){
        if($scope.searchData.experience.min != 0 || $scope.searchData.experience.max != 50){
            $scope.filClearValue = true;
         }else if($scope.searchData.salary.min == 0 && $scope.searchData.salary.max == 100000 && $scope.searchData.experience.min == 0 && $scope.searchData.experience.max == 50 && $scope.filterData.rating.length < 1 && $scope.filterData.vehicleType.length < 1 && $scope.filterData.machineType.length < 1
            && $scope.filterData.employmentType.length < 1 && $scope.filterData.qualification.length < 1){
             $scope.filClearValue = false;
         }
    }, true);

    $scope.$watchGroup(['searchData.salary.min','searchData.salary.max'], function(newValue, oldValue){
        if($scope.searchData.salary.min != 0 || $scope.searchData.salary.max != 100000){
            $scope.filClearValue = true;
         }else if($scope.searchData.experience.min == 0 && $scope.searchData.experience.max == 50 && $scope.searchData.salary.min == 0 && $scope.searchData.salary.max == 100000 && $scope.filterData.rating.length < 1 && $scope.filterData.vehicleType.length < 1 && $scope.filterData.machineType.length < 1
            && $scope.filterData.employmentType.length < 1 && $scope.filterData.qualification.length < 1){
             $scope.filClearValue = false;
         }
    }, true);

    $scope.removeRating = function (index) {
        $scope.searchData.rating.splice(index, 1);
    };

    $scope.removeVehicleType = function (index) {
        $scope.searchData.vehicleType.splice(index, 1);
    };

    $scope.removeMachineType = function (index) {
        $scope.searchData.machineType.splice(index, 1);
    };

    $scope.removeEmploymentType = function (index) {
        $scope.searchData.employmentType.splice(index, 1);
    };

    $scope.removeQualification = function (index) {
        $scope.searchData.qualification.splice(index, 1);
    };

    $scope.openDetails = function (index) {
        $scope.searchResults[index].showDetails = !$scope.searchResults[index].showDetails;
        var id = $scope.searchResults[index].seller_bc_reg_id;
        if ($scope.searchResults[index].details == undefined) {
            $http({
                method: 'POST',
                url: config.serverUrl + 'bluecollar/seller-details',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: {'id': id},
            }).then(function (response) {
                //console.log(httpResponse.data.data);
                $scope.searchResults[index].details = response.data.data;
            }, function (response) {

            });
        }
    };

    $scope.isDetailsHidden = function (index) {
        if ($scope.searchResults[index].showDetails == undefined) {
            $scope.searchResults[index].showDetails = false;
        }
        return $scope.searchResults[index].showDetails;
    };

    $scope.$watch('searchData', function (newValue, oldValue) {
        $http({
            url: serverUrl + 'bluecollar/buyer-search',
            method: 'POST',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: $scope.searchData,
        }).then(function success(response) {
            var data = response.data;
            if (data.data.hasOwnProperty('response')) {
                $scope.searchResults = data.data.response.docs;
            }
            // response=JSON.stringify(response);
            // response=JSON.parse(response);
        }, function error(response) {

        });
    }, true);

    // console.log($scope.searchData);
    $scope.getLanguages = function (string) {
        if (string != undefined && string != '') {
            var obj = JSON.parse(string);
            var s = '';
            for (var i in obj) {
                if (s != '') {
                    s += ','
                }
                s += obj[i].language;
            }
            return s;
        }
    };

    $scope.postCard = function () {
        BuyerSearchServices.setPostCardPageData($scope.searchData);
        $location.url('/bluecollar-buyer-post-card');
    };

    $scope.modifyData = function () {
        $('body #modificationModal').modal('show');
    };

    $scope.profileTypes = [{key: 'Profile Type', value: ''}, {key: 'DRIVER', value: 'DRIVER'}, {
        key: 'CLEANER',
        value: 'CLEANER'
    }, {key: 'SKILLED', value: 'SKILLED'}, {key: 'SEMISKILLED', value: 'SEMISKILLED'}];
    //$scope.vehicleTypes = ['Vehicle Type', 'BIKE', 'LMV', 'MMV', 'HMV'];
    // $scope.vehicleTypes = [{key: 'Vehicle Type', value: ''},{key: 'BIKE', value: 'BIKE'},{key: 'LMV', value: 'LMV'},{key: 'MMV', value: 'MMV'},{key: 'HMV', value: 'HMV'}];
    $scope.employmentTypes = [{key: 'Employment Type', value: ''}, {
        key: 'FULL TIME',
        value: 'FULL_TIME'
    }, {key: 'PART TIME', value: 'PART_TIME'}, {key: 'CONTRACT', value: 'CONTRACT'}];
    $scope.salaryTypes = [{key: 'Salary', value: ''}, {key: 'PER DAY', value: 'PER_DAY'}, {
        key: 'PER WEEK',
        value: 'PER_WEEK'
    }, {key: 'PER MONTH', value: 'PER_MONTH'}];
    $scope.locationPH = $scope.prevData.location.city_name + ', ' + $scope.prevData.location.state_name;
    $scope.formData = BuyerSearchServices.getSearchPageData();

    $scope.formError = {
        profileType: false,
        location: false,
        vehicleType: false,
        employmentType: false,
        experience: false,
        salaryType: false
    };

    // $scope.searchLocResults = [];
    //
    // $scope.searchLocations = function(){
    //   //$scope.searchResults = [{'cur_state_or_city': 'PUNED'}, {'cur_state_or_city': 'hydra'}];
    //   $http({
    //     method: 'POST',
    //     url: config.serverUrl+'bluecollar/buyer-location-search',
    //     headers: {
    //       'authorization': 'Bearer ' + localStorage.getItem("access_token")
    //     },
    //     data: {'search': $scope.locationPH},
    //   }).then(function(response){
    //     //console.log(httpResponse.data.data);
    //     $scope.searchLocResults = response.data.data;
    //   }, function(response){
    //
    //   });
    // };

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
            if (parseInt($scope.formData.experience1) > 50) {
                $scope.formError.experience1 = true;
                validated = false;
            } else {
                $scope.formError.experience1 = false;
            }
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

    $scope.modify = function () {
        if ($scope.validate()) {
            BuyerSearchServices.setSearchPageData($scope.formData);
            $scope.prevData = $scope.formData;
            initializeFilters();
            $('body #modificationModal').modal('hide');
            // for (d in $scope.prevData) {
            //     if (d != 'experience') {
            //         if (d == 'machineType' || d == 'vehicleType' || d == 'employmentType' || d == 'salaryType') {
            //             if ($scope.prevData[d] != "" && $scope.searchData[d].indexOf($scope.prevData[d]) == -1) {
            //                 $scope.searchData[d].push($scope.prevData[d]);
            //             }
            //         } else {
            //             $scope.searchData[d] = $scope.prevData[d];
            //         }
            //     }
            //
            //
            // }
        }
    };


    $scope.goToBookNow = function (index) {
        $scope.cartId = $scope.searchResults[index].id;
        //console.log($scope.searchResults[index]);
        // return false;
        $dataExchanger.request.serviceId = _BLUECOLLAR_;
        $dataExchanger.request.BlueCollarbookData = $scope.searchResults[index];
        $dataExchanger.request.BlueCollarData = $scope.searchResults[index];
        console.log($scope.searchResults[index]);

        //console.log($dataExchanger.request);
        //var confirm1 = confirm("Do you want to book");
        //console.log("Buyer Quote::", confirm1);

            var booknowSerachObj = $dataExchanger.request;
            console.log('dataexchange',$dataExchanger.request);
            //return false;
            $scope.bookPostObj = {
                initialDetails: {
                    serviceId: $dataExchanger.request.serviceId,
                    serviceType: '',
                    sellerId: $scope.searchResults[index].seller_bc_reg_id,
                    buyerId: Auth.getUserID(),
                    postType: "BP",
                    sellerQuoteId: '', //need to discuss
                    searchData: booknowSerachObj,
                    carrierIndex: index,
                    quote: '',
                    postId : $scope.searchResults[index].id
                }
            };
            //console.log("$scope.bookPostObj::", JSON.stringify($scope.bookPostObj));

            apiServices.bookNow(serverUrl, JSON.stringify($scope.bookPostObj)).then(function (response) {

                //console.log($scope.searchResults[index]);
                //console.log("gotobooknow",response);return false;

                if (response.isSuccessful && response.payload.id) {
                    $scope.cartId = response.payload.enc_id;
                    $state.go('order-booknow', {serviceId: _BLUECOLLAR_, cartId: $scope.cartId});
                }
            })





        // var confirm1 = confirm("Do you want to book,\n Seller Name: "+sellerdata+ ", and Price : ");


        // var value = $scope.searchResult[index];
        // var price ='';
        // if(value.rate_base_distance != '') {
        //     price = 'RS '+value.base_distance*value.rate_base_distance+' /- (For '+value.base_distance+' KM )';
        // } else if(value.base_time != '') {
        //     price = 'RS '+value.base_time*value.cost_base_time+' /- (For '+value.base_time+' Hours )';
        // }
        // // console.log("quote::",$scope.searchResult[index]);
        // var confirm1 = confirm("Do you want to book,\n Seller Name: "+value.seller+ ", and Price : "+price);
        // console.log("Buyer Quote::", confirm1);
        // if(confirm1){
        //     var booknowSerachObj = $dataExchanger.request;
        //     $scope.bookPostObj = {
        //         initialDetails: {
        //             serviceId: $dataExchanger.request.serviceId,
        //             serviceType: '',
        //             sellerId: quote.seller_id,
        //             buyerId: Auth.getUserID(),
        //             postType: "BP",
        //             sellerQuoteId: quote.seller_post_id, //need to discuss
        //             searchData: booknowSerachObj,
        //             carrierIndex: index,
        //             quote: quote
        //         }
        //     };
        //     console.log("$scope.bookPostObj::", JSON.stringify($scope.bookPostObj));
        //     apiServices.bookNow(serverUrl, JSON.stringify($scope.bookPostObj)).then(function(response) {
        //         if (response.isSuccessful && response.payload.id) {
        //             $scope.cartId = response.payload.enc_id;
        //             $state.go('order-booknow', { serviceId: 3, cartId: $scope.cartId });
        //         }
        //     })
        // }


    };

}]);
