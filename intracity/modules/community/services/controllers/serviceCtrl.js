
app.controller('serviceCtrl', ['$window','$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger', function ($window, $scope, $http, config, apiServices, type_basis, $state, $dataExchanger) {
    var serverUrl = config.serverUrl;
    $scope.storagePath = STORAGE_PATH; 

    $scope.formData = {
        message_from : '',
        message_to : '',
        message_subject : '',
        message_body : '',
    };

    $scope.cmtToServiceRedirect = function(serviceId,serviceUrl) {
        if(serviceUrl.length>0){
        $window.location.href = 'index.html#'+serviceUrl;    
        }
    }

    if (localStorage.getItem("communityServices") === null) {
      $scope.cmtServicId = 3;
    }
    else{
        $scope.cmtServicId = localStorage.getItem("communityServices");
    }
    
    $scope.replyMessageModal = function (value) {
        $scope.UserName = Auth.getUserName();
        $("#message_from").val("From: " + $scope.UserName);
        $("#message_to").val("To: " + value.name);
        $scope.toName = value.name;
        $("#messageReplyModal").modal('show');
    };
    
    $scope.replyMessages = function () {
        $scope.formData.message_from = $dataExchanger.request.data.sender_id;
        $scope.formData.message_to = $scope.toName;
        
        $http({
            url: serverUrl + 'messages/communityMessage/',
            method: 'POST',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: JSON.stringify($scope.formData),
        }).then(function success(response) {
            if (response.data.isSucessfull == true) {
                $("#replyStatus").html("Message sent Successfully").addClass("text-success");
                setTimeout(function () {
                    $("#messageReplyModal").modal('hide');
                }, 2000);
                location.reload();
            }
        }, function error(response) {
            $('.loaderGif').hide();

        });
    };
    
    $scope.filterData = {
        cmtServicId:$scope.cmtServicId,
        location: {},
        locationPH: '',
        profileType: [],
        vehicleType: [],
        machineType: [],
        employmentType: [],
        salaryType: [],
        qualification: [],
        status: [],
        pageLoader : 5,
        pageNextValueCount : 5
    };
     // $scope.employementType =  $state.params.type.split("-").join(" ");

    $http({
        url: serverUrl+'community/get-all-seller-community',
        method: 'POST',
        data:$scope.filterData,
        headers: {
          'authorization': 'Bearer ' + localStorage.getItem("access_token")
        },
    }).then(function success(response) {
        $scope.sellerdata = response.data.payload.data;
        console.log('Seller Datas',$scope.sellerdata);
    }, 
    function error(response) { }
    );

}]);    