
var app = angular.module('sellerPostmasterSettingSaveApp', []);
app.controller('sellerPostmasterSettingSave', ['$scope', '$http', 'config', 'apiHyperlocalServices', 'type_basis', '$state', 'trackings', function ($scope, $http, config, apiHyperlocalServices, type_basis, $state, trackings) {

    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
	
	
    $scope.data = [];
    $scope.type_basis = type_basis.type_basis;
    $scope.post_Status = POST_STATUS;
    $scope.servicetype = SERVICE_TYPE;
	
	$scope.form-check = function(){
		console.log('oooooo');
	}

/* 	$scope.change = function($event) {
		$timeout(function() {
			angular.element($event.target.form).triggerHandler('submit');
		});
	};	
	
	$scope.postData = function(){
		console.log('tttt');
	}
    // get category
    var getCategoryUrl = serverUrl + 'hyperlocal/product-category';
	
    $scope.getProductCat = function (getCategoryUrl) {
        apiHyperlocalServices.category(getCategoryUrl).then(function (response) {
            $scope.categories = response.data;
            console.log('Categoriesss', $scope.categories);
        });
    }
    //  Get product
    $scope.getProductCat(getCategoryUrl);

    $scope.getCity(url); */

}]);
