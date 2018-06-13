app.controller('MessageDetailCtrl', ['$scope', '$http', 'config', 'messageServices', '$dataExchanger', '$state', 'apiServices', function ($scope, $http, config, messageServices, $dataExchanger, $state, apiServices) {

    var serverUrl = config.serverUrl;
    $scope.sellerList = [];

    messageServices.MessageType().then(function (response) {
        $scope.MessageType = response.payload;
        console.log("$scope.MessageType::", $scope.MessageType);
    });

    messageServices.loadAllUsers().then(function (response) {
        $scope.buyerList = response.payload;
        console.log("$scope.buyerList ::", $scope.buyerList);
        setTimeout(function () {
            $("#message_to").tokenInput($scope.buyerList, {propertyToSearch: 'username'});
            $("#n_message_to").tokenInput($scope.buyerList, {propertyToSearch: 'username'});
        }, 1000);

    });
    $scope.getMessageObj = {
        "user_id": Auth.getUserID(),
        "serviceId": $dataExchanger.request.serviceId,
    };

    messageServices.getMessage(JSON.stringify($scope.getMessageObj)).then(function (response) {
        $scope.messages = response.payload;
        console.log("$scope.messages Data::", $scope.messages);

    });
    console.log("User Details::", Auth);

    $scope.MessageObj = {
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
        "message_services": "22",
        "message_type": "",
        "message_keywords": "",
        "from_date": "",
        "to_date": ""
    }


}]);
