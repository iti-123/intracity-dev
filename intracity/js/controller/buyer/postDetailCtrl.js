app.controller('postDetailCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', 'trackings','$dataExchanger', function ($scope, $http, config, apiServices, type_basis, $state, trackings,$dataExchanger) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.data = [];
    $scope.lowestRoutes=lowestRoutes;
    $scope.type_basis = type_basis.type_basis;
    $scope.post_Status = POST_STATUS;
    $scope.trackings = trackings.type;

    $scope.storagePath = TERMFILE_PATH;
    var transitHour = [];
    $scope.transitHour = TRANSIT_HOUR;
    var i = 0;
    for (var property in TRANSIT_HOUR) {


        transitHour[i] = {"id": property, "value": property};
        i++;
    }
    $scope.TRANSIT_HOUR = transitHour;
    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
            //console.log($scope.cities);
        });
    };
    //  Get city of intracity 

    $scope.getCity(url);

    $scope.filterData = {
        fromLocation: [],
        toLocation: [],
        postType: [],
        postStatus:[],
        vehicleType: [],
        orderDate: [],
        orderNumber: [],
        sellerType: [],
        fromDate:[],
        toDate:[],
        type: '',
        postId: '',
        isInbound: 'outbound',//default
        serviceId: 3,
        offset:0,
        totalRow:10
    };


    $scope.addFilterData = function (arr, value) {
       // alert(value);
        // console.log(arr);
        var index = arr.indexOf(value);
        if (index == -1) {
            arr.push(value);
        } else {
            arr.splice(index, 1);
        }

        // console.log($scope.filterData);
    };

    $scope.showHideDetail = function (key) {
      $(".toggle-minus-" + key).toggleClass("detail-minus");
      $("#detail-tabel-data" + key).slideToggle();
      $("#detail-tabel-data-row" + key).slideToggle();
      $("#detail-tabel-data-rows" + key).slideToggle();
    };
    
    $scope.showquoterow=function(key,rowkey){
     $('.genratecontractrow').css("display", "none"); 
      if(key==rowkey.id)
      {
        $('#genratecontractrow'+key).css("display", "table-row");
        $('#genratecontractrow2'+key).css("display", "table-row");
      }
    }

    $scope.showcontract=function(key){
     $("#contract_title_"+key).css("display", "block");
     $("#generate_data"+key).css("display", "block");
     $("#contract_files"+key).css("display", "block");
     $("#Contract").css("display", "none");
    }
   
    $scope.formError = {
        contract: ''
    }
    
    $scope.GenerateContract=function(quotedata,quotevalue,key){
        console.log('Quote',quotevalue);
        if(!quotevalue.contract.documentfile){
            $scope.formError.contract = true;
            return false;
        }else{
            $scope.formError.contract = false;
        }  

        $scope.Termdata = {
            intra_hp_post_quotations_id:quotevalue.id,
            route_id:quotevalue.route_id,
            post_id:quotevalue.post_id,
            contract_title:quotevalue.contract.contract_title,
            contract_quantity:quotevalue.contract.contract_quantity,
            contract_price:quotevalue.contract.contract_price,
            lkp_service_id:quotevalue.lkp_service_id,
            term_buyer_quote_id:quotevalue.post_id,
            term_buyer_quote_item_id:quotevalue.route_id,
            seller_id:quotevalue.seller_id,
            uploaddocument:quotevalue.contract.documentfile ? quotevalue.contract.documentfile:'',   
        }
        console.log('Term Contract',$scope.Termdata);
        //return false;
        
        $scope.Termdata.uploaddocument.type='contractdocument';
         var url = serverUrl + 'hyperlocal/buyer-post-term-contract';
         apiServices.Termcontract(url, JSON.stringify($scope.Termdata)).then(function (response) {
            $scope.response=response.payload;
            $scope.contract_no=response.payload.contract_no;

            if(response.isSuccessfull){
                str = "Your contract has been genrated successfully.Contract No is " + $scope.contract_no + " ";
                $("#responsetext").html(str);
                $(".waitText button").attr("data-type", "IntracitybuyerDetails");
                $('#buyerDetailModal').modal({ 'show': true });
            }else{
        
            }
            
         });
    }
    
    $scope.cancelcontract = function(quotevalue){
      $scope.quoteupdate = {
        buyer_id:quotevalue.buyer_id,
        id:quotevalue.id,
      }
    
      var url = serverUrl + 'hyperlocal/cancel-quote-status';
      apiServices.cancelcontract(url, JSON.stringify($scope.quoteupdate)).then(function (response) {
         console.log('respone',response);
         $scope.posts.quotes[0].status = response.payload.status;
         
      });
    }

    $scope.closeModel = function() {
        $('#buyerDetailModal').modal({ 'show': false });
        setTimeout(function() {
            $state.reload();
        },2000);
    }

    $scope.rowenable=function(key){
      $('input[type="checkbox"]').not('#chk'+key).prop("checked", false);
      $('#contract_qty_'+key).prop('disabled', true);
      $('#contract_rate_'+key).prop('disabled', true);
      if($('#chk'+key).prop('checked')){
      $('#contract_rate_'+key).prop('disabled', false);
      $('#contract_qty_'+key).prop('disabled', false);
      $('#tr_row'+key).prop('disabled', false);
      }
   }

    $scope.postdetails = function () {
        $scope.filterData.postId = $state.params.id;
        console.log("$state.params",$state.params);

        var url = serverUrl + 'buyer-post-details';
        apiServices.searchFilter(serverUrl + 'get-post-details', $scope.filterData).then(function (response) {
            $scope.posts = response.payload[0];
            $scope.post = response.payload;
            console.log('BUYER POST DETAILSSSSSSSSSSS',response.payload);
            console.log('$scope.post',$scope.post);
        });
    };
    $scope.postdetails();

    $http({
        method: 'POST',
        url: serverUrl+'intra-private-post',
        dataType: 'json',
        data: {'id':$state.params.id},
        headers: {
          'authorization': 'Bearer ' + localStorage.getItem("access_token")
        }
    }).then(function success(response){
        if(response.data.success){
          $scope.privateSellers = response.data.data;
        }
    }, function error(response){
        //
    });


    var buyerurl = serverUrl + 'getallbuyer';
    apiServices.getallbuyer(buyerurl).then(function (response) {
        $scope.sellerList = response.payload;
    });

    /*** Get Vehicle Type ***/
    var url = serverUrl + 'locations/getVehiletype';
    $scope.getVehicleType = function (url) {
        apiServices.vehiclesType(url).then(function (response) {
            $scope.vehicles = response;
            //console.log($scope.vehicles);
        });
    };
    $scope.getVehicleType(url);

    $scope.deletePost = function (id) {
        var x = confirm("Are you sure you want to delete?");
        if (x) {
            var url = serverUrl + 'buyer-post-delete';
            apiServices.BuyerPostDelete(url, id).then(function (response) {
                $state.reload();

            });

        }

        else {

        }


    };

    /*** Get All Seller List ***/
    apiServices.getAllSellers(serverUrl).then(function (response) {
        $scope.sellerList = response;
        setTimeout(function () {
            $("#sellerList").tokenInput($scope.sellerList, {propertyToSearch: 'username'});
        }, 1000);

        //console.log("$scope.sellerList::", $scope.sellerList);
    });
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
        apiServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            //console.log($scope.locations);
        });
    };
    /*** Get Location By Id ***/
 
 

    $scope.displayFirmQuoteAction = function(index, action) {
        $("#quote-action-"+index).toggle(1000);
    }

    $scope.bookNowForm = function(value,index) {
        console.log("Quotes::",value);
        $scope.onSelect(value.city);
        $("#quote-locality-"+index).toggle(1000);

    }

    $scope.onSelect = function (data) {
        var city_id;
        console.log('location', data);
        var city_id = parseInt(data.id);
        console.log("OK",isNaN(city_id));
        if (!isNaN(city_id)) {
            url = serverUrl + 'locations/getlocality/' + city_id;
            apiServices.getLocationByCity(url).then(function (response) {
                $scope.locations = response;

            });
        }
    };


    $scope.bookNow = function (value, index) {
        console.log("Quotes::",value);
        var quotedBySeller = value.quotes[index];
        value.finalPrice = quotedBySeller.initial_quote_price;
        var confirm1 = true;
        if (confirm1) {
            var booknowSerachObj = $dataExchanger.request;
            apiServices.getTodayDate().then(response => {
                booknowSerachObj.data.dispatchDate = response;
            });
            $dataExchanger.request.data.fromLocation = quotedBySeller.from_location;
            $dataExchanger.request.data.toLocation = quotedBySeller.to_location;
            $dataExchanger.request.data.city = value.city;
            $scope.bookPostObj = {
                initialDetails: {
                    serviceId: $dataExchanger.request.serviceId,
                    serviceType: '',
                    sellerId: quotedBySeller.seller_id,
                    buyerId: Auth.getUserID(),
                    postType: "BP",
                    sellerQuoteId: value.id, //need to discuss
                    buyerPostId:  value.id,
                    searchData: booknowSerachObj,
                    carrierIndex: index,
                    quote: value
                }
            };
            value.id = value.enc_id;
            
            apiServices.bookNow(serverUrl, JSON.stringify($scope.bookPostObj)).then(function (response) {
               
                if (response.isSuccessful && response.payload.id) {
                    $scope.cartId = response.payload.enc_id;
                    // debugger;   
                    $state.go('order-booknow', {serviceId: 3, cartId: $scope.cartId});
                } else if(response.isCartValue) { //if(response.isCartValue)
                    console.log(response);
                    $("#cartModal").modal('show');
                }
            })
        }
    };


    $scope.buyerQuoteAction = function(index, action) {

        console.log($scope.posts.quotes[index]);
        $scope.posts.quotes[index].action = action;

        let url = serverUrl+'negotation/buyerQuoteAction';
        apiServices.postMethod(url,$scope.posts.quotes[index]).then(response => {
            console.log(response);
            location.reload();
        }).catch();
    }


    $scope.validateQuote = function(index) {
        if($scope.posts[index].action == 'COUNTER_BY_BUYER') {
            if ($scope.posts[index].buyerPrice == '') {
                $scope.posts[index].buyerPriceError = 'Enter counter price ';
            } else {
                $scope.posts[index].buyerPriceError = '';
            }
        }
    }

    $scope.viewDetails = function(index) {
        $("#quote-detail-"+index).toggle(); 
        $scope.posts.quotes[index].isActive = !$scope.posts.quotes[index].isActive; 
              
    }

    /* $scope.viewDetails  = function(index)
    { 
        $scope.posts.quotes[index].isActive = !$scope.posts.quotes[index].isActive;
    }*/



    /*********************data filter base on vechile ******************/

    $scope.NewVehicleType = [];
    $scope.NewVehicleTypeFilter = function (item) {

        if (item != null) {
            if ($scope.NewVehicleType.length == 0)
                return item;
            else {
                return $scope.NewVehicleType.indexOf(item.vehicle) !== -1;
            }
        }

    };
    $scope.selectNewVehicleType = function (operator, obj) {

        alert(obj);

        if ($scope.filterdata.indexOf(obj) !== -1) {

            $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1)
        }
        else {
            $scope.filterdata.push(obj);

        }

        var idx = $scope.NewVehicleType.indexOf(operator);
        if (idx > -1)
            $scope.NewVehicleType.splice(idx, 1);
        else

            $scope.NewVehicleType.push(operator);
        if ($scope != null)
            $scope.NewVehicleType = $scope.NewVehicleType;

    };


}]);
