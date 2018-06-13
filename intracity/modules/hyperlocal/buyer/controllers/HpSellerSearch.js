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
    }
    //  Get city of intracity 
    $scope.getCity(url);


    /*** Get Location By Id ***/
    $scope.onSelect = function (data) {
        
    }
    $scope.data.city_id = {id: ''};
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


    $('#search').click(function () {

        var isValidated = true;
        var city = $.trim($('#city').val());
        if (city == '') {
           $('#mcity').css('color', 'red');
            $('#city').focus();
            isValidated = false;
            return false
        }
        $('#mcity').css('color', '');
        var departingDate = $('#departingDate').val();
        if (departingDate == '') {
            $('#mdepartingDate').css('color', 'red');
            $('#departingDate').focus();
            
         return false   
        }
        $('#mdepartingDate').css('color', '');

        var from_location = $.trim($('#from_location').val());
        if (from_location == '') {
            $('#mfrom_location').css('color', 'red');
            $('#from_location').focus();
            isValidated = false;
            return false
        }
        $('#mfrom_location').css('color', '');
        $('#from_location').css('color', '');
        var to_location = $.trim($('#to_location').val());
        if (to_location == '') {
            $('#mto_location').css('color', 'red');
            $('#to_location').focus();
            isValidated = false;
     return false
        }
$('#mto_location').css('color', '');
       
if(from_location==to_location){
  $('#mto_location').css('color', 'red');
  return false;  
}
$('#mto_location').css('color', '');

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
                type:searchdata.type,

            };
            $dataExchanger.request.data = $scope.sellersearch;

            $state.go("hp-seller-search-results");
        }
        else {
          //  alert("Please fillup all the field");
        }


    })


}]);