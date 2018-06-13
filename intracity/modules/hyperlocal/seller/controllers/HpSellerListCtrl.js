app.controller('HpSellerListCtrl', ['$scope', '$http', 'config', 'apiHyperlocalServices', 'type_basis', 'discount', '$state', '$location', 'apiServices','$dataExchanger','trackings','$window',function ($scope, $http, config, apiHyperlocalServices, type_basis, discount, $state, $location, apiServices,$dataExchanger,trackings,$window) {
    $scope.searchFormSubmit = "Search";

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    var item = null;
    var usr = null;
    $scope.servicetype = SERVICE_TYPE;
    $scope.materialtype = HYPERLOCAL_MATERIAL_TYPE;
    $scope.transitHour = HP_TRANSIT_HOUR;
    $scope.weight = HYPERLOCAL_WEIGHT;
    $scope.discount_list = discount.discount_type;
    $scope.servicetype = SERVICE_TYPE;
    $scope.post_Status = POST_STATUS;

    $scope.tracking_type = trackings.type;
    $scope.categories = hyerCatergories;
    $scope.filClearValue = false;
    $scope.excel_download_path = $location.host()+'/intracity-dev/intracity';
    // download sample excel file method
    $scope.downloadTemplate = function(){
        var anchor = angular.element('<a/>');
        anchor.attr({
            href: 'data:attachment/csv;charset=utf-8,',
            download: 'SellerRateCardWithDiscount.xlsx'
        })[0].click();        
    }
    
    $scope.postDraft = $window.sessionStorage.getItem('postDraft');

    setTimeout(function () {
      $scope.$apply(function() {
        $scope.postDraft = false;
        sessionStorage.removeItem('postDraft');
       });
    },8000);
    
    $scope.setting_data = {
        seller_spot_enquiries_related: true,
        seller_spot_enquiries_partly_related: false,
        seller_spot_enquiries_un_related: false,

        seller_spot_lead_related: true,
        seller_spot_lead_partly_related: false,
        seller_spot_lead_un_related: false,

        seller_term_enquiries_related: true,
        seller_term_enquiries_partly_related: false,
        seller_term_enquiries_un_related: false,

        seller_term_lead_related: true,
        seller_term_lead_partly_related: false,
        seller_term_lead_un_related: false,

        user_pk: Auth.getUserID(),
        user_id: Auth.getUserID(),
        user_type: 2, // flag ( 2 = 'seller', 1 = 'buyer' )
        role_id: 2,
        service_id: _HYPERLOCAL_,
        page_type: 88,
        setting_type: 0, // flag ( 0 = 'post_master_setting', 1 = 'notification_setting' )
        updated_by: Auth.getUserID()
    };

    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiHyperlocalServices.city(url).then(function (response) {
                $scope.cities = response;
                item = response;
        });
    }
    $scope.getCity(url);

    var buyerurl = serverUrl + 'getallbuyer';
    apiServices.getMethod(buyerurl).then(function (response) {
        $scope.users = response.payload;
        usr = response.payload;
    });

    $scope.filterData = {
        city: [],
        service_type: [],
        type: [],
        usernames: [],       
        from_date: [],
        to_date: [],
        pageLoader : 5,
        pageNextValueCount : 1
    };

    $scope.checkedFilterData = function (arr, value) {
        var index = arr.indexOf(value);
        if (index == -1) {
            return false;
        }
        return true;
    }
    
    $scope.addFilterData = function (arr,value) {
        $scope.filClearValue = true;
        var index = arr.indexOf(value);
        if (index == -1) {
            arr.push(value);
        } else {
           arr.splice(index,1);
           if($scope.filterData.city.length < 1 && $scope.filterData.username.length < 1 && $scope.filterData.city == '' && $scope.filterData.post_type.length < 1 
            && $scope.filterData.from_date == '' && $scope.filterData.to_date == ''){
                $scope.filClearValue = false;
           }
        }
    }

    $scope.getCity = function (id) {
        for (let v of item) {
            if (v.id == id) {
                return v.city_name;
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

    $scope.getUsername = function (id) {
        for (let v of usr) {
            if (v.id == id) {
                return v.username;
            }
        }
    }

    $scope.removeElem = function (arr, index) {
        arr.splice(index, 1);
        if($scope.filterData.city.length < 1 && $scope.filterData.service_type.length < 1 && $scope.filterData.usernames < 1 
            && $scope.filterData.from_date == '' && $scope.filterData.to_date == ''){
            $scope.filClearValue = false;
        }
    };

    $scope.$watch('filterData', function (newValue, oldValue) {
        $http({
            url: serverUrl + 'hyperlocal/seller-list',
            method: 'POST',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: $scope.filterData,
        }).then(function success(response) {
            if (response.data.status == 'success') {
                $scope.results = response.data.payload.data;
            }
        }, function error(response) {
            $scope.spinnerOperator = false;

        });
    }, true);
    
    $scope.search_filter = function (type) {
        $scope.show = '';
        $scope.filterData.pageLoader = 5;
        $scope.filterData.pageNextValueCount = 1;
        if(type == 'all'){
           $scope.filterData.type = 'all';
        } else if(type == 'Inbound'){
           $scope.filterData.type = 'Inbound';
        } else if(type == 'Outbound'){
          $scope.filterData.type = 'Outbound';  
        } else if(type == 'public'){
           $scope.filterData.type = 'public'; 
        } else if(type == 'private'){
           $scope.filterData.type = 'private'; 
        }
    };
    $scope.search_filter('all');

    $scope.search_filters = function (type) {
        $scope.show = '';
        if(type == 'all'){
           $scope.filterData.type = 'all';
        } else if(type == 'Inbound'){
           $scope.filterData.type = 'Inbound';
        } else if(type == 'Outbound'){
          $scope.filterData.type = 'Outbound';  
        } else if(type == 'public'){
           $scope.filterData.type = 'public'; 
        } else if(type == 'private'){
           $scope.filterData.type = 'private'; 
        }
    };

    $(window).scroll(function () {
       if ($(window).scrollTop()) {
           $scope.filterData.pageLoader = $scope.filterData.pageLoader + $scope.filterData.pageNextValueCount;
           $scope.search_filters($scope.filterData.type);
           $scope.$apply();
       }
    });

    $scope.clearAll = function (){
        $scope.filClearValue = false;
        $scope.filterData = {
            city: [],
            service_type: [],
            type: [],
            usernames: [],       
            from_date: [],
            to_date: [],
            pageLoader : 5,
            pageNextValueCount : 1  
        };
    }

    $scope.goToLink = function(value) {
        if(value.post_status == 1){
            $location.path('/hyperlocal-seller-post-details/' + value.id);
        }else if(value.post_status == 0){
            $location.path('/hp-seller-drafts-rate-card/' + value.id);
        }
    };

    $scope.inboundResult = function () {        
        var url = serverUrl + 'hyperlocal/hp-get-seller-inbound-details';
      //  $scope.filterData.title = title1;
        apiHyperlocalServices.BuyerInboundDetails(url, $scope.filterData).then(function (response) {
            $scope.filterData.totalRow = response.data.length;
            //console.log('row',response);
            $scope.listdata = response.data;
            for (let key in $scope.listdata) {
                $scope.listdata[key].title=$scope.filterData.title;                
            } 
            
            //console.log("$scope.listdata",$scope.listdata);
        })
    }

    // $scope.$watch('filterData', function (newValue, oldValue) {      
    //     $scope.inboundResult();   
    // }, true);

    $scope.save_setting = function () {
        $http({
            url: serverUrl + 'hyperlocal/seller-setting-save',
            method: 'POST',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: $scope.setting_data,
        }).then(function success(response) {
          
        }).catch(function (error) {
            apiServices.errorHandeler(error);
            $('#loaderGif').hide();
        });
    }

    $scope.type_basis = type_basis.type_basis;

   // $scope.data.city_id = { id: '' };
    $scope.onSelect = function (data) {
        var city_id = parseInt(data.id);
        if (typeof (city_id) != NaN || city_id != '') {
            $scope.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id);
        }
    }


    $scope.getLocationByCity = function (url) {
        apiHyperlocalServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            //console.log('Locations:', $scope.locations);
        });
    }
    /*** Get Location By Id ***/

    /**Seller post List */
    var url = serverUrl + 'hyperlocal/seller-list';
    $scope.getSellerPostList = function (url) {
        apiHyperlocalServices.sellerPost(url).then(function (response) {
            $scope.results = response;

        });
    }
   // $scope.getSellerPostList(url);


    /**Seller post count */
    var url = serverUrl + 'hyperlocal/seller-list-counts';
    $scope.getSellerPostCount = function (url) {
        apiHyperlocalServices.sellerCounts(url).then(function (response) {
            $scope.postCount = response.payload;
        });
    }
    $scope.getSellerPostCount(url);

    /************************For Active Class On Click Filters************/
    $scope.changeClass = function (type) {
        $('.all,.public,.private,.Inbound,.Outbound').removeClass('search_nav_active');
        $('.' + type.post_type).addClass("search_nav_active");
        $('.' + type.bound).addClass("search_nav_active");

    };
    /************************For Active Class On Click Filters************/

    /**Seller post count */
    $scope.selectdata = { post_type: 'public', bound: 'Outbound' };

    $scope.getInboundData = function(data) {
        var url = serverUrl + 'hyperlocal/hp-get-all-records-inbound';
        apiHyperlocalServices.InboundFilter(url, data).then(function (response) {
            $scope.spotsList = response.data;
            $scope.spotsList.totalLeadCount = 0;
            $scope.spotsList.forEach(function(element) {
                $scope.spotsList.totalLeadCount = $scope.spotsList.totalLeadCount+ element.data.length;
            }, this);
        });
        $scope.changeClass($scope.selectdata);
    }

    // $scope.search_filter = function (type) {

    //     switch (type) {
    //         case 'all':
    //             $scope.selectdata.post_type = 'all'
    //             break;
    //         case 'public':
    //             $scope.selectdata.post_type = 'public'
    //             break;
    //         case 'private':
    //             $scope.selectdata.post_type = 'private'
    //             break;
    //         case 'Inbound':
    //             $scope.selectdata.bound = 'Inbound'
    //             break;
    //         case 'Outbound':
    //             $scope.selectdata.bound = 'Outbound'
    //             break;
    //         default:
    //             $scope.selectdata = { post_type: 'public', bound: 'Outbound' };
    //     }


    //     $scope.filterdata = type;
    //     if ($scope.selectdata.bound == 'Inbound') {
    //         $scope.getInboundData($scope.selectdata);
    //     }

    //     if ($scope.selectdata.bound == 'Outbound') {
    //         $scope.show = '';
    //         var url = serverUrl + 'hyperlocal/seller-search-filters';
    //         apiHyperlocalServices.sellerListAcdngFilter(url, $scope.selectdata).then(function (response) {
    //             $scope.results = response.payload;
    //             $scope.changeClass($scope.selectdata);
    //         });
    //     }
    // }
    // $scope.getInboundData($scope.selectdata);
  //  $scope.search_filter('all');

    /**
     * filter accoriding city
     */
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
    // to use 1
    $scope.selectCity = function (operator) {
        var num = operator.toString();
        var idx = $scope.Newcity.indexOf(num);
        if (idx > -1)
            $scope.Newcity.splice(idx, 1);
        else
            $scope.Newcity.push(num);
        if ($scope != null)
            $scope.Newcity = $scope.Newcity;

    };

    /**
     * filter accoriding city
     */

    /************************ Vehicle type filter **********/
    $scope.NewServiceType = [];
    $scope.NewServiceTypeFilter = function (item) {
        if (item != null) {
            if ($scope.NewServiceType.length == 0)
                return item;
            else {

                return $scope.NewServiceType.indexOf(item.service_type) !== -1;
            }
        }

    };
    // to do 2
    $scope.selectNewServiceType = function (operator) {
        var num = operator.toString();
        var idx = $scope.NewServiceType.indexOf(num);
        if (idx > -1)
            $scope.NewServiceType.splice(idx, 1);
        else
            $scope.NewServiceType.push(num);
        if ($scope != null)
            $scope.NewServiceType = $scope.NewServiceType;

    };

    /************************Buyer filter **********/

    $scope.NewBuyers = [];
    $scope.NewBuyersFilter = function (item) {
        if (item != null) {
            if ($scope.NewBuyers.length == 0)
                return item;
            else {
                //console.log('FILTERSSSSSS', item.assign_buyer);
                return $scope.NewBuyers.indexOf(item.assign_buyer) !== -1;

            }
        }

    };
    
    // to do 3
    $scope.selectBuyer = function (operator) {
        console.log('operator', operator);
        console.log('NewBuyers', $scope.NewBuyers);         
         
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

    $scope.NewPostCategory = [];
    $scope.NewCategoryFilter = function (item) {
        if (item != null) {
            if ($scope.NewPostCategory.length == 0)
                return item;
            else {
                return $scope.NewPostCategory.indexOf(item.category.toString()) !== -1;
            }
        }

    };

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
    
    $scope.getData = function (value) {
        $dataExchanger.request.data = value;
    }

    $scope.paymentMethod = function (key,listKey, method) {
        if($scope.listdata[listKey].get_all_route[key].payment_method==method){
            $scope.listdata[listKey].get_all_route[key].payment_method = '';

        }else {
            $scope.listdata[listKey].get_all_route[key].payment_method = method;
        }
    }

     $scope.submitFinalQuotation = function(key,listKey,status) {
         var finalQuote = $scope.listdata[listKey].get_all_route[key].quote;
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

     $scope.sellerQuoteAction = function(key,listKey,action) {
        
        console.log($scope.listdata[listKey].get_all_route[key].quote);
        let quote =$scope.listdata[listKey].get_all_route[key].quote;
        
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
        if($scope.validated(listKey,key)){
           var data = $scope.getQuoteData(listKey,key);
           $http({
             method: 'POST',
             url: config.serverUrl+'seller-quote-submission',
             headers: {
               'authorization': 'Bearer ' + localStorage.getItem("access_token")
             },
             data: data,
           }).then(function(response){
            $scope.listdata[listKey].get_all_route[key].quote = response.data.intra_hp_post_quotation;
           }, function(response){
             console.log(response);
           });
        }
      };

      $scope.getQuoteData = function(listKey,key){
        var data = $scope.listdata[listKey].get_all_route[key];
        console.log(data);
        var paymentMethods = ['', 'NEFT_RTGS', 'CREDIT_CARD', 'DEBIT_CARD'];
        var formData = {
          route_id: data.id,
          post_id: data.fk_buyer_seller_post_id,
          buyer_id: Auth.getUserID(),
          lkp_service_id: _HYPERLOCAL_,
          quotation_type: data.price_type,
          transit_day: data.transit_days==''?'':data.transit_days,
          tracking_type: data.tracking_type==''? 2:data.tracking_type,
          payment_term: data.payment_term,
          credit_days: data.credit_days,
          firm_price: data.price_type == 2? data.firm_price: data.quotedPrice,
         
          payment_method: paymentMethods.indexOf(data.payment_method)
        };
        
        console.log("formData::",formData);
        
        return formData;
      }; 
        
        $scope.setTaskFiles = function (element, fileType) {
            $scope.$apply(function (scope) {
                console.log('files:', element.files);
                // Turn the FileList object into an Array
                $scope.taskFile = element.files[0]

            });
        };  
        
        $scope.uploadExcel = false;
        $scope.uploadFileRecord = function () {
            $('.loaderGif').show();
            var fd = new FormData();
            fd.append("uploadFile", $scope.taskFile);//file.size > 1024*1024

            var filetype = "task"
            
            let url = serverUrl+'hyperlocal/sellerExcelUpload';
            
            apiServices.uploadFile(url, fd).then(function (response) {
                console.log(response);
                $('.loaderGif').hide();
				



                // no record check start here

                if ( response.no_record_flag ) {

                    var htm = '<p style="color:red;"><center>File Upload Failed</center></p>';

                    var no_record_report = JSON.parse( response.no_record_data );



                    no_record_report.map(function(current_value, index, element) {

                            if( current_value.page == '1' ){

                                    htm += '<p style="color:red;"> MASTER PAGE : </p>';
                                    htm += '<p style="color:red;"><center>'+ current_value.error_msg +'</center></p>';

                            }else if( current_value.page == '2' ){

                                    htm += '<p style="color:red;"> RATE CARD PAGE : </p>';
                                    htm += '<p style="color:red;"><center>'+ current_value.error_msg +'</center></p>';

                            }else{

                                    htm += '<p style="color:red;"> DISCOUNT PAGE : </p>';
                                    htm += '<p style="color:red;"><center>'+ current_value.error_msg +'</center></p>';
                            }

                    });


                    $('#uploadModalBody').html(htm);
                    //$('#btnConfirmOK').hide();
                    $('#uploadMsgModal').modal('toggle');
                    $scope.uploadExcel = false;

                }
                // no record check enb here





                if (response.isSuccessful) {

                    $('#uploadModalBody').html('<p>File Uploaded Successfully</p>');
                    $('#modal-footer').html('');
                    $('#uploadMsgModal').modal('toggle');
                    $scope.uploadExcel = true;
                } else {

                    var htm = '<p style="color:red;">File Upload Failed</p>';
                    var error_report = JSON.parse(response.payload);

                    function loopEachRow(idx, param) {
                        var arr = tempjson = [];
                        var param = Object.values(param);

                        if(idx ==  1){
                                param.map(function(current_value, index, element) {
                                        for(var index in current_value) {

                                                arr.push(current_value[index]);
                                        }
                                });
                                        arr.forEach(function(el,key){

                                                htm += '<p>Row no.' + index + ' : ' +  el + ' </p>';	
                                        });									


                        }
                        if(idx ==  2 || idx ==  3){
                                param.map(function(current_value, index, element) {	

                                        var arr = Object.values(current_value);
                                          for(var index in current_value) { }
                                         
                                            arr.forEach(function(el,key){  

                                                    htm += '<p>Row no.' + index + ' : ' +  el + ' </p>';	
                                                });
                                });									
                        }

                    }

                    error_report.map(function(current_value, index, element) {
//                        console.log(current_value);
               for(var index in current_value) {

                    if(index==1){

                            htm += '<p style="color:red;"> MASTER PAGE </p>';

                                            loopEachRow(index, current_value);
                    }

                    if(index==2){

                            htm += '<p style="color:red;"> RATE CARD PAGE </p>';

                                            loopEachRow(index, current_value);
                    }

                    if(index==3){

                            htm += '<p style="color:red;"> DISCOUNT PAGE </p>';

                                            loopEachRow(index, current_value);
                            break;							
                    }}

                    });						


                    $('#uploadModalBody').html(htm);
                    //$('#btnConfirmOK').hide();                    
                    $('#uploadMsgModal').modal('toggle');
                    $scope.uploadExcel = false;
                }                                

            });

        };         
        
        

}]);
