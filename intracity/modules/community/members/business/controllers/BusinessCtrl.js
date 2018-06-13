app.controller('BusinessCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger','apiCommunityServices', function ($scope, $http, config, apiServices, type_basis, $state, $dataExchanger,apiCommunityServices) {
    
    var serverUrl = config.serverUrl;
    $scope.searchData = {
        name: "",
    };

    $scope.getAllBusiness = function() {        
        let url = serverUrl + 'community/getAllBusiness?type=partner';        
        apiServices.postMethod(url,$scope.searchData).then(response => {
            $scope.business = response.payload.business;
            console.log($scope.business);

        }).catch();
    }
    $scope.getAllBusiness();
    
    $scope.search = function() {
        setTimeout(function(){
            $scope.getAllBusiness();
        },100);
    }

    $scope.sendInvitationForConnection = function(type,value) {        
        let data = {
            type: type,
            userId:value.id,
            name: value.username
        };
        let url = serverUrl + 'community/sendInvitation';
        apiServices.postMethod(url,data).then(response => {
            $scope.message = response.payload.message;
            $scope.getAllBusiness();            
        }).catch();
    }
    
    setTimeout(function() {
        $scope.message = '';
    }, 10);

    $scope.getInvitation = function() {
        let url = serverUrl + 'community/getInvitation?type=partner';
        apiServices.getMethod(url).then(response => {
            $scope.invitations = response.payload;
            console.log($scope.invitations);          
        }).catch();
    }

    $scope.actionOnInvitation = function(value,action) {
        let url = serverUrl + 'community/invitation/action';

        let data = {
            action: action,
            id:value.id,
            name: value.username
        };

        apiServices.postMethod(url,data).then(response => {
            $scope.message = response.payload.message;        
            $scope.getInvitation();             
        }).catch();
    }
    $scope.getInvitation();

    $scope.follow = function(userId) {        
        let url = serverUrl + 'community/follow';   
        let data = {
            role:'seller',
            userId: userId
        }     
        apiServices.postMethod(url,data).then(response => {
            $scope.getAllBusiness();
        }).catch();
    }
}]);    