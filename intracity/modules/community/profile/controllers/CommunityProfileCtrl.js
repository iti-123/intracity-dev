app.controller('CommunityProfileCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger','apiCommunityServices', function ($scope, $http, config, apiServices, type_basis, $state, $dataExchanger,apiCommunityServices) {
    var serverUrl = config.serverUrl;
    
    $scope.getProfileDetail = function(slug) {
        let url = serverUrl+'community/getProfileDetail?q='+slug;
        apiServices.getMethod(url).then(response => {
            $scope.profile = response.payload.data;
            $scope.profileDetail = response.payload;
            console.log(response.payload);
        }).catch();
    }

    $scope.getProfileDetail($state.params.slug);

    $scope.isActive = false;
    $scope.activeButton = function() {
        $scope.isActive = !$scope.isActive;
    }  

}]);    