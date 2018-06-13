app.service('BuyerSearchServices', function ($q, $state, $http, $dataExchanger, config) {
    // var searchPagedata;
    var serverUrl = config.serverUrl;
    var postId = 0;
    return {
        checkBuyer: function(){
          if(Auth.getUserActiveRole()!='Buyer'){
            $state.go('home');
          }
        },
        getSearchPageData: function () {
            return $dataExchanger.request.BSData;
        },
        setSearchPageData: function (data) {
            $dataExchanger.request.BSData = data;
        },
        getBlueCollarData: function () {
            return $dataExchanger.request.BCServiceData;
        },
        setBlueCollarData: function (data) {
            $dataExchanger.request.BCServiceData = data;
        },
        getPostId: function () {
            return postId;
        },
        setPostId: function (data) {
            postId = data;
        },
        getPostCardPageData: function () {
            return $dataExchanger.request.PCData;
        },
        setPostCardPageData: function (data) {
            $dataExchanger.request.PCData = data;
        },
        suggestLocation: function (location) {
            //$scope.searchResults = [{'cur_state_or_city': 'PUNED'}, {'cur_state_or_city': 'hydra'}];
            var deferred = $q.defer();
            $http({
                method: 'POST',
                url: serverUrl + 'bluecollar/buyer-location-search',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: {'search': location},
            }).then(function (response) {
                //console.log(httpResponse.data.data);
                deferred.resolve(response.data.data);
            }, function (response) {
                deferred.reject(response);
            });
            return deferred.promise;
        }
    }
});
