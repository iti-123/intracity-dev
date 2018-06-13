app.controller('IndividualInvitationCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger','apiCommunityServices', function ($scope, $http, config, apiServices, type_basis, $state, $dataExchanger,apiCommunityServices) {
    var serverUrl = config.serverUrl;

    $scope.data = {};
    $scope.getAllBusiness = function() {        
        let url = serverUrl + 'community/getAllBusiness?type=individual';        
        apiServices.getMethod(url).then(response => {
            $scope.allusers = response.payload.business;
            console.log($scope.allusers);
        }).catch();
    }
    $scope.getAllBusiness();

    $scope.sendBulkInvitation = function(invitationData) {
        let data = {
            type: 'individual',
            userId:invitationData.emailIds,
            name: '',
            message: invitationData.message
        };
        let url = serverUrl + 'community/sendBulkInvitation';
        apiServices.postMethod(url,data).then(response => {
            $scope.message = response.payload.message;
            $("#responsetext").html($scope.message);
            $("#InvitationModal").modal("show");

            $scope.getAllBusiness();            
        }).catch();
    }

    $('.chosen-select').chosen();
    $('.chosen-select-deselect').chosen({ allow_single_deselect: true });   

    $scope.closeSellerCard = function() {
        $("#InvitationModal").modal("hide");
        location.reload();
    }

}]);    