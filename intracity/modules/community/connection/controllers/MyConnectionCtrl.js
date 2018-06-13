app.controller('MyConnectionCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger', function ($scope, $http, config, apiServices, type_basis, $state, $dataExchanger) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.storagePath = STORAGE_PATH;
    $scope.servicetype = SERVICE_TYPE;
    $scope.activeUserProfileForComunity();
    let role = Auth.getUserActiveRole().toLowerCase();

    $scope.tab = {
        Individual:true,
        Partner:false,
        Groups:false
    }

    $scope.getTab = function(tab) {
        $scope.tab = {
            Individual:tab == 'Individual'?true:false,
            Partner:tab == 'Partner'?true:false,
            Groups:tab == 'Groups'?true:false,
        }      
    }


}]);

