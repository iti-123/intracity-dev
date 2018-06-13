app.controller('GroupsCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger', function ($scope, $http, config, apiServices, type_basis, $state, $dataExchanger,apiCommunityServices) {
    var serverUrl = config.serverUrl;
    $scope.storagePath = STORAGE_PATH;
    console.log($scope.storagePath);
    $scope.isPublic = 'public';
    $scope.getGroup = function(type) {
        let url = serverUrl+'community/getGroup?t='+type;
        apiServices.getMethod(url).then( response => {
            $scope.groups = response;
            $scope.isPublic = type;
            console.log(response);
        }).catch();
    }
    $scope.getGroup($scope.isPublic);
    
    $scope.sendInvitationForConnection = function(type,key) {  
        let value = $scope.groups.payload.data[key];      
        let data = {
            type: type,
            userId:value.created_by,
            name: value.name,
            groupId:value.id
        };
        let url = serverUrl + 'community/sendInvitation';
        apiServices.postMethod(url,data).then(response => {
            $scope.message = response.payload.message;   
            $scope.groups.payload.data[key].is_joining = true; 
            
            $scope.getGroup($scope.isPublic);                    
        }).catch();
    }

    $scope.getInvitation = function() {
        let url = serverUrl + 'community/getInvitation?type=group';
        apiServices.getMethod(url).then(response => {
            $scope.invitations = response.payload;
            console.log("$scope.invitations",$scope.invitations);          
        }).catch();
    }
    $scope.getInvitation();

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
}]);    