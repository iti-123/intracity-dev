
app.controller('SellerRegistrationCtrl',
 ['$scope', '$http', 'config', 'trackings', 'apiServices', 'discount',
 '$state','SellerSearchServices','$q',function ($scope, $http, config, trackings, apiServices, 
    discount,$state,SellerSearchServices,$q) {
    SellerSearchServices.checkSeller();
    var serverUrl = config.serverUrl;
    $scope.first = true;

    var EducationModel = function (data) {
        var self = this;
        self.qualification = '';
        self.board = '';
        self.university = '';
        self.city = '';
        self.percentage = '';
        self.doc = '';

        self._init = function (d) {
            if (d != null && d != undefined) {
                self.qualification = d.qualification;
                self.board = d.board;
                self.university = d.university;
                self.city = d.city;
                self.percentage = d.percentage;
            }
        };
        self._init(data);
        return self;
    };

    var LanguageModel = function (data) {
        var self = this;
        self.language = '';
        self.speak = false;
        self.read = false;
        self.write = false;

        self._init = function (d) {
            if (d != null && d != undefined) {
                self.language = d.language;
                self.speak = d.speak;
                self.read = d.read;
                self.write = d.write;
            }
        };

        self._init(data);
        return self;
    };

    var ExperienceModel = function (data) {
        var self = this;
        self.vehicleType = '';
        self.experience = '';
        self.employerName = '';
        self.location = '';
        self.salary = '';

        self._init = function (d) {
            if (d != null && d != undefined) {
                self.vehicleType = d.vehicleType;
                self.experience = d.experience;
                self.employerName = d.employerName;
                self.location = d.location;
                self.salary = d.salary;
            }
        };

        self._init(data);
        return self;
    };

    var ValidateIds = function () {
        var self = this;
        self.pan = function (number) {
            var re = new RegExp('^[A-Z]{5}[0-9]{4}[A-Z]{1}$');
            var valid = false;
            if (re.test(number)) {
                valid = true;
            }
            return valid;
        };
        self.licenceVal = function (number) {
            var re = new RegExp('^[A-Z]{2}[0-9]{11}$');
            var valid = false;
            if (re.test(number)) {
                valid = true;
            }
            return valid;
        };
        self.digitVal = function (number, digits) {
            var re = new RegExp('^[0-9]{' + digits + '}$');
            var valid = false;
            if (re.test(number)) {
                valid = true;
            }
            return valid;
        };
        self.rangeVal = function (number, min, max) {
            console.log('^[0-9]{' + min + ', ' + max + '}$');
            var re = new RegExp('^[0-9]{' + min + ', ' + max + '}$');
            var valid = false;
            if (re.test(number)) {
                valid = true;
            }
            return valid;
        };
    };

    var validateIds = new ValidateIds();

    $scope.bloodGroups = ['Blood Group', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    $scope.salaryTypes = [{key: 'Salary Type', value: ''}, {key: 'PER DAY', value: 'PER_DAY'}, {
        key: 'PER WEEK',
        value: 'PER_WEEK'
    }, {key: 'PER MONTH', value: 'PER_MONTH'}];
    $scope.qualifications = [{key: 'Qualification', value: ''}, {key: 'SSLC', value: 'SSLC'}, {
        key: 'Intermediate',
        value: 'INTERMEDIATE'
    }, {key: 'Graduate', value: 'GRADUATE'}, {key: 'Post Graduate', value: 'POST_GRADUATE'}];
    $scope.defaultForm = {
        accountType: 'Registration For',
        firstName: "",
        lastName: '',
        dob: new Date(),
        bg: 'Blood Group',
        currentAddress: {
            address: '',
            street: '',
            locality: '',
            pincode: '',
            city: '',
            landline: '',
            mobile: '',
            mobile2: ''
        },
        permanentAddress: {
            address: '',
            street: '',
            locality: '',
            pincode: '',
            city: '',
            landline: '',
            mobile: '',
            mobile2: ''
        },
        ids: {
            email: '',
            pan: '',
            adhar: '',
            ration: ''
        },
        qualifications: [],
        qModelData: {
            qualification: '',
            board: '',
            university: '',
            city: '',
            percentage: ''
        },
        qModelError: {
            qualification: false,
            board: false,
            university: false,
            city: false,
            percentage: false
        },
        lic: {
            status: ''
        },
        healthPol: {
            status: ''
        },
        medicalPol: {
            status: ''
        },
        langModelData: {
            language: 'Language',
            speak: false,
            read: false,
            write: false,
        },
        languages: [],
        vehicleType: '',
        vehicleTypes: [],
        licence: {
            no: '',
            state: '',
            validFrom: '',
            validTo: '',
            transportEndorsement: ''
        },
        expModelData: {
            vehicleType: '',
            experience: '',
            employerName: '',
            location: '',
            salary: ''
        },
        expModelError: {
            vehicleType: false,
            experience: false,
            employerName: false,
            location: false,
            salary: false
        },
        experience: []
    };

    $scope.formPlaceholders = {
        suggestions: [{city: 'papa1'}, {city: 'dad3'}],
        currentAddressSuggestions: [],
        permanentAddressSuggestions: [],
        vehicleTypes: [],
        machineTypes: [],
        dob: undefined,
        dateLimit: new Date(),
        qModelData: {
            qualification: '',
            board: '',
            university: '',
            city: '',
            percentage: ''
        },
        idsError: {
            pan: '',
            adhar: '',
            ration: ''
        },
        qModelError: {
            qualification: false,
            board: false,
            university: false,
            city: false,
            percentage: false
        },
        langModelData: {
            language: 'Language',
            speak: false,
            read: false,
            write: false,
        },
        vehicleType: 'Vehicle Type',
        machineType: 'Machine Type',
        expModelData: {
            vehicleType: '',
            experience: '',
            employerName: '',
            location: '',
            salary: ''
        },
        expModelError: {
            vehicleType: false,
            experience: false,
            employerName: false,
            location: false,
            salary: false
        },
        currentAddress: {
            city: ''
        },
        permanentAddress: {
            city: ''
        },
        currentAddressErrors: {
            pincode: false,
            landline: false,
            mobile: false,
            mobile2: false,
            cityActive: true
        },
        permanentAddressErrors: {
            pincode: false,
            landline: false,
            mobile: false,
            mobile2: false,
            cityActive: true
        },
        licence: {
            no: '',
            state: '',
            validFrom: undefined,
            validTo: undefined,
            transportEndorsement: '',
            licenceDoc: ''
        },
        experience: false,
        languages: false,
        employmentType: {
            fullTime: false,
            partTime: false,
            contract: false
        }
    };

    $scope.formData = {
        accountType: 'Registration For',
        bg: "Blood Group",
        firstName: "",
        lastName: '',
        dob: '',
        currentSalary: '',
        totalExperience: '',
        salaryType: '',
        currentAddress: {
            address: '',
            street: '',
            locality: '',
            pincode: '',
            city: '',
            landline: '',
            mobile: '',
            mobile2: ''
        },
        permanentAddress: {
            address: '',
            street: '',
            locality: '',
            pincode: '',
            city: '',
            landline: '',
            mobile: '',
            mobile2: ''
        },
        ids: {
            email: '',
            pan: '',
            adhar: '',
            ration: '',
            panDoc: '',
            adharDoc: '',
            rationDoc: ''
        },
        qualifications: [],
        lic: {
            status: ''
        },
        healthPol: {
            status: ''
        },
        medicalPol: {
            status: ''
        },
        languages: [],
        vehicleTypes: [],
        machineTypes: [],
        licence: {
            no: '',
            state: '',
            validFrom: '',
            validTo: '',
            transportEndorsement: '',
            licenceDoc: ''
        },
        experience: [],
        employmentType: []
    };
     
    $scope.formError = {
        accountType: 'Registration For',
        bg: "Blood Group",
        firstName: "",
        lastName: '',
        dob: '',
        currentSalary: '',
        totalExperience: '',
        salaryType: '',
        currentAddress: {
            address: '',
            street: '',
            locality: '',
            pincode: '',
            city: '',
            landline: '',
            mobile: '',
            mobile2: ''
        },
        permanentAddress: {
            address: '',
            street: '',
            locality: '',
            pincode: '',
            city: '',
            landline: '',
            mobile: '',
            mobile2: ''
        },
        ids: {
            email: '',
            pan: '',
            adhar: '',
            ration: '',
            panDoc: '',
            adharDoc: '',
            rationDoc: '',
            emailCheck: '',
        },
        qualifications: [],
        lic: {
            status: ''
        },
        healthPol: {
            status: ''
        },
        medicalPol: {
            status: ''
        },
        languages: [],
        vehicleTypes: [],
        machineTypes: [],
        licence: {
            no: '',
            state: '',
            validFrom: '',
            validTo: '',
            transportEndorsement: '',
            licenceDoc: ''
        },
        experience: [],
        employmentType: false
    };
    $scope.formError.qualificationsError = '';

    $http({
        method: 'GET',
        url: serverUrl + 'bluecollar/vehicle-types',
        headers: {
            'authorization': 'Bearer ' + localStorage.getItem("access_token")
        }
    }).then(function success(response) {
        if (response.data) {
            $scope.formPlaceholders.vehicleTypes = response.data.data;
            $scope.formPlaceholders.vehicleTypes.unshift({name: "Vehicle Type", id: ""});
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
            $scope.formPlaceholders.machineTypes = response.data.data;
            $scope.formPlaceholders.machineTypes.unshift({name: "Machine Type", id: ""});
        }
    }, function error(response) {
        //
    });

    $scope.selectedCity = function ($item, $model, $label, type) {
        if (type == 'current') {
            $scope.formData.currentAddress.pincode = $item.pincode;
            $scope.formData.currentAddress.city = $item;
            $scope.formPlaceholders.currentAddress.city = $label;
        } else {
            $scope.formData.permanentAddress.pincode = $item.pincode;
            $scope.formData.permanentAddress.city = $item;
            $scope.formPlaceholders.permanentAddress.city = $label;
        }
    };

    $scope.showQulFileError = function (index) {
        if (typeof($scope.formError.qualifications[index].doc) == 'boolean') {
            return $scope.formError.qualifications[index].doc;
        } else {
            return false;
        }
    };

    $scope.dobChange = function (date) {
        let month = date.getMonth();
        let day = date.getDate();
        if (month < 10) {
            month = '0' + month;
        }
        if (day < 10) {
            day = '0' + day;
        }
        return date.getFullYear() + '-' + month + '-' + day;
    };

    $scope.validatePan = function () {
        $scope.formError.ids.pan = false;
        if (validateIds.pan($scope.formData.ids.pan)) {
            $scope.formPlaceholders.idsError.pan = false;
        } else {
            $scope.formPlaceholders.idsError.pan = true;
        }
    };

    $scope.validateDigits = function (number, digits) {
        $scope.formError.currentAddress.mobile = false;
        let valid;
        if (validateIds.digitVal(number, digits)) {
            valid = false;
        } else {
            valid = true;
        }
        return valid;
    };

    $scope.validateDigitsCurrentAddress = function (number, digits) {
        $scope.formError.currentAddress.mobile2 = false;
        let valid;
        if (validateIds.digitVal(number, digits)) {
            valid = false;
        } else {
            valid = true;
        }
        return valid;
    };

    $scope.validateDigitsAdharNo = function (number, digits) {
        $scope.formError.ids.adhar = false;
        let valid;
        if (validateIds.digitVal(number, digits)) {
            valid = false;
        } else {
            valid = true;
        }
        return valid;
    };

    $scope.validateDigitsRationNo = function (number, digits) {
        $scope.formError.ids.ration = false;
        let valid;
        if (validateIds.digitVal(number, digits)) {
            valid = false;
        } else {
            valid = true;
        }
        return valid;
    };

    $scope.validateRange = function (number, min, max) {
        let valid;
        if (validateIds.rangeVal(number, min, max)) {
            valid = false;
        } else {
            valid = true;
        }
        return valid;
    };

    $scope.addEducation = function () {
        let qModel = $scope.formPlaceholders.qModelData;
        let qError = $scope.formPlaceholders.qModelError;
        for (let q in qModel) {
            if (qModel[q] == '' || qModel[q] == undefined || qModel[q] == null) {
                qError[q] = true;
            } else {
                qError[q] = false;
                if (q == 'percentage') {
                    var re = new RegExp('^[0-9]{2}(\.[0-9]{1,2})?$');
                    if (re.test(qModel[q])) {
                        qError[q] = false;
                    } else {
                        qError[q] = true;
                    }
                }
            }
        }
        let add = true;
        for (let e in qError) {
            if (qError[e]) {
                add = false;
                break;
            }
        }
        if (add) {
            $scope.formData.qualifications.push(new EducationModel(qModel));
            $scope.formError.qualifications.push(new EducationModel());
            $scope.formPlaceholders.qModelData = angular.copy($scope.defaultForm.qModelData);
            $scope.formPlaceholders.qModelError = angular.copy($scope.defaultForm.qModelError);
        }
    };

    $scope.deleteEducation = function (index) {
        $scope.formData.qualifications.splice(index, 1);
        $scope.formError.qualifications.splice(index, 1);
    };

    $scope.showFileError = function (status) {
        console.log('Status',status);
        //$scope.formError.licence.licenceDocs = false;
        if (typeof(status) != 'boolean') {
           return false;
        } else {
             $scope.formError.licence.licenceDocs = false;
            return !status;
        }
    };

    $scope.AcceptCheck = function (status) {
        if (typeof(status) == 'boolean') {
            return status;
        } else {
            return false;
        }
    };

    $scope.DenyCheck = function (status) {
        if (typeof(status) == 'boolean') {
            return !status;
        } else {
            return false;
        }
    };

    $scope.licClick = function ($evt) {
        if (typeof($scope.formData.lic.status) == 'string') {
            $scope.formData.lic.status = true;
            if ($evt.currentTarget.id == 'AcceptLic') {
                $scope.formData.lic.status = false;
            }
        }
        $scope.formData.lic.status = !$scope.formData.lic.status;
    };

    $scope.hpClick = function ($evt) {
        if (typeof($scope.formData.healthPol.status) == 'string') {
            $scope.formData.healthPol.status = true;
            if ($evt.currentTarget.id == 'AcceptHP') {
                $scope.formData.healthPol.status = false;
            }
        }
        $scope.formData.healthPol.status = !$scope.formData.healthPol.status;
    };

    $scope.mpClick = function ($evt) {
        if (typeof($scope.formData.medicalPol.status) == 'string') {
            $scope.formData.medicalPol.status = true;
            if ($evt.currentTarget.id == 'AcceptMP') {
                $scope.formData.medicalPol.status = false;
            }
        }
        $scope.formData.medicalPol.status = !$scope.formData.medicalPol.status;
    };

    $scope.pincodeKeyup = function (type) {
        var status = false;
        if (type == 'current') {
            status = $scope.formPlaceholders.currentAddressErrors.cityActive = $scope.formPlaceholders.currentAddressErrors.pincode = $scope.validateDigits($scope.formData.currentAddress.pincode, 6);
            $scope.formError.currentAddress.pincode = false;
        } else {
            status = $scope.formPlaceholders.permanentAddressErrors.cityActive = $scope.formPlaceholders.permanentAddressErrors.pincode = $scope.validateDigits($scope.formData.permanentAddress.pincode, 6);
            $scope.formError.permanentAddress.pincode = false;
        }
        
        if (!status) {
            $scope.autocompCity(type);
        }
    };

    $scope.addLanguage = function () {
        if ($scope.formPlaceholders.langModelData.language != 'Language') {
            if ($scope.formPlaceholders.langModelData.speak || $scope.formPlaceholders.langModelData.read || $scope.formPlaceholders.langModelData.write) {
                let exists = false;
                for (let l of $scope.formData.languages) {
                    if (l.language == $scope.formPlaceholders.langModelData.language) {
                        l.speak = $scope.formPlaceholders.langModelData.speak;
                        l.read = $scope.formPlaceholders.langModelData.read;
                        l.write = $scope.formPlaceholders.langModelData.write;
                        exists = true;
                    }
                }
                if (!exists) {
                    $scope.formData.languages.push(new LanguageModel($scope.formPlaceholders.langModelData));
                    $scope.formPlaceholders.langModelData = angular.copy($scope.defaultForm.langModelData);
                }
            }
        }
    };

    $scope.addVehicleType = function () {
        var vehicleType = JSON.parse($scope.formPlaceholders.vehicleType);
        if (vehicleType != '') {
            let exists = false;
            for (let v of $scope.formData.vehicleTypes) {
                if (v.name == vehicleType.name) {
                    exists = true;
                }
            }
            if (!exists) {
                $scope.formData.vehicleTypes.push(vehicleType);
            }
        }
    };

    $scope.addMachineType = function () {
        var vehicleType = JSON.parse($scope.formPlaceholders.machineType);
        if (vehicleType != '') {
            if (vehicleType.name != 'Machine Type' && vehicleType.name != '') {
                let exists = false;
                for (let v of $scope.formData.machineTypes) {
                    if (v.name == vehicleType.name) {
                        exists = true;
                    }
                }
                if (!exists) {
                    $scope.formData.machineTypes.push(vehicleType);
                }
            }
        }
    };

    $scope.removeVehicleType = function (index) {
        $scope.formData.vehicleTypes.splice(index, 1);
    };

    $scope.removeMachineType = function (index) {
        $scope.formData.machineTypes.splice(index, 1);
    };

    $scope.autocompCity = function (type,pin) {
        var deferred = $q.defer();
       // var isTrue = $scope.pincodeKeyup(type);
        if (true) {
            $http({
                method: 'POST',
                url: serverUrl + 'bluecollar/city-suggestion',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: {
                    'text': pin
                }
            }).then(function success(response) {
               deferred.resolve(response.data.data);
            }, function error(response) {
               deferred.reject(e);
            });
            return deferred.promise;
        }
    };

    $scope.addExperience = function () {
        let qModel = $scope.formPlaceholders.expModelData;
        let qError = $scope.formPlaceholders.expModelError;
        for (let q in qModel) {
            if (qModel[q] == '' || qModel[q] == undefined || qModel[q] == null) {
                qError[q] = true;
            } else {
                qError[q] = false;
                if (q == 'salary') {
                    var re = new RegExp('^[0-9]+$');
                    if (re.test(qModel[q])) {
                        qError[q] = false;
                    } else {
                        qError[q] = true;
                    }
                }
            }
        }
        let add = true;
        for (let e in qError) {
            if (qError[e]) {
                add = false;
                break;
            }
        }
        if (add) {
            $scope.formData.experience.push(new ExperienceModel(qModel));
            $scope.formPlaceholders.expModelData = angular.copy($scope.defaultForm.expModelData);
            $scope.formPlaceholders.expModelError = angular.copy($scope.defaultForm.expModelError);
        }
    };

    $scope.deleteExperience = function (index) {
        $scope.formData.experience.splice(index, 1);
    };

    $scope.showError = function (error) {
        if (typeof(error) == 'boolean') {
            return error;
        } else {
            return false;
        }
    };
    $scope.file = true;
    $scope.isValid = function () {
        var valid = true;
        $scope.formPlaceholders.currentAddressErrors.mobile2 = false;
        $scope.formPlaceholders.permanentAddressErrors.mobile2 = false;
        $scope.formPlaceholders.idsError.adhar = false;
        $scope.formPlaceholders.idsError.ration = false;
        $scope.formPlaceholders.idsError.pan = false;

        data = $scope.formData;
        console.log('Test',data);
        error = $scope.formError;

        if (data.accountType == '' || data.accountType == 'Registration For') {
            error.accountType = true;
            valid = false;
        } else {
            error.accountType = false;
        }

        if (data.firstName == '') {
            error.firstName = true;
            valid = false;
        } else {
            error.firstName = false;
        }

        if (data.lastName == '') {
            error.lastName = true;
            valid = false;
        } else {
            error.lastName = false;
        }

        if (data.dob == '') {
            error.dob = true;
            valid = false;
        } else {
            error.dob = false;
        }

        if (data.bg == '' || data.bg == 'Blood Group') {
            error.bg = true;
            valid = false;
        } else {
            error.bg = false;
        }

        for (let a in data.currentAddress) {
            switch (a) {
                case 'pincode':
                    if (data.currentAddress[a] == '' || !validateIds.digitVal(data.currentAddress[a], 6)) {
                        error.currentAddress[a] = true;
                        $scope.formPlaceholders.currentAddressErrors.pincode = false;
                        valid = false;
                    } else {
                        error.currentAddress[a] = false;
                    }
                    break;
                // case 'landline':
                case 'mobile':
                case 'mobile2':
                    if (data.currentAddress[a] == '' || !validateIds.digitVal(data.currentAddress[a], 10)) {
                        error.currentAddress[a] = true;
                        $scope.formPlaceholders.currentAddressErrors.mobile = false;
                        valid = false;
                    } else {
                        error.currentAddress[a] = false;
                    }
                    break;
                default:
                    if (data.currentAddress[a] == '') {
                        error.currentAddress[a] = true;
                        valid = false;
                    } else {
                        error.currentAddress[a] = false;
                    }
            }
        }

        if (data.permanentAddress.address != '') {
            for (let a in data.permanentAddress) {
                switch (a) {
                    case 'pincode':
                        if (data.permanentAddress[a] == '' || !validateIds.digitVal(data.permanentAddress[a], 6)) {
                            error.permanentAddress[a] = true;
                            $scope.formPlaceholders.permanentAddressErrors.pincode = false;
                            valid = false;
                        } else {
                            error.permanentAddress[a] = false;
                        }
                        break;
                    // case 'landline':
                    case 'mobile':
                    case 'mobile2':
                        if (data.permanentAddress[a] == '' || !validateIds.digitVal(data.permanentAddress[a], 10)) {
                            error.permanentAddress[a] = true;
                            valid = false;
                        } else {
                            error.permanentAddress[a] = false;
                        }
                        break;
                    default:
                        if (data.permanentAddress[a] == '') {
                            error.permanentAddress[a] = true;
                            valid = false;
                        } else {
                            error.permanentAddress[a] = false;
                        }
                }
            }
        }

        if (data.ids.email == '') {
            error.ids.email = true;
            valid = false;
        } else {
            error.ids.email = false;
        }

        if (data.ids.pan == '' || !validateIds.pan(data.ids.pan)) {
            error.ids.pan = true;
            valid = false;
        } else {
            error.ids.pan = false;
        }

        if (data.ids.panDoc == '' || typeof(data.ids.panDoc) == 'boolean') {
            error.ids.panDoc = true;
            valid = false;
        } else {
            error.ids.panDoc = false;
        }

        if (data.ids.adhar == '' || !validateIds.digitVal(data.ids.adhar, 12)) {
            error.ids.adhar = true;
            valid = false;
        } else {
            error.ids.adhar = false;
        }

        if (data.ids.adharDoc == '' || typeof(data.ids.adharDoc) == 'boolean') {
            error.ids.adharDoc = true;
            valid = false;
        } else {
            error.ids.adharDoc = false;
        }

        if (data.ids.ration == '' || !validateIds.digitVal(data.ids.ration, 12)) {
            error.ids.ration = true;
            valid = false;
        } else {
            error.ids.ration = false;
        }

        if (data.ids.rationDoc == '' || typeof(data.ids.rationDoc) == 'boolean') {
            error.ids.rationDoc = true;
            valid = false;
        } else {
            error.ids.rationDoc = false;
        }
        
        if (data.qualifications.length == 0) {
            error.qualificationsError = true;
            valid = false;
        } else {
            error.qualificationsError = false;
        }

        for (let q in data.qualifications) {
            for (let d in data.qualifications[q]) {
                if (data.qualifications[q][d] == '') {
                    error.qualifications[q][d] = true;
                    valid = false;
                } else {
                    error.qualifications[q][d] = false;
                }
            }
        }

        if (data.accountType == 'DRIVER' || data.accountType == 'SKILLED') {

            if (data.accountType == 'DRIVER') {
                if (data.vehicleTypes.length == 0) {
                    error.vehicleTypes = true;
                    valid = false;
                } else {
                    error.vehicleTypes = false;
                }
            } else {
                if (data.machineTypes.length == 0) {
                    error.machineTypes = true;
                    valid = false;
                } else {
                    error.machineTypes = false;
                }
            }

            for (let a in data.licence) {
                 if (data.licence[a] == '') {
                    error.licence[a] = true;
                    valid = false;
                   
                 }else{
                    error.licence[a] = false;
                } 

               if(a == 'no'){
                 if (!validateIds.licenceVal(data.licence.no)) {
                     error.licence[a] = true;
                     valid = false;
                 } else {
                     error.licence[a] = false;
                 }
               }
            }
            
            if (data.licence.licenceDoc == '' || typeof(data.licence.licenceDoc) == 'boolean') {
                error.licence.licenceDocs = true;
                $scope.showFileError(data.licence.licenceDoc);
                valid = false;
            } else {
                error.licence.licenceDocs = false;
            }

            if (data.experience.length == 0) {
                error.experience = true;
                valid = false;
            } else {
                error.experience = false;
            }
        }

        if (data.languages.length == 0) {
            error.languages = true;
            valid = false;
            $scope.formPlaceholders.languages = true;
        } else {
            error.languages = false;
        }

        if (!$scope.formPlaceholders.employmentType.fullTime && !$scope.formPlaceholders.employmentType.partTime && !$scope.formPlaceholders.employmentType.contract) {
            $scope.formError.employmentType = true;
            valid = false;
        } else {
            $scope.formData.employmentType = [];
            if ($scope.formPlaceholders.employmentType.fullTime) {
                $scope.formData.employmentType.push('FULL_TIME');
            }
            if ($scope.formPlaceholders.employmentType.partTime) {
                $scope.formData.employmentType.push('PART_TIME');
            }
            if ($scope.formPlaceholders.employmentType.contract) {
                $scope.formData.employmentType.push('CONTRACT');
            }
            $scope.formError.employmentType = false;
        }

        if (data.currentSalary == '' || isNaN(data.currentSalary)) {
            error.currentSalary = true;
            valid = false;
        } else {
            error.currentSalary = false;
        }
        if (data.totalExperience == '' || isNaN(data.totalExperience)) {
            error.totalExperience = true;
            valid = false;
        } else {
            error.totalExperience = false;
        }
        if (data.salaryType == '') {
            error.salaryType = true;
            valid = false;
        } else {
            error.salaryType = false;
        }
        return valid;
    };

    $scope.errorClass = function (error) {
        if (error == true) {
            return 'invalid-data';
        }
    };

    $scope.alphaOnly = function () {
        var key = event.keyCode;
        return ((key >= 65 && key <= 90) || key == 8);
    };

    $scope.validateEmail = function (email) {
       var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email);
    };

    $scope.checkEmail = function (email) {
        var valid = $scope.validateEmail(email);
        if(valid == false){
          $scope.formError.ids.email = true;
        }else{
          $scope.formError.ids.email = false;
          $http({
                method: 'POST',
                url: serverUrl + 'bluecollar/email-check-seller',
                headers: {
                    'authorization': 'Bearer ' + localStorage.getItem("access_token")
                },
                data: {'email':email}
                }).then(function (response) {
                 if(response.data.success == true){
                   valid = true;
                   $scope.formPlaceholders.permanentAddressErrors.email = true;
                }else{
                   valid = false;
                   $scope.formPlaceholders.permanentAddressErrors.email = false;
                }
          });
        }
        return valid;       
    };

    $scope.submitForm = function () {
            if($scope.isValid()) {
                $scope.first = false;
                $http({
                    method: 'POST',
                    url: serverUrl + 'bluecollar/seller-registration',
                    headers: {
                        'authorization': 'Bearer ' + localStorage.getItem("access_token")
                    },
                    data: $scope.formData
                }).then(function (response) {
                    var data = response.data;
                    if (data.success) {
                         $state.go('bluecollar-seller-reg-req-list');
                        //$('#confirmationModal').modal('show');
                    }
                }, function (response) {

                });
            }
    };

}]);
