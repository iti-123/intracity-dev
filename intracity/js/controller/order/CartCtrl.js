app.controller('CartCtrl', ['$scope', '$http', 'config', 'consignment', 'apiServices', '$state','$location', function($scope, $http, config, consignment, apiServices, $state,$location) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.BCdate = new Date();

    $scope.consignment = consignment.consignment_type;
    
    $scope.lkp_intracity_service_id = _INTRACITY_;
    $scope.lkp_hyperlocal_service_id = _HYPERLOCAL_;
    $scope.lkp_bluecollar_service_id = _BLUECOLLAR_;
    
    var isUndefinedOrNull = function(val) {
        
        return ( angular.isUndefined(val) || val === null || val === 0 || val == 0 ) ? 0 : val;
    }

    apiServices.getCartItems(serverUrl + "intracity/carts").then(function(response) {
        

        console.log('mmmmmm',response);
        $scope.bookNow = response.payload;
        if (!$scope.bookNow.length) {
            $state.go('cart');
        }
        $scope.searchData = JSON.parse(response.payload[0].search_data);
        console.log("$scope.bookNow::", $scope.bookNow);

        //console.log('xxx',response.payload.length);
        if (response.payload.length == 0) {
            $state.go("cart");
        }
        console.log('lenth',$scope.bookNow.length);
        var total = 0;
        for (var i = 0; i < $scope.bookNow.length; i++) {
            console.log(i);
            
            var productprice = isUndefinedOrNull($scope.bookNow[i].price) ? isUndefinedOrNull($scope.bookNow[i].price) : 0.00;
            console.log('product price ',parseFloat(productprice));
            total +=  parseFloat(productprice);
        }
        console.log('ddd',total);
        $scope.totalAmount = total.toFixed(2);
    });



    $scope.deleteItem = function(index, cartId, buyerId) {
        if(confirm('Are you sure you want to delete this item')) {
            var url = serverUrl + "intracity/deleteCarts";
            apiServices.deleteCartItems(url, cartId, buyerId ).then(function(response) {
                console.log(response);
                var res = response.isSuccessfull;
                if (res) {
                    $scope.bookNow.splice(index, 1);
                }

                location.reload();

            });
        }        
    };

    $scope.ContinueShopping = function() {
        $state.go("cart");
    };
    $scope.clearCart = function(buyerId) {        
        var url = serverUrl + "intracity/clearCarts";
        apiServices.clearCartItems(url, buyerId).then(function(response) {

            var res = response.isSuccessfull;
            if (res) {
               
                $state.go("cart");

            }

        });
    }

}]);