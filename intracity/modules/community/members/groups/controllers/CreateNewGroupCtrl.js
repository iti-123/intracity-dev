app.controller('CreateNewGroupCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger', function ($scope, $http, config, apiServices, type_basis, $state, $dataExchanger,apiCommunityServices) {
    var serverUrl = config.serverUrl;

    $scope.isMemberShow = false;
    $scope.group = {};

    $scope.getAllBusiness = function() {        
        let url = serverUrl + 'community/getAllBusiness?type=individual';        
        apiServices.getMethod(url).then(response => {
            $scope.allusers = response.payload.business;
            console.log($scope.allusers);
        }).catch();
    }
    $scope.getAllBusiness();

    $('.chosen-select').chosen();
    $('.chosen-select-deselect').chosen({ allow_single_deselect: true });   

    $scope.showMember = function(isPublic) {
        if (isPublic == 'private') {
            $scope.isMemberShow = true;
        } else {
            $scope.group.members = [];
            $scope.isMemberShow = false;
        }
    }

    $scope.newGroup = function(data) {
        let url = serverUrl + 'community/createNewGroup';        
        apiServices.postMethod(url,data).then(response => {
            $scope.groupResponse = response;
            console.log(response);
            $state.go("groups");
        }).catch();
    }
}]);    