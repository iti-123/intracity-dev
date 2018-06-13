app.controller('articleCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger','apiCommunityServices', function ($scope, $http, config, apiServices, type_basis, $state, $dataExchanger,apiCommunityServices) {
    $scope.STATUS=STATUS;
    $scope.ARTICLE=ARTICLE;
    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;

    $scope.storagePATH = STORAGE_PATH;
    $scope.servicetype = SERVICE_TYPE;
    $scope.data = [];
    $scope.type_basis = type_basis.type_basis;
    $scope.UNIT = ESTIMATED_UNIT;
    $scope.priceTypes = HYPER_PRICE_TYPES;
    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
        });
    }
    //  Get city of intracity 
    $scope.getCity(url);
    $scope.getLocationByCity = function (url) {
        apiServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            console.log('Locations:', $scope.locations);
        });
    }

   
    $scope.img = 'Image';
    if($state.params.id != '' || $state.params.id != undefined){
        $http({
            method: 'POST',
            url: serverUrl + 'community/edit-article',
            data: {id:$state.params.id},
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            }
        }).then(function success(response) {
            if(response.data.isSuccessful == true) {
                console.log('Data',response.data.data);
                $scope.article.id = response.data.data.id;
                $scope.article.title = response.data.data.title;
                $scope.article.heading = response.data.data.heading;
                $scope.article.discription = response.data.data.description;
                $scope.img = response.data.data.file_path;
                $scope.article.articletype = response.data.data.articletype;
                $scope.article.price = response.data.data.price;
                $scope.article.status = response.data.data.status;
            }else{
                
            }
        }, function error(response) {
            //
        });
    }

    $scope.createArticle = function()
    {   
        if($state.params.id != '' || $state.params.id != undefined){
            $scope.article.id = $state.params.id;
        }
        $scope.article.post_type = 1;
        var requestPayload = {
            "data": JSON.stringify($scope.article),
            "serviceName": $dataExchanger.request.serviceName,
        };

        if($scope.img != 'Image'){
            var url = serverUrl + 'community/edit-articles';
            apiCommunityServices.addArticle(url,requestPayload).then(function (response) {
            $scope.payload = response;

                if($scope.payload.status == 'success')
                {
                    if(parseInt($scope.payload.payload.articletype) === 1) {
                        $state.go("article-list",{type:'free'});
                    } else if( parseInt($scope.payload.payload.articletype) === 2) {
                        $state.go("article-list",{type:'paid'});
                    }
                }
            });
        }else{
            var url = serverUrl + 'community/add-article';
            apiCommunityServices.addArticle(url,requestPayload).then(function (response) {
            $scope.payload = response;
            
                if($scope.payload.status == 'success')
                {
                    if(parseInt($scope.payload.payload.articletype) === 1) {
                        $state.go("article-list",{type:'free'});
                    } else if( parseInt($scope.payload.payload.articletype) === 2) {
                        $state.go("article-list",{type:'paid'});
                    }
                }
            });
        }
    }

}]);