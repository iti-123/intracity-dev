app.controller('CheckoutCtrl', ['$scope', '$http', 'config', 'consignment', 'apiServices', '$state','$rootScope', function($scope, $http, config, consignment, apiServices, $state,$rootScope) {

   $scope.orderid='';
   $rootScope.orderno='';
    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.BCdate = new Date();
    
    $scope.lkp_intracity_service_id = _INTRACITY_;
    $scope.lkp_hyperlocal_service_id = _HYPERLOCAL_;
    $scope.lkp_bluecollar_service_id = _BLUECOLLAR_;

    $scope.consignment = consignment.consignment_type;
    $scope.bookNow = [];
    apiServices.getCartItems(serverUrl + "intracity/carts").then(function(response) {
        $scope.bookNow = response.payload;
        console.log('cartdata',$scope.bookNow);
        if (!$scope.bookNow.length) {
            $state.go('buyer-search');
        } 
        $scope.searchData = JSON.parse(response.payload[0].search_data);
        //console.log("Book Now Data::", $scope.searchData);
    });
    
    $scope.getTotal = function() {
        var total = 0;
        for (var i = 0; i < $scope.bookNow.length; i++) {
            var product = $scope.bookNow[i].price;
            total = (total + parseFloat(product));
        }
        // console.log(total);
        return parseFloat(total).toFixed(2);
    };
    /// order place  orderplace
    apiServices.order(serverUrl + "orderplace").then(function(response) {
        if(response.status=='success')
         {
          $scope.orderid=response.payload.id;  
          apiServices.prepaidOrderData(serverUrl + "intracity/dataPrepaid",$scope.orderid).then(function(response) {
            $scope.prepaid=response.payload;
            });
         }
    });

    /******hdfc payment getwaycode************/
   
    /**********hdfc payment getway end here**********/
    //temprary payment
    $scope.makepayment=function() {
          var orderid=$scope.orderid;
          console.log('sss',orderid);
          if($("#Accept2").prop('checked') == false){
            alert('Please Accept Terms & Conditions');
            return false;
                 }
              casevalue=$scope.model.prepaid;
              console.log('casevalue',casevalue);
              switch($scope.model.prepaid) {
                  case 1:
                      document.getElementById("hdfc").submit();
                      break;
                  case 2:
                      
                      document.getElementById("hdfc").submit();
                      break;
                 
                  case 3:
                  
                  apiServices.orderConform(serverUrl + "orderconform", orderid).then(function(response) {
                      if(response.status=='success')
                      {
                          $rootScope.orderno=orderid;
                          $state.go('success',{'id':orderid});
                      }   
                  });
               default:
               
          }
     
    }
    $scope.resetpayment=function()
    {
      $('input[name="prepaid"]').prop('checked', false);
      $scope.model.prepaid=3;
    }

}]);