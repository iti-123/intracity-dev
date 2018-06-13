app.controller('MessageCtrl', ['$scope', '$http', 'config', 'messageServices', '$dataExchanger', '$state', 'apiServices', '$location', function ($scope, $http, config, messageServices, $dataExchanger, $state, apiServices, $location) {

    var serverUrl = config.serverUrl;

    $scope.getUserActiveRole = Auth.getUserActiveRole();
    $scope.storagePath = STORAGE_PATH;
    // console.log('ACTIVEEE ROLEE', $scope.getUserActiveRole);
    $scope.sellerList = [];


    messageServices.MessageType().then(function (response) {
        $scope.MessageType = response.payload;
        // console.log("$scope.MessageType::", $scope.MessageType);
    });

    messageServices.loadAllUsers().then(function (response) {
        $scope.buyerList = response.payload;
        // console.log("$scope.buyerList ::", $scope.buyerList);
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
    // console.log("User Details::", Auth);

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
        "message_services": _HYPERLOCAL_,
        "message_type": "",
        "message_keywords": "",
        "from_date": "",
        "to_date": ""
    };

    $scope.attachedFile = {
        "file": "",
        "userMessageId": ""
    };

    $scope.replyMessageModal = function (value) {
        $("#quoteObject").val(JSON.stringify(value));
        $("#from_name").val("From: " + value.toName);
        $("#to_name").val("To: " + value.fromName);
        $("#message_subject").val("Ref:- POST:" + value.message_no);

        $("#messageReplyModal").modal('show');
    };

    $scope.replyMessage = function (attachedFile) {
        console.log("attachedFile", typeof attachedFile);
        var myForm = document.querySelector('#MessageReplyData');
        var formData = new FormData(myForm);

        // Start Upload File 
        var postDetails = JSON.parse(formData.get('quoteObject'));

        $scope.MessageObj.message_from = $dataExchanger.request.data.sender_id;
        $scope.MessageObj.message_to = $dataExchanger.request.data.recepient_id;
        $scope.MessageObj.message_subject = formData.get('message_subject');
        $scope.MessageObj.message_body = formData.get('message_body');
        $scope.MessageObj.message_id = formData.get('message_body');

        $scope.MessageObj.lkp_service_id = $dataExchanger.request.serviceId;
        $scope.MessageObj.buyer_quote_item = postDetails.post_item_id;
        $scope.MessageObj.buyer_quote = postDetails.quote_item_id;

        $("#message_body").css("border-color", "#e2e2e2");
        if ($scope.MessageObj.message_body == '') {
            $("#message_body").css("border-color", "red");
            return false;
        }
        $scope.status = false;

        if (typeof attachedFile !== 'undefined' && attachedFile !== undefined) {

            $scope.attachedFile.file = attachedFile;
            messageServices.ReplyMessage(JSON.stringify($scope.MessageObj), postDetails.id).then(function (response) {
                if (response.isSucessfull == true) {
                    // $scope.files = {documentId: response.payload.id, documentName: response.payload.file_name};
                    $scope.attachedFile.userMessageId = response.payload.id;
                    //console.log("HELLO WITH DOCUMENT", $scope.MessageObj.userMessageId);
                    //console.log("NEW MESSAGE OBJECT ", $scope.MessageObj);
                    messageServices.uploadDocument($scope.attachedFile).then(function (response) {
                        //console.log(response.payload);
                        $scope.messages = response.payload;


                    });

                    $("#replyStatus").html("Message sent Successfully").addClass("text-success");
                    setTimeout(function () {
                        $("#messageReplyModal").modal('hide');
                    }, 1000);

                }
            });
        } else {

            messageServices.ReplyMessage(JSON.stringify($scope.MessageObj), postDetails.id).then(function (response) {
                $scope.MessageObj.docId = "";
                console.log("Replyed Message::", response.payload);

                $scope.messages = response.payload;
                $("#replyStatus").html("Message sent Successfully").addClass("text-success");

                setTimeout(function () {
                    $("#messageReplyModal").modal('hide');
                }, 1000);

            });

        }

        $scope.messageBroadcast();
    };

    // $scope.updateMessage = function(value){
    //     //console.log('UPDATEE MESSAGE::', value);
    //     var Id = value.id;
    //     console.log('UPDATEE ID::', Id);
    //     apiServices.updateMessage(Id).then(function (response) {
    //     })

    // };

    $scope.showDetails = function (key) {
        $("#detail-*").hide();
        $("#detail-" + key).slideToggle();
    };

    $scope.getMessageDetail = function (value) {
        var Id = value.id;
        $dataExchanger.request.data.sender_id = Auth.getUserID();
        $dataExchanger.request.data.recepient_id = value.sender_id;
        $dataExchanger.request.data.messageSendByUser = value.sender_id;
        apiServices.updateMessage(Id).then(function (response) {
        })
        $state.go('getmessagedetails');
    };

    /**           Get message by id         */

    function messageReceived(senderId) {
        messageServices.getMessageDetailsById(serverUrl + 'getMessageById/' + senderId).then(function (response) {
            $scope.messageDetails = response.payload;
            $scope.messageDetail = response.payload[0];
            console.log('MESSAGE DETAILSSS::', $scope.messageDetail);
        });
    }

   $scope.messageBroadcast = function () {
        console.log("Broadcasting");
        messageReceived($dataExchanger.request.data.messageSendByUser);
        // messageServices.PusherService($pusher, 'messages', 'send-message', function (data) {
            
        // });
    };
    $scope.messageBroadcast();
    var senderId = $dataExchanger.request.data.messageSendByUser;

    if (senderId) {
        $scope.messageBroadcast();
    }    


}]);