app.controller('EventCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger','apiCommunityServices', function ($scope, $http, config, apiServices, type_basis, $state, $dataExchanger,apiCommunityServices) {
    $scope.STATUS=STATUS;
    $scope.ARTICLE=ARTICLE;
    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.servicetype = SERVICE_TYPE;
    $scope.data = [];
    $scope.type_basis = type_basis.type_basis;
    $scope.UNIT = ESTIMATED_UNIT;
    $scope.priceTypes = HYPER_PRICE_TYPES;
    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
        });
    }
    //  Get city of intracity 
    $scope.getCity(url);
    $scope.getLocationByCity = function (url) {
        apiServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            console.log('Locations:', $scope.locations);
        });
    }
    $scope.createArticle=function()
    {   
        $scope.event.post_type = 2;
        var requestPayload = {
            "data": JSON.stringify($scope.event),
            "serviceName": $dataExchanger.request.serviceName,
        };
                
        var url = serverUrl + 'community/add-article';
            apiCommunityServices.addArticle(url,requestPayload).then(function (response) {
            $scope.payload = response;
            if($scope.payload.status=='success');
            {
                if($scope.payload.payload.articletype === 1) {
                    $state.go("event-list",{type:'free'});
                } else if(parseInt($scope.payload.payload.articletype)  === 2) {
                    $state.go("event-list",{type:'paid'});
                }
            }
        });
    }

}]);