app.controller('postDetailCtrl', ['$scope', 'messageServices', '$rootScope', '$http', 'config', 'apiServices','type_basis', '$state', 'trackings', '$dataExchanger', function ($scope, messageServices, $rootScope, $http, config, apiServices, type_basis, $state, trackings, $dataExchanger) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.data = [];
    $scope.pm = [];
    $scope.type_basis = type_basis.type_basis;
    $scope.post_Status = POST_STATUS;
    $scope.trackings = trackings.type;
    $scope.tracking_type = trackings.type;
    var transitHour = [];
    $scope.transitHour = TRANSIT_HOUR;
    var i = 0;
    for (var property in TRANSIT_HOUR) {
        transitHour[i] = {"id": property, "value": property};
        i++;
    }
    $scope.TRANSIT_HOUR = transitHour;

    $rootScope.postdetails = function () {

        var url = serverUrl + 'seller-post-details';
        apiServices.sellerPostDetail(url + '/' + $state.params.id).then(function (response) {
            $rootScope.post = response.payload;
        });
    };

    $scope.postdetails();



    /*********get discount details*******/
    $scope.discountDetails = function () {

        var url = serverUrl + 'seller-post-discout';
        apiServices.postdiscount(url + '/' + $state.params.id).then(function (response) {
            $scope.discount = response.payload;
        });
    };
    $scope.discountDetails();
    /*** Get Vehicle Type ***/
    var url = serverUrl + 'locations/getVehiletype';
    $scope.getVehicleType = function (url) {
        apiServices.vehiclesType(url).then(function (response) {
            $scope.vehicles = response;
        });
    };
    $scope.getVehicleType(url);

    $scope.deletePost = function (id) {
        var x = confirm("Are you sure you want to delete?");
        if (x) {
            var url = serverUrl + 'seller-post-delete';
            apiServices.sellerPostDelete(url, id).then(function (response) {
                $state.reload();
            });
        }
        else {}
    };

    /*********************City ******************/
    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
        });
    }
    $scope.getCity(url);
    /*********************City ******************/


    var url = serverUrl + 'getbuyerdetails';
    $scope.getbuyer = function (url) {
        apiServices.getMethod(url).then(function (response) {
            response.push({ 'id': "-1", 'full_name': "All" });
            $scope.users = response;
        });
    }
    $scope.getbuyer(url);



    /************************city filter **********/
    $scope.Newcity = [];
    $scope.NewCityFilter = function (item) {
        if (item != null) {
            if ($scope.Newcity.length == 0)
                return item;
            else {

                return $scope.Newcity.indexOf(item.city_id) !== -1;
            }
        }

    };
    $scope.selectCity = function (operator) {
        var idx = $scope.Newcity.indexOf(operator);
        if (idx > -1)
            $scope.Newcity.splice(idx, 1);
        else
            $scope.Newcity.push(operator);
        if ($scope != null)
            $scope.Newcity = $scope.Newcity;

    };

    /**
     * filter accoriding city
     */

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
    $scope.selectNewVehicleType = function (operator) {


        var idx = $scope.NewVehicleType.indexOf(operator);
        if (idx > -1)
            $scope.NewVehicleType.splice(idx, 1);
        else

            $scope.NewVehicleType.push(operator);
        if ($scope != null)
            $scope.NewVehicleType = $scope.NewVehicleType;

    };
    /*********************data filter base on transit ******************/

    $scope.Newtransit = [];
    $scope.NewtrnsitFilter = function (item) {
        if (item != null) {
            if ($scope.Newtransit.length == 0)
                return item;
            else {

                transit = item.transit_hour;

                return $scope.Newtransit.indexOf(transit) !== -1;
            }
        }

    };
    $scope.transitSelect = function (operator) {
        var idx = $scope.Newtransit.indexOf(operator);
        if (idx > -1)
            $scope.Newtransit.splice(idx, 1);
        else
            $scope.Newtransit.push(operator);

        if ($scope != null)
            $scope.Newtransit = $scope.Newtransit;

    };
    /*********************data filter base on tracking ******************/

    $scope.Newtrackings = [];
    $scope.NewtrackingFilter = function (item) {

        if (item != null) {
            if ($scope.Newtrackings.length == 0)
                return item;
            else {
                tracking = item.tracking;
                tracking = tracking.toString();
                return $scope.Newtrackings.indexOf(tracking) !== -1;
            }
        }

    };
    $scope.selecttrackingType = function (operator) {
        operator = operator.toString();
        var idx = $scope.Newtrackings.indexOf(operator);
        if (idx > -1)
            $scope.Newtrackings.splice(idx, 1);
        else
            $scope.Newtrackings.push(operator);

        if ($scope != null)
            $scope.Newtrackings = $scope.Newtrackings;

    };


    /************************Buyer filter **********/

    $scope.NewBuyers = [];
    $scope.NewBuyersFilter = function (item) {

        if (item != null) {
            if ($scope.NewBuyers.length == 0)
                return item;
            else {
                return $scope.NewBuyers.indexOf(item.assign_buyer) !== -1;

            }
        }

    };
    $scope.selectBuyer = function (operator) {
        // alert(operator);
        var idx = $scope.NewBuyers.indexOf(operator);
        console.log('indexof', idx);
        if (idx > -1)
            $scope.NewBuyers.splice(idx, 1);
        else
            $scope.NewBuyers.push(operator);
        if ($scope != null)
            $scope.NewBuyers = $scope.NewBuyers;

    };
    /************************filter for post category**********/


   /*********************City ******************/
    var url = serverUrl + 'seller-leads-details';
    $scope.leadDetails = function (url) {
        apiServices.leadDetails(url).then(function (response) {
            $scope.leads = response;
            for(var i=0;i<$scope.leads.length;i++){
                var leads = $scope.leads[i].attribute = JSON.parse($scope.leads[i].attribute);
            }
            console.log('fdgfdgd', response);
        });
    }
    $scope.leadDetails(url);
    /*********************City ******************/


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


     $scope.paymentMethod = function(index,method){
        console.log('Index',index);
        console.log('Method',method);
        var indexOfM = $scope.pm.indexOf(method);
        if(indexOfM==-1){
            $scope.pm.push(method);
        }else{
            $scope.pm.splice(indexOfM);
        }
        console.log('$scope.pm',$scope.pm);

        $scope.leads[index].payment_method = JSON.stringify($scope.pm);
    };


      $scope.validated = function(index) {
        var valid = true;
        var data = $scope.leads[index].quote;   

        if(data.quotedPrice == "" || data.quotedPrice == null){
            data.quoted_price_error = true;
          valid = false;
        }else{
            data.quoted_price_error = false;
        }

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

      $scope.submitInitialQuotation = function(index,action){
        if($scope.validated(index)){
            var data = $scope.leads[index].quote;  
            var routeId = $scope.leads[index].routeId; 
            data.post_id = $scope.leads[index].postId;
            
            data.lkp_service_id = _INTRACITY_;
            data.quotation_type = $scope.leads[index].lead_type;
            data.seller_id = Auth.getUserID();
            // data.buyer_id = $scope.leads[index].buyer.id;  
            data.buyer_id = $scope.leads[index].posted_by
            data.route_id = routeId;
            data.transit_day = data.transit_days;
            //data.firm_price = route.price_type == 2 ? route.firm_price: data.quotedPrice,
            
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
        console.log('MESSAGE VALUESSSSSS ID::',value);
        $("#id").val("Id:" + value.routeId)
        $("#from_name").val("From: " + Auth.getUserName());
        $("#to_name").val("To: " + value.username);
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
        $scope.MessageObj.id = parseInt(postDetails.routeId);
       
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

    $scope.termisChecked=function(index){
        var isChecked = $scope.leads[index].termisChecked;
        $scope.leads[index].termisDisable = !isChecked;
    }

    $scope.termsubmitQuotation = function(index) {
        var data = $scope.leads[index];
        if(!data.termquoteprice){
            alert("Please enter term quote price.");
            return false;
        }else if(data.award_criteria == 1){
          if(data.termquoteprice != data.emd_amount){
            alert("Please enter term quote price equal to EMD Amount.");
            return false;
          }
        }

        var data = $scope.getTermQuoteData(index);
        $http({
          method: 'POST',
          url: config.serverUrl + 'seller-quote-submission',
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

    $scope.getTermQuoteData = function(index){
        var data = $scope.leads[index];
        var formData = {
            route_id: data.routeId,
            post_id: data.postId,
            buyer_id: data.posted_by,
            lkp_service_id: _INTRACITY_,
            quotation_type: data.price_type == '' ? '':data.price_type,
            lead_type:data.lead_type,
            quotedPrice:data.termquoteprice == '' ? '':data.termquoteprice
        };
        return formData;
    }; 

    $scope.acceptOffer=function(value,index)
    {
       $scope.updatedata = {
          id:value.quoteId,
          finalquoteprice:value.finalquoteprice,
          lkp_service_id:value.lkp_service_id
       }
       
      var url = serverUrl + 'hyperlocal/seller-quote-contract-accept';
      apiServices.sellerfinalQuote(url, JSON.stringify($scope.updatedata)).then(function(response) {  
        console.log('responce',$scope.leads[index]);
        $scope.leads[index].status = response.payload.status;
        $scope.leads[index].contract_status = response.payload.contract_status;
      });
    }

}]);
