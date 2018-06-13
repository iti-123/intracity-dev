app.controller('profileCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger', function ($scope, $http, config, apiServices, type_basis, $state, $dataExchanger) {

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
    
    $scope.getLocationByCity = function (url) {
        apiServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            console.log('Locations:', $scope.locations);
        });
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