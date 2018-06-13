app.controller('SuccessCtrl', ['$scope', '$http', 'config', 'consignment', 'apiServices', '$state', '$rootScope', function ($scope, $http, config, consignment, apiServices, $state, $rootScope) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;

    $scope.lkp_intracity_service_id = _INTRACITY_;
    $scope.lkp_hyperlocal_service_id = _HYPERLOCAL_;
    $scope.lkp_bluecollar_service_id = _BLUECOLLAR_;
    $scope.consignment = consignment.consignment_type;
    //console.log('================',$scope.orderno);
    //console.log('xxxxxxxxxxxxxxxxx',$state.prams.id);
    
    if ($scope.orderno == undefined) {
        $state.go('home');
   } else {
        var orderid = $scope.orderno;
        
        apiServices.orderDetail(serverUrl + "orderinfo",orderid).then(function (response) {

            console.log('response', response);
            $scope.orderdata = response.payload.order_items;
            $scope.calculatePrice = response.payload;

            var total = 0;
            for (var i = 0; i < $scope.calculatePrice.order_items.length; i++) {
                $scope.orderdata[i].order_no =$scope.calculatePrice.order_no;
                
                $scope.orderdata[i].consignee_city =$scope.calculatePrice.consignee_city;
                $scope.orderdata[i].order_city =$scope.calculatePrice.city;
                var product = $scope.calculatePrice.order_items[i].price;
                total = (total + parseFloat(product));
            }
            $scope.totalAmount = parseFloat(total).toFixed(2);

            console.log('$scope.orderdata', $scope.orderdata);
            
        });
    }

}]);