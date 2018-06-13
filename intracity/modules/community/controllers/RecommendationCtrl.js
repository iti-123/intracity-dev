app.controller('RecommendationCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger', function ($scope, $http, config, apiServices, type_basis, $state, $dataExchanger) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.servicetype = SERVICE_TYPE;
    $scope.activeUserProfileForComunity();
    let role = Auth.getUserActiveRole().toLowerCase();


    $scope.tab = {
        followFresh:true,
        following:false,
        followers:false
    }

    $scope.getTab = function(tab) {
        $scope.tab = {
            followFresh:tab == 'followFresh'?true:false,
            following:tab == 'following'?true:false,
            followers:tab == 'followers'?true:false,
        }
        if($scope.tab.followers) {
            $scope.getMyFollowers();
        }
        if($scope.tab.following) {
            $scope.getMyFollowing();
        }    
    }
    
    $scope.getFollowers = function() {        
        let url = serverUrl + 'community/getFollowers';        
        apiServices.postMethod(url,{role:'seller'}).then(response => {
            $scope.followers = response.payload.followers;
            $scope.totalFollowers = response.payload.totalFollowers;
            $scope.totalFollowing = response.payload.totalFollowing;
            // console.log("$scope.followers",$scope.followers);

        }).catch();
    }

    $scope.getMyFollowers = function() {        
        let url = serverUrl + 'community/getMyFollowers';        
        apiServices.postMethod(url,{role:role}).then(response => {
            $scope.myFollowers = response.payload;
            // console.log("getMyFollowers",$scope.myFollowers);

        }).catch();
    }

    $scope.getMyFollowing = function() {        
        let url = serverUrl + 'community/getMyFollowing';        
        apiServices.postMethod(url,{role:role}).then(response => {
            $scope.myFollowing = response.payload;
            console.log("getMyFollowing",$scope.myFollowing);
        }).catch();
    }
   
    $scope.getFollowers();

    $scope.follow = function(userId,index) {        
        let url = serverUrl + 'community/follow';   
        let data = {
            role:'seller',
            userId: userId
        }     
        apiServices.postMethod(url,data).then(response => {
            $scope.getFollowers();
        }).catch();
    }

    $scope.unFollow = function(followingId) {        
        let url = serverUrl + 'community/unFollow';   
        let data = {
            role:role,
            followingId: followingId
        }     
        apiServices.postMethod(url,data).then(response => {
            $scope.getFollowers();
            $scope.getMyFollowing();
        }).catch();
    }



}]);