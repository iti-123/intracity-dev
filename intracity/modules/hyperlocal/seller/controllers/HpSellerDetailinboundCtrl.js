app.controller('HpSellerDetailinboundCtrl', ['$scope', '$http', 'config', 'trackings', 'apiServices', 'apiHyperlocalServices','$compile', '$state', '$stateParams', '$dataExchanger', function ($scope, $http, config, trackings, apiServices,apiHyperlocalServices ,$compile, $state, $stateParams, $dataExchanger) {

    var transitHour = [];
    var serverUrl = config.serverUrl;
    $scope.filterdata = [];
    $scope.servicetype = SERVICE_TYPE;
    $scope.materialtype = HYPERLOCAL_MATERIAL_TYPE;
    $scope.post_Status = POST_STATUS;
    $scope.weight = HYPERLOCAL_WEIGHT;
    $scope.tracking_type = trackings.type;
    
    var url = serverUrl + 'hyperlocal/product-category';
    $scope.getProductCategory = function (url) {
        apiServices.category(url).then(function (response) {
            $scope.categories = response.data;
            //console.log('Product Category:', $scope.categories);
        });
    }
    $scope.getProductCategory(url);

    

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
        totalRow:5,
       
       
    };
   // console.log('xxxxx',$scope.filterData);
    /*********inbound detials ********************/

       
         $scope.inboundResult = function () {
        
        var url = serverUrl + 'hyperlocal/hp-get-seller-inbound-details';
        apiHyperlocalServices.SellerInboundDetails(url, $scope.filterData).then(function (response) {
        
          $scope.filterData.totalRow = response.data.length;
          
          $scope.listdata = response.data;
          //console.log('listdata',$scope.listdata);
         for (let key in $scope.listdata) {
              $scope.listdata[key].title=$scope.params.replace(/-/g, ' ');
               
         }
           
       })
    }

    $scope.$watch('filterData', function (newValue, oldValue) {      
        $scope.inboundResult();   
    }, true);
     setTimeout(function(){
      $(window).scroll(function() {    
            $scope.filterData.offset = 5;   
            if ($(window).scrollTop() == $(document).height() - $(window).height()) {
                $(".page-data-loader").fadeIn();       
                $scope.inboundResult(); 
            }
        });
    },1000);

   $scope.addFilterData = function (arr, value) {
        var index = arr.indexOf(value);
        if (index == -1) {
            arr.push(value);
        } else {
            arr.splice(index, 1);
        }

       console.log($scope.filterData);
    };
    
   $scope.validated = function(value){
       var valid = true;
       var data =value;

    
    

       if(data.tracking_type==""||data.tracking_type==null){
         data.tracking_type_error = true;
         valid=false;
       }else{
         data.tracking_type_error = false;
       }

       if(data.payment_term==""||data.payment_term==null){
         data.payment_term_error = true;
         valid=false;
       }else{
         data.payment_term_error = false;
       }

       if(data.payment_term=='CREDIT'&&(data.credit_days==""||data.credit_days==null)){
         data.credit_days_error = true;
         valid=false;
       }else{
         data.credit_days_error = false;
       }

       if(data.payment_method==""||data.payment_method==null){
         data.payment_method_error = true;
         valid=false;
       }else{
         data.payment_method_error = false;
       }
       return valid;
     };

  $scope.submitQuotation = function(value,data){
     
      value.post_id= value.fk_buyer_seller_post_id;
      value.buyer_id= data.posted_by;
      value.route_id=  value.id;
      value.quotation_type=value.price_type;
      if(value.price_type==1)
      {
        value.initial_quote_price=value.quotedPrice;
      }else{
         value.initial_quote_price=value.firm_price;
      }
     // console.log(value);

       if($scope.validated(value)){
          var data = value;
          $http({
            method: 'POST',
            url: config.serverUrl+'seller-quote-submission',
            headers: {
              'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: data,
          }).then(function(response){
             //console.log($scope.listdata);
             //console.log('postid',value.fk_buyer_seller_post_id);
             //console.log('rootid',value.id);
             for(let key in $scope.listdata)
               if($scope.listdata[key].id==value.fk_buyer_seller_post_id)
                  {
                   for(let k in $scope.listdata[key].get_all_route)
                       {
                        if($scope.listdata[key].get_all_route[k].id==value.id)
                        {
                          $scope.listdata[key].get_all_route[k].quote=response.data.intra_hp_post_quotation;
                          break; 
                        }
                       }
                    break; 
                  }
             //console.log('ddd',$scope.listdata);
            //$scope.searchResult[index].quote = response.data.intra_hp_post_quotation;
          }, function(response){
            console.log(response);
          });
       }
     };
   $scope.paymentMethod = function (val, method) {
        if(val.payment_method==method){
            val.payment_method = '';

        }else {
            val.payment_method = method;
        }
    }

}]);

