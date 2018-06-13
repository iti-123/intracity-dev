app.controller('BuyerPostCardCtrl', ['$scope', '$http', 'config', '$location', '$q', 'BuyerSearchServices','$state', function($scope, $http, config, $location, $q, BuyerSearchServices,$state){
  BuyerSearchServices.checkBuyer();
  var serverUrl = config.serverUrl;

  $scope.profileTypes = [{key: 'DRIVER', value: 'DRIVER'},{key: 'CLEANER', value: 'CLEANER'},{key: 'SKILLED', value: 'SKILLED'},{key: 'SEMISKILLED', value: 'SEMISKILLED'}];
  //$scope.vehicleTypes = ['Vehicle Type', 'BIKE', 'LMV', 'MMV', 'HMV'];
  $scope.multiplePosts = [];
  $scope.employmentTypes = [{key: 'FULL TIME', value: 'FULL_TIME'}, {key: 'PART TIME', value: 'PART_TIME'}, {key: 'CONTRACT', value: 'CONTRACT'}];
  $scope.salaryTypes = [{key: 'PER DAY', value: 'PER_DAY'}, {key: 'PER WEEK', value: 'PER_WEEK'}, {key: 'PER MONTH', value: 'PER_MONTH'}];
  $scope.quotationTypes = [{key: 'FIRM', value: 'FIRM'}, {key: 'COMPETITIVE', value: 'COMPETITIVE'}];
  $scope.qualificationTypes = [{key: 'SSLC', value: 'SSLC'}, {key: 'Intermediate', value: 'INTERMEDIATE'}, {key: 'Graduate', value: 'GRADUATE'}, {key: 'Post Graduate', value: 'POST_GRADUATE'}];
  //$scope.locationPH = '';
  $scope.vehicleTypePH = 'Vehicle Type';
  $scope.machineTypePH = 'Machine Type';
  $scope.isSellerListVisible = false;
  $scope.sellerPH = '';
  //$scope.locationPH = '';
  $scope.first = true;
  $scope.isP = true;
   
  $scope.vehicleTypes = vehType;

  /*$http({
    method: 'GET',
    url: serverUrl+'bluecollar/vehicle-types',
    headers: {
      'authorization': 'Bearer ' + localStorage.getItem("access_token")
    }
  }).then(function success(response){
    if(response.data){
      $scope.vehicleTypes = response.data.data;
      $scope.vehicleTypes.unshift({name: "Vehicle Type", id: ""});
    }
  }, function error(response){
    //
  });*/
  
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
    url: serverUrl+'bluecollar/machine-types',
    headers: {
      'authorization': 'Bearer ' + localStorage.getItem("access_token")
    }
  }).then(function success(response){
    if(response.data){
      $scope.machineTypes = response.data.data;
      $scope.machineTypes.unshift({name: "Machine Type", id: ""});
    }
  }, function error(response){
    //
  });

  //$scope.prevData = BuyerSearchServices.getPostCardPageData();

  $scope.formData = {
    profileType: '',
    location: '',
    locationPH: '',
    vehicleTypePH: '',
    machineTypePH: '',
    vehicleTypes: [],
    machineTypes: [],
    employmentTypes: '',
    experience: '',
    salaryType: '',
    quotationType: '',
    salary: '',
    qualifications: '',
    privacy: '',
    sellers: [],
  };

  $scope.defaultForm = angular.copy($scope.formData);

  $scope.formError = {
    profileType : false,
    location: false,
    vehicleTypes: false,
    machineTypes: false,
    employmentTypes: false,
    experience: false,
    salaryType: false,
    salary: false,
    sellers: false,
    sellerPH: false,
    sellerListPH: false,
    qualifications: false,
    quotationType: false
  };

  $scope.addMultiplePosts = function (data) {
    if($scope.validate()){
      $scope.new = angular.copy(data);
      $scope.multiplePosts.push($scope.new);

      $scope.isP = true;
      $scope.formData = angular.copy($scope.defaultForm);
      $scope.formData.locationPH = '';
      console.log("AddMultiple Posts ::", $scope.multiplePosts);
    }
  }
  
  $scope.deleteMultiplePosts = function(index) {
    $scope.multiplePosts.splice(index,1);
  }
  
  $scope.editMultipleRate = function(value,$index) {
   // console.log("editMultipleRate",$scope.multiplePosts);
    $scope.multiplePosts.splice($index,1);
    $scope.formData = value;
    $scope.locationPH = value.location;
    $scope.formError.sellers = false;

    if(value.privacy == 'PRIVATE'){
      $scope.isSellerListVisible = true;
    }else if(value.privacy == 'PUBLIC' || value.privacy == ''){
      $scope.isSellerListVisible = false;
    }
    
    $scope.isUpdate = true;
    console.log('Abc',value);
  }

 // for(var i in $scope.prevData){
 //   if(i!='salary'&&i!='salaryType'&&i!='experience'&&i!='rating'){
  //    $scope.formData[i] = $scope.prevData[i];
 //   }
 //   if(i=='location'){
  //    $scope.formData.locationPH = $scope.formData[i].city_name+', '+$scope.formData[i].state_name;
  //  }
 // }

  $scope.searchResults = [];

  $scope.searchSeller = function(text){
    var deferred = $q.defer();
    $http({
      method: 'POST',
      url: serverUrl+'bluecollar/buyer-seller-search',
      headers: {
        'authorization': 'Bearer ' + localStorage.getItem("access_token")
      },
      data: {'search': text}
    }).then(function(response){
      deferred.resolve(response.data.data);
    }, function(response){
      deferred.reject(response);
    });
    return deferred.promise;
  }

  $scope.selectSeller = function($item, $model, $label){

  }

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
          $scope.searchResults = response.data.data;
        }else{
          $scope.searchResults = '';
        }
    }, function(response){

    });
  };

  $scope.addVehicleType = function(){
    var vehicleType = JSON.parse($scope.vehicleTypePH);
    var index = -1;
    if(vehicleType!=''){
      let exists = false;
      for(let v in $scope.formData.vehicleTypes){
        if($scope.formData.vehicleTypes[v].name==vehicleType.name){
          exists = true;
          index = v;
        }
      }
      if(!exists){
        $scope.formData.vehicleTypes.push(vehicleType);
      }else{
        $scope.formData.vehicleTypes.splice(index, 1);
      }
    }
  };

  $scope.addMachineType = function(){
    var vehicleType = JSON.parse($scope.machineTypePH);
    var index = -1;
    if(vehicleType!=''){
      if(vehicleType.name!='Machine Type'&&vehicleType.name!=''){
        let exists = false;
        for(let v in $scope.formData.machineTypes){
          if($scope.formData.machineTypes[v].name==vehicleType.name){
            exists = true;
            index = v;
          }
        }
        if(!exists){
          $scope.formData.machineTypes.push(vehicleType);
        }else{
          $scope.formData.machineTypes.splice(index, 1);
        }
      }
    }
  };

  $scope.selectedLocation = function($item, $model, $label){
    $scope.formData.location = $item;
  };

  $scope.selectSeller = function($item, $model, $label){
    var exists = false;
    for(let l of $scope.formData.sellers){
      if(l.id==$item.id){
        exists = true;
      }
    }
    if(!exists){
      $scope.formData.sellers.push($item);
    }
    $scope.sellerPH = '';
  };

  $scope.removeSeller = function(index){
    $scope.formData.sellers.splice(index, 1);
  };

  $scope.showHideSellerList = function(privacy){
    if(privacy=='private'){
      $scope.isSellerListVisible = true;
      console.log('Private',$scope.formData.sellers);
    }else{
      $scope.formData.sellers = [];
      $scope.formError.sellers = false;
      $scope.isSellerListVisible = false;
    }
  };

  $scope.validate = function(){
    var validated = true;
    if($scope.formData.profileType==''){
      $scope.formError.profileType = true;
      validated = false;
    }else{
      $scope.formError.profileType = false;
    }
    if(isNaN($scope.formData.experience)||$scope.formData.experience==''){
      $scope.formError.experience = true;
      validated = false;
    }else{
      $scope.formError.experience = false;
    }

    if($scope.formData.employmentTypes==''){
      $scope.formError.employmentTypes = true;
      validated = false;
    }else{
      $scope.formError.employmentTypes = false;
    }

    if($scope.formData.salaryType==''){
      $scope.formError.salaryType = true;
      validated = false;
    }else{
      $scope.formError.salaryType = false;
    }

    if($scope.formData.profileType=='DRIVER' && $scope.formData.vehicleTypePH == ''){
      $scope.formError.vehicleTypes = true;
      validated = false;
    }else{
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

    if($scope.formData.qualifications==''){
      $scope.formError.qualifications = true;
      validated = false;
    }else{
      $scope.formError.qualifications = false;
    }

    if(isNaN($scope.formData.salary) || $scope.formData.salary == ''){
      $scope.formError.salary = true;
      $scope.formError.salaryDigits = false;
      validated = false;
    }else{
      if($scope.formData.salary < 100){
        $scope.formError.salaryDigits = true;
        $scope.formError.salary = false;
        validated = false;
      }else{
        $scope.formError.salaryDigits = false;
      }
      $scope.formError.salary = false;
    }

    if($scope.formData.quotationType==''){
      $scope.formError.quotationType = true;
      validated = false;
    }else{
      $scope.formError.quotationType = false;
    }

    if($scope.isSellerListVisible){
      console.log($scope.formData.sellers);
      if($scope.sellerPH != '' && ($scope.formData.sellers.length == 0)){
        $scope.formError.sellerListPH = true;
        $scope.formError.sellers = false;
        $scope.formError.sellerPH = false;
        validated = false;
        return false;
      }else{
        $scope.formError.sellerListPH = false;
      }

      if(!$scope.formData.sellers.length){
        $scope.formError.sellers = true;
        validated = false;
      }else{
        $scope.formError.sellers = false;
      }

      if($scope.sellerPH != '' && ($scope.formData.sellers.length > 0)){
        $scope.formError.sellerPH = true;
        validated = false;
      }else{
        $scope.formError.sellerPH = false;
      }
    }
    return validated;
  };
  
  $scope.closeConfirmationPopup = function () {
    $("#postConfirmationModal").modal("hide");
    setTimeout(function () {
      $state.go('bluecollar-buyer-post-list');
    }, 1000);
  }

  $scope.post = function(multiplePosts,status) {

      if ($scope.multiplePosts.length < 1) {
          alert("Please Create at least one post");
          return false;
      }
 
      if (!$('#Accept').is(':checked')) {
          alert('Accept Terms & Conditions ');
          return false;
      }

      if(status == 1){
        //+sessionStorage.setItem('postDraft',false);
      }else if(status == 0){
        +sessionStorage.setItem('postDraft',true);
      }

      $scope.first = false;
      var requestPayload = {
          "data": JSON.stringify(multiplePosts),
          "status": status,
      };

      var str = "Your request for post has been successfully posted to the relevant seller."
     // $('.loaderGif').show();
      $http({
        url: serverUrl+'bluecollar/buyer-post/' + status,
        method: 'POST',
        headers: {
          'authorization': 'Bearer ' + localStorage.getItem("access_token")
        },
        data: JSON.stringify(multiplePosts),
      }).then(function success(response){
        if(response.data.success){
          
          $scope.vehicleTypePH = 'Vehicle Type';
          $scope.machineTypePH = 'Machine Type';
          $scope.isSellerListVisible = false;
          $scope.sellerPH = '';

          $("#responsetext").html(str);
          if(status == 1){
            $('body #postConfirmationModal').modal('show');
          }else{
            $state.go('bluecollar-buyer-post-list');
          }
          
        }
      }, function error(response) {
          $('.loaderGif').hide();
      });
  };
}]);
