app.controller('HpSellerSearch', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger', function ($scope, $http, config, apiServices, type_basis, $state, $dataExchanger) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.data = [];
    $scope.type_basis = type_basis.type_basis;

    var url = serverUrl + 'locations/getCity';
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
            console.log($scope.cities);
        });
    };
    //  Get city of intracity 
    $scope.getCity(url);


    /*** Get Location By Id ***/
    $scope.onSelect = function (data) {
        // 
        console.log(data);
    };
    $scope.data.city_id = {id: ''};
    $scope.onSelect = function (data) {
        console.log("City Id::", parseInt(data.id));
        var city_id = parseInt(data.id);
        if (typeof (city_id) != NaN || city_id != '') {
            $scope.getLocationByCity(serverUrl + 'locations/getlocality/' + city_id);
            // $scope.getListBuyerAccordingFilter(url, city_id);
        }
    };


    $scope.getLocationByCity = function (url) {
        apiServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
            console.log('Locations:', $scope.locations);
        });
    };


    $('#search').click(function () {

        $('#city').css('border-color', '');
        $('#departingDate').css('border-color', '');
        $('#from_location').css('border-color', '');
        $('#to_location').css('border-color', '');


        var isValidated = true;
        var city = $.trim($('#city').val());
        if (city == '') {
            $('#city').css('border-color', 'red');
            $('#city').focus();
            isValidated = false;
            // alert("Please fillup all the field");
        }
        var departingDate = $('#departingDate').val();
        if (departingDate == '') {
            $('#departingDate').css('border-color', 'red');
            $('#departingDate').focus();
            isValidated = false;
            //alert("Please fillup all the field");
        }
        var from_location = $.trim($('#from_location').val());
        if (from_location == '') {
            $('#from_location').css('border-color', 'red');
            $('#from_location').focus();
            isValidated = false;
            //alert("Please fillup all the field");
        }
        var to_location = $.trim($('#to_location').val());
        if (to_location == '') {
            $('#to_location').css('border-color', 'red');
            $('#to_location').focus();
            isValidated = false;
            //alert("Please fillup all the field");
        }

        $dataExchanger.$default({
            request: {
                serviceId: 'SERVICE_ID',
                serviceName: SERVICE_NAME,
                fullName: "",
                imagePath: "",
                data: {}
            }
        });

        if (isValidated) {
            var searchdata = $scope.data;

            $scope.sellersearch = {
                city: searchdata.city_id.id ? searchdata.city_id.id : '',
                date: searchdata.departingDate ? searchdata.departingDate : '',
                from_location: searchdata.from_location ? searchdata.from_location : '',
                to_location: searchdata.to_location ? searchdata.to_location : '',


            };
            $dataExchanger.request.data = $scope.sellersearch;

            console.log("$scope.sellersearch :: ", $scope.sellersearch);
            console.log("DataExchanger :: ", $dataExchanger.request);

            // $.ajax({
            //     url: serverUrl + 'hyperlocal/seller-search-result',
            //     type: "POST",
            //     data:  $scope.sellersearch,
            //    // dataType: 'json',


            //     headers: {
            //         'authorization': 'Bearer ' + localStorage.getItem("access_token")
            //     },    
            // });

            $state.go("hp-seller-search-result");
        }
        else {
            alert("Please fillup all the field");
        }


    })


}]);