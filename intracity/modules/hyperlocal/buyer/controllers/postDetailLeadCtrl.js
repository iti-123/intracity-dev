app.controller('postDetailLeadCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', 'trackings','$dataExchanger', function ($scope, $http, config, apiServices, type_basis, $state, trackings,$dataExchanger) {
   
    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.data = [];
   
    $scope.filterDatas = {
        sellerType: []
    };
    $scope.filClearValue = false;
    var url = serverUrl + 'hyperlocal/buyer-postlead-details';
    apiServices.hyperbuyerPostDetail(url + '/' + $state.params.id).then(function (response) {
        arr = response.data;
        $scope.post = arr;
        console.log('POSSTT DETAILSS', $scope.post);
    });
    
    $scope.$watch('filterDatas', function(newValue, oldValue){
        $scope.filterDatas.ids = $state.params.id;
        $http({
          url: serverUrl + 'hyperlocal/hp-buyer-post-lead-list',
          method: 'POST',
          headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
          },
          data: $scope.filterDatas,
        }).then(function success(response){
          if(response.data.success){
            $scope.postHpLeads = response.data.data;
            $scope.getLocationByCity($scope.postHpLeads[0]['city_id']);
          }else{
            $scope.postHpLeads = response.data.data;
          }
          console.log('Post Leads',$scope.postHpLeads[0]['city_id']);
        },function error(response){
           
        });
    },true);
    
    $scope.getLocationByCity = function (city_id) {
        apiServices.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id).then(function (response) {
            $scope.locations = response;
            console.log('Locations:', $scope.locations);
        });
    };

    $scope.openDetails = function (index) {
      $scope.postHpLeads[index].showDetails = !$scope.postHpLeads[index].showDetails;
    };
    
    $scope.getValidFrom = function (arr) {
      var arr1 = JSON.parse(arr);
      return arr1[0].from_date;
    };

    $scope.getValidTo = function (arr) {
      var arr1 = JSON.parse(arr);
      return arr1[0].todate;
    };

    $scope.isDetailsHidden = function (index) {
      if ($scope.postHpLeads[index].showDetails == undefined) {
          $scope.postHpLeads[index].showDetails = false;
      }
      return $scope.postHpLeads[index].showDetails;
    };
    
    $scope.checkedFilterData = function (arr, value) {
      var index = arr.indexOf(value);
      if (index == -1) {
          return false;
      }
      return true;
    }
    
    $scope.addFilterData = function (arr, value) {
      $scope.filClearValue = true;
      var index = arr.indexOf(value);
      if(index == -1){
          arr.push(value);
      }else{
          arr.splice(index, 1);
          if($scope.filterDatas.sellerType.length < 1){
                $scope.filClearValue = false;
          }
      }
    };
    
    $scope.clearAll = function () {
      $scope.filClearValue = false;
      $scope.filterDatas = {
         sellerType: [],
      };
    }
    
    $scope.getUsername = function (id) {
      for (let v of $scope.sellerList) {
          if (v.id == id) {
              return v.username;
          }
      }
    }
    
    $scope.removeElem = function (arr, index) {
        arr.splice(index,1);
        if($scope.filterDatas.sellerType.length < 1){
              $scope.filClearValue = false;
        }
    };
    
    $scope.validateQuotation = function (post,index) {
        var valid = true;
        if ($scope.postHpLeads[index].fromLocation == undefined || $scope.postHpLeads[index].fromLocation == '') {
            valid = false;
            $scope.postHpLeads[index].from_location = true;
        } else {
            $scope.postHpLeads[index].from_location = false;
        }

        if ($scope.postHpLeads[index].tolocation == undefined || $scope.postHpLeads[index].tolocation == '') {
            $scope.postHpLeads[index].to_location = true;
            valid = false;
        } else {
            $scope.postHpLeads[index].to_location = false;
        }
        return valid;
    }

    $scope.bookNowLeads = function (post,index) {
        if ($scope.validateQuotation(post,index)) {
          var value = $scope.postHpLeads[index];
          value.data = { 
                         'departingDate':value.from_date
                       };
                       
          var booknowSerachObj = value;
          $scope.bookPostObj = {
              initialDetails: {
                serviceId: value.lkp_service_id,
                serviceType: '',
                sellerId: value.posted_by,
                buyerId: Auth.getUserID(),
                postType: "BP",
                searchData: booknowSerachObj,
                post: post
              }
          };
          apiServices.bookNowLeads(serverUrl, JSON.stringify($scope.bookPostObj)).then(function (response) {
             if (response.isSuccessful && response.payload.id) {
                  $scope.cartId = response.payload.enc_id;
                  console.log('Cart Id',$scope.cartId);
                  $state.go('order-booknow', {serviceId: _HYPERLOCAL_, cartId: $scope.cartId});
              }
          })
        }
    };
    
    apiServices.getAllSellers(serverUrl).then(function (response) {
        $scope.sellerList = response;
        setTimeout(function () {
            $("#sellerList").tokenInput($scope.sellerList, {propertyToSearch: 'username'});
        }, 1000);
    });
}]);
