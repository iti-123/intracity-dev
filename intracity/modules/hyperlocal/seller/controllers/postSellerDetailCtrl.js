app.controller('postSellerDetailCtrl', ['$scope', '$http', 'config', 'apiHyperlocalServices', 'type_basis', '$state', 'trackings','$dataExchanger', function ($scope, $http, config, apiHyperlocalServices, type_basis, $state, trackings,$dataExchanger) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.data = [];
    $scope.type_basis = type_basis.type_basis;
    $scope.post_Status = POST_STATUS;
    $scope.servicetype = SERVICE_TYPE;

    // get category
    var getCategoryUrl = serverUrl + 'hyperlocal/product-category';
    $scope.getProductCat = function (getCategoryUrl) {
        apiHyperlocalServices.category(getCategoryUrl).then(function (response) {
            $scope.categories = response.data;
            console.log('Categoriesss', $scope.categories);

        });
    }

   
    //  Get product
    $scope.getProductCat(getCategoryUrl);

	//console.log(serverUrl);
    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiHyperlocalServices.city(url).then(function (response) {
            $scope.cities = response;
            //console.log($scope.cities);
        });
    };
    //  Get city of intracity 

    $scope.getCity(url);

    // var url = serverUrl + 'hyperlocal/buyer-post-details';
    // apiHyperlocalServices.hyperbuyerPostDetail(url + '/' + $state.params.id).then(function (response) {

    //     arr = response.data;
    //     for (let a of arr) {
    //         a.multiple_location = JSON.parse(a.multiple_location);
    //     }

    //     $scope.post = arr;

    //     city_id = arr[0].get_all_route[0].city_id;
    //     ///get locality
    //     getlocationurl = serverUrl + 'locations/getlocality/' + city_id;
    //     apiHyperlocalServices.getLocationByCity(getlocationurl).then(function (response) {
    //         $scope.locations = response;
    //         console.log('Locations:', response);
    //     });

    //     //end locality


    // });

    // var buyerurl = serverUrl + 'getallbuyer';
    // apiHyperlocalServices.getallbuyer(buyerurl).then(function (response) {
    //     $scope.sellerList = response.payload;
    // });

    // $scope.deletePost = function (id) {
    //     var x = confirm("Are you sure you want to delete?");
    //     if (x) {
    //         var url = serverUrl + 'buyer-post-delete';
    //         apiHyperlocalServices.BuyerPostDelete(url, id).then(function (response) {
    //             $state.reload();

    //         });

    //     }

    //     else {

    //     }


    // };

    /*** Get All Seller List ***/
    // apiHyperlocalServices.getAllSellers(serverUrl).then(function (response) {
    //     $scope.sellerList = response;
    //     setTimeout(function () {
    //         $("#sellerList").tokenInput($scope.sellerList, {propertyToSearch: 'username'});
    //     }, 1000);

    //     //console.log("$scope.sellerList::", $scope.sellerList);
    // });
    /*** Get Location By Id ***/
    $scope.onSelect = function (data) {
        // 
        console.log(data);
    };
    $scope.data.city_id = {id: ''};
    $scope.onSelect = function (data) {
        //console.log("City Id::", parseInt(data.id));
        var city_id = parseInt(data.id);
        if (typeof(city_id) != NaN || city_id != '') {
            $scope.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id);
            // $scope.getListBuyerAccordingFilter(url, city_id);
        }
    };


    $scope.getLocationByCity = function (url) {
        apiHyperlocalServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            //console.log($scope.locations);
        });
    };
    /*** Get Location By Id ***/
    /*********************data filter from loaction******************/

    $scope.Newfromlocation = [];
    $scope.fromFilter = function (item) {

        if (item != null) {
            if ($scope.Newfromlocation.length == 0)
                return item;
            else {
                return $scope.Newfromlocation.indexOf(item.from_location) !== -1;
            }
        }

    };
    $scope.selectfromlocation = function (operator) {


        var idx = $scope.Newfromlocation.indexOf(operator);
        if (idx > -1)
            $scope.Newfromlocation.splice(idx, 1);
        else

            $scope.Newfromlocation.push(operator);
        if ($scope != null)
            $scope.Newfromlocation = $scope.Newfromlocation;

    };
    /*********************data filter to loaction******************/

    $scope.Newtolocation = [];
    $scope.toFilter = function (item) {
        // console.log(item.vehicle);
        if (item != null) {
            if ($scope.Newtolocation.length == 0)
                return item;
            else {
                return $scope.Newtolocation.indexOf(item.to_location) !== -1;
            }
        }

    };
    $scope.selectlocationto = function (operator) {
        var idx = $scope.Newtolocation.indexOf(operator);
        if (idx > -1)
            $scope.Newtolocation.splice(idx, 1);
        else

            $scope.Newtolocation.push(operator);
        if ($scope != null)
            $scope.Newtolocation = $scope.Newtolocation;

    };

    $scope.NewServiceType = [];
    $scope.NewServiceTypeFilter = function (item) {
        if (item != null) {
            if ($scope.NewServiceType.length == 0)
                return item;
            else {

                return $scope.NewServiceType.indexOf(item.service_type) !== -1;
            }
        }

    };
    $scope.selectNewServiceType = function (operator) {
        alert(operator);
        var num = operator.toString();
        var idx = $scope.NewServiceType.indexOf(num);
        if (idx > -1)
            $scope.NewServiceType.splice(idx, 1);
        else
            $scope.NewServiceType.push(num);
        if ($scope != null)
            $scope.NewServiceType = $scope.NewServiceType;

    };


    $scope.NewSellerName = [];
    $scope.NewSellerNameFilter = function (item) {


        if (item != null) {
            if ($scope.NewSellerName.length == 0)
                return item;
            else {
                return $scope.NewSellerName.indexOf(item.seller.toString()) !== -1;
            }
        }

    };
    $scope.selectNewSellerName = function (operator) {
        var num = operator.toString();
        var idx = $scope.NewSellerName.indexOf(num);
        if (idx > -1)
            $scope.NewSellerName.splice(idx, 1);
        else
            $scope.NewSellerName.push(num);
        if ($scope != null)
            $scope.NewSellerName = $scope.NewSellerName;

    };

    $scope.deletePost = function (id) {
        var x = confirm("Are you sure you want to delete?");
        if (x) {
            var url = serverUrl + 'hyperlocal/buyer-post-delete';
            apiHyperlocalServices.sellerPostDelete(url, id).then(function (response) {
                $state.reload();

            });

        }

        else {

        }


    }

    // var getPostDetailsUrl = serverUrl + 'hyperlocal/seller-post-details/1';
    // $scope.getProductCat = function (getCategoryUrl) {
    //     apiHyperlocalServices.category(getCategoryUrl).then(function (response) {
    //         $scope.categories = response.data;

    //     });
    // }
    // //  Get product
    // $scope.getProductCat(getCategoryUrl);

    $scope.date = $dataExchanger.request.data ;


    $scope.postdetails = function () {

        var url = serverUrl + 'hyperlocal/seller-post-details';
        apiHyperlocalServices.sellerPostDetail(url + '/' + $state.params.id).then(function (response) {
            $scope.posts = response;
            $scope.postDetails = response.payload;
           // console.log('POSSTTTT DEETAILSSS:',$scope.postDetails);
        });
    };
    $scope.postdetails();

}]);
