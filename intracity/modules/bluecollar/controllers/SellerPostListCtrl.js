app.controller('SellerPostListCtrl', ['$scope', '$http', 'config', '$location','SellerSearchServices','$window', function ($scope, $http, config, $location,SellerSearchServices,$window) {
    

    SellerSearchServices.checkSeller();
    var serverUrl = config.serverUrl;
    $scope.allPosts = [];

    $scope.locationSearch = [];

    $scope.profileTypes = [{key: 'DRIVER', value: 'DRIVER'}, {key: 'CLEANER', value: 'CLEANER'}, {
        key: 'SKILLED',
        value: 'SKILLED'
    }, {key: 'SEMISKILLED', value: 'SEMISKILLED'}];


    //$scope.vehicleTypes = [{key: 'BIKE', value: 'BIKE'},{key: 'LMV', value: 'LMV'},{key: 'MMV', value: 'MMV'},{key: 'HMV', value: 'HMV'}];
    $scope.employmentTypes = [{key: 'FULL TIME', value: 'FULL_TIME'}, {
        key: 'PART TIME',
        value: 'PART_TIME'
    }, {key: 'CONTRACT', value: 'CONTRACT'}];
    $scope.salaryTypes = [{key: 'PER DAY', value: 'PER_DAY'}, {key: 'PER WEEK', value: 'PER_WEEK'}, {
        key: 'PER MONTH',
        value: 'PER_MONTH'
    }];
    $scope.qualificationTypes = [{key: 'SSLC', value: 'SSLC'}, {
        key: 'Intermediate',
        value: 'INTERMEDIATE'
    }, {key: 'Graduate', value: 'GRADUATE'}, {key: 'Post Graduate', value: 'POST_GRADUATE'}];
    $scope.statusTypes = [{key: 'ACTIVE', value: 'ACTIVE'}, {key: 'INACTIVE', value: 'INACTIVE'}, {
        key: 'DELETED',
        value: 'DELETED'
    }];
    $scope.locationResults = [];
    $scope.locationPH = '';

    $scope.vehicleTypes = [];
    $scope.machineTypes = [];
    $scope.listType = '';
    
    $scope.postDraft = $window.sessionStorage.getItem('postDraft');

    setTimeout(function () {
      $scope.$apply(function() {
        $scope.postDraft = false;
        sessionStorage.removeItem('postDraft');
       });
    },8000);

    var getURL = function (type) {
        var url = '';
        if (type == 'all') {
            url = serverUrl + 'bluecollar/seller-post-list';
        } else if (type == 'inbound') {
            url = serverUrl + 'bluecollar/seller-inbound';
        } else {
            url = serverUrl + 'bluecollar/seller-outbound';
        }
        return url;
    }

    var fetch = function (type) {
        console.log('fetch function input',$scope.filterData);
        if ($scope.listType != type) {
            $scope.listType = type;
            var url = getURL(type);
            $http({
                url: url,
                method: 'POST',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: $scope.filterData
            }).then(function success(response) {
                if (response.data.success) {
                    $scope.allPosts = response.data.data.data;
                    console.log('fetch function response',response.data.data.data);
                }
            }, function error(response) {

            });
        }
    }
    
    $scope.goToLink = function(post) {
        $location.path('/bluecollar-seller-post-details/' + post.id);
    };

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

    $scope.filClearValue = false;

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

    $scope.formPlaceholder = {
        location: '',
        profileType: '',
        vehicleType: '',
        machineType: '',
        employmentType: '',
        salaryType: '',
        qualification: '',
        status: ''
    };

    $scope.boundCount = {
        inbound: 0,
        outbound: 0
    }

    fetch('all');

    $http({
        method: 'GET',
        url: serverUrl + 'bluecollar/seller-bound-count',
        headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
        }
    }).then(function success(response) {
        if (response.data) {
            $scope.boundCount = response.data.data;
        }
    }, function error(response) {
        //
    });

    $http({
        url: getURL($scope.listType),
        method: 'POST',
        headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
        },
    }).then(function success(response) {
        if (response.data.success) {
            $scope.allPosts = response.data.data.data;
        }
    }, function error(response) {

    });

    $scope.$watch('filterData', function (newValue, oldValue) {
        $scope.spinnerOperator = true;
        $http({
            url: getURL($scope.listType),
            method: 'POST',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: $scope.filterData,
        }).then(function success(response) {
            $scope.spinnerOperator = false;
            if (response.data.success) {
                $scope.allPosts = response.data.data.data;
            }
            // response=JSON.stringify(response);
            // response=JSON.parse(response);
        }, function error(response) {
            $scope.spinnerOperator = false;

        });
    }, true);

    $scope.searchLocations = function () {
        console.log('Loc',$scope.filterData.locationPH);
        //$scope.searchResults = [{'cur_state_or_city': 'PUNED'}, {'cur_state_or_city': 'hydra'}];
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

    $scope.fetch = function (type) {
        fetch(type);
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

    $scope.checkedFilterData = function (arr, value) {
        var index = arr.indexOf(value);
        if (index == -1) {
            return false;
        }
        return true;
    }

    $scope.post = function () {
        if ($scope.validate()) {
            $http({
                url: serverUrl + 'bluecollar/buyer-post',
                method: 'POST',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: $scope.formData,
            }).then(function success(response) {
                if (response.data.success) {
                    $scope.formData = angular.copy($scope.defaultForm);
                    $('body #postConfirmationModal').modal('show');
                }
            }, function error(response) {

            });
        }
    };

    $scope.showQuotations = function (quote,e) {
        e.stopPropagation();
        if (quote.length > 0 && quote != undefined) {
            if (quote.show) {
                quote.show = false;
            } else {
                quote.show = true;
            }
        } else {
            quote.show = false;
        }

    };

    $scope.getSellerQuoteDays = function (quote) {
        let day = '';
        if (quote.seller_final_transit_days != null) {
            day = quote.seller_final_transit_days;
        } else {
            day = quote.transit_day;
        }
        return day;
    };

    $scope.getSellerQuotePrice = function (quote) {
        let price = '';
        if (quote.seller_quote_price != null) {
            price = quote.seller_quote_price;
        } else {
            price = quote.initial_quote_price;
        }
        return price;
    };

    $scope.getBuyerQuoteDays = function (quote) {
        let day = '';
        if (quote.buyer_counter_transit_days != null) {
            day = quote.buyer_counter_transit_days;
        }
        return day;
    };

    $scope.getBuyerQuotePrice = function (quote) {
        let price = '';
        if (quote.buyer_quote_price != null) {
            price = quote.buyer_quote_price;
        }
        return price;
    };

    $scope.showElem = function (flag, key, index) {
        if (typeof flag[key] == 'boolean') {
            flag[key] = !flag[key];
        } else {
            flag[key] = true;
        }
    };

    $scope.showActionButton = function (quote) {
        if (quote.buyer_status != 'ACCEPT' && quote.buyer_status != 'DENY' && quote.seller_status != 'ACCEPT' && quote.seller_status != 'DENY') {
            return true;
        } else {
            return false;
        }
    }

    $scope.quoteAction = function (post,index,childIndex,action) {
        var quote = $scope.allPosts[index].quote[childIndex];
        console.log('Quote',quote);
        var quoteData = {
            postId: $scope.allPosts[index].quote[childIndex].post_id,
            quoteId: $scope.allPosts[index].quote[childIndex].id,
            quoteDays: $scope.allPosts[index].quote.quoteDays,
            quotePrice: $scope.allPosts[index].quote.quotePrice
        }
        if (action == 'submit') {
            if ($scope.validateQuotation(quote,index,childIndex)) {
                quoteData.action = "COUNTER";
                $scope.quoteActionRequest(post,index,childIndex,quoteData);
            }
        }
        if (action == 'accept') {
            quoteData.action = "ACCEPT";
            $scope.quoteActionRequest(post,index,childIndex,quoteData);
        }
        if (action == 'deny') {
            quoteData.action = "DENY";
            $scope.quoteActionRequest(post,index,childIndex,quoteData);
        }
    };
    
    $scope.validateQuotation = function (quote,index,childIndex) {
        var valid = true;
        if ($scope.allPosts[index].quote.quoteDays == undefined || $scope.allPosts[index].quote.quoteDays == '') {
            valid = false;
            $scope.allPosts[index].quoteDaysError = true;
            $scope.allPosts[index].errQuoteZeroDays = false;
        } else {
            if($scope.allPosts[index].quote.quoteDays == 0){
                $scope.allPosts[index].errQuoteZeroDays = true;
                $scope.allPosts[index].quoteDaysError = false;
                valid = false;
            }else{
                $scope.allPosts[index].errQuoteZeroDays = false;
            }

            $scope.allPosts[index].quoteDaysError = false;
        }

        if ($scope.allPosts[index].quote[childIndex].quotation_type == 'COMPETITIVE') {
            if ($scope.allPosts[index].quote.quotePrice == undefined || $scope.allPosts[index].quote.quotePrice == '') {
                $scope.allPosts[index].quotePriceError = true;
                $scope.allPosts[index].quoteZeroPriceError = false;
                valid = false;
            } else {
                if($scope.allPosts[index].quote.quotePrice < 100){
                  $scope.allPosts[index].quoteZeroPriceError = true;
                  $scope.allPosts[index].quotePriceError = false;
                  valid = false;
                }else{
                   $scope.allPosts[index].quoteZeroPriceError = false;
                }
                $scope.allPosts[index].quotePriceError = false;
            }
        }
        return valid;
    }

    $scope.quoteActionRequest = function (post,index,childIndex,quoteData) {
        $http({
            url: serverUrl + 'bluecollar/seller-quote-action',
            method: 'POST',
            headers: {
                'authorization': 'Bearer ' + localStorage.getItem("access_token")
            },
            data: quoteData
        }).then(function success(response) {
            if (response.data.success) {
                post.quote[childIndex] = response.data.data;
            }
        }, function error(response) {

        });
    }

    $scope.submitQuote = function (post, index) {
        var quote = post.quote[index];
        var quoteData = {
            action: "COUNTER",
            postId: quote.post_id,
            quoteId: quote.id,
            quoteDays: quote.quoteDay,
            quotePrice: quote.quotePrice,
            quotationType: quote.quotation_type
        }
        if ($scope.validateQuotation(quote)) {
            $http({
                url: serverUrl + 'bluecollar/seller-quote-action',
                method: 'POST',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: quoteData
            }).then(function success(response) {
                if (response.data.success) {
                    post.quote[index] = response.data;
                }
            }, function error(response) {

            });
        }
    };

   // $(window).scroll(function () {
     //   $scope.spinnerOperator = false;
       // if ($(window).scrollTop() + $(window).height() == $(document).height()) {
         //   $scope.filterData.pageLoader = $scope.filterData.pageLoader + $scope.filterData.pageNextValueCount;
        //    fetch($scope.listType);
          //  $scope.$apply();
       // }
    //});

}]);
