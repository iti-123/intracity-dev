app.controller('BuyerBookCtrl',
    ['$scope', '$location', function ($scope, $location) {

        $scope.goToBookNow = function (id) {
            BuyerSearchServices.setPostId(id);
            $location.url('/bluecollar-buyer-service-book');
        };

    }]);
