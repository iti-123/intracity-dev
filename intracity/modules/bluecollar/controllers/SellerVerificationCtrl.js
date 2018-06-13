app.controller('SellerVerificationCtrl', ['$scope', '$http', 'config', '$routeParams', '$state','SellerSearchServices', function ($scope, $http, config, $routeParams, $state,SellerSearchServices) {
    SellerSearchServices.checkSeller();
    var serverUrl = config.serverUrl;
    $scope.sellerData = undefined;
    $scope.verify = 'Verify';

    $scope.getImageUrl = function (path) {
        return serverUrl + path;
    };

    $scope.jsonfyL = function (text) {
        return JSON.parse(text);
    };

    $scope.verifySeller = function () {
        if ($scope.verify == 'Verify') {
            $http({
                method: 'GET',
                url: serverUrl + 'bluecollar/seller-verify?sellerId=' + $scope.sellerData.enc_id,
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
            }).then(function success(response) {
                let data = response.data;
                if (data.hasOwnProperty('success')) {
                    $scope.verify = 'Verified';

                    $state.go('home');

                }
            }, function error(response) {
                console.log(error);
            });
        }
    };

    $http({
        method: 'GET',
        url: serverUrl + 'bluecollar/seller-verification-data?sellerId=' + $state.params.sellerId,
        headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
        },
    }).then(function success(response) {
        let data = response.data;
        if (data.hasOwnProperty('data')) {
            $scope.sellerData = data.data;
            $scope.sellerData.languages = $scope.jsonfyL($scope.sellerData.languages);
        }
    }, function error(response) {
        console.log(error);
    });

}]);
