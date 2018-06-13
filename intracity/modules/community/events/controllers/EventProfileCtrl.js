app.controller('EventProfileCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger','apiCommunityServices','$sce', function ($scope, $http, config, apiServices, type_basis, $state, $dataExchanger,apiCommunityServices,$sce) {
    
    var serverUrl = config.serverUrl;

    $scope.storagePath = STORAGE_PATH;

    $scope.$sce = $sce;

    $scope.getarticleLIst=function()
    {
      url=serverUrl + 'community/article-list/'+$state.params.id
      apiCommunityServices.viewarticle(url).then(function (response) {
      $scope.event = response.data[0];
      console.log('$scope.eventDetail',$scope.event);      
      });
    }
   
    $scope.getarticleLIst();

}]); 