app.directive('loading',   ['$http' ,function ($http)
{
    return {
        restrict: 'E',
        template : '<div class="loading-spiner"><img src="../intracity/images/loader.gif" /></div>',     
        link: function (scope, elm, attrs)
        {
            scope.isLoading = function () {
                return $http.pendingRequests.length > 0;
            };
            scope.$watch(scope.isLoading, function (v)
            {
                if(v){
                    elm.show();
                }else{
                    elm.hide();
                }
            });
        }
    };

}]);