app.controller('SellerSearchResultCtrl', ['$scope', 'messageServices', '$http', 'config', 'trackings', 'apiServices', '$compile', '$state','$stateParams','$dataExchanger',function($scope, messageServices, $http, config, trackings, apiServices, $compile,$state,$stateParams,$dataExchanger) {

    var serverUrl = config.serverUrl;
     $scope.pm = [];
    $scope.tracking_type = trackings.type;
    $scope.storagePath = TERMFILE_PATH;
   // console.log($scope.tracking_type);
    /**********************************************************
     *
     *        Get city
     *
    *************************************************************/
    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function(url) {
        apiServices.city(url).then(function(response) {
            $scope.cities = response;
            ///console.log($scope.cities);
        });
    };
     $scope.getCity(url);
    /****************************************************************
     *
     *           get getVehiletype
     *
     *****************************************************************/
    $scope.vehicles = vechileType;
    $scope.hourDistanceSlabs = distanceHourSlab;
    $scope.timeslot = TIME_SLOT;
      /*************************************************************
       *  Get buyer list
       *
      ***************************************************************/
      $('#city').keypress(function (e) {
        var keyCode = e.keyCode == 0 ? e.charCode : e.keyCode;
        var ret = ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || 
            (keyCode >= 97 && keyCode <= 122) || (specialKeys.indexOf(e.keyCode) != -1 && e.charCode != e.keyCode));
          return ret;
     });
    
     $('#fromLocation').keypress(function (e) {
        var keyCode = e.keyCode == 0 ? e.charCode : e.keyCode;
        var ret = ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || 
            (keyCode >= 97 && keyCode <= 122) || (specialKeys.indexOf(e.keyCode) != -1 && e.charCode != e.keyCode));
          return ret;
     });
 
    $('#toLocation').keypress(function (e) {
        var keyCode = e.keyCode == 0 ? e.charCode : e.keyCode;
        var ret = ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || 
            (keyCode >= 97 && keyCode <= 122) || (specialKeys.indexOf(e.keyCode) != -1 && e.charCode != e.keyCode));
          return ret;
    });

      var buyerurl =serverUrl+'getbuyerdetails';
      $scope.getbuyer = function(url) {
        apiServices.getMethod(url).then(function(response) {
            $scope.buyers = response;
            //console.log($scope.buyers);
        });
    };
      /*************************************************************
       * price slider
      *************************************************************/
         // Price Slider
      $scope.value = 150;
      $scope.slider = {
          min: 100,
          max: 180,
          options: {
              floor: 0,
              ceil: 450
          }
      };

      $scope.getbuyer(buyerurl);
     
      $scope.compare={};
      $scope.sellersearch=$dataExchanger.request.data;
      var searchdata=$scope.sellersearch;


       if(angular.equals($scope.sellersearch, $scope.compare))
       {
         $state.go("seller-search");
       }
       /***********************************************************
        * buyer search result
        *
        ************************************************************/


        var url = serverUrl + 'buyer-post-search';

        apiServices.buyerSearchResult(url,JSON.stringify(searchdata)).then(function(response) {
            $scope.searchBuyers = response.payload;
            
            console.log("Buyer Search ::",response.payload);

        });
     /*****************************************************************************
     *                    filter search start here
     *
     ******************************************************************************/
    $scope.termisChecked=function(index){
      var isChecked = $scope.searchBuyers[index].termisChecked;
      $scope.searchBuyers[index].termisDisable = !isChecked;
    }
    
    $scope.termsubmitQuotation = function(index){
        var data = $scope.searchBuyers[index];
        if(!data.termquoteprice){
            alert("Please enter term quote price.");
            return false;
        }else if(data.post.award_criteria == 1){
          if(data.termquoteprice != data.post.emd_amount){
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
     var data = $scope.searchBuyers[index];
     var formData = {
       route_id: data.id,
       post_id: data.post.id,
       buyer_id: data.post.posted_by,
       lkp_service_id: _INTRACITY_,
       quotation_type: data.price_type == '' ? '':data.price_type,
       lead_type:data.lead_type,
       quotedPrice:data.termquoteprice == '' ? '':data.termquoteprice
     };
     console.log("formData::",formData);
     return formData;
   }; 

    $scope.NewBuyers= [];
    $scope.NewBuyersFilter = function (item) {

        if (item != null) {
            if ($scope.NewBuyers.length == 0)
                return item;
            else {

                return $scope.NewBuyers.indexOf(item.buyer) !== -1;
            }
        }

    };
    $scope.selectBuyer = function (operator) {
        //console.log($scope.NewBuyers);

        var idx = $scope.NewBuyers.indexOf(operator);
        console.log('indexof',idx);
        if (idx > -1)
            $scope.NewBuyers.splice(idx, 1);
        else
            $scope.NewBuyers.push(operator);
        if ($scope != null)
            $scope.NewBuyers = $scope.NewBuyers;

    };
    /**************************************************************************/

     $scope.NewVehicleType= [];
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


    /*****************************************************************************
     *                    filter search end  here
     ******************************************************************************/


     /*
        quotation code start
     */
    
     $scope.paymentMethod = function(index, method){
        var indexOfM = $scope.pm.indexOf(method);
        if(indexOfM == -1){
          $scope.pm.push(method);
        }else{
          $scope.pm.splice(indexOfM);
        }
        console.log('$scope.pm',$scope.pm);
        $scope.searchBuyers[index].payment_method  = JSON.stringify($scope.pm);
     };

     $scope.validated = function(index) {
       var valid = true;
       var data = $scope.searchBuyers[index];

       if($scope.searchBuyers[index].transit_days==""||$scope.searchBuyers[index].transit_days==null){
         $scope.searchBuyers[index].transit_days_error = true;
         valid=false;
       }else{
         $scope.searchBuyers[index].transit_days_error = false;
       }

        if(data.quotedPrice == "" || data.quotedPrice == null || data.quotedPrice == undefined){
           data.quoted_price_error = true;
           valid = false;
        }else{
            data.quoted_price_error = false;
        }

       if(data.tracking_type == "" || data.tracking_type==null){
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

       if(data.payment_term == 'CREDIT'&&(data.credit_days==""||data.credit_days==null)){
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



     $scope.submitQuotation = function(index){
       if($scope.validated(index)){
          var data = $scope.getQuoteData(index);
          
          $http({
            method: 'POST',
            url: config.serverUrl+'seller-quote-submission',
            headers: {
              'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: data,
          }).then(function(response){
            $scope.searchBuyers[index].quote = response.data.intra_hp_post_quotation;
             $("#initialquotemodel").modal('show');
          }, function(response){
            
          });
       }
     };

     $scope.getQuoteData = function(index){
       var data = $scope.searchBuyers[index];
      
        console.log('$scope.pmccc',data);
        //   debugger;
        var pmethod=[];
        var paymentMethods = ['', 'NEFT_RTGS', 'CREDIT_CARD', 'DEBIT_CARD'];
        $.each(paymentMethods, function(index, value) {
            if($.inArray(value, $scope.pm)!='-1'){
                pmethod.push(index);
            };
        });
      
        pmethod=pmethod.join();
      
        var formData = {
            post_id: data.post.id,
            route_id: data.id,
            buyer_id: data.post.posted_by,
            lkp_service_id: _INTRACITY_,
            quotation_type: data.price_type,
            transit_day: parseInt(data.transit_days),
            tracking_type: data.tracking_type,
            payment_term: data.payment_term,
            credit_days: data.credit_days,
            quotedPrice: parseInt( data.price_type) == 2 ? parseInt(data.firm_price):parseInt(data.quotedPrice),
            payment_method: pmethod,
        };
        console.log("formData::",formData);
        // debugger;
        return formData;
     };
     /*
        quotation code end
     */

    $scope.modifySearch = function(data) {
        $scope.timeslot=TIME_SLOT;
        $scope.data = angular.copy(data);
        console.log("modifySearch::",data);
        console.log("modifySearch::",$scope.data.timeSlot);
        $("#myModal").modal("show");
        if(data.timeSlot) {
            $scope.data.timeSlot = data.timeSlot.split(' ')[0];
        }
        // $scope.data.period = data.timeSlot.split(' ')[1];
        $scope.onSelectModify(data.city);
    };

    $scope.searchModified = function(data) {
        $dataExchanger.request.data = data;
       console.log($dataExchanger.request.data);

        $("#myModal").modal("hide");
        setTimeout(function(){
            $state.reload();
        },1000);

    };

    $scope.onSelect = function(data) {
        var city_id;
        console.log('location', data);
        $scope.data.fromLocation = '';
        $scope.data.toLocation = '';
        var city_id = parseInt(data.id);
        if (typeof(city_id) != NaN || city_id != '') {
            url = serverUrl + 'locations/getlocality/' + city_id;
            apiServices.getLocationByCity(url).then(function(response) {
                $scope.locations = response;
            });
        }
    }

    $scope.onSelectModify = function(data) {
        var city_id;
        console.log('location', data);
        var city_id = parseInt(data.id);
        if (typeof(city_id) != NaN || city_id != '') {
            url = serverUrl + 'locations/getlocality/' + city_id;
            apiServices.getLocationByCity(url).then(function(response) {
                $scope.locations = response;
            });
        }
    }

    $scope.closeModal = function() {
        $("#initialquotemodel").modal('hide');
        setTimeout(function() {
            location.reload();
        }, 2000);
    }

    $scope.acceptOffer=function(value,index)
    {
       $scope.updatedata = {
          id:value.quote.id,
          finalquoteprice:value.finalquoteprice,
          lkp_service_id:value.lkp_service_id
       }
       
      var url = serverUrl + 'hyperlocal/seller-quote-contract-accept';
      apiServices.sellerfinalQuote(url, JSON.stringify($scope.updatedata)).then(function(response) {  
        console.log('responce',$scope.searchBuyers[index]);
        $scope.searchBuyers[index].quote.status = response.payload.status;
      });
    }


    // Lets start sending Message
    $scope.sendMessageModal = function (value) {
        console.log('MESSAGE VALUESSSSSS ID::',value.id);
        $("#id").val("Id:" + value.id)
        $("#from_name").val("From: " + Auth.getUserName());
        $("#to_name").val("To: " + value.post.post_by.username);
        $("#message_subject").val("Ref:- POST:INTRACITY/2017/" + value.fk_buyer_seller_post_id);
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
        $scope.MessageObj.message_to = postDetails.post.post_by.id;
        $scope.MessageObj.message_subject = formData.get('message_subject');
        $scope.MessageObj.message_body = formData.get('message_body');
        $scope.MessageObj.id = postDetails.id;
       
        $scope.MessageObj.lkp_service_id = $dataExchanger.request.serviceId;
        $scope.MessageObj.buyer_quote_item = postDetails.id;
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


    $scope.trackVisit = function(value,index) {
        console.log("Value",value);
        let params = {
            routeId: value.id,
            serviceId:$dataExchanger.request.serviceId,
            roleId:Auth.getUserActiveRole().toLowerCase(),
            type:1 // Buyer Post
        };
        let url = serverUrl+'track';
        
        $("#open_show" + (index+1)).toggle();
        // $(".show_hide").find('i').toggleClass('fa fa-plus fa fa-minus');

        apiServices.postMethod(url,params).then(response=> {
        }).catch();
    }

    $scope.sellerQuoteAction = function(key,action) {
        
        console.log($scope.searchBuyers[key].quote);
        let quote = $scope.searchBuyers[key].quote;
        
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

    $scope.submitFinalQuotation = function(key,status) {
        var finalQuote = $scope.searchBuyers[key].quote;
        let url = serverUrl+'sellerQuoteAction';
        let params = {
            sellerFinalQuotePrice : finalQuote.sellerFinalQuotePrice,
            sellerFinalTransitDay: finalQuote.sellerFinalTransitDay,
            action: status,
            id: finalQuote.id
        };

        apiServices.postMethod(url,params).then(response => {
            location.reload();
        }).catch();
    }

}]);
