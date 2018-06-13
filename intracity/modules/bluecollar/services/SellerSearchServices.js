app.service('SellerSearchServices', function ($q,$state,$http,$dataExchanger) {
    //var searchPagedata;
    return {
        checkSeller: function(){
          if(Auth.getUserActiveRole()!='Seller'){
            $state.go('home');
          }
        },
        getSearchPageData: function () {
            return $dataExchanger.request.BSData;
        },
        setSearchPageData: function (data) {
            $dataExchanger.request.BSData = data;
        },
        getPostCardPageData: function () {
            return $dataExchanger.request.BSData;
        },
        setPostCardPageData: function (data) {
            $dataExchanger.request.BSData = data;
        }
    }
});
