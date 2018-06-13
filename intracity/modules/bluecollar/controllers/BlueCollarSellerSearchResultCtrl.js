app.controller('BlueCollarSellerSearchResultCtrl',['$scope','$http','config','trackings',
	'apiServices',
  '$compile','$state','$stateParams','$location','$window','SellerSearchServices',
  function($scope,$http,config,trackings,apiServices,
    $compile,$state, $stateParams,$location,$window,SellerSearchServices){

  var serverUrl = config.serverUrl;
  SellerSearchServices.checkSeller();
  $scope.prevData = SellerSearchServices.getSearchPageData();
  $scope.searchResults = [];
  $scope.profileTypes = [{key: 'DRIVER', value: 'DRIVER'},{key: 'CLEANER', value: 'CLEANER'},{key: 'SKILLED', value: 'SKILLED'},{key: 'SEMISKILLED', value: 'SEMISKILLED'}];

  $scope.vehicleTypes = [];
  $scope.machineTypes = [];

  $scope.hideform = true;


  $scope.searchData = {
    experience: {
      min: 0,
      max: 50,
     
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
    console.log('Search Data',$scope.searchData);
    console.log('Filter Data',$scope.filterData);
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
    url: serverUrl+'bluecollar/vehicle-types',
    headers: {
      'authorization': 'Bearer ' + localStorage.getItem("access_token")
    }
  }).then(function success(response){
    if(response.data){
      $scope.vehicleTypes = response.data.data;
    }
  }, function error(response){
    //
  });

  $http({
    method: 'GET',
    url: serverUrl+'bluecollar/machine-types',
    headers: {
      'authorization': 'Bearer ' + localStorage.getItem("access_token")
    }
  }).then(function success(response){
    if(response.data){
      $scope.machineTypes = response.data.data;
    }
  }, function error(response){
    //
  });

  $scope.modifyData = function(){
    $('body #modificationModal').modal('show');
  }

  $scope.searchLocResults = [];

  $scope.searchLocations = function(){
    //$scope.searchResults = [{'cur_state_or_city': 'PUNED'}, {'cur_state_or_city': 'hydra'}];
    $http({
      method: 'POST',
      url: config.serverUrl+'bluecollar/buyer-location-search',
      headers: {
        'authorization': 'Bearer ' + localStorage.getItem("access_token")
      },
      data: {'search': $scope.formData.locationPH},
    }).then(function(response){
      if(response.data.data.length){
          $scope.searchLocResults = response.data.data;
      }else{
          $scope.searchLocResults = '';
      }
      //$scope.searchLocResults = response.data.data;
    }, function(response){

    });
  };

  $scope.selectedLocation = function($item, $model, $label){
    $scope.formData.location = $item;
  }

  $scope.employmentTypes = [{key: 'FULL TIME', value: 'FULL_TIME'}, {key: 'PART TIME', value: 'PART_TIME'}, {key: 'CONTRACT', value: 'CONTRACT'}];
  $scope.salaryTypes = [{key: 'PER DAY', value: 'PER_DAY'}, {key: 'PER WEEK', value: 'PER_WEEK'}, {key: 'PER MONTH', value: 'PER_MONTH'}];
  $scope.location = '';
  $scope.locationPH = $scope.prevData.location.city_name+', '+$scope.prevData.location.state_name;

  $scope.formData = SellerSearchServices.getSearchPageData();

  $scope.formError = {
    profileType : false,
    location: false,
    vehicleType: false,
    employmentType: false,
    experience1: false,
    salaryType: false,
  };

  $scope.validate = function(){
     console.log($scope.formData);
    var validated = true;
    if($scope.formData.profileType==''||typeof($scope.formData.profileType) == 'undefined'){
      $scope.formError.profileType = true;
      validated = false;
    }else{
      $scope.formError.profileType = false;
    }
    if(isNaN($scope.formData.experience1)||typeof($scope.formData.experience1) == 'undefined'){
      $scope.formError.experience1 = true;
      validated = false;
    }else{
      $scope.formError.experience1 = false;
    }
    if($scope.formData.employmentType==''||typeof($scope.formData.employmentType) == 'undefined'){
      $scope.formError.employmentType = true;
      validated = false;
    }else{
      $scope.formError.employmentType = false;
    }
    if($scope.formData.salaryType==''||typeof($scope.formData.salaryType) == 'undefined'){
      $scope.formError.salaryType = true;
      validated = false;
    }else{
      $scope.formError.salaryType = false;
    }
    if($scope.formData.vehicleType==''||typeof($scope.formData.vehicleType) == 'undefined'){
      $scope.formError.vehicleType = true;
      validated = false;
    }else{
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
  }

  $scope.validateQuoteData = function(quoteData,index){
     var validated = true;
     if(quoteData.quotationType == 'COMPETITIVE'){
       if((quoteData.quotePrice=='') || typeof(quoteData.quotePrice) == 'undefined'){
          $scope.searchResults[index].errQuotePrice = true;
          $scope.searchResults[index].errQuotePriceSalary = false;
          $scope.searchResults[index].errQuoteHundredPriceSalary = false;
          validated = false;
        }else{
          if(quoteData.quotePrice > $scope.searchResults[index].buyer_salary){
            $scope.searchResults[index].errQuotePriceSalary = true;
            $scope.searchResults[index].errQuoteHundredPriceSalary = false;
            validated = false;
          }else{
            $scope.searchResults[index].errQuotePriceSalary = false;
          }

          if(quoteData.quotePrice < 100){
            $scope.searchResults[index].errQuoteHundredPriceSalary = true;
            $scope.searchResults[index].errQuotePriceSalary = false;
            validated = false;
          }else{
            $scope.searchResults[index].errQuoteHundredPriceSalary = false;
          }

          $scope.searchResults[index].errQuotePrice = false;
        }
      }
     
     if(quoteData.quoteDays == '' || typeof(quoteData.quoteDays) == 'undefined'){
        $scope.searchResults[index].errQuoteDays = true;  
        $scope.searchResults[index].errQuoteZeroDays = false;
        validated = false;
      }else{
        if(quoteData.quoteDays == 0){
            $scope.searchResults[index].errQuoteZeroDays = true;
            $scope.searchResults[index].errQuoteDays = false;
            validated = false;
        }else{
            $scope.searchResults[index].errQuoteZeroDays = false;
        }
        $scope.searchResults[index].errQuoteDays = false;
      }
      return validated;
  }

  $scope.postQuote = function(index,quoteType){
    //console.log(priceType);
    var quoteData = $scope.searchResults[index].quoteData;
    if(quoteData != undefined){
       console.log(quoteData);
       quoteData.action = 'INITIALISING';
       quoteData.quotationType = quoteType;
       quoteData.postId = $scope.searchResults[index].id;

      if($scope.validateQuoteData(quoteData,index)){
        $http({
          method: 'POST',
          url: config.serverUrl+'bluecollar/seller-quote-action',
          headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
          },
          data: quoteData,
        }).then(function(response){
            console.log(response.data.data);
            if(response.data.success == true){
               $scope.searchResults[index].isQuoted = true;
              // console.log($scope.searchResults[index].quoteData);
               $scope.hideform = true;
            }else if(response.data.success == false){
               $scope.hideform = true;
            }
        }, function(response){

        });
     }
    }else{
      quoteData = {};
      quoteData.action = 'INITIALISING';
      quoteData.quotationType = quoteType;
      quoteData.quotePrice = '';
      quoteData.quoteDays = '';
      // console.log(quoteData);
      $scope.validateQuoteData(quoteData,index);
    }
  };

  $scope.modify = function(){
    if($scope.validate()){
      SellerSearchServices.setSearchPageData($scope.formData);
      $scope.prevData = $scope.formData;
      initializeFilters();
      $('body #modificationModal').modal('hide');
     // for(d in $scope.prevData){
    //    if(d!='experience'){
     //     if(d=='machineType'||d=='vehicleType'||d=='employmentType'||d=='salaryType'){
      //      if($scope.prevData[d]!=""&&$scope.searchData[d].indexOf($scope.prevData[d])==-1){
      //        $scope.searchData[d].push($scope.prevData[d]);
        //      console.log($scope.searchData);
      //      }
       //   }else{
       //     $scope.searchData[d] = $scope.prevData[d];
      //    }
       // }

     // $('body #modificationModal').modal('hide');}
    }
  };
  
  $scope.toggleFilter = function () {
        if ($scope.showFilters) {
            $scope.showFilters = false;
        } else {
            $scope.showFilters = true;
        }
  };
  
 $scope.setRating = function(type){
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


  $scope.removeRating = function(index){
    $scope.searchData.rating.splice(index, 1);
  };

  $scope.removeVehicleType = function(index){
    $scope.searchData.vehicleType.splice(index, 1);
  };

  $scope.removeEmploymentType = function(index){
    $scope.searchData.employmentType.splice(index, 1);
  };

  $scope.removeQualification = function(index){
    $scope.searchData.qualification.splice(index, 1);
  };

  $scope.openDetails = function(index,Quoted){
    $scope.searchResults[index].showDetails = !$scope.searchResults[index].showDetails;
  };

  $scope.isDetailsHidden = function(index){
    if($scope.searchResults[index].showDetails==undefined){
      $scope.searchResults[index].showDetails = false;
    }
    return $scope.searchResults[index].showDetails;
  }
  
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

 $scope.$watch('searchData', function(newValue, oldValue){
    $http({
      url: serverUrl+'bluecollar/seller-search',
      method: 'POST',
      headers: {
        'authorization': 'Bearer ' + localStorage.getItem("access_token")
      },
      data: $scope.searchData,
    }).then(function success(response){
      var data = response.data;
      if(data.data.hasOwnProperty('response')){
        $scope.searchResults = data.data.response.docs;
        console.log($scope.searchResults);
      }
      // response=JSON.stringify(response);
      // response=JSON.parse(response);
    }, function error(response){

    });
  }, true);

//  for(d in $scope.prevData){
 //   if(d!='experience'){
 //     if(d=='machineType'||d=='vehicleType'||d=='employmentType'||d=='salaryType'){
 //       $scope.searchData[d].push($scope.prevData[d]);
//      }else{
 //       $scope.searchData[d] = $scope.prevData[d];
//      }
//    }
//  }

}]);
