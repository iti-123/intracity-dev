app.controller('HomeCtrl', ['$scope', '$dataExchanger', '$rootScope', '$location', '$state', 'homeServices', function ($scope, $dataExchanger, $rootScope, $location, $state, homeServices) {
    $('.selectSeller').addClass('active-user-type');
    $('.selectBuyer').removeClass('active-user-type');

    $scope._INTRACITY_ = _INTRACITY_;
    $scope._BLUECOLLAR_ = _BLUECOLLAR_;
    $scope._HYPERLOCAL_ = _HYPERLOCAL_;
    
    

    $scope.userType = Auth.getUserActiveRole().toLowerCase();
    $scope.Auth = Auth;


    if ($scope.userType == 'buyer') {
        $scope.selectedTab = 'Search and Book';
        $('#Buyer').show();
        $('#Seller').hide();
        $('.selectSeller').removeClass('active-user-type');
        $('.selectBuyer').addClass('active-user-type');
    }
    else {
        $scope.selectedTab = 'Search and Submit Quote';
        $('#Buyer').hide();
        $('#Seller').show();
        $('.selectSeller').addClass('active-user-type');
        $('.selectBuyer').removeClass('active-user-type');
    }
    $scope.getSelectedTabMenu = function (tabVal) {
        console.log("tabVal::", tabVal);
        $scope.selectedTab = tabVal;

        if ($scope.selectedTab == 'Search and Book') {
            $('li.ntsearch a').addClass('active');
            $('li.ntpost a').removeClass('active');
        } else {
            $('li.ntsearch a').removeClass('active');
            $('li.ntpost a').addClass('active');
        }
    };
    // $(".tabs.notsettingsTabs ul.nav li").hover(function() {
    // 	$(".tabs.notsettingsTabs ul.nav li a").click();
    // });
    $scope.getUserActiveRole = Auth.getUserActiveRole();
    if ($scope.getUserActiveRole.toLowerCase() == 'buyer') {
        $scope.headingSearch = ' Search & Book';
        $scope.headingPost = 'Post & Get Quote';
    } else {
        $scope.headingSearch = ' Search & Submit Quote';
        $scope.headingPost = ' Post Rate Card';
    }



	$scope.getNavigation = function(serviceId) {
  
  $(".sub_service > li > a").removeClass("active_service");
  $("#"+serviceId).addClass("active_service");
  sessionStorage.setItem("selected_service_id",serviceId);
   localStorage.setItem("dataExchanger-request",JSON.stringify(SERVICE_API_NAME['service_'+serviceId]));
    //return false;

          //Assign selected service details to dataexchanger
          // serviceName = 'INTRACITY';
         //  fullName = 'INTRACITY';
	 //      $dataExchanger.request.data.serviceId = serviceId;
	   //    $dataExchanger.request.serviceName = serviceName;
	   //    $dataExchanger.request.fullName = fullName;
		   
			if($scope.userType=='buyer' && serviceId==_INTRACITY_ && $scope.selectedTab == 'Search and Book'){
				 $state.go('buyer-search');
			} else if($scope.userType=='buyer' && serviceId==_INTRACITY_ &&   $scope.selectedTab == 'Post and Get Quote'){
				$state.go('post-buyer-as-term');
			} else if($scope.userType=='seller' && serviceId==_INTRACITY_ && $scope.selectedTab == 'Search and Book'){
				 $state.go('seller-search');
			} else if($scope.userType=='seller' && serviceId==_INTRACITY_ &&   $scope.selectedTab == 'Post and Get Quote'){
				$state.go('/seller');
			} else if($scope.userType=='buyer' && serviceId==_HYPERLOCAL_ && $scope.selectedTab == 'Search and Book'){
				 $state.go('hp-search-buyer');
			} else if($scope.userType=='buyer' && serviceId==_HYPERLOCAL_ &&   $scope.selectedTab == 'Post and Get Quote'){
				$state.go('hp-buyer-post');
			} else if($scope.userType=='seller' && serviceId==_HYPERLOCAL_ && $scope.selectedTab == 'Search and Book'){
				$state.go('hp-seller-search');
			} else if($scope.userType=='seller' && serviceId==_HYPERLOCAL_ &&   $scope.selectedTab == 'Post and Get Quote'){
				$state.go('hp-seller-rate-card');
			} else if($scope.userType=='seller' && serviceId==_BLUECOLLAR_ &&   $scope.selectedTab == 'Post and Get Quote'){
				$state.go('bluecollar-seller-rate-card');
			} else if($scope.userType=='buyer' && serviceId==_BLUECOLLAR_ &&   $scope.selectedTab == 'Post and Get Quote'){
				$state.go('bluecollar-buyer-post-card');
			} else if($scope.userType=='seller' && serviceId==_BLUECOLLAR_ &&   $scope.selectedTab == 'Search and Book'){
				$state.go('bluecollar-seller-search');
			} else if($scope.userType=='buyer' && serviceId==_BLUECOLLAR_ &&   $scope.selectedTab == 'Search and Book'){
				$state.go('bluecollar-buyer-search');
			}
	};
	
	$scope.getSelectedTab = function(tabVal){
		
	};
	
	 $scope.getUserActiveRole = Auth.getUserActiveRole();
	 console.log($scope.getUserActiveRole);
	 
}]);



