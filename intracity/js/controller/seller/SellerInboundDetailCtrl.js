app.controller('SellerInboundDetailCtrl', ['$scope', 'messageServices', '$http', 'config', 'apiServices', 'type_basis', '$state','$dataExchanger','trackings', function ($scope, messageServices, $http, config, apiServices, type_basis, $state,$dataExchanger,trackings) {
    
      var serverUrl = config.serverUrl;
      var authToken = config.appAuthToken;
      $scope.data = [];
      $scope.pm = [];
      $scope.tracking_type = trackings.type;
      $scope.type_basis = type_basis.type_basis;
      $scope.post_Status = POST_STATUS;
      $scope.show = '';
      $scope.boundList = [];
      var url = serverUrl + 'locations/getCity';
      $scope.getCity = function (url) {
          apiServices.city(url).then(function (response) {
              $scope.cities = response;            
          });
      };
      //  Get city of intracity
      $scope.getCity(url);
      
  
      $scope.getVehiclesType = function (url) {
          apiServices.vehiclesType(url).then(function (response) {
              $scope.vehicles = response;            
          });
      };
  
      $scope.getMaterialType = function () {
          apiServices.getMethod(serverUrl + 'getLoadType').then(function (response) {
              $scope.materialType = response;
              // console.log($scope.cities);
          });
      }
      $scope.getMaterialType();
  
  
      /*** Get Filtered Record ***/
  
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
          type: 'settings',
          title:($state.params.title).split('-').join(" "),
          isInbound: 'inbound',//default
          role: Auth.getUserRole(),
          serviceId: $dataExchanger.request.serviceId,
          settings: []
      };
  
      $scope.addFilterData = function (arr, value) {
          console.log(arr);
          var index = arr.indexOf(value);
          if (index == -1) {
              arr.push(value);
          } else {
              arr.splice(index, 1);
          }
  
          console.log($scope.filterData);
      };
  
  
      $scope.search_filter = function (type) {   
          
          $scope.filterData.type = type;
          
          var url = serverUrl + 'seller-inbound-list';
          apiServices.searchFilter(url,$scope.filterData).then(function (response) {
              $scope.spotsList = response;     
              
              for (var key in $scope.spotsList.payload) {
                  if ($scope.spotsList.payload.hasOwnProperty(key)) {                    
                      $scope.spotsList.payload[key].title = $scope.filterData.title; 
                      for(var k in $scope.spotsList.payload[key].routes) {
                          $scope.spotsList.payload[key].routes[k].buyer_id = $scope.spotsList.payload[key].buyer.id;
                      }                   
                  }
              }
             
              console.log('All List', $scope.spotsList);
          });
  
          console.log($scope.filterData);
          
      };
      $scope.search_filter('all'); 
    
      $scope.$watch("filterData",function() {
          $scope.search_filter($scope.filterData.type);
      },true);
      /*** Get Filtered Record ***/
  
  
      /*** Get Location By Id ***/
      $scope.onSelect = function (data) {
          //
          console.log(data);
      };
      $scope.data.city_id = {id: ''};
      $scope.onSelect = function (data) {
          console.log("City Id::", parseInt(data.id));
          var city_id = parseInt(data.id);
          if (typeof(city_id) != NaN || city_id != '') {
              $scope.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id);
              // $scope.getListBuyerAccordingFilter(url, city_id);
          }
      };
  
  
      $scope.getLocationByCity = function (url) {
          apiServices.getLocationByCity(url).then(function (response) {
              $scope.locations = response;
              // console.log('Locations:', $scope.locations);
          });
      };
      /*** Get Location By Id ***/
  
  
      /*** Get Vehicle Type ***/
      var url = serverUrl + 'locations/getVehiletype';
      $scope.getVehicleType = function (url) {
          apiServices.vehiclesType(url).then(function (response) {
              $scope.vehicles = response;
              console.log($scope.vehicles);
          });
      };
      $scope.getVehicleType(url);
      /*** Get Vehicle Type ***/
  
  
      /*** Get All Seller List ***/
      apiServices.getAllSellers(serverUrl).then(function (response) {
          $scope.sellerList = response;
          setTimeout(function () {
              $("#sellerList").tokenInput($scope.sellerList, {propertyToSearch: 'username'});
          }, 1000);
  
          console.log("$scope.sellerList::", $scope.sellerList);
      });
      /*** Get All Seller List ***/
  
  
  
  
      
      /************************For Active Class On Click Filters************/
      $scope.changeClass = function (type) {
          $('.all,.spot,.term,.public,.private').removeClass('search_nav_active');
          $('.' + type).addClass("search_nav_active");
         
      };
      /************************For Active Class On Click Filters************/
  
  
      /**
       * Submit counter offer
       *
       */
      $scope.submitCounterOffer = function (buyerData, id) {
          console.log("buyerData", buyerData);
      };
  
  
    
  
      $scope.redirectToDetail = function(id) {
          $state.go("buyerDetails",{"id":id});
      }
  
  
      $scope.redirectToListDetail = function(value) {
          console.log("value::",(value.title).split(" ").join("-").toLowerCase());
          var title = (value.title).split(" ").join("-").toLowerCase();
          $state.go("buyerlistdetail",{"title":title});
      }
  
      $scope.showHideDetail = function ($index) {
          $(".toggle-minus-" + $index).toggleClass("detail-minus");
          if($("#detail-" + $index).css("display") == "block") {
              $(".detail-data-form-"+$index).hide();
              $("#detail-" + $index).hide();
          } else {
              $(".detail-data-form-"+$index).show();
              $("#detail-" + $index).show();
          }        
          
      };
  
      $scope.viewFormNow = function(quote, index) {
          $(".detail-data-form-"+index).toggle();       
      }
  
      $scope.bookNow = function (quote, index) {
          console.log(quote);
          // debugger;
          // var value = $scope.searchResult[index];
          // var price = '';
          // if (value.rate_base_distance != '') {
          //     price = 'RS ' + value.base_distance * value.rate_base_distance + ' /- (For ' + value.base_distance + ' KM )';
          // } else if (value.base_time != '') {
          //     price = 'RS ' + value.base_time * value.cost_base_time + ' /- (For ' + value.base_time + ' Hours )';
          // }
          // // console.log("quote::",$scope.searchResult[index]);
          // var confirm1 = confirm("Do you want to book,\n Seller Name: " + value.seller + ", and Price : " + price);
          // console.log("Buyer Quote::", confirm1);
          
          quote.price = quote.base_distance * quote.rate_base_distance;
          var confirm1 = true;
          if (confirm1) {
              var booknowSerachObj = $dataExchanger.request;
              $scope.bookPostObj = {
                  initialDetails: {
                      serviceId: $dataExchanger.request.serviceId,
                      serviceType: '',
                      sellerId: quote.seller_id,
                      buyerId: Auth.getUserID(),
                      postType: "BP",
                      sellerQuoteId: quote.id, //need to discuss
                      searchData: booknowSerachObj,
                      carrierIndex: index,
                      quote: quote
                  }
              };
              quote.id = quote.encId;
              // console.log("$scope.bookPostObj::", $scope.bookPostObj);
              apiServices.bookNow(serverUrl, JSON.stringify($scope.bookPostObj)).then(function (response) {
                  if (response.isSuccessful && response.payload.id) {
                      $scope.cartId = response.payload.enc_id;
                      $state.go('order-booknow', {serviceId: 3, cartId: $scope.cartId});
                  }
              })
          }
      };

      $scope.paymentMethod = function(index,routeKey, method){
        
        var indexOfM = $scope.pm.indexOf(method);
        if(indexOfM==-1){
            $scope.pm.push(method);
        }else{
            $scope.pm.splice(indexOfM);
        }
        console.log('$scope.pm',$scope.pm);

        $scope.spotsList.payload[index].routes[routeKey].quote.payment_method  = JSON.stringify($scope.pm);
    };


      $scope.validated = function(index,routeKey) {
        var valid = true;
        var data = $scope.spotsList.payload[index].routes[routeKey].quote;
 
        if(data.transit_days==""||data.transit_days==null){
            data.transit_days_error = true;
          valid=false;
        }else{
            data.transit_days_error = false;
        }
 
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
        if($scope.pm.length==0){
          data.payment_method_error = true;
          valid=false;
          }else{
            data.payment_method_error = false;
          }
          return valid;
      };

      $scope.submitInitialQuotation = function(index,routeKey,action){
        if($scope.validated(index,routeKey)){
            var data = $scope.spotsList.payload[index].routes[routeKey].quote;  
            var route = $scope.spotsList.payload[index].routes[routeKey]; 
            data.post_id = $scope.spotsList.payload[index].id;
            data.lkp_service_id = $scope.spotsList.payload[index].lkp_service_id;
            data.quotation_type = $scope.spotsList.payload[index].lead_type;
            data.seller_id = Auth.getUserID();
            data.buyer_id = $scope.spotsList.payload[index].buyer.id;  
            data.route_id = route.id;
            data.transit_day = data.transit_days;
            data.firm_price = route.price_type == 2? route.firm_price: data.quotedPrice,
           $http({
             method: 'POST',
             url: config.serverUrl+'seller-quote-submission',
             headers: {
               'authorization': 'Bearer ' + localStorage.getItem("access_token")
             },
             data: data,
           }).then(function(response){
            data = response.data.intra_hp_post_quotation;
                
              $("#initialquotemodel").modal('show');
              
           }, function(response){
             
           });
        }
    };

    $scope.closeQuoteModel = function() {
        $("#initialquotemodel").modal('hide');
        location.reload();
    }

      


    $scope.submitQuotation = function(key,routeKey,status) {
        var finalQuote = $scope.spotsList.payload[key].routes[routeKey].quote;
        let url = serverUrl+'sellerQuoteAction';
        let params = {
            sellerFinalQuotePrice : finalQuote.sellerFinalQuotePrice,
            sellerFinalTransitDay: finalQuote.sellerFinalTransitDay,
            action: status,
            id: finalQuote.id
        };

        apiServices.postMethod(url,params).then(response => {
            console.log(response);
            location.reload();
        }).catch();
        
        console.log(params);
        console.log(finalQuote);
        
    }

    $scope.sellerQuoteAction = function(key,routeKey,action) {
        
        console.log($scope.spotsList.payload[key].routes[routeKey].quote);
        let quote = $scope.spotsList.payload[key].routes[routeKey].quote;
        
        let url = serverUrl+'sellerQuoteAction';
        let data = {
            action: action,
            id: quote.id
        }
        apiServices.postMethod(url,data).then(response => {
            console.log(response);
            location.reload();
        }).catch();
    }
  
  // Lets start sending Message
    $scope.sendMessageModal = function (value) {
        console.log('MESSAGE VALUESSSSSS ID::',value.id);
        console.log(value);
        $("#id").val("Id:" + value.id)
        $("#from_name").val("From: " + Auth.getUserName());
        $("#to_name").val("To: " + value.buyer.username);
        $("#message_subject").val("Ref:- POST:" + value.post_transaction_id);
        $("#quoteObject").val(JSON.stringify(value));
        

    };

    $scope.MessageObj = {
        "id":"",
        "message_from": "",
        "message_to": "",
        "message_subject": "",
        "message_body": "",
        "buyer_quote": "",
        "seller_post": "",
        "buyer_quote_item": "",
        "buyer_quote_item_leads": "",
        "buyer_quote_item_seller": "",
        "buyer_quote_item_seller_leads": "",
        "order_id_for_model": "",
        "is_term": "",
        "buyer_quote_item_for_search": "",
        "buyer_quote_item_for_search_seller": "",
        "order_id_for_model_seller": "",
        "message_id": "",
        "lkp_service_id": "",
        "postId": ""

    };
    $scope.message_attachment = '';
    $scope.attachedFile = {
        "file": "",
        "userMessageId": ""
    };

    $scope.sendMessage = function (attachedFile) {
        //console.log("attachedFile", typeof attachedFile); 
        var myForm = document.querySelector('#MessageData');
        var formData = new FormData(myForm);

        // Start Upload File
        var postDetails = JSON.parse(formData.get('quoteObject'));

        $scope.MessageObj.message_from = Auth.getUserID();
        $scope.MessageObj.message_to = postDetails.buyer.id;
        $scope.MessageObj.message_subject = formData.get('message_subject');
        $scope.MessageObj.message_body = formData.get('message_body');
        $scope.MessageObj.id = postDetails.id;
        $scope.MessageObj.postId = postDetails.id;
       
        $scope.MessageObj.lkp_service_id = $dataExchanger.request.serviceId;
        $scope.MessageObj.buyer_quote_item = '';
        $scope.MessageObj.buyer_quote = '';
        //console.log("$scope.MessageObj.message_body::", $scope.MessageObj.message_body);
        $("#message_body").css("border-color", "#e2e2e2");
        if ($scope.MessageObj.message_body == '') {
            $("#message_body").css("border-color", "red");
            return false;
        }
        $scope.status = false;
        if (attachedFile != '') {
            $scope.attachedFile.file = attachedFile;
            messageServices.PostMessage($scope.MessageObj).then(function (response) {

                if (response.isSuccessful == true) {
                    // $scope.files = {documentId: response.payload.id, documentName: response.payload.file_name};
                    $scope.attachedFile.userMessageId = response.payload.id;
                    //console.log("HELLO WITH DOCUMENT", $scope.MessageObj.userMessageId);
                 //   console.log("NEW MESSAGE OBJECT ", $scope.MessageObj);
                    messageServices.uploadDocument($scope.attachedFile).then(function (response) {
                        //console.log(response.payload);
                        $scope.messages = response.payload;
                        $scope.status = true;
                        $("#status").html("Message sent Successfully").addClass("text-success");
                        $("#messageModal").modal('hide');
                        setTimeout(function () {
                            $state.reload();
                        }, 1000);


                    })
                }
            });
        } else {
            //console.log("$scope.MessageObj ::", $scope.MessageObj);
            messageServices.PostMessage($scope.MessageObj).then(function (response) {
                $scope.MessageObj.docId = "";
               // console.log(response.payload);

                $scope.messages = response.payload;
                $("#status").html("Message sent Successfully").addClass("text-success");
                setTimeout(function () {
                    $("#messageModal").modal('hide');
                }, 1000);
            });
        }
    };
      
  
  }]);
  