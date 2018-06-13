app.controller('BuyerCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger', function ($scope, $http, config, apiServices, type_basis, $state, $dataExchanger) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.servicetype = SERVICE_TYPE;
    $scope.data = [];
    $scope.type_basis = type_basis.type_basis;
    $scope.UNIT = ESTIMATED_UNIT;
    $scope.priceTypes = HYPER_PRICE_TYPES;



    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
            console.log($scope.cities);
        });
    }
    //  Get city of intracity 
    $scope.getCity(url);

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

    $('#from_location').keypress(function (e) {
        var keyCode = e.keyCode == 0 ? e.charCode : e.keyCode;
        var ret = ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || 
            (keyCode >= 97 && keyCode <= 122) || (specialKeys.indexOf(e.keyCode) != -1 && e.charCode != e.keyCode));
          return ret;
    });

    $('#to_location').keypress(function (e) {
        var keyCode = e.keyCode == 0 ? e.charCode : e.keyCode;
        var ret = ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || 
            (keyCode >= 97 && keyCode <= 122) || (specialKeys.indexOf(e.keyCode) != -1 && e.charCode != e.keyCode));
          return ret;
    });


    /*** Get Location By Id ***/
    $scope.onSelect = function (data) {
        // 
        console.log(data);
    }
    $scope.data.city_id = { id: '' };
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
            console.log('Locations:', $scope.locations);
        });
    }
    /*** Get Location By Id ***/

    $scope.categories = hyerCatergories;
    console.log('Category',$scope.categories);
    console.log('hyerCatergories',$scope.UNIT);
    $scope.data = {
        title: '',
        type: 1,
        is_fragile: 0,
        location: '',
        depart_date: '',
        from_date: '',
        todate: '',
        service_type: '',
        category: '',
        unit: '',
        quantity: '',
        price_type: '',
        firm_price: '',
        from_location: '',
        to_location: '',
        max_weight: '',
        max_no_parcel: '',
        no_of_trucks:'',
        average_turn_over:'',
        income_tax_assesse:1,
        no_of_years:'',
        term_contract_woc:1,
        quote_date:'',
        validtime:'',
        visibleToSellers:'',
        is_private_public:0,
    };
    $scope.defaultForm = angular.copy($scope.data);
    $scope.formError = {
        max_weight : false,
        quantity: false,
        max_no_parcel: false,
    };

    $scope.multipleLocation = [];

    $scope.addMore = function (multipledata) {
        var isValidated = true;
       
        if ($scope.data.title == '' || typeof($scope.data.title) == 'undefined') {
            $('#title_div').css('border-bottom', '1px solid red');
            $('#title_div').focus();
            isValidated = false;
        }else{
            $('#title_div').css('border-bottom', '');
        }

        if ($scope.data.from_location == '' || typeof($scope.data.from_location) == 'undefined') {
            $('#from_location1').css('border-bottom', '1px solid red');
            $('#from_location1').focus();
            isValidated = false;
        }else{
            $('#from_location1').css('border-bottom', '');
        }
        
        if ($scope.data.to_location == '' || typeof($scope.data.to_location) == 'undefined') {
            $('#to_location1').css('border-bottom', '1px solid red');
            $('#to_location1').focus();
            isValidated = false;
        }else{
            $('#to_location1').css('border-bottom', '');
        }

        if (($scope.data.depart_date == '' || typeof($scope.data.depart_date) == 'undefined') && $scope.data.type == 1) {
            $('#valid_from_date2').css('border-bottom', '1px solid red');
            $('#valid_from_date2').focus();
            isValidated = false;
        }else{
            $('#valid_from_date2').css('border-bottom', '');
        }

        if(($scope.data.from_date == '' || typeof($scope.data.from_date) == 'undefined') && $scope.data.type == 2) {
            $('#fromdate1').css('border-bottom', '1px solid red');
            $('#fromdate1').focus();
            isValidated = false;
        }else{
            $('#fromdate1').css('border-bottom', '');
        }

        if ($scope.data.city_id == null || typeof($scope.data.city_id) == 'undefined' || typeof($scope.data.city_id.id) == 'undefined') {
            $('#city1').css('border-bottom', '1px solid red');
            $('#city1').focus();
            isValidated = false;
        }else{
            $('#city1').css('border-bottom', '');
        }

        if(($scope.data.todate == '' || typeof($scope.data.todate) == 'undefined') && $scope.data.type == 2) {
            $('#todate1').css('border-bottom', '1px solid red');
            $('#todate1').focus();
            isValidated = false;
        }else{
            $('#todate1').css('border-bottom', '');
        }
        
        if(($scope.data.unit == null || $scope.data.unit == '' || typeof($scope.data.unit) == 'undefined') && $scope.data.type == 2) {
            $('#unit1').css('border-bottom','1px solid red');
            $('#unit1').focus();
            isValidated = false;
        }else{
            $('#unit1').css('border-bottom','');
        }
       
        if(($scope.data.quantity == '' || typeof($scope.data.quantity) == 'undefined') && $scope.data.type == 2) {
            $('#quantity1').css('border-bottom','1px solid red');
            $('#quantity1').focus();
            $scope.formError.quantity = false; 
            isValidated = false;
        }else{
            if($scope.data.quantity == 0 && $scope.data.quantity != ''){
              console.log('Abc',$scope.data.quantity);
              $scope.formError.quantity = true;
            }else if($scope.data.type == 1){
              console.log('Ab',$scope.data.quantity);
              $scope.formError.quantity = false;  
            }else{
              $scope.formError.quantity = false;  
            }
            $('#quantity1').css('border-bottom','');
        }
       
        if(($scope.data.price_type == '' || typeof($scope.data.price_type) == 'undefined') && $scope.data.type == 2) {
            $('#price_type1').css('border-bottom','1px solid red');
            $('#price_type1').focus();
            isValidated = false;
        }else{
            if($scope.data.firm_price == 0){
                $scope.formError.firm_price = true;
                isValidated = false;
            }else{
                $scope.formError.firm_price = false;  
            }
            $('#price_type1').css('border-bottom','');
        }
        console.log('Abc',$scope.data.firm_price);
        console.log('Price Type',$scope.data.price_type);
        if(($scope.data.price_type != '' && typeof($scope.data.price_type) != 'undefined') && $scope.data.type == 2){
            if (($scope.data.firm_price == '' || typeof($scope.data.firm_price) == 'undefined') && 
                ($scope.data.type == 2 && $scope.data.price_type.id == 2)) {
                $('#price1').css('border-bottom', '1px solid red');
                $('#price1').focus();
                $scope.formError.firm_price = false;  
                isValidated = false;
            }else{
                if($scope.data.firm_price == 0){
                  $scope.formError.firm_price = true;
                  isValidated = false;
                }else{
                  $scope.formError.firm_price = false;  
                }
                $('#price1').css('border-bottom', '');
            }
        }else{
            $('#price1').css('border-bottom','');
            $scope.formError.firm_price = false;  
        }
        

        if($scope.data.category == '' || typeof($scope.data.category) == 'undefined') {
            $('#category_div').css('border-bottom', '1px solid red');
            $('#category').focus();
            isValidated = false;
        }else{
            $('#category_div').css('border-bottom', '');
        }

        if($scope.data.service_type == '' || typeof($scope.data.service_type) == 'undefined') {
            $('#service_type_div').css('border-bottom', '1px solid red');
            $('#service_type').focus();
            isValidated = false;
        }else{
            $('#service_type_div').css('border-bottom', '');
        }
        
        if($scope.data.max_weight == '' || typeof($scope.data.max_weight) == 'undefined') {
            $('#weight1').css('border-bottom','1px solid red');
            $('#weight1').focus();
            $scope.formError.max_weight = false;  
            isValidated = false;
        }else{
            if($scope.data.max_weight == 0){
              $scope.formError.max_weight = true;
              isValidated = false;
            }else{
              $scope.formError.max_weight = false;  
            }
            $('#weight1').css('border-bottom','');
        }
        
        if($scope.data.max_no_parcel == '' || typeof($scope.data.max_no_parcel) == 'undefined') {
            $('#parcel1').css('border-bottom','1px solid red');
             $scope.formError.max_no_parcel = false;  
            $('#parcel1').focus();
            isValidated = false;
        }else{
            if($scope.data.max_no_parcel == 0){
              $scope.formError.max_no_parcel = true;
              isValidated = false;
            }else{
              $scope.formError.max_no_parcel = false;  
            }
            $('#parcel1').css('border-bottom','');
        }



        console.log('root', $scope.data);
        if (isValidated) {

            var searchdata = $scope.data;
             console.log('searchdatasearchdata',searchdata);
            $scope.tolocationdata = {
                title: searchdata.title ? searchdata.title : '',
                type: searchdata.type ? searchdata.type : '',
                city_id: searchdata.city_id ? searchdata.city_id : '',
                from_location: searchdata.from_location ? searchdata.from_location : '',
                to_location: searchdata.to_location ? searchdata.to_location : '',
                service_type: searchdata.service_type ? searchdata.service_type : '',
                category: searchdata.category ? searchdata.category : '',
                max_weight: searchdata.max_weight ? searchdata.max_weight : '',
                max_no_parcel: searchdata.max_no_parcel ? searchdata.max_no_parcel : '',
                is_fragile: searchdata.is_fragile ? searchdata.is_fragile : '0',
                quantity: searchdata.quantity ? searchdata.quantity : '',
                price_type: searchdata.price_type ? searchdata.price_type : '',
                firm_price: searchdata.firm_price ? searchdata.firm_price : '',
                from_date: searchdata.from_date ? searchdata.from_date : '',
                todate: searchdata.todate ? searchdata.todate : '',
                quote_date: searchdata.quote_date ? searchdata.quote_date : '',
                depart_date: searchdata.depart_date ? searchdata.depart_date : '',
                unit: searchdata.unit ? searchdata.unit : '',
                validtime: searchdata.validtime ? searchdata.validtime : '',
                no_of_trucks: searchdata.no_of_trucks ? searchdata.no_of_trucks : '',
                average_turn_over: searchdata.average_turn_over ? searchdata.average_turn_over : '',
                income_tax_assesse: searchdata.income_tax_assesse ? searchdata.income_tax_assesse : '',
                no_of_years: searchdata.no_of_years ? searchdata.no_of_years : '',
                term_contract_woc: searchdata.term_contract_woc ? searchdata.term_contract_woc : '',
                term_upload_docx: searchdata.term_upload_docx ? searchdata.term_upload_docx : '',
                comment: searchdata.comment ? searchdata.comment : ''

            };
           
            if ($scope.rid === '' || $scope.rid == undefined) {
                $scope.new = angular.copy($scope.tolocationdata);
                $scope.multipleLocation.push($scope.new);
            } else {
                $scope.multipleLocation.splice($scope.rid, 1);
                $scope.new = angular.copy($scope.tolocationdata);
                $scope.multipleLocation.push($scope.new);
                $scope.rid = '';

            }
            console.log('multipleLocationmultipleLocation', $scope.multipleLocation);
            $("#showdata").show();
            searchdata.to_location = "";
            searchdata.max_weight = "";
            searchdata.max_no_parcel = "";
                        
            $scope.formData = angular.copy($scope.data);
            console.log('Form Data',$scope.formData);
            if($scope.data.type == 1){
                $scope.defaultForm.type = 1;
                console.log('Type',$scope.data.type);
            }else{
                $scope.defaultForm.type = 2;
            }
            console.log('Form',$scope.formData);
            $scope.data = angular.copy($scope.defaultForm);
        }

    }

    $scope.removeItem = function (x) {

        $scope.multipleLocation.splice(x, 1);
    }
    $scope.editlocation = function (value, index) {

        $scope.data = value;
        console.log('edit data', $scope.data);
        $scope.rid = index;

    }



    $('#confirm').click(function () {

        if ($scope.multipleLocation.length == 0) {
            alert('Please Add location');
            return false;
        }

        if($scope.new.from_date || $scope.new.todate){
            if(($scope.data.quote_date < $scope.new.from_date || $scope.data.quote_date > $scope.new.todate)){
                alert('Please select last date in between valid from and valid to date.');
                return false;
            }
        }

        if ($('#PostPrivate').is(':checked')) {
            var seller = $('#sellerListTerm').val();
            if (seller == '') {
                isValidated = false;
                alert("Please select atleast one seller");
                return false;
            }
        }


        if ($scope.data.is_accept_terms_cond != true) {
            alert('Accept Terms & Conditions');
            return false;
        }

        $('#valid_from_date').css('border-color', '');
        $('#validtime').css('border-color', '');

         $('#title').css('border-color', '');
        $('#title').css('border-color', '');

        var quote_date = $.trim($('#quote_date').val());
        if (quote_date == '' && $scope.data.type == 2) {
            $('#quote_date').css('border-color', 'red');
            $('#quote_date').focus();
            return false;
        }
        var validtime = $.trim($('#validtime').val());
        if (validtime == '' && $scope.data.type == 2) {
            $('#validtime').css('border-color', 'red');
            $('#validtime').focus();
            return false;
        }

        var searchdata = $scope.formData;
        
        $scope.buyersearch = {
            type: searchdata.type ? searchdata.type : '',
            title: searchdata.title ? searchdata.title : '',
            city_id: searchdata.city_id ? searchdata.city_id : '',
            from_location: searchdata.from_location ? searchdata.from_location : '',
            to_location: searchdata.to_location ? searchdata.to_location : '',
            service_type: searchdata.service_type ? searchdata.service_type : '',
            category: searchdata.category ? searchdata.category : '',
            max_weight: searchdata.max_weight ? searchdata.max_weight : '',
            max_no_parcel: searchdata.max_no_parcel ? searchdata.max_no_parcel : '',
            is_fragile: searchdata.is_fragile ? searchdata.is_fragile : '0',
            quantity: searchdata.quantity ? searchdata.quantity : '',
            price_type: searchdata.price_type ? searchdata.price_type : '',
            firm_price: searchdata.firm_price ? searchdata.firm_price : '',
            from_date: searchdata.from_date ? searchdata.from_date : '',
            todate: searchdata.todate ? searchdata.todate : '',
            is_accept_terms_cond: searchdata.is_accept_terms_cond ? '1' : '0',
            depart_date: searchdata.depart_date ? searchdata.depart_date : '',
            unit: searchdata.unit ? searchdata.unit : '',
            quote_date: $scope.data.quote_date,
            validtime: $scope.data.validtime,
            post_status: 1,
            is_private_public: $scope.data.is_private_public,
            visibleToSellers: $scope.data.visibleToSellers,
            no_of_trucks: $scope.data.no_of_trucks,
            average_turn_over: $scope.data.average_turn_over,
            income_tax_assesse: $scope.data.income_tax_assesse,
            no_of_years: $scope.data.no_of_years,
            term_contract_woc: $scope.data.term_contract_woc,
            term_upload_docx: $scope.data.term_upload_docx,
            comment: $scope.data.comment,
            everyaddlocation: $scope.multipleLocation ? $scope.multipleLocation : '',

        };

        console.log('ALL DATAAAA::', $scope.buyersearch);
         //  return false;
        $.ajax({
            url: serverUrl + 'hyperlocal/buyer-post',
            method: 'POST',
            data: $scope.buyersearch,
            dataType: 'json',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem("access_token")
            }
        }).then(function (response) {
            console.log("Draft Response::", response);
            var tran = response.tran_id;
            var postid = response.postid;

            $scope.uploadBidTermsDocument(postid);
            if (response.isSuccessful) {
                var tran = response.tran_id;
                var str = "Your request for post has been successfully posted to the relevant sellers. Your transacton id is " + tran + " You would be getting the enquiries from the sellers online."
                $(".statusText button").attr("data-type", response.isSuccessful);
                $("#responsetext").html(str);
                $('#QuoteConfirmationPopup').modal({ 'show': true });
            } else {
                $("#responsetext").html("Somthing went wrong please try Again");
                $('#QuoteConfirmationPopup').modal({ 'show': true });
            }
        });

    });

    $('#draft').click(function () {
        if ($scope.multipleLocation.length == 0) {
            alert('Please Add location');
            return false;
        }

        if($scope.new.from_date || $scope.new.todate){
            if(($scope.data.quote_date < $scope.new.from_date || $scope.data.quote_date > $scope.new.todate)){
                alert('Please select last date in between valid from and valid to date.');
                return false;
            }
        }

        if ($scope.data.is_accept_terms_cond != true) {
            alert('Accept Terms & Conditions');

            return false;
        }
        $('#valid_from_date').css('border-color', '');
        $('#validtime').css('border-color', '');

        var quote_date = $.trim($('#quote_date').val());
        if (quote_date == '' && $scope.data.type == 2) {
            $('#quote_date').css('border-color', 'red');
            $('#quote_date').focus();
            return false;
        }
        var validtime = $.trim($('#validtime').val());
        if (validtime == '' && $scope.data.type == 2) {
            $('#validtime').css('border-color', 'red');
            $('#validtime').focus();
            return false;
        }
        var searchdata = $scope.formData;

        $scope.buyersearch = {

           type: searchdata.type ? searchdata.type : '',
            title: searchdata.title ? searchdata.title : '',
            city_id: searchdata.city_id ? searchdata.city_id : '',
            from_location: searchdata.from_location ? searchdata.from_location : '',
            to_location: searchdata.to_location ? searchdata.to_location : '',
            service_type: searchdata.service_type ? searchdata.service_type : '',
            category: searchdata.category ? searchdata.category : '',
            max_weight: searchdata.max_weight ? searchdata.max_weight : '',
            max_no_parcel: searchdata.max_no_parcel ? searchdata.max_no_parcel : '',
            is_fragile: searchdata.is_fragile ? searchdata.is_fragile : '0',
            quantity: searchdata.quantity ? searchdata.quantity : '',
            price_type: searchdata.price_type ? searchdata.price_type : '',
            firm_price: searchdata.firm_price ? searchdata.firm_price : '',
            from_date: searchdata.from_date ? searchdata.from_date : '',
            todate: searchdata.todate ? searchdata.todate : '',
            is_accept_terms_cond: searchdata.is_accept_terms_cond ? '1' : '0',
            depart_date: searchdata.depart_date ? searchdata.depart_date : '',
            unit: searchdata.unit ? searchdata.unit : '',
            quote_date: $scope.data.quote_date,
            validtime: $scope.data.validtime,
            post_status: 0,
            is_private_public: $scope.data.is_private_public,
            visibleToSellers: $scope.data.visibleToSellers,
            no_of_trucks: $scope.data.no_of_trucks,
            average_turn_over: $scope.data.average_turn_over,
            income_tax_assesse: $scope.data.income_tax_assesse,
            no_of_years: $scope.data.no_of_years,
            term_contract_woc: $scope.data.term_contract_woc,
            term_upload_docx: $scope.data.term_upload_docx,
            comment: $scope.data.comment,
            everyaddlocation: $scope.multipleLocation ? $scope.multipleLocation : '',

        };

        $.ajax({
            url: serverUrl + 'hyperlocal/buyer-post',
            method: 'POST',
            data: $scope.buyersearch,
            dataType: 'json',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem("access_token")
            }
        }).then(function (response) {
            console.log("Draft Response::", response);
            var tran = response.tran_id;
            var postid = response.postid;
            $scope.uploadBidTermsDocument(postid);
            if (response.isSuccessful) {
                +sessionStorage.setItem('postDraft',true);
                $state.go("hp-buyer-list");
            } else {
                $("#responsetext").html("Somthing went wrong please try Again");
                $('#QuoteConfirmationPopup').modal({ 'show': true });
            }

            // console.log("success::", response.data.payload.data.id);
        });

    });
    /*------------------------Get All Sellers--------------------------------*/

    apiServices.getAllSellers(serverUrl).then(function (response) {
        $scope.sellerList = response;
        setTimeout(function () {
            $("#sellerList").tokenInput($scope.sellerList, { propertyToSearch: 'username' });
            $("#sellerListTerm").tokenInput($scope.sellerList, { propertyToSearch: 'username' });
        }, 1000);

        // console.log("$scope.sellerList::", $scope.sellerList);
    });

    /*-----------------------------------------------------------------------------------------*/


    $scope.data.is_private_public = false;
    $scope.showHide = function (val) {

        console.log($scope.dataSpot.is_private_public);
        if (val == 'private') {
            $('#token-input-sellerListTerm').show();
            $('#token-input-sellerList').show();
            $scope.isSellerVisible = true;
            $("#sellerList").val('');
            $scope.data.is_private_public = [];

        } else {
            $('li.token-input-token').remove();
            $('#token-input-sellerListTerm').hide();
            $('#token-input-sellerList').hide();
            $scope.dataSpot.visibleToSellers = '';
            $scope.dataSpot.visibleToSellers = [];
            $scope.isSellerVisible = false;
            $("#sellerList").val('');

        }
    };



    $scope.closeConfirmationPopup = function () {
        console.log($(".statusText button").attr("data-type"));
        var st = $(".statusText button").attr("data-type");
        $("#QuoteConfirmationPopup").modal("hide");
        if (st) {
            setTimeout(function () {
                $state.go("hp-buyer-list");
            }, 1000);

        }
    }

    $scope.showHideType = function (str) {
        $scope.data = angular.copy($scope.defaultForm);;
        $scope.multipleLocation = [];
        $scope.data.type = str;
        $scope.data.is_private_public = 0;
        $scope.data.is_fragile = 0;
        $scope.data.is_fragile = 0;
        $scope.data.income_tax_assesse = 1;
        $scope.data.term_contract_woc = 1;

    }

    $scope.setDocument = function (element) {
        // $scope.documentFiles = [];
        $scope.$apply(function (scope) {
            console.log('files:', element.files);
            $scope.documentFiles = element.files[0];
        });
    };

    $scope.bidDocumentFiles = [];
    $scope.setBidTermsDocument = function (element) {
        $scope.$apply(function (scope) {
            //console.log('files:', element.files);
            $scope.bidDocumentFiles.push(element.files[0])
            console.log($scope.bidDocumentFiles);
        });

    };
    /* ----------------------------------*/
    $scope.removeBidDocument = function (index) {

        if ($scope.termUploadDocxs.length > 1) {
            $scope.termUploadDocxs.splice(index, 1);
        }

    }
    /*------addDocx-------*/

    $scope.termUploadDocxs = [{ term_upload_docx: "" }];
    $scope.addDocx = function () {
        
        $scope.termUploadDocxs.push({ term_upload_docx: "" });
    };

    $scope.uploadBidTermsDocument = function (buyerPostTermId) {
        console.log('ccccccccccccccc',$scope.bidDocumentFiles);
        var fd = new FormData();
        for (var i = 0; i < $scope.bidDocumentFiles.length; i++) {
            console.log('xxxxxxxx',$scope.bidDocumentFiles[i]);
            fd.append("uploadFile", $scope.bidDocumentFiles[i]); //file.size > 1024*1024
            fd.append("type", 'bid_term_condition');
            fd.append("buyerPostTermId", buyerPostTermId);
            console.log('fdfdfdfdfdfdfdfdfd',fd);
            apiServices.Documentupload(serverUrl, fd).then(function (response) {
                if (response.isSuccessful == true) {
                    // $scope.buyerQuoteTerm.attributes.bidTermsAndConditionsDocs.push({ documentId: response.payload.id, documentName: response.payload.file_name });

                } else {
                    clearInterval($scope.checkTimeUpload);
                    $('#validateMsgBody').html('Bid documents upload failed.');
                    $('#alertModalValidateSpot').modal('toggle');
                    i = $scope.bidDocumentFiles.length + 1;
                }
            });
        }
    }


}]);