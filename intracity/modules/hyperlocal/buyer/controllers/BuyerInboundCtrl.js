app.controller('BuyerInboundCtrl', ['$scope', '$http', 'config', 'trackings', 'apiServices', 'apiHyperlocalServices','$compile', '$state', '$stateParams', '$dataExchanger', function ($scope, $http, config, trackings, apiServices,apiHyperlocalServices ,$compile, $state, $stateParams, $dataExchanger) {

    var transitHour = [];
    var serverUrl = config.serverUrl;
    $scope.filterdata = [];
    $scope.servicetype = SERVICE_TYPE;
    $scope.materialtype = HYPERLOCAL_MATERIAL_TYPE;
    $scope.weight = HYPERLOCAL_WEIGHT;
    var url = serverUrl + 'hyperlocal/product-category';
    $scope.getProductCategory = function (url) {
        apiServices.category(url).then(function (response) {
            $scope.categories = response.data;
            //console.log('Product Category:', $scope.categories);
        });
    }
    $scope.getProductCategory(url);

    /**
     *      Check if data exist or not
     */

    if (angular.equals($scope.buyerSearch, {})) { $state.go("hp-search-buyer"); }

    /**********************************************************
     *
     *        Get city
     *
     *************************************************************/
    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
        });
    }
    $scope.getCity(url);



    /*** Get Location By Id ***/
    $scope.onSelect = function (data) {
        // 
        console.log(data);
    }
    // $scope.data.city_id = { id: '' };
    $scope.onSelect = function (data) {
        console.log("City Id::", parseInt(data.id));
        var city_id = parseInt(data.id);
        if (typeof (city_id) != NaN || city_id != '') {
            $scope.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id);
            // $scope.getListBuyerAccordingFilter(url, city_id);
        }
    }



    $scope.getLocationByCity = function (url) {
        apiServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            //console.log('Locations:', $scope.locations);
        });
    }
    

    /*------------------------Get All Sellers--------------------------------*/
    var buyerurl = serverUrl + 'getallbuyer';
    apiServices.getallbuyer(buyerurl).then(function (response) {
        $scope.sellerList = response.payload;
    });

  
   

    $scope.showHideDetail = function ($index) {

        $(".toggle-minus-" + $index).toggleClass("detail-minus");
        $("#detail-" + $index).slideToggle();
    };
    $scope.params=$state.params.id;
   $scope.filterData = {
        category: [],
        service: [],
        postStatus: [],       
        date: [],
        sellerName: [],
        serviceId: _HYPERLOCAL_,
        is_private_public:0,
        title:$scope.params,
        offset:0,
        totalRow:10,
       
       
    };
   // console.log('xxxxx',$scope.filterData);
    /*********inbound detials ********************/

       
        $scope.inboundResult = function () {        
            var url = serverUrl + 'hyperlocal/hp-get-buyer-inbound-details';
            apiHyperlocalServices.BuyerInboundDetails(url, $scope.filterData).then(function (response) {
                $scope.filterData.totalRow = response.data.length;
                console.log('row',response);
                $scope.listdata = response.data;
                for (let key in $scope.listdata) {
                    $scope.listdata[key].title=$scope.params.replace(/-/g, ' ');                
                }            
            })
        }

        $scope.$watch('filterData', function (newValue, oldValue) {      
            $scope.inboundResult();   
        }, true);

    
      $(window).scroll(function() {    
            $scope.filterData.offset = 10;   
            if ($(window).scrollTop() == $(document).height() - $(window).height()) {
                $(".page-data-loader").fadeIn();       
                $scope.inboundResult(); 
            }
        });
    
     



   $scope.addFilterData = function (arr, value) {
        var index = arr.indexOf(value);
        if (index == -1) {
            arr.push(value);
        } else {
            arr.splice(index, 1);
        }

       console.log($scope.filterData);
    };
    




}]);

