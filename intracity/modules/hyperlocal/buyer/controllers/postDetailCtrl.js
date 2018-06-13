app.controller('postDetailCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', 'trackings','$dataExchanger', function ($scope, $http, config, apiServices, type_basis, $state, trackings,$dataExchanger) {
   
    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.data = [];
    $scope.type_basis = type_basis.type_basis;
    $scope.post_Status = POST_STATUS;
    $scope.servicetype = SERVICE_TYPE;
    $scope.servicetype = SERVICE_TYPE;
    $scope.lowestRoutes=lowestRoutes;
    //console.log('lllllllllllllllllll',$scope.lowestRoutes);

    // get category
    var getCategoryUrl = serverUrl + 'hyperlocal/product-category';
    $scope.getProductCat = function (getCategoryUrl) {
        apiServices.productCategory(getCategoryUrl).then(function (response) {
            $scope.categories = response.data;

        });
    }
    //  Get product
    $scope.getProductCat(getCategoryUrl);


    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
            //console.log($scope.cities);
        });
    };
    //  Get city of intracity 

    $scope.getCity(url);

    var url = serverUrl + 'hyperlocal/buyer-post-details';
    apiServices.hyperbuyerPostDetail(url + '/' + $state.params.id).then(function (response) {

        arr = response.data;
        for (let a of arr) {
            a.multiple_location = JSON.parse(a.multiple_location);
        }

        $scope.post = arr;
        console.log('POSSTT DETAILSS', $scope.post);
         //console.log(arr);
        //city_id = arr[0].get_all_route[0].city_id;
        ///get locality
        // getlocationurl = serverUrl + 'locations/getlocality/' + city_id;
        // apiServices.getLocationByCity(getlocationurl).then(function (response) {
        //     $scope.locations = response;
        //     console.log('Locations:', response);
        // });

        //end locality


    });

    var buyerurl = serverUrl + 'getallbuyer';
    apiServices.getallbuyer(buyerurl).then(function (response) {
        $scope.sellerList = response.payload;
    });

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
     /*********************data filter status******************/

    $scope.Newstatus= [];
    $scope.statusFilter = function (item) {
       //console.log('dada',item)

        if (item != null) {
            if ($scope.Newstatus.length == 0)
                return item;
            else {
                return $scope.Newstatus.indexOf(item.post_status) !== -1;
            }
        }

    };
    $scope.postStatus = function (operator) {

        var idx = $scope.Newstatus.indexOf(operator);
        if (idx > -1)
            $scope.Newstatus.splice(idx, 1);
        else

            $scope.Newstatus.push(operator);
        if ($scope != null)
            $scope.Newstatus = $scope.Newstatus;

    };
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
            apiServices.sellerPostDelete(url, id).then(function (response) {
                $state.reload();

            });

        }

        else {

        }


    }

    // get category
    var getCategoryUrl = serverUrl + 'hyperlocal/product-category';
    $scope.getProductCat = function (getCategoryUrl) {
        apiServices.productCategory(getCategoryUrl).then(function (response) {
            $scope.categories = response.data;
           // console.log('Product Category:', $scope.categories);
        });
    };

/// post root with quote///
  $scope.filterData = {
        type:'',
        isBound: 'all',
        serviceId: _HYPERLOCAL_,
        is_private_public:0,
        leadtype:'',
        offset:0,
        totalRow:10,
        postId:$state.params.id
       
    };
    $scope.postroutequote = function () {
    var url = serverUrl + 'hyperlocal/buyer-post-route-quote';
    apiServices.hyperbuyerpostquote(url + '/' + $state.params.id,$scope.filterData).then(function (response) {
        $scope.quotedata = response.payload;
        
    });
  }
    $scope.postroutequote();
     
     $scope.bookNow = function (quotedata, value) {
        
        console.log('quotedata', quotedata);
        console.log('value', value);
        //debugger;
        let quotePrice;
        if(value.status == 2 || value.status == 4) {
            quotePrice = value.buyer_quote_price;
        } else if(value.status == 3) {
            quotePrice = value.seller_quote_price;
        } else {
            quotePrice = value.initial_quote_price;
        }
        quotedata.post.price=quotePrice; 
        quotedata.post.from_date=quotedata.valid_from;
        quotedata.post.to_date=quotedata.valid_to;
        quotedata.post.vendor=value.postedto.username;
        quotedata.post.posted_by=value.postedto.id;
        //quotedata.post.from_datevalue.initial_quote_price;
        var booknowSerachObj = $dataExchanger.request;
        $scope.bookPostObj = {
            initialDetails: {
                serviceId: $dataExchanger.request.serviceId,
                serviceType: '',
                sellerId: quotedata.post.posted_by,
                serviceId: _HYPERLOCAL_,
                buyerId: Auth.getUserID(),
                postType: "BP",
                sellerQuoteId: quotedata.post.id, // need to discuss
                searchData: booknowSerachObj,
                carrierIndex: 0,
                quote: quotedata.post
            }
        };
        console.log("$scope.bookPostObj::", JSON.stringify($scope.bookPostObj));
        apiServices.bookNow(serverUrl, JSON.stringify($scope.bookPostObj)).then(function (response) {
            if (response.isSuccessful && response.payload.id) {
                $scope.cartId = response.payload.enc_id;
                $state.go('order-booknow', { serviceId: _HYPERLOCAL_, cartId: $scope.cartId });
            }
        })
        
    };

    $scope.showHideDetail = function (key) {
      $(".toggle-minus-" + key).toggleClass("detail-minus");
      $("#detail-tabel-data" + key).slideToggle();
      $("#detail-tabel-data-row" + key).slideToggle();
      $("#detail-tabel-data-rows" + key).slideToggle();
    };

   $scope.showcontract=function(key){
     $("#contract_title_"+key).css("display", "block");
     $("#generate_data"+key).css("display", "block");
     $("#contract_files"+key).css("display", "block");
     $("#Contract").css("display", "none");
   }
   
    $scope.formError = {
        contract: ''
    }

   $scope.GenerateContract=function(quotedata,quotevalue,key)
   {
    console.log('quotedata',quotevalue.contract.documentfile);
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

    console.log('term contract',quotevalue.contract.documentfile);
    $scope.Termdata.uploaddocument.type='contractdocument';
     var url = serverUrl + 'hyperlocal/buyer-post-term-contract';
     apiServices.Termcontract(url, JSON.stringify($scope.Termdata)).then(function (response) {
        $scope.response=response.payload;
        $scope.contract_no=response.payload.contract_no;

        if(response.isSuccessfull){
            str = "Your contract has been genrated successfully.  contract No is " + $scope.contract_no + " ";
            $("#responsetext").html(str);
            $(".waitText button").attr("data-type", "hpsellerPost");
            $('#myModal').modal({ 'show': true });
            //  $scope.uploadOrderDocument(quotevalue,key);
        }else{
            // $state.go("seller-post-list");
        }
        
     });
   }
    
    $scope.showFileError = function (status) {
        console.log('Status',status);
        //$scope.formError.licence.licenceDocs = false;
        if (typeof(status) != 'boolean') {
           return false;
        } else {
             $scope.formError.licence.licenceDocs = false;
            return !status;
        }
    };

   $scope.rowenable=function(key)
   {
      $('input[type="checkbox"]').not('#chk'+key).prop("checked", false);
      $('#contract_qty_'+key).prop('disabled', true);
      $('#contract_rate_'+key).prop('disabled', true);
      if($('#chk'+key).prop('checked')){
      $('#contract_rate_'+key).prop('disabled', false);
      $('#contract_qty_'+key).prop('disabled', false);
      $('#tr_row'+key).prop('disabled', false);
      }
   }
   $scope.showquoterow=function(key,rowkey)
   {
        
         $('.genratecontractrow').css("display", "none"); 
          if(key==rowkey.id)
          {
            
            $('#genratecontractrow'+key).css("display", "table-row");
             $('#genratecontractrow2'+key).css("display", "table-row");
            
          }
   }
   $scope.counterOffer=function(quotedata,quotevalue,key)
   {
     console.log('keykeykeykey',key);
     console.log('quotevalue',quotevalue);
      
      if(quotevalue.contract==null)
      {
        alert('Please Select Route');
        return false;
      }
      $scope.quoteupdate={
        buyer_id:quotevalue.buyer_id,
        id:quotevalue.id,
        buyer_id:quotevalue.buyer_id,
        route_id:quotevalue.route_id,
        contract_price:quotevalue.contract.contract_price,
        contract_quantity:quotevalue.contract.contract_quantity
      }
      

      var url = serverUrl + 'hyperlocal/update-quote-status';
      apiServices.Negotiation(url, JSON.stringify($scope.quoteupdate)).then(function (response) {
        
         console.log('respone',response);
        $scope.quotedata[0].quotes[key].status=response.payload.status;
        $scope.quotedata[0].quotes[key].buyer_quote_price=response.payload.buyer_quote_price;

       console.log('$scope.quotedata.quotes[key]',$scope.quotedata.quotes);
        
        
      });

   }
  $scope.cancelcontract=function(quotedata,quotevalue,key)
   {
     
      $scope.quoteupdate={
        buyer_id:quotevalue.buyer_id,
        id:quotevalue.id,
        buyer_id:quotevalue.buyer_id,
      }
      var url = serverUrl + 'hyperlocal/cancel-quote-status';
      apiServices.cancelcontract(url, JSON.stringify($scope.quoteupdate)).then(function (response) {
        
         console.log('respone',response);
         $scope.quotedata[0].quotes[key].status=response.payload.status;
        
      });

   }

   

    // $scope.uploadOrderDocument = function(document,key) {   
    //     document.orderItemId = 12;
    //     document.type = 'buyercontractDocument';     
    //     apiServices.uploadDocument(document).then(response => {
            
    //         console.log(response);
    //     }).catch(err => {
            
    //     });
    // }

    $scope.displayFirmQuoteAction = function(index, action) {
        $("#quote-action-"+index).toggle(1000);
    }

    $scope.viewDetails = function(index) {
        $("#quote-detail-"+index).toggle(); 
        $scope.quotedata[0].quotes[index].isActive = !$scope.quotedata[0].quotes[index].isActive; 
              
    }

    $scope.buyerQuoteAction = function(index, action) {
        
        console.log($scope.quotedata[0]);
        $scope.quotedata[0].quotes[index].action = action;
        let url = serverUrl+'negotation/buyerQuoteAction';
        apiServices.postMethod(url,$scope.quotedata[0].quotes[index]).then(response => {
            console.log(response);
            location.reload();
        }).catch();
    }


}]);
