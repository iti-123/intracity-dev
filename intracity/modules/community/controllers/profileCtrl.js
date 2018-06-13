app.controller('profileCtrl', ['$scope', '$http', 'config', 'apiServices','apiCommunityServices','type_basis', '$state', '$dataExchanger', '$sce', function ($scope, $http, config, apiServices,apiCommunityServices, type_basis, $state, $dataExchanger,$sce) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.storagePath = STORAGE_PATH;
    $scope.servicetype = SERVICE_TYPE;
    $scope.$sce = $sce;

    $scope.Auth = Auth;
    console.log($scope.Auth.getUserName());
    $scope.activeUserProfileForComunity();
    let role = Auth.getUserActiveRole().toLowerCase();
    
    $scope.getFollowers = function() {        
        let url = serverUrl + 'community/getFollowers';        
        apiServices.postMethod(url,{role:'seller'}).then(response => {
            $scope.followers = response.payload.followers;
        }).catch();
    }

    $scope.getFollowers();

    $scope.getAllBusiness = function() {        
        let url = serverUrl + 'community/getAllBusiness?type=individual';        
        apiServices.postMethod(url).then(response => {
            $scope.allusers = response.payload.business;
        }).catch();
    }
    $scope.getAllBusiness();

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

    $scope.sendInvitationForConnection = function(type,value) {  
        let data = {
            type: type,
            userId:value.id,
            name: value.name
        };
        let url = serverUrl + 'community/sendInvitation';
        apiServices.postMethod(url,data).then(response => {            
            $scope.getAllBusiness();                    
        }).catch();
    }

    $scope.getCommunityPost = function() {
        var url = serverUrl + 'community/article-list';
        apiServices.getMethod(url).then(function (response) {
            $scope.posts = response;
            console.log($scope.posts);
        });
    }

    $scope.getCommunityPost();

    $scope.viewAll = function(key,type) {
        // console.log($scope.posts.data[key].description);
        let length = 150;
        if(type == 'more') {
            length = $scope.posts.data[key].description.length;
        } else if(type == 'less') {
            length = 150;
        }
        $scope.posts.data[key].limit = length;
    }

    $scope.like = function(id,type) {
        $scope.requestdata={type:type,id:id};
        var url=serverUrl+"community/article-likes";
        apiServices.postMethod(url,$scope.requestdata).then(function(response){
            $scope.getCommunityPost();
            console.log(response);
        });
    }

    $scope.comment=function(key,event)
    {
        $scope.requestdata=$scope.posts.data[key];
        var url =serverUrl +'community/post-comment';
        apiCommunityServices.postComment(url,$scope.requestdata).then(function (response) {                     
            if(response.isSuccessful==true)
            {   
                $scope.getCommunityPost();
            }               
        });            
        
    }

    $scope.doReply = function(key,cKey) {
        $scope.posts.data[key].comments[cKey].isReply = true;
    }

    $scope.showPreviousComments = function(key,length) {
        $scope.posts.data[key].commentLimit = length;
    }

    $scope.viewRepliedComment = function(key,cKey) {
        $scope.posts.data[key].comments[cKey].isDisplayReplied = !$scope.posts.data[key].comments[cKey].isDisplayReplied;
    }

    $scope.replyComment=function(value,$event)
    {
        console.log(value);
      if(event.keyCode==13)
           {
              $scope.requestdata=value;
                var url =serverUrl +'community/post-comment-reply';
                apiCommunityServices.postCommentReply(url,$scope.requestdata).then(function (response){
                    if(response.isSuccessful==true) {
                        $scope.getCommunityPost();
                    }
                });
           }
    }

    $scope.seeLess = function(desc,limit) {
        return desc.substring(0,limit);
    }

    $scope.myfunction = function(id) {
      $scope.getCommentId = id;
      $("#all_comment"+$scope.getCommentId).toggleClass("active_view_all");
    }

}]);

