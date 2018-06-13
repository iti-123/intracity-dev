
app.controller('JobsCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger', function ($scope, $http, config, apiServices, type_basis, $state, $dataExchanger) {
    var serverUrl = config.serverUrl;    
    
    $scope.storagePath = STORAGE_PATH;
    $scope.profileTypes = [{key:'DRIVER',value:'DRIVER'},{key:'CLEANER',value:'CLEANER'},{
        key: 'SKILLED',value:'SKILLED'},{key:'SEMISKILLED',value:'SEMISKILLED'}];
    
    $scope.employmentTypes = [{key:'FULL TIME',value:'FULL_TIME'}, {key:'PART TIME',value:'PART_TIME'},
    {key:'CONTRACT',value:'CONTRACT'}];
    
    $scope.salaryTypes = [{key:'PER DAY',value:'PER_DAY'},{key:'PER WEEK',value:'PER_WEEK'},{key:'PER MONTH',
        value:'PER_MONTH'
    }];

    $scope.qualificationTypes = [{key:'SSLC',value:'SSLC'},{key:'Intermediate',value: 'INTERMEDIATE'
        },{key:'Graduate',value:'GRADUATE'},{key:'Post Graduate',value:'POST_GRADUATE'}];

    $scope.statusTypes = [{key: 'ACTIVE', value: 'ACTIVE'}, {key: 'INACTIVE', value: 'INACTIVE'}, {
        key: 'DELETED',
        value: 'DELETED'
    }];

    $http({
        method: 'GET',
        url: serverUrl + 'bluecollar/vehicle-types',
        headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
        }
    }).then(function success(response) {
        if (response.data) {
            $scope.vehicleTypes = response.data.data;
        }
    }, function error(response) {
        //
    });

    $http({
        method: 'GET',
        url: serverUrl + 'bluecollar/machine-types',
        headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
        }
    }).then(function success(response) {
        if (response.data) {
            $scope.machineTypes = response.data.data;
        }
    }, function error(response) {
        //
    });
    
    $scope.searchLocations = function () {
        console.log('Loc',$scope.filterData.locationPH);
        $http({
            method: 'POST',
            url: config.serverUrl + 'bluecollar/buyer-location-search',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: {'search': $scope.filterData.locationPH},
        }).then(function (response) {
            //console.log(httpResponse.data.data);
            $scope.locationResults = response.data.data;
        }, function (response) {

        });
    };

    $scope.filterData = {
        postDraft: '',
        location: {},
        locationPH: '',
        profileType: [],
        vehicleType: [],
        machineType: [],
        employmentType: getEmploymentType(),
        salaryType: [],
        qualification: [],
        status: [],
        pageLoader : 5,
        pageNextValueCount : 5
    };

    $scope.employementType =  $state.params.type.split("-").join(" ");
    $scope.getUserActiveRole = Auth.getUserActiveRole();
    
    function getEmploymentType() {
        $scope.empType = $state.params.type;
        if ($scope.empType == 'full-time') {
            return ['FULL_TIME'];
        } else if ($scope.empType == 'part-time') {
            return ['PART_TIME'];
        } else if ($scope.empType == 'contract') {
            return ['CONTRACT'];
        } else {
            return [];
        }  
    }

    $scope.$watch('filterData', function (newValue, oldValue) {
        $http({
            url: serverUrl + 'bluecollar/seller-post-list',
            method: 'POST',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: $scope.filterData,
        }).then(function success(response) {
            if (response.data.success) {
                $scope.jobs = response.data.data;
            }
        }, function error(response) {
            $scope.spinnerOperator = false;

        });
    }, true);

    // $http({
    //     url: serverUrl+'bluecollar/seller-post-list',
    //     method: 'POST',
    //     data:$scope.filterData,
    //     headers: {
    //       'authorization': 'Bearer ' + localStorage.getItem("access_token")
    //     },
    // }).then(function success(response) {
    //     if(response.data.success){
    //       $scope.jobs = response.data.data;
    //       console.log($scope.jobs);
    //     }
    // }, function error(response) { });
    
    $scope.filterFiltersData = function (filterArr, text) {
        var retArr = [];
        if (text != '' && text != undefined) {
            for (var v in filterArr) {
                if (filterArr[v].value.includes(text.toUpperCase())) {
                    retArr.push(filterArr[v]);
                }
            }
        } else {
            retArr = filterArr;
        }
        return retArr;
    }
    
    $scope.filterVMFiltersData = function (filterArr, text) {
        var retArr = [];
        if (text != '' && text != undefined) {
            for (var v in filterArr) {
                if (filterArr[v].name.toUpperCase().includes(text.toUpperCase())) {
                    retArr.push(filterArr[v]);
                }
            }
        } else {
            retArr = filterArr;
        }
        return retArr;
    }
    
    $scope.checkedFilterData = function (arr, value) {
        var index = arr.indexOf(value);
        if (index == -1) {
            return false;
        }
        return true;
    }

    $scope.addFilterData = function (arr, value) {
        $scope.filClearValue = true;
        var index = arr.indexOf(value);
        if (index == -1) {
            arr.push(value);
        } else {
           arr.splice(index, 1);
           //console.log('Filter Data',$scope.filterData);
           if($scope.filterData.profileType.length < 1 && $scope.filterData.locationPH == '' && $scope.filterData.vehicleType.length < 1 && $scope.filterData.status.length < 1
             && $scope.filterData.machineType.length < 1 && $scope.filterData.employmentType.length < 1 && $scope.filterData.salaryType.length < 1 && $scope.filterData.qualification.length < 1){
               $scope.filClearValue = false;
            }
        }
    }

    $scope.selectedLocation = function ($item, $model, $label) {
        $scope.filClearValue = true;
        $scope.filterData.location = $item;
    }

    $scope.removeElem = function (arr, index) {
        arr.splice(index, 1);
        if($scope.filterData.profileType.length < 1 && $scope.filterData.locationPH != '' && $scope.filterData.vehicleType.length < 1 && $scope.filterData.status.length < 1
            && $scope.filterData.machineType.length < 1 && $scope.filterData.employmentType.length < 1 && $scope.filterData.salaryType.length < 1 && $scope.filterData.qualification.length < 1){
            $scope.filClearValue = false;
        }
    };

    $scope.removeLocation = function () {
        $scope.filterData.location = {};
        $scope.filterData.locationPH = '';
        if($scope.filterData.profileType.length < 1 && $scope.filterData.vehicleType.length < 1 && $scope.filterData.status.length < 1
            && $scope.filterData.machineType.length < 1 && $scope.filterData.employmentType.length < 1 && $scope.filterData.salaryType.length < 1 && $scope.filterData.qualification.length < 1){
            $scope.filClearValue = false;
        }
    }
    
    $scope.getVehicle = function (id) {
        for (let v of $scope.vehicleTypes) {
            if (v.id == id) {
                return v.name;
            }
        }
    }

    $scope.getMachine = function (id) {
        for (let v of $scope.machineTypes) {
            if (v.id == id) {
                return v.name;
            }
        }
    }

    $scope.clearAll = function () {
        $scope.filClearValue = false;
        $scope.filterData = {
            location: {},
            locationPH: '',
            profileType: [],
            vehicleType: [],
            machineType: [],
            employmentType: [],
            salaryType: [],
            qualification: [],
            status: [],
        };
    }

    $scope.goToBookNow = function (index) {
        $scope.cartId = $scope.jobs.data[index].id;
        console.log('Index',$scope.jobs.data[index]);

        $dataExchanger.request.serviceId = _BLUECOLLAR_;
        $dataExchanger.request.BlueCollarbookData = $scope.jobs.data[index];
        $dataExchanger.request.BlueCollarData = $scope.jobs.data[index];

        $dataExchanger.request.BlueCollarbookData.seller_salary = $scope.jobs.data[index]['salary'];
        $dataExchanger.request.BlueCollarbookData.seller_experience = $scope.jobs.data[index]['experience'];
        $dataExchanger.request.BlueCollarbookData.seller_qualification = []; 
        $dataExchanger.request.BlueCollarbookData.seller_qualification.push($scope.jobs.data[index]['qualification']);
        $dataExchanger.request.BlueCollarbookData.seller_first_name = $scope.jobs.data[index]['posted_by']['username'];
        
        if($scope.jobs.data[index]['profile_type'] == 'DRIVER' || $scope.jobs.data[index]['profile_type'] == 'SKILLED'){
            $scope.vehicles = $scope.getVehicle($scope.jobs.data[index]['veh_mach'][0]['vm_id']);
            $dataExchanger.request.BlueCollarbookData.seller_vehicle_type = []; 
            $dataExchanger.request.BlueCollarbookData.seller_vehicle_type.push($scope.vehicles);
        }else{
            $dataExchanger.request.BlueCollarbookData.seller_vehicle_type = []; 
        }   
        
        var booknowSerachObj = $dataExchanger.request;
        $scope.bookPostObj = {
            initialDetails: {
                serviceId: $dataExchanger.request.serviceId,
                serviceType: '',
                sellerId: $scope.jobs.data[index].posted_by.id,
                buyerId: Auth.getUserID(),
                postType: "BP",
                sellerQuoteId: '', //need to discuss
                searchData: booknowSerachObj,
                carrierIndex: index,
                quote: '',
                postId : $scope.jobs.data[index].id
            }
        };

        apiServices.bookNow(serverUrl, JSON.stringify($scope.bookPostObj)).then(function (response) {
            if (response.isSuccessful && response.payload.id) {
                $scope.cartId = response.payload.enc_id;
                $state.go('order-booknow',{serviceId: _BLUECOLLAR_,cartId: $scope.cartId});
            }
        })
    };


    $scope.sharableData  = {};
    
    $scope.postUrl = function(url,id,commonData) 
    {
        $scope.sharableData.url = url+id;
        $scope.sharableData.commonData = commonData;
        $scope.sharableData.type='jobs';
    
    }
    
    
    
    $scope.insertPost=function(value) {
      var shareData = $scope.sharableData;
      console.log('SHARE DATA', $scope.sharableData);
      // console.log('TESST VALUEEEEE',$scope.sharableData);
        var url = serverUrl + 'community/share';
        apiShareServices.insertPost(url,shareData).then(function(response){
          $scope.postData = response;
          console.log('POSST DATAA::', $scope.postData);
        })
            
    }

    // $scope.sharableData = {};
    

    // $scope.getJobDetails = function(getJobDetails) {
    //     $scope.details = getJobDetails;
    //     // console.log('JOB DETAILSSS', getJobDetails);
    // }

}]);    