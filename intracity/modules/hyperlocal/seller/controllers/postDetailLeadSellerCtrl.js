app.controller('postDetailLeadSellerCtrl', ['$scope', 'messageServices', '$http', 'config', 'apiServices', 'type_basis', '$state', 'trackings','$dataExchanger', function ($scope, messageServices, $http, config, apiServices, type_basis, $state, trackings,$dataExchanger) {
   
    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.data = [];
    $scope.categories = hyerCatergories;
    $scope.servicetype = SERVICE_TYPE;
    $scope.filterDatas = {
        buyerType: []
    };
   
    var url = serverUrl + 'hyperlocal/seller-postlead-details';

    apiServices.hyperbuyerPostDetail(url + '/' + $state.params.id).then(function (response) {
        arr = response.data;
        $scope.post = arr;
        console.log('POSSTT DETAILSS', $state.params.id);
    });

    $scope.$watch('filterDatas', function(newValue, oldValue){
        $scope.filterDatas.ids = $state.params.id;
        $http({
          url: serverUrl + 'hyperlocal/hp-seller-post-lead-list',
          method: 'POST',
          headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
          },
          data: $scope.filterDatas,
        }).then(function success(response){
          if(response.data.success){
            $scope.postHpLeads = response.data.data;
          }else{
            $scope.postHpLeads = response.data.data;
          }

        },function error(response){
           
        });
    },true);
    
    $scope.openDetails = function (index) {
      $scope.postHpLeads[index].showDetails = !$scope.postHpLeads[index].showDetails;
    };

    $scope.removeElem = function (arr,index) {
        arr.splice(index,1);
        if($scope.filterData.sellerList.length < 1){
                $scope.filClearValue = false;
        }
    };

    $scope.isDetailsHidden = function (index) {
      if ($scope.postHpLeads[index].showDetails == undefined) {
          $scope.postHpLeads[index].showDetails = false;
      }
      return $scope.postHpLeads[index].showDetails;
    };
    
    $scope.checkedFilterData = function (arr, value) {
      var index = arr.indexOf(value);
      if (index == -1) {
          return false;
      }
      return true;
    }
    
    $scope.addFilterData = function (arr, value) {
      $scope.filClearValue = true;
      var index = arr.indexOf(value);
      if(index == -1){
          arr.push(value);
      }else{
          arr.splice(index, 1);
          if($scope.filterDatas.buyerType.length < 1){
              $scope.filClearValue = false;
          }
      }
    };
    
    $scope.clearAll = function () {
      $scope.filClearValue = false;
      $scope.filterDatas = {
         buyerType: [],
      };
    }
    
    $scope.getUsername = function (id) {
      for (let v of $scope.sellerList) {
          if (v.id == id) {
              return v.username;
          }
      }
    }
    
    $scope.removeElem = function (arr, index) {
        arr.splice(index,1);
    };

    $scope.pm = [];

    $scope.paymentMethod = function(index,method){
        var indexOfM = $scope.pm.indexOf(method);
        if(indexOfM == -1){
            $scope.pm.push(method);
        }else{
            $scope.pm.splice(indexOfM);
        }
        $scope.postHpLeads[index].quote.payment_method = JSON.stringify($scope.pm);
    };
    
    $scope.termisChecked = function(index){
      var isChecked = $scope.searchResult[index].termisChecked;
      $scope.searchResult[index].termisDisable = !isChecked;
       
    }

    $scope.termsubmitQuotation = function(index){
        if(!$scope.postHpLeads[index].termquoteprice){
            alert("Please enter term quote price.");
            return false;
        } else if($scope.postHpLeads[index].termquoteprice == 0){
            alert("Please enter term quote price greater than 0.");
            return false;
        }
        var data = $scope.getQuoteData(index);
        $http({
          method: 'POST',
          url: config.serverUrl+'seller-quote-submission',
          headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
          },
          data: data,
        }).then(function(response){
          if(response.data.success){
             // $scope.searchResult[index].quote = response.data.intra_hp_post_quotation;
              $("#initialquotemodel").modal('show');
          }else{
              $("#initialquoteerrormodel").modal('show');
          }
        }, function(response){
          console.log(response);
        });
    };

    // $scope.getQuoteData = function(index){
    //    var data = $scope.postHpLeads[index];
    //    console.log('datadatadata',data);
       
    //    var formData = {
    //      route_id: data.id,
    //      post_id: data.post_result.id,
    //      buyer_id: data.posted_by,
    //      lkp_service_id: _HYPERLOCAL_,
    //      quotation_type: data.price_type,
    //      lead_type:data. data.lead_type,
    //      quotedPrice: data.termquoteprice == '' ? '':data.termquoteprice
    //    };
    //     console.log("formData::",formData);
    //   // return formData;
    // }; 
    
    $scope.getQuoteData = function(index){
      var data = $scope.postHpLeads[index];
      
      var formData = {
        route_id: data.item_id,
        post_id: data.fk_buyer_seller_post_id,
        buyer_id: data.posted_by,
        lkp_service_id: _HYPERLOCAL_,
        quotation_type: data.price_type,
        quotedPrice: data.termquoteprice == '' ? '':data.termquoteprice
      };
      
      console.log("formData::",formData);
      return formData;
    }; 

    $scope.validated = function(index) {
        var valid = true;
        if($scope.postHpLeads[index].quote.transit_days == "" || $scope.postHpLeads[index].quote.transit_days == undefined){
            $scope.postHpLeads[index].quote.transit_days_error = true;
            valid=false;
        }else{
            $scope.postHpLeads[index].quote.transit_days_error = false;
        }

        if($scope.postHpLeads[index].quote.quotedPrice == "" || $scope.postHpLeads[index].quote.quotedPrice == undefined){
            $scope.postHpLeads[index].quote.quoted_price_error = true;
            valid=false;
        }else{
            $scope.postHpLeads[index].quote.quoted_price_error = false;
        }

        if($scope.postHpLeads[index].quote.payment_term == "" || $scope.postHpLeads[index].quote.payment_term == undefined){
          $scope.postHpLeads[index].quote.payment_term_error = true;
          valid=false;
        }else{
          $scope.postHpLeads[index].quote.payment_term_error = false;
        }
 
        if($scope.postHpLeads[index].quote.payment_term=='CREDIT'&&($scope.postHpLeads[index].quote.credit_days == "" || $scope.postHpLeads[index].quote.credit_days == undefined)){
          $scope.postHpLeads[index].quote.credit_days_error = true;
          valid=false;
        }else{
          $scope.postHpLeads[index].quote.credit_days_error = false;
        }
        
        if($scope.pm.length == 0 || $scope.pm.length == undefined){
          $scope.postHpLeads[index].quote.payment_method_error = true;
          valid=false;
        }else{
          $scope.postHpLeads[index].quote.payment_method_error = false;
        }
        return valid;
    };

    $scope.submitInitialQuotation = function(index){
        if($scope.validated(index)){
            var data = $scope.postHpLeads[index].quote; 
            console.log('Quote Data',data); 
            var routeId = $scope.postHpLeads[index].item_id; 
            data.post_id = $scope.postHpLeads[index].postid;
            console.log('POSSSTT IDDDDD::',data);
            data.lkp_service_id = $scope.postHpLeads[index].lkp_service_id;
            data.quotation_type = $scope.postHpLeads[index].price_type;
            data.seller_id = Auth.getUserID();
            data.buyer_id = $scope.postHpLeads[index].posted_by;
            data.route_id = routeId;
            data.transit_day = data.transit_days;
           
           $http({
             method: 'POST',
             url: serverUrl + 'hp-seller-quote-submission',
             headers: {
               'authorization': 'Bearer ' + localStorage.getItem("access_token")
             },
             data: data,
           }).then(function(response){
              if(response.data.success){
                $("#initialquotemodel").modal('show');
              }else{
                $("#initialquoteerrormodel").modal('show');
              }
          }, function(response){
             
          });
        }
    };
    
    $scope.sellerQuoteAction = function(index,action) {
        
        console.log($scope.postHpLeads[index]);
        let quote =$scope.postHpLeads[index];
        
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

    $scope.submitQuotation = function(key,listKey){
        if($scope.validated(key)){
           var data = $scope.getQuoteData(key);
           $http({
             method: 'POST',
             url: config.serverUrl+'seller-quote-submission',
             headers: {
               'authorization': 'Bearer ' + localStorage.getItem("access_token")
             },
             data: data,
           }).then(function(response){
            $scope.postHpLeads[index] = response.data.intra_hp_post_quotation;
           }, function(response){
             console.log(response);
           });
        }
      };
      $scope.submitFinalQuotation = function(index,status) {
        var finalQuote = $scope.postHpLeads[index];;
        console.log(finalQuote);
        let url = serverUrl+'sellerQuoteAction';
        let params = {
            sellerFinalQuotePrice : finalQuote.sellerFinalQuotePrice,
            sellerFinalTransitDay: '',
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

    $scope.acceptOffer=function(value,index)
    {
        $scope.updatedata = {
          id:value.id,
          lkp_service_id:value.lkp_service_id
        }
        var url = serverUrl + 'hyperlocal/seller-quote-contract-accept';
        apiServices.sellerfinalQuote(url, JSON.stringify($scope.updatedata)).then(function(response) {
          if(response.isSuccessfull)
          {
              console.log('responce',$scope.postHpLeads[index]);
              $scope.postHpLeads[index].status = response.payload.status;
          }
        });
    }
   
    $scope.termisChecked=function(index){
      var isChecked = $scope.postHpLeads[index].termisChecked;
      $scope.postHpLeads[index].termisDisable = !isChecked; 
    }

    $scope.closeQuoteModel = function() {
        $("#initialquotemodel").modal('hide');
        location.reload();
    }

    apiServices.getAllSellers(serverUrl).then(function (response) {
        $scope.sellerList = response;
        setTimeout(function () {
            $("#sellerList").tokenInput($scope.sellerList, {propertyToSearch: 'username'});
        }, 1000);
    });


    $scope.sendMessageModal = function (value) {
        console.log('MESSAGE VALUESSSSSS ID::',value);
        $("#id").val("Id:" + value.item_id)
        $("#from_name").val("From: " + Auth.getUserName());
        $("#to_name").val("To: " + value.posted_username);
        $("#message_subject").val("Ref:- POST:" + value.post_transaction_id);
        $("#quoteObject").val(JSON.stringify(value));
        

    };
//http://localhost/intracity-dev/intracity/index.html#/hyperlocal-seller-post-lead-list/1
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
        "lkp_service_id": ""

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
        $scope.MessageObj.message_to = postDetails.posted_by;
        $scope.MessageObj.message_subject = formData.get('message_subject');
        $scope.MessageObj.message_body = formData.get('message_body');
        $scope.MessageObj.id = parseInt(postDetails.item_id);
       
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
                        setTimeout(function () {
                            $("#messageModal").modal('hide');
                            //$state.reload();
                        }, 1000);


                    })
                }
            });
        } else {
            console.log("$scope.MessageObj ::", $scope.MessageObj);
            messageServices.PostMessage($scope.MessageObj).then(function (response) {
                $scope.MessageObj.docId = "";
                console.log(response.payload);

                $scope.messages = response.payload;
                $("#status").html("Message sent Successfully").addClass("text-success");
                setTimeout(function () {
                    $("#messageModal").modal('hide');
                }, 1000);
            });
        }
    };



}]);
