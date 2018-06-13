app.controller('SellerRegistrationRequestsCtrl', ['$scope', '$http', 'config', 'trackings', 'apiServices', 'discount','SellerSearchServices', function ($scope, $http, config, trackings, apiServices, discount,SellerSearchServices) {
    SellerSearchServices.checkSeller();
    var serverUrl = config.serverUrl;
    
    $scope.sellerList = [];

    $http({
        method: 'GET',
        url: serverUrl + 'bluecollar/seller-all-unverified',
        headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
        },
    }).then(function success(response) {
        let data = response.data;
        if (data.hasOwnProperty('data')) {
            $scope.sellerList = data.data.data;
        }
    }, function error(response) {
        console.log(error);
    });

}]);
