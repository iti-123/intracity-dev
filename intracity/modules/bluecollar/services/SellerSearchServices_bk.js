app.service('SellerSearchServices', function ($q, $http) {
    var searchPagedata;
    return {
        getSearchPageData: function () {
            return searchPagedata;
        },
        setSearchPageData: function (data) {
            searchPagedata = data;
            console.log(searchPagedata.location);
        }
    }
});
