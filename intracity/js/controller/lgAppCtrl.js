app.controller('lgAppCtrl', ['$window','$scope','$state','$rootScope', 'config', '$http', '$state', '$dataExchanger', 'apiServices','rx', function ($window,$scope,$state ,$rootScope, config, $http, $state, $dataExchanger, apiServices,rx) {

    // var myObservable = rx.Observable.create(observer => {
    //     observer.next('HOOOOOOOOOOOOOOOOOOO');       
    //   });
    // myObservable.subscribe(value => console.log(value));

    var cartSubject = new rx.Subject();
    $scope.baseurlFrontend = BASEURL_FRONTEND;
//    console.log('BASEURL::', $scope.baseurlFrontend);
    
    $scope.page = 'home';
    var serverUrl = config.serverUrl;
    $scope.setAppTitle($state);
    $rootScope.activeService = _INTRACITY_;
    $rootScope.activeServiceName = 'INTRACITY';

    var Auth = new AuthenticationContext();

    $scope.closeModel = function () {
        var dataType = $(".waitText button").attr("data-type");
        console.log("dataType::", dataType);
        if (typeof (dataType) != undefined && dataType == 'sellerPost') {
            console.log("dataType::", dataType);
            $state.go("seller-post-list");
        } 
        if (typeof (dataType) != undefined && dataType == 'hpsellerPost') {
            console.log("dataType::", dataType);
            $state.go("hp-buyer-list");
        }
        else if (typeof (dataType) != undefined || dataType != '') {
            console.log("dataType::", dataType);
            $state.go("buyer-list");
        }
        else if (typeof (dataType) != undefined && dataType == 'IntracitybuyerDetails') {
            console.log("dataType::", dataType);
            $state.go("hp-buyer-list");
        }

        
        

    }

        $rootScope.selectedT = 0;
        $scope.tabclick =function(item,index){
        $rootScope.selectedT = index;
        var x = item.currentTarget.getAttribute("data-id");
        $(".tab-firsts").removeClass("active-t1");
        $("#"+x).addClass("active-t1");
        
    } 

    $scope.popconfirmbox = function(){
        confirm("You have not subscribed to the service. Do you want to subscribe now?");
    }

 $scope.cmtServiceRedirect = function(serviceId,serviceUrl) {
        localStorage.setItem("communityServices", JSON.stringify(serviceId));
       // if(serviceUrl.length>0){
            $state.reload();
       // }
        
        
    }



    $scope.init = function () {serverUrl
        $dataExchanger.$default({request: {serviceId:_INTRACITY_, serviceName: "", fullName: "", imagePath: "", data: {}}});
    };
    $scope.init();

    if (!Auth.isAuthenticated())
        window.location.replace("login.html");

    $scope.getUserActiveRole = Auth.getUserActiveRole();
    
    if ($scope.getUserActiveRole.toLowerCase() == 'buyer') {
        $scope.switchRole = 'Seller';
    } else {
        $scope.switchRole = 'Buyer';
    }


    $scope.$watch(function () {
        $scope.manageCommunityPanel($state.$current.url.source);
        
        hideLeftPanel($state.$current.url.source);
       // alert($scope.activeService);
      //  $dataExchanger.request.serviceId = $scope.activeService;       
    });

   
    $scope.manageCommunityPanel = function(url) {
        $scope.sideBar = {
            isProfile: false,
            isEvent: false,
            isArtilce:false,
            isLeftPannel:false,
            isMember:false,
            isJob:false,
            isComunity:true,
            isServices:false,
        }        


  

        if (url == '/community-profile' || url=='/connection') {
            $scope.sideBar.isProfile = true;
        }        

        else if (url == '/community-services-listing') {
            $scope.sideBar.isServices = true;
        }
        else if (url == '/event-list' || url == '/event-list/:type' || url == '/create-event' || url == '/free-event-list') {
            $scope.sideBar.isEvent = true;              
        }          
        else if (url == '/create-article') {
            $scope.sideBar.isArtilce = true;
        }
        else if (url == '/individuals' || url == '/business' || url == '/groups' || url == '/businesspartnership-invitation' || url =='/individuals-invitation' || url=='/create-new-group') {
            $scope.sideBar.isMember = true;
        }
        else if (url == '/community-jobs/:type') {
            $scope.sideBar.isJob = true;
        }
        else if (url == '/article-list' || url=="/artical-view/:id" || url == '/article-list/:type') {
            $scope.sideBar.isArtilce = true; 
        }
        else if (url == '/artical-view') {
            $scope.sideBar.isArtilce = true;
        } else if (url == '/recommendation' || url == '/community/profile/:slug' || url == '/event-profile/:id' || url=='/event-register/:title'){

        } else {
            $scope.sideBar.isLeftPannel = true;
            $scope.sideBar.isComunity = false; 
        }
    };
    
    

$scope.left_nave_services = function(url,serviceId){

if(parseInt(serviceId)==_INTRACITY_ ||parseInt(serviceId)==_HYPERLOCAL_ || parseInt(serviceId)==_BLUECOLLAR_) {
  $(".sub_service > li > a").removeClass("active_service");
  $("#"+serviceId).addClass("active_service");
  sessionStorage.setItem("selected_service_id",serviceId);
   localStorage.setItem("dataExchanger-request",JSON.stringify(SERVICE_API_NAME['service_'+serviceId]));
   window.location.href=APPLICATION_PATH+url;
//$state.go(url);
}

}



    $rootScope.activeUserProfileForComunity = function() {

        let url = serverUrl + 'community/communityProfile';
        let role = Auth.getUserActiveRole().toLowerCase();
        apiServices.postMethod(url,{role:role}).then(response => {
            $scope.profile = response.payload;           

        }).catch();
    }

    $scope.activeUserProfileForComunity();

    // console.log("$scope.activeService", $dataExchanger.request.serviceId);



    role = Auth.getUserActiveRoleId();
    id = Auth.getUserID();
    
    /**For Left Side Menu  */
    var url = serverUrl + 'left-nav-data/' + id + '/' +role;
    $scope.getLeftSideMenu = function (url) {

    
        apiServices.getLeftSideMenu(url).then(function (response) {
        $scope.leftSideMenu = response;

        $scope.selected_service=sessionStorage.getItem("selected_service_id");
        //parseInt
          if(parseInt($scope.selected_service)==46){
            $scope.selected_service_parent='Blue Collar';
        } else{
            $scope.selected_service_parent='Domestic Logistics';
        }
//$scope.selected_service_parent='Domestic Logistics';
 //console.log($scope.leftSideMenu = response);
        
        });
    }
    $scope.getLeftSideMenu(url);


    $scope.cartCount = function () {
        apiServices.getCartCount(serverUrl).then(function (response) {
            
            if (response != 0) {
                cartSubject.onNext(response);
            } else {
                $scope.cartCount = '';
            }
        });
    };
    
    $scope.cartCount();    

    $scope.userType = Auth.getUserActiveRole().toLowerCase();

    var subscribe = cartSubject.subscribe((response)=> {
        $scope.cartCount = response; 
    });

   
}]);

app.controller('HeaderCtrl', ['$scope', 'config', '$http', '$state', '$dataExchanger', 'apiServices', '$rootScope', function ($scope, config, $http, $state, $dataExchanger, apiServices, $rootScope) {
    var Auth = new AuthenticationContext();
    var serverUrl = config.serverUrl;
    $scope.currentServiceId = $dataExchanger.request.serviceId;
    $scope.getUserActiveRole = Auth.getUserActiveRole();

    $rootScope.serviceImage = IMAGE_PATH;
    
    
    $scope.UserName = Auth.getUserName();
    if ($scope.getUserActiveRole.toLowerCase() == 'buyer') {
        $scope.switchRole = 'Seller';
        $scope.headingSearch = ' Search & Submit Quote';
        $scope.headingPost = ' Search & Submit Quote';
    } else {
        $scope.switchRole = 'Buyer';
        $scope.headingSearch = ' Search & Book';
        $scope.headingPost = ' Post & Get Quote';
    }


    $scope.logout = function () {

        $.ajax({
            type: 'GET',
            contentType: "application/json",
            url: BASE_URL + 'getSignOut',
            crossDomain: true,
            async: false,
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            success: function (response) {
                localStorage.setItem("access_token", '');
                window.location.replace("login.html");
            },
            error: function (err) {
                handleAuthenticationError(err);
                localStorage.setItem("access_token", '');
            }
        });
    };
    $scope.roleSwitcherModel = function () {
        $("#sellerBuyerToggleModal").modal('show');
    }

    $scope.roleSwitcher = function () {
        console.log("Switch Role");
        var activeRoleId = Auth.getUserActiveRoleId();
        
        var nextRoleID = (Auth.getUserActiveRole() == Auth.getUserSecondaryRole()) ? Auth.getUserPrimaryRoleId() : Auth.getUserSecondaryRoleId();
        


  if(activeRoleId == Auth.getUserPrimaryRoleId()){
            if(!Auth.getUserSecondaryRoleId()) {
                alert('sorry you have no access to seller!');
                window.location.replace("index.html");
            }
            nextRoleID = Auth.getUserSecondaryRoleId();
        } else if(activeRoleId == Auth.getUserSecondaryRoleId()){
            nextRoleID = Auth.getUserPrimaryRoleId();
        }

        $.ajax({
            type: "GET",
            processData: false,
            dataType: 'json',
            headers: {

                'Authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            url: BASE_URL + "switchRole/" + nextRoleID,
            success: function (response) {
                
                
            },
            error: function (err) {
                handleAuthenticationError(err);
                  
                if(err.status === 200) {
               
                    localStorage.setItem("access_token", err.responseText);
               
                    Auth = new AuthenticationContext();
                    $scope.Auth = Auth;

                    var type = Auth.getUserActiveRole().toLowerCase();
                    
                    $("#sellerBuyerToggleModal").modal('hide');
                    setTimeout(function () {
                        window.location.replace("index.html");
                    }, 2000);
                }               

            }
        });
    };


    $scope.updateMessage = function(value){
        //console.log('UPDATEE MESSAGE::', value);
        var Id = value.id;
        
        apiServices.updateMessage(Id).then(function (response) {
        
    })
    };

    var url = serverUrl + 'hyperlocal/getMessageCount';
    $scope.getMessageCount = function (url) {
        apiServices.getMessageCount(url).then(function (response) {
        $scope.count = response;
        //console.log('MESSAGE COUNTSSS::',$scope.count);
        });
    }
    $scope.getMessageCount(url);

    var url = serverUrl + 'total-count?r='+$scope.getUserActiveRole+'&s='+$dataExchanger.request.serviceId;
    $scope.totalCount = function (url,serviceId) {
        apiServices.totalCount(url, serviceId).then(function (response) {
        $scope.totalCount = response;
        });
    }
    $scope.totalCount(url);

    var checkUser = Auth.getUserActiveRole();
        
    /**Header Notifications Counts Start */
    $scope.userID = Auth.getUserID();
    $scope.userRoleId = $scope.getUserActiveRole == 'Seller' ? '2' : '1';
    
    var url = serverUrl + 'post-master/'+$scope.userID+'/'+$scope.userRoleId;
    $scope.postMasterCounts = function (url) {
        apiServices.postMasterCounts(url).then(function (response) {
            $scope.postMasterCounts = response;
        });
    }

$scope.service_redirect = function(url,serviceId){
  

if(parseInt(serviceId)==_INTRACITY_ || parseInt(serviceId)==_HYPERLOCAL_ || parseInt(serviceId)==_BLUECOLLAR_){

  $(".sub_service > li > a").removeClass("active_service");
  $("#"+serviceId).addClass("active_service");
  sessionStorage.setItem("selected_service_id",serviceId);

   localStorage.setItem("dataExchanger-request",JSON.stringify(SERVICE_API_NAME['service_'+serviceId]));
    
    window.location.href=APPLICATION_PATH+url;
/*    if(parseInt(serviceId)==_INTRACITY_){
     $state.go("buyer-list");
    }
if(parseInt(serviceId)==_HYPERLOCAL_){
     $state.go("hp-buyer-list");
    }
if(parseInt(serviceId)==_BLUECOLLAR_){
     $state.go("bluecollar-buyer-post-list");
    }
*/
}

}

$scope.service_redirect_ord = function(url,serviceId){
    
if(parseInt(serviceId)==_INTRACITY_ || parseInt(serviceId)==_BLUECOLLAR_ || parseInt(serviceId)==_HYPERLOCAL_){
  $(".sub_service > li > a").removeClass("active_service");
  $("#"+serviceId).addClass("active_service");
  sessionStorage.setItem("selected_service_id",serviceId);

   localStorage.setItem("dataExchanger-request",JSON.stringify(SERVICE_API_NAME['service_'+serviceId]));
    
    window.location.href=APPLICATION_PATH+url;
   /* if(parseInt(serviceId)==3){
     $state.go("buyer-list");
    }
if(parseInt(serviceId)==47){
     $state.go("hp-buyer-list");
    }
if(parseInt(serviceId)==46){
     $state.go("bluecollar-buyer-post-list");
    }*/

}

}









    $scope.postMasterCounts(url);
    /**Header Notifications Counts Start */

    /**Header Notifications Counts Start for Order Master */
    $scope.userID = Auth.getUserID();
    $scope.userRoleId = $scope.getUserActiveRole == 'Seller' ? '2' : '1';
    
    var url = serverUrl + 'order-master/'+$scope.userID+'/'+$scope.userRoleId;
    $scope.orderMasterCounts = 
    function (url) {
        apiServices.orderMasterCounts(url).then(function (response) {
            $scope.orderMasterCounts = response;
        });
    }
    $scope.orderMasterCounts(url);
    /**Header Notifications Counts Start for Order Master */




    /** Message Notification Section*/
    var url = serverUrl + 'hyperlocal/getMsgNotification';
    $scope.getMessageNotification = function (url) {
        apiServices.getMessageNotification(url).then(function (response) {
            $scope.messages = response;
        });
    }
    $scope.getMessageNotification(url);
    /** Message Notification Section*/

    /**For Popup Menu  */
    
    $scope.getPostPopupMenu = function (url) {
        apiServices.getPostPopupMenu(url).then(function (response) {
        $rootScope.PostPopupMenu = response;
        console.log('LEFTggggg SIDE MENU::', $scope.PostPopupMenu);
        });
    }
    

    // Display order master modal
   
    $scope.showHeaderModal = function(type ,index) {
        var roleid = Auth.getUserActiveRoleId();
        var userid = Auth.getUserID();
        if(type=="post-master"){
            var postmastertype = "postmaster"; 
           
        } 
        if(type=="order-master"){
            var postmastertype = "order";


        }
        var url = serverUrl + 'popup-menu-data/' + userid + '/' + roleid + '/' + postmastertype;
        $scope.getPostPopupMenu(url);
        $rootScope.selectedT = 0;
        $("#masterModalType").attr("data-type",type);
        $("#change-service").modal("show");
    }

}]);


app.controller('TopHeaderCtrl', ['$scope', 'config', '$http', '$state', '$dataExchanger', 'apiServices', function ($scope, config, $http, $state, $dataExchanger, apiServices) {
    $scope.getHeaderNavigation = function(serviceName) {
        fullName = serviceName;
        
        $dataExchanger.request.serviceName = serviceName;
        $dataExchanger.request.fullName = fullName;
       
        $scope.type = $("#masterModalType").attr("data-type");
         if ($scope.type == 'post-master') {
            if ($scope.userType == 'buyer' && serviceName == 'INTRACITY') {



  $(".sub_service > li > a").removeClass("active_service");
  $("#"+_INTRACITY_).addClass("active_service");
  sessionStorage.setItem("selected_service_id",_INTRACITY_);
   localStorage.setItem("dataExchanger-request",JSON.stringify(SERVICE_API_NAME['service_'+_INTRACITY_]));

                $dataExchanger.request.serviceId = _INTRACITY_;
         //       $dataExchanger.request.data.serviceId = _INTRACITY_;
                $state.go('buyer-list');
            } else if ($scope.userType == 'seller' && serviceName == 'INTRACITY') {


  $(".sub_service > li > a").removeClass("active_service");
  $("#"+_INTRACITY_).addClass("active_service");
  sessionStorage.setItem("selected_service_id",_INTRACITY_);
   localStorage.setItem("dataExchanger-request",JSON.stringify(SERVICE_API_NAME['service_'+_INTRACITY_]));


                $dataExchanger.request.serviceId = _INTRACITY_;
           //     $dataExchanger.request.data.serviceId = _INTRACITY_;
                $state.go('seller-post-list');
            } else if ($scope.userType == 'buyer' && serviceName == 'HYPERLOCAL') {
            

  $(".sub_service > li > a").removeClass("active_service");
  $("#"+_HYPERLOCAL_).addClass("active_service");
  sessionStorage.setItem("selected_service_id",_HYPERLOCAL_);
   localStorage.setItem("dataExchanger-request",JSON.stringify(SERVICE_API_NAME['service_'+_HYPERLOCAL_]));


                $dataExchanger.request.serviceId = _HYPERLOCAL_;
          //      $dataExchanger.request.data.serviceId = _HYPERLOCAL_;
                $state.go('hp-buyer-list');
            } else if ($scope.userType == 'seller' && serviceName == 'HYPERLOCAL') {
               


  $(".sub_service > li > a").removeClass("active_service");
  $("#"+_HYPERLOCAL_).addClass("active_service");
  sessionStorage.setItem("selected_service_id",_HYPERLOCAL_);
   localStorage.setItem("dataExchanger-request",JSON.stringify(SERVICE_API_NAME['service_'+_HYPERLOCAL_]));


               $dataExchanger.request.serviceId = _HYPERLOCAL_;
//                $dataExchanger.request.data.serviceId = _HYPERLOCAL_;
                $state.go('hp-seller-list');
            }else if ($scope.userType == 'seller' && serviceName == 'BLUECOLLAR') {


$(".sub_service > li > a").removeClass("active_service");
  $("#"+_BLUECOLLAR_).addClass("active_service");
  sessionStorage.setItem("selected_service_id",_BLUECOLLAR_);
   localStorage.setItem("dataExchanger-request",JSON.stringify(SERVICE_API_NAME['service_'+_BLUECOLLAR_]));


//                $dataExchanger.request.serviceId = _BLUECOLLAR_;
//                $dataExchanger.request.data.serviceId = _BLUECOLLAR_;
                $state.go('bluecollar-seller-post-list');
            } else if ($scope.userType == 'buyer' && serviceName == 'BLUECOLLAR') {

$(".sub_service > li > a").removeClass("active_service");
  $("#"+_BLUECOLLAR_).addClass("active_service");
  sessionStorage.setItem("selected_service_id",_BLUECOLLAR_);
   localStorage.setItem("dataExchanger-request",JSON.stringify(SERVICE_API_NAME['service_'+_BLUECOLLAR_]));


//                $dataExchanger.request.serviceId = _BLUECOLLAR_;
//                $dataExchanger.request.data.serviceId = _BLUECOLLAR_;
                $state.go('bluecollar-buyer-post-list');
            }
        }
        
        if ($scope.type == 'order-master') {
            if (serviceName == 'INTRACITY') {


$(".sub_service > li > a").removeClass("active_service");
  $("#"+_INTRACITY_).addClass("active_service");
  sessionStorage.setItem("selected_service_id",_INTRACITY_);
   localStorage.setItem("dataExchanger-request",JSON.stringify(SERVICE_API_NAME['service_'+_INTRACITY_]));



                $dataExchanger.request.serviceId = _INTRACITY_;
      //          $dataExchanger.request.data.serviceId = _INTRACITY_;                
            } else if(serviceName == 'HYPERLOCAL') {



$(".sub_service > li > a").removeClass("active_service");
  $("#"+_HYPERLOCAL_).addClass("active_service");
  sessionStorage.setItem("selected_service_id",_HYPERLOCAL_);
   localStorage.setItem("dataExchanger-request",JSON.stringify(SERVICE_API_NAME['service_'+_HYPERLOCAL_]));

                $dataExchanger.request.serviceId = _HYPERLOCAL_;
     //           $dataExchanger.request.data.serviceId = _HYPERLOCAL_;
            } else if(serviceName == 'BLUECOLLAR') {




$(".sub_service > li > a").removeClass("active_service");
  $("#"+serviceId).addClass("active_service");
  sessionStorage.setItem("selected_service_id",_BLUECOLLAR_);
   localStorage.setItem("dataExchanger-request",JSON.stringify(SERVICE_API_NAME['service_'+_BLUECOLLAR_]));



                  $dataExchanger.request.serviceId = _BLUECOLLAR_;
          //      $dataExchanger.request.data.serviceId = _BLUECOLLAR_;
            }
            // Check existing Service id 
            
           $state.go('ordermaster',{serviceName: serviceName.toLowerCase()});
        }

        $("#change-service").modal("hide");
    }



    
   
}]);


   