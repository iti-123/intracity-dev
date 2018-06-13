//app.controller('BuyerSearchCtrl',function($scope){

app.controller('BuyerSearchCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', function ($scope, $http, config, apiServices, type_basis, $state) {
    $scope.searchFormSubmit = "Search";

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.servicetype = SERVICE_TYPE;
    $scope.materialtype = HYPERLOCAL_MATERIAL_TYPE;
    $scope.weight = HYPERLOCAL_WEIGHT;
    console.log("Service Type:", $scope.servicetype);

    $scope.data = [];
    $scope.type_basis = type_basis.type_basis;

    console.log("URL ::", serverUrl);

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
        if (typeof(city_id) != NaN || city_id != '') {
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
    /*** Get Location By Id ***/


    $scope.StartSearch = function () {

        if (!$("#searchForm").data('bootstrapValidator').validate().isValid()) {

            return false;
        }

        $scope.searchFormSubmit = "Processing...";
        $scope.$apply();

    };
}]);
