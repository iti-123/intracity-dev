app.controller('IndividualCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger','apiCommunityServices', function ($scope, $http, config, apiServices, type_basis, $state, $dataExchanger,apiCommunityServices) {
    var serverUrl = config.serverUrl;
    $scope.searchData = {
        name: "",
        location:"",
        company:""
    };
    
    $scope.search = function() {
        setTimeout(function(){
            $scope.getAllBusiness();
        },2000);
    }

    $scope.getAllBusiness = function() {        
        let url = serverUrl + 'community/getAllBusiness?type=individual';        
        apiServices.postMethod(url,$scope.searchData).then(response => {
            $scope.allusers = response.payload.business;
        }).catch();
    }
    $scope.getAllBusiness();

    /*$scope.$watch("searchData",function() {
        setTimeout(function(){
            console.log($scope.searchData);

        },2000);
    },true);*/
   
    $scope.is_active = false;
    $scope.toggle = function() {

      $scope.is_active = !$scope.is_active;
    };


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

    $scope.getInvitation = function() {
        let url = serverUrl + 'community/getInvitation?type=individual';
        apiServices.getMethod(url).then(response => {
            $scope.invitations = response.payload;
            console.log($scope.invitations);          
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