app.controller('BuyerListCtrl', ['$scope','$http','$location','config','apiServices','type_basis','$state', '$dataExchanger','apiHyperlocalServices','$window', function ($scope, $http,$location, config, apiServices, type_basis, $state, $dataExchanger,apiHyperlocalServices,$window) {
    
    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.data = [];
    $scope.type_basis = type_basis.type_basis;
    $scope.post_Status = POST_STATUS;
    $scope.servicetype = SERVICE_TYPE;
    $scope.weight = HYPERLOCAL_WEIGHT;
    $scope.filClearValue = false;

    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
            console.log($scope.cities);
        });
    };

    $scope.getCity(url);
// get category
    var getCategoryUrl = serverUrl + 'hyperlocal/product-category';
    $scope.getProductCat = function (getCategoryUrl) {
        apiServices.productCategory(getCategoryUrl).then(function (response) {
            $scope.categories = response.data;
            console.log('Product Category:', $scope.categories);
        });
    };
    //  Get product
    $scope.getProductCat(getCategoryUrl);

    $scope.postDraft = $window.sessionStorage.getItem('postDraft');
    setTimeout(function () {
      $scope.$apply(function() {
        $scope.postDraft = false;
        sessionStorage.removeItem('postDraft');
       });
    },8000);
    //count public private post/
    var url = serverUrl + 'hyperlocal/get-post-count';

    apiServices.hyperpostcount(url).then(function (response) {
        $scope.postrecord = response.payload;
        console.log('count', $scope.postrecord);
    });

    $scope.clearAll = function () {
        $scope.filClearValue = false;
        $scope.filterData = {
            category: [],
            service: [],  
            lastDate: '',
            sellerName: [],
            serviceId: _HYPERLOCAL_,
            is_private_public:0,
            type:'all',
            pageLoader : 5,
            pageNextValueCount : 1
        };
    }

    $scope.filterData = {
        category: [],
        service: [],  
        lastDate: '',
        sellerName: [],
        serviceId: _HYPERLOCAL_,
        is_private_public:0,
        type:'all',
        pageLoader : 5,
        pageNextValueCount : 1
    };

    $scope.addFilterData = function (arr,value) {
        $scope.filClearValue = true;
        var index = arr.indexOf(value);
        if (index == -1) {
            arr.push(value);
        } else {
           arr.splice(index,1);
           if($scope.filterData.category.length < 1 && $scope.filterData.service.length < 1 && $scope.filterData.lastDate == '' 
            && $scope.filterData.sellerName.length < 1){
                $scope.filClearValue = false;
           }
        }
        console.log('Filter Datas',$scope.filterData);
    }

    $scope.checkedFilterData = function (arr, value) {
        var index = arr.indexOf(value);
        if (index == -1) {
            return false;
        }
        return true;
    }

    $scope.removeElem = function (arr, index) {
        arr.splice(index, 1);
        if($scope.filterData.category.length < 1 && $scope.filterData.service.length < 1 && $scope.filterData.lastDate == '' 
            && $scope.filterData.sellerName.length < 1){
            $scope.filClearValue = false;
        }
    };

    /*** Get Filtered Record ***/
    $scope.search_filter = function (type) {
        $scope.show = '';
        $scope.filterData.pageLoader = 5;
        $scope.filterData.pageNextValueCount = 1;
        if(type == 0){
           $scope.filterData.type = 'public';
        } else if(type == 1){
           $scope.filterData.type = 'private';
        } else if(type == 'spot'){
          $scope.filterData.type = 'spot';  
        } else if(type == 'term'){
           $scope.filterData.type = 'term'; 
        } else if(type == 'all'){
           $scope.filterData.type = 'all'; 
        }
    };

    $scope.search_filter('all');

    $scope.search_filters = function (type) {
        $scope.show = '';
        if(type == 0){
           $scope.filterData.type = 'public';
        } else if(type == 1){
           $scope.filterData.type = 'private';
        } else if(type == 'spot'){
          $scope.filterData.type = 'spot';  
        } else if(type == 'term'){
           $scope.filterData.type = 'term'; 
        } else if(type == 'all'){
           $scope.filterData.type = 'all'; 
        }
    };

    $(window).scroll(function () {
       if ($(window).scrollTop()) {
           $scope.filterData.pageLoader = $scope.filterData.pageLoader + $scope.filterData.pageNextValueCount;
           $scope.search_filters($scope.filterData.type);
           $scope.$apply();
       }
    });

    $scope.$watch('filterData', function (newValue, oldValue) {
        $http({
            url: serverUrl + 'hyperlocal/hp-get-all-records',
            method: 'POST',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: $scope.filterData,
        }).then(function success(response) {
            if (response.data.isSuccessful) {
                $scope.spotsList = response.data.data.data;
            }
            console.log('Spots List',response.data);
        }, function error(response) {
            $scope.spinnerOperator = false;

        });
    }, true);

    $scope.getUsername = function (id) {
      for (let v of $scope.sellerList) {
            if (v.id == id) {
                return v.username;
            }
        }
    }

    $scope.getCategory = function (id) {
      for (let v of $scope.categories) {
            if (v.id == id) {
                return v.name;
            }
        }
    }

    $scope.getServiceType = function (id) {
      for (let v of $scope.servicetype) {
            if (v.id == id) {
                return v.value;
            }
        }
    }

    $scope.search_filter1 = function (types) {
         
            switch(types) {
                case 0:
                    $scope.filterData.type='public';
                    break;
                case 1:
                    $scope.filterData.type='private';
                    break;
                case 'spot':
                    $scope.filterData.type='spot';
                    break;
                case 'term':
                    $scope.filterData.type='term';
                    break;
                default:
                        $scope.filterData.type='all';
            }
       
        console.log("$scope.filterData::",$scope.filterData );
            var url = serverUrl + 'hyperlocal/hp-get-buyer-inbound';
            // $scope.filterType = 'all';
            apiHyperlocalServices.BuyerInboundFilter(url, $scope.filterData).then(function (response) {
            $scope.inboundlist = response.data;

          
            $scope.inboundcount=0;
           
            for (i = 0; i<response.data.length; i++) {
                $scope.inboundcount+=response.data[i].data.length;
               } 
               
           })

            var url = serverUrl + 'hyperlocal/hp-get-all-records';
            apiServices.searchFilter(url, $scope.filterData).then(function (response) {
                $scope.spotsList = response.payload;
                $scope.changeClass(types);
                $scope.filterData.totalRow = response.payload.length
                $(".page-data-loader").fadeOut(1000);
            });

         
    
    };
    
    $scope.goToLink = function(value) {
        if(value.post_status == 1){
            $location.path('/hyperlocal-buyer-post-Details/' + value.enc_id);
        }else if(value.post_status == 0){
            $location.path('/hp-buyer-post-draft/' + value.enc_id);
        }
    };
  
    $scope.setBound = function(type)
    {
        $scope.filterData.isBound = type;
        $scope.filterType=type;

         $('.Inbound,.ountbound').removeClass('search_nav_active');
        $('.'+type).addClass("search_nav_active");
         
    }


    $scope.totalLink=function(data)
   {
       $state.go("hyperlocal-buyer-post-Details",{id:data})
      
   }


     /************************For Active Class On Click Filters************/
    
    $scope.changeClass = function (types) {

        $('.all,.spot,.term,.0,.1').removeClass('search_nav_active');
         
        $('.'+types).addClass("search_nav_active");
       
    };
  
    
    /************************For Active Class On Click Filters************/


    $scope.getLocationByCity = function (url) {
        apiServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            console.log('Locations:', $scope.locations);
        });
    };
    /*** Get Location By Id ***/


    /*** Get All Seller List ***/
    apiServices.getAllSellers(serverUrl).then(function (response) {
        $scope.sellerList = response;
        setTimeout(function () {
            $("#sellerList").tokenInput($scope.sellerList, {propertyToSearch: 'username'});
        }, 1000);

        console.log("$scope.sellerList::", $scope.sellerList);
    });
    /*** Get All Seller List ***/


    /*** Get Total Numbers of Buyer Post Spots ***/
    var url = serverUrl + 'count-buyerpost-spots';
    $scope.getTotalBuyerPost = function (url) {
        apiServices.totalBuyerPost(url).then(function (response) {
            $scope.postCount = response;
        });
    };
    $scope.getTotalBuyerPost(url);
    /*** Get Total Numbers of Buyer Post Spots ***/

    /*** Get List of Buyer Post Spots ***/
    var url = serverUrl + 'buyer-post-list';
    $scope.getListBuyerPost = function (url) {
        apiServices.listBuyerPost(url).then(function (response) {
            $scope.spotsList = response;
            console.log("Post List ::", $scope.spotsList);
        });
    };
    // $scope.getListBuyerPost(url);
    /*** Get List of Buyer Post Spots ***/


    /*** Get List of Buyer Post According Filters ***/
    var url = serverUrl + 'buyer-listing-according-filters';
    $scope.getListBuyerAccordingFilter = function (url, city_id) {
        apiServices.listBuyerAccordingFilter(url, city_id).then(function (response) {
            $scope.spotsList = response;
            console.log("Post List ::", $scope.spotsList);
        });
    };

    /*** Get List of Buyer Post According Filters ***/
    /************************filter for post Status**********/
    $scope.NewPostStatus = [];
    $scope.NewPostStatusFilter = function (item) {
        if (item != null) {
            if ($scope.NewPostStatus.length == 0)
                return item.post;
            else {
                return $scope.NewPostStatus.indexOf(item.post.post_status.toString()) !== -1;
            }
        }

    };
    // 3
    $scope.postStatus = function (operator) {

        var num = operator.toString();
        var idx = $scope.NewPostStatus.indexOf(num);
        if (idx > -1)
            $scope.NewPostStatus.splice(idx, 1);
        else
            $scope.NewPostStatus.push(num);
        if ($scope != null)
            $scope.NewPostStatus = $scope.NewPostStatus;

    };


    /************************filter for post type city**********/
    $scope.Newcity = [];
    $scope.NewCityFilter = function (item) {

        if (item != null) {
            if ($scope.Newcity.length == 0)
                return item;
            else {

                return $scope.Newcity.indexOf(item.city_name) !== -1;
            }
        }

    };
    $scope.selectCity = function (operator) {

        var idx = $scope.Newcity.indexOf(operator);
        console.log('indexof', idx);
        if (idx > -1)
            $scope.Newcity.splice(idx, 1);
        else
            $scope.Newcity.push(operator);
        if ($scope != null)
            $scope.Newcity = $scope.Newcity;

    };
    /************************filter for post type city**********/


    /************************filter for post seller name**********/
    $scope.NewSellerName = [];
    $scope.NewSellerNameFilter = function (item) {


        if (item.post != null) {
            if ($scope.NewSellerName.length == 0)
                return item.post;
            else {
                return $scope.NewSellerName.indexOf(item.post.posted_by) !== -1;
            }
        }

    };
    console.log('filterggggggggg',$scope.NewSellerName);
    $scope.selectNewSellerName = function (operator) {
        operator=operator.toString();
        var idx = $scope.NewSellerName.indexOf(operator);
        if (idx > -1)
            $scope.NewSellerName.splice(idx, 1);
        else
            $scope.NewSellerName.push(operator);
        if ($scope != null)
            $scope.NewSellerName = $scope.NewSellerName;

    };
    /************************filter for post seller name**********/

    /************************filter for post category**********/

    $scope.NewPostCategory = [];
    $scope.NewCategoryFilter = function (item) {

        if (item.post != null) {
            if ($scope.NewPostCategory.length == 0)
                return item.post;
            else {
                return $scope.NewPostCategory.indexOf(item.post.category.toString()) !== -1;
            }
        }

    };
    // Buyer List Ctrl Clear All
    $scope.BuyerListCtrlClearAll = function(){
        console.log($scope.data);
        $('.fillter_div .filter_from_location').find('input[type=checkbox]').prop('checked', false);
        $scope.NewPostCategory.length = 0;
        $scope.NewPostService.length = 0;
        $scope.NewPostStatus.length = 0;
        $scope.NewSellerName.length = 0;
        
        //$scope.data.dispatchdate = '';
        $scope.data.name = '';
        $scope.data.post_type = '';
        $scope.data.vehicle_type = '';
    }
    
    // 1 
    $scope.selectNewCategory = function (operator) {

        var num = operator.toString();
        var idx = $scope.NewPostCategory.indexOf(num);
        if (idx > -1)
            $scope.NewPostCategory.splice(idx, 1);
        else
            $scope.NewPostCategory.push(num);
        if ($scope != null)
            $scope.NewPostCategory = $scope.NewPostCategory;

    };
    /**********filter service type*********************************/
    $scope.NewPostService = [];
    $scope.NewserviceFilter = function (item) {

        if (item.post != null) {
            if ($scope.NewPostService.length == 0)
                return item.post;
            else {
                return $scope.NewPostService.indexOf(item.post.servicetype.toString()) !== -1;
            }
        }

    };
    // 2
    $scope.selectNewService = function (operator) {

        var num = operator.toString();
        var idx = $scope.NewPostService.indexOf(num);
        if (idx > -1)
            $scope.NewPostService.splice(idx, 1);
        else
            $scope.NewPostService.push(num);
        if ($scope != null)
            $scope.NewPostService = $scope.NewPostService;

    };

    /*****************service filter end here*********************/
    /**************date filter*******************************/




    $scope.NewFromLocation = [];
    $scope.NewFromLocationFilter = function (item) {
        if (item != null) {
            if ($scope.NewFromLocation.length == 0)
                return item;
            else {
                return $scope.NewFromLocation.indexOf(item.from_location) !== -1;
            }
        }

    };
    $scope.selectNewFromLocation = function (operator) {
        var num = operator.toString();
        var idx = $scope.NewFromLocation.indexOf(num);
        if (idx > -1)
            $scope.NewFromLocation.splice(idx, 1);
        else
            $scope.NewFromLocation.push(num);
        if ($scope != null)
            $scope.NewFromLocation = $scope.NewFromLocation;

    };
    /************************filter for From Location**********/

    /************************filter for To Location**********/

    $scope.NewToLocation = [];
    $scope.NewToLocationFilter = function (item) {
        if (item != null) {
            if ($scope.NewToLocation.length == 0)
                return item;
            else {
                return $scope.NewToLocation.indexOf(item.to_location) !== -1;
            }
        }

    };
    $scope.selectNewToLocation = function (operator) {
        var num = operator.toString();
        var idx = $scope.NewToLocation.indexOf(num);
        if (idx > -1)
            $scope.NewToLocation.splice(idx, 1);
        else
            $scope.NewToLocation.push(num);
        if ($scope != null)
            $scope.NewToLocation = $scope.NewToLocation;

    };
    
    // $scope.$watch('filterData', function (newValue, oldValue) {
    //     // $('#loaderGif').show();
    //     $http({
    //         url: serverUrl + 'hyperlocal/hp-get-buyer-inbound',
    //         method: 'POST',
    //         headers: {
    //             'authorization': 'Bearer ' + localStorage.getItem("access_token")
    //         },
    //         data: $scope.filterData,
    //     }).then(function success(response) { 

    //         // console.log('Ajax',response.data.data);  
    //         $scope.inboundlist = response.data.data;  
            
    //         // $scope.postDraft =  +sessionStorage.getItem("post-status");
    //         // if (!$scope.postDraft) {
    //         //     setTimeout(function () { 
    //         //         console.log('POST STATUSS IN setTimeout', $scope.postDraft);
    //         //        sessionStorage.setItem("post-status", 1);      
    //         //     }, 1000);
    //         // }

    //     }).catch(function(error) {
    //         apiServices.errorHandeler(error);    
    //         $('#loaderGif').hide();        
    //     });
    // }, true);

     $scope.showHideDetail = function ($index) {
       
        $(".toggle-minus-" + $index).toggleClass("detail-minus");
        $("#detail-" + $index).slideToggle();
    };

}]);