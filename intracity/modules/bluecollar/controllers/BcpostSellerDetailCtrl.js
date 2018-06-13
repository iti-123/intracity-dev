
app.controller('BcpostSellerDetailCtrl', ['$scope', '$http', 'config','apiServices','$state', function ($scope, $http, config,apiServices,$state) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;


    console.log($state.params.id);
    //return false;


    var url = serverUrl + 'bluecollar/seller-post-detail-page';
    apiServices.sellerDetailPage(url + '/' + $state.params.id).then(function (response) {
        $scope.postDetail = response.data;
        console.log('POSSTTTT DEETAILSSS:',response.data);
    });


}]);
