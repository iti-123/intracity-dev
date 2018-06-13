app.controller('HpSellerSearchResult', ['$scope', '$http', 'config', 'apiHyperlocalServices','apiServices', 'type_basis', 'trackings', '$state','$dataExchanger', function ($scope, $http, config, apiHyperlocalServices,apiServices, type_basis, trackings, $state,$dataExchanger) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.servicetype = SERVICE_TYPE;
    $scope.filterdata = [];
  var date=new Date();
    // var day=date.getDate();
    // var mon=date.getMonth();
    // var yy=date.getFullYear();

    $scope.currentDate = moment().format("YYYY-MM-dd");
    
    var specialKeys = new Array();
    specialKeys.push(8); //Backspace
    specialKeys.push(9); //Tab
    specialKeys.push(46); //Delete
    specialKeys.push(36); //Home
    specialKeys.push(35); //End
    specialKeys.push(37); //Left
    specialKeys.push(39); //Right

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

    $scope.data = [];
    $scope.tracking_type = trackings.type;
    $scope.type_basis = type_basis.type_basis;
    $scope.sellersearch = $dataExchanger.request.data;
    
    // validation scope and limitation
    $scope.quoted_price_validation_min_limit = 2;
    $scope.quoted_price_validation_max_limit = 8;
    
    $scope.hrToSecMoment = function( param ){

         var post_moment = moment( param, "YYYY-MM-DD HH:mm:ss" ).unix();
         var current_moment = moment().unix();
//        console.log( 'post :' + post_moment ) ;
//        console.log( 'current :' + moment().unix() ) ;
//        console.log( 'diff :' + (post_moment - current_moment) );         
         return current_moment < post_moment ? 1 : 0;
         

        
    }

    var url = serverUrl + 'hyperlocal/hp-buyer-post-search';
    apiHyperlocalServices.hpSellerSearchBuyer(url, JSON.stringify($scope.sellersearch)).then(function(response) {
        $scope.searchResult = response.payload;
        console.log("$scope.searchResult::", response.payload);
    });

    var url = serverUrl + 'getbuyerdetails';
    $scope.getbuyer = function (url) {
        apiHyperlocalServices.getMethod(url).then(function (response) {
            response.push({ 'id': "-1", 'full_name': "All" });
            $scope.users = response;
        });
    }
    $scope.getbuyer(url);


    var url = serverUrl + 'hyperlocal/product-category';
    $scope.getProductCategory = function (url) {
        apiHyperlocalServices.category(url).then(function (response) {
            $scope.categories = response;
        });
    }
    $scope.getProductCategory(url);


        

    $scope.NewCategoryType = [];
    $scope.NewCategoryTypeFilter = function (item) {
        if (item != null) {
            if ($scope.NewCategoryType.length == 0)
                return item;
            else {

                return $scope.NewCategoryType.indexOf(item.post_result.category) !== -1;
            }
        }

    };
    $scope.selectNewCategoryType = function (operator ,obj) {


        if ($scope.filterdata.indexOf(obj) !== -1) {

            $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1)
        }
        else {
            $scope.filterdata.push(obj);

        }
        var num = operator.toString();
        var idx = $scope.NewCategoryType.indexOf(num);
        if (idx > -1)
            $scope.NewCategoryType.splice(idx, 1);
        else
            $scope.NewCategoryType.push(num);
        if ($scope != null)
            $scope.NewCategoryType = $scope.NewCategoryType;
           

    };

    $scope.NewServiceType = [];
    $scope.NewServiceTypeFilter = function (item) {
        if (item != null) {
            if ($scope.NewServiceType.length == 0)
                return item;
            else {

                return $scope.NewServiceType.indexOf(item.post_result.servicetype) !== -1;
            }
        }

    };
    $scope.selectNewServiceType = function (operator,obj) {

        if ($scope.filterdata.indexOf(obj) !== -1) {

            $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1)
        }
        else {
            $scope.filterdata.push(obj);

        }
        var num = operator.toString();
        var idx = $scope.NewServiceType.indexOf(num);
        if (idx > -1)
            $scope.NewServiceType.splice(idx, 1);
        else
            $scope.NewServiceType.push(num);
        if ($scope != null)
            $scope.NewServiceType = $scope.NewServiceType;

    };


    $scope.NewBuyerType = [];
    $scope.NewBuyerFilter = function (item) {
        if (item != null) {
            if ($scope.NewBuyerType.length == 0)
                return item;
            else {

                return $scope.NewBuyerType.indexOf(item.post_result.post_by.id) !== -1;
            }
        }

    };
    $scope.selectNewBuyerType = function (operator,obj) {

         if ($scope.filterdata.indexOf(obj) !== -1) {

            $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1)
        }
        else {
            $scope.filterdata.push(obj);

        }
        //alert(operator);
        var num = operator.toString();
        var idx = $scope.NewBuyerType.indexOf(num);
        if (idx > -1)
            $scope.NewBuyerType.splice(idx, 1);
        else
            $scope.NewBuyerType.push(num);
        if ($scope != null)
            $scope.NewBuyerType = $scope.NewBuyerType;

    };
    $scope.removefilter = function (index, obj) {
        if (obj.hasOwnProperty('full_name')) {

            uncheckid = obj.id;
            $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1);
            
        }
        else if (obj.hasOwnProperty('name')) {
            $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1);
           
            uncheckid = obj.id

        }
        else if (obj.hasOwnProperty('value')) {

           $scope.filterdata.splice($scope.filterdata.indexOf(obj), 1);
           
            uncheckid = obj.id


        }

        $("#vl" + uncheckid).prop('checked', false);
        setTimeout(function () {

        }, 100);
          $("#v2" + uncheckid).prop('checked', false);
        setTimeout(function () {

        }, 100);
    };
      
      $scope.clearall = function () {
        $scope.filterdata = [];
        $('input:checkbox').prop('checked', false);
       
      }



    $scope.modifySearch = function(data) {
          console.log("City extra Id::",parseInt(data.city.id));
        var city_id = parseInt(data.city.id);
        if (typeof (city_id) != NaN || city_id != '') {
            $scope.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id);
        }
        $scope.data = angular.copy(data);
        $("#myModal").modal("show");
    }

  


    $scope.searchModified = function(data) {
        $('#city').css('border-color', '');
        $('#valid_to_date').css('border-color', '');
        $('#fromLocation').css('border-color', '');
        $('#toLocation').css('border-color', '');


         var isValidated = true;
        var city = $.trim($('#city').val());
        if (city == '') {
            $('#city').css('border-color', 'red');
            $('#city').focus();
            isValidated = false;
            // alert("Please fillup all the field");
        }
        var valid_to_date = $('#valid_to_date').val();
        if (valid_to_date == '') {
            $('#valid_to_date').css('border-color', 'red');
            $('#valid_to_date').focus();
            isValidated = false;
            //alert("Please fillup all the field");
        }
        var fromLocation = $.trim($('#fromLocation').val());
        if (fromLocation == '') {
            $('#fromLocation').css('border-color', 'red');
            $('#fromLocation').focus();
            isValidated = false;
            //alert("Please fillup all the field");
        }
        var toLocation = $.trim($('#toLocation').val());
       
            if (toLocation == '') {
                $('#toLocation').css('border-color', 'red');
                $('#toLocation').focus();
                isValidated = false;
                //alert("Please fillup all the field");
            }
      
        if(isValidated){
        console.log('modifySearch',data);
        $scope.sellersearch = data;
        $dataExchanger.request.data = data;
        $("#myModal").modal("hide");
        setTimeout(function(){
            $state.reload();
        },1000);
        }else{
            alert('Please fill all the field')
        }

    }

    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiHyperlocalServices.city(url).then(function (response) {
            $scope.cities = response;
        });
    }

    $scope.getCity(url);

    $scope.onSelect = function (data) {
        console.log('dada',data);
    }
    $scope.data.city_id = { id: '' };
    $scope.onSelect = function (data) {
        console.log("City Id::", parseInt(data.id));
        var city_id = parseInt(data.id);
        if (typeof (city_id) != NaN || city_id != '') {
            $scope.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id);
        }
    }


    $scope.getLocationByCity = function (url) {
        apiHyperlocalServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            console.log('Locations:', $scope.locations);
        });
    }

    $scope.paymentMethod = function (index, method) {
        if($scope.searchResult[index].payment_method==method){
            $scope.searchResult[index].payment_method = '';

        }else {
            $scope.searchResult[index].payment_method = method;
        }
    }

    $scope.validated = function(index){
       var valid = true;
       var data = $scope.searchResult[index];
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

     $scope.submitFinalQuotation = function(key,status) {
        var finalQuote = $scope.searchResult[key].quote;
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

    $scope.submitQuotation = function(index){
        var data = $scope.searchResult[index];
        if(data.quotedPrice == 0){
          alert("Please enter spot quote price greater than 0.");
          return false;
        }
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
            if(response.data.success){
               // $scope.searchResult[index].quote = response.data.intra_hp_post_quotation;
                $("#initialquotemodel").modal('show');
            }else{
                $("#initialquoteerrormodel").modal('show');
            }
          }, function(response){
            console.log(response);
          });
        }
     };

     $scope.sellerQuoteAction = function(key,action) {
        
        console.log($scope.searchResult[key].quote);
        let quote = $scope.searchResult[key].quote;
        
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

     $scope.termsubmitQuotation = function(index){
          var data = $scope.searchResult[index];
          console.log('Post Price',data);
          if(!data.quotedPrice){
              alert("Please enter term quote price.");
              return false;
          }else if(data.price_type == 2){
            if(data.quotedPrice != data.firm_price){
              alert("Please enter term quote price equal to firm price amount.");
              return false;
            }
          } else if(data.price_type == 1){
            if(data.quotedPrice == 0){
              alert("Please enter term quote price greater than 0.");
              return false;
            }
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
     
    $scope.closeQuoteModel = function() {
        $("#initialquotemodel").modal('hide');
        location.reload();
    }

   $scope.getQuoteData = function(index){
       var data = $scope.searchResult[index];
       console.log('datadatadata',data);
       console.log('QUOTE DATA',data.post_result.posted_by.id);
      
    //    debugger;
       var paymentMethods = ['', 'NEFT_RTGS', 'CREDIT_CARD', 'DEBIT_CARD'];
       var formData = {
         route_id: data.id,
         post_id: data.post_result.id,
         buyer_id: data.post_result.post_by.id,
         lkp_service_id: _HYPERLOCAL_,
         quotation_type: data.price_type,
         transit_day: data.transit_days==''?'':data.transit_days,
         tracking_type: data.tracking_type==''? 2:data.tracking_type,
         payment_term: data.payment_term,
         credit_days: data.credit_days,
         firm_price: data.price_type == 2 ? data.firm_price: data.quotedPrice,
         lead_type:data.post_result.lead_type,
         quotedPrice:data.quotedPrice=='' ? '':data.quotedPrice,
         payment_method: paymentMethods.indexOf(data.payment_method)
       };
        console.log("formData::",formData);
       return formData;
     }; 


     $scope.sendMessageModal = function (value) {
        console.log('MESSAGEEE VAALUESSSSSS',value);
        $("#from_name").val("From: " + Auth.getUserName());
        $("#to_name").val("To: " + value.post_result.post_by.username);
        $("#message_subject").val("Ref:- POST:" + value.post_result.post_transaction_id);
        $("#quoteObject").val(JSON.stringify(value));
        $scope.MessageObj.id = value.id;
    };   

    $scope.MessageObj = {
        "id": "",
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

    $scope.MessageReplyObj = {
        "id": "",
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
        "files": ""

    };

    $scope.MessageSearchObj = {
        "message_services": _HYPERLOCAL_,
        "message_type": "",
        "message_keywords": "",
        "from_date": "",
        "to_date": ""
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
        $scope.MessageObj.message_to = postDetails.post_result.post_by.id;
        $scope.MessageObj.message_subject = formData.get('message_subject');
        $scope.MessageObj.message_body = formData.get('message_body');

        $scope.MessageObj.lkp_service_id = _HYPERLOCAL_;
        //$scope.MessageObj.buyer_quote_item = postDetails.id;
       // $scope.MessageObj.buyer_quote = postDetails.seller_post_id;
        //console.log("$scope.MessageObj.message_body::", $scope.MessageObj.message_body);
        $("#message_body").css("border-color", "#e2e2e2");
        if ($scope.MessageObj.message_body == '') {
            $("#message_body").css("border-color", "red");
            return false;
        }
        $scope.status = false;
        if (attachedFile != '') {
            $scope.attachedFile.file = attachedFile;
            apiHyperlocalServices.PostMessage($scope.MessageObj).then(function (response) {

                if (response.isSuccessful == true) {
                    // $scope.files = {documentId: response.payload.id, documentName: response.payload.file_name};
                    $scope.attachedFile.userMessageId = response.payload.id;
                    //console.log("HELLO WITH DOCUMENT", $scope.MessageObj.userMessageId);
                    //console.log("NEW MESSAGE OBJECT ", $scope.MessageObj);
                    apiHyperlocalServices.uploadDocument($scope.attachedFile).then(function (response) {
                        //console.log(response.payload);
                        $scope.messages = response.payload;
                        $scope.status = true;
                        $("#status").html("Message sent Successfully").addClass("text-success");
                        $("#messageModallll").modal('hide');
                        setTimeout(function () {
                            $state.reload();
                        }, 1000);


                    })
                }
            });
        } else {
            //console.log("$scope.MessageObj ::", $scope.MessageObj);
            apiHyperlocalServices.PostMessage($scope.MessageObj).then(function (response) {
                $scope.MessageObj.docId = "";
                //console.log(response.payload);

                $scope.messages = response.payload;
                $("#status").html("Message sent Successfully").addClass("text-success");
                setTimeout(function () {
                    $("#messageModallll").modal('hide');
                }, 1000);
            });
        }
    };
    
    $scope.isChecked=function(index){
        var isChecked=$scope.searchResult[index].isChecked;
         $scope.searchResult[index].isDisable=!isChecked;
       
    }
    $scope.termisChecked=function(index){
      
        var isChecked=$scope.searchResult[index].termisChecked;
       
         $scope.searchResult[index].termisDisable=!isChecked;
       
    }
    $scope.finalquoteSeller=function(value,index)
    {
         console.log(value);
         //console.log('index',index);
         $scope.updatedata={
            id:value.quote.id,
            finalquoteprice:value.finalquoteprice,
            lkp_service_id:value.lkp_service_id
         }
            var url = serverUrl + 'hyperlocal/seller-update-quote-final';
            apiServices.sellerfinalQuote(url, JSON.stringify($scope.updatedata)).then(function(response) {
               

                if(response.isSuccessfull)
                {
                    console.log('responce',$scope.searchResult[index]);
                    $scope.searchResult[index].quote.status=response.payload.status;
                    $scope.searchResult[index].quote.seller_quote_price=response.payload.seller_quote_price;
                }
                
            });
    }

    $scope.acceptOffer=function(value,index)
    {
         $scope.updatedata={
            id:value.quote.id,
            finalquoteprice:value.finalquoteprice,
            lkp_service_id:value.lkp_service_id
         }
    
        var url = serverUrl + 'hyperlocal/seller-quote-contract-accept';
        apiServices.sellerfinalQuote(url, JSON.stringify($scope.updatedata)).then(function(response) {
            if(response.isSuccessfull)
            {
                console.log('responce',$scope.searchResult[index]);
                $scope.searchResult[index].quote.status=response.payload.status;
            }
        });
    }
   
    
}]);