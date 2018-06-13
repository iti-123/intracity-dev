app.directive("fileread", [function () {
    return {
        scope: {
            fileread: "="
        },
        link: function (scope, element, attributes) {
            element.bind("change", function (changeEvent) {
                var reader = new FileReader();
                var valid_formats = ["application/pdf", "image/jpeg", "image/png", "image/jpg"];
                var file_type = changeEvent.target.files[0].type;
                reader.onload = function (loadEvent) {
                    if ((file_type == valid_formats[0]) || (file_type == valid_formats[1]) || (file_type == valid_formats[2]) || (file_type == valid_formats[3])) {
                        scope.$apply(function () {
                            scope.fileread = loadEvent.target.result;
                        });
                    } else {
                        scope.$apply(function () {
                            scope.fileread = false;
                        });
                    }
                };
                reader.readAsDataURL(changeEvent.target.files[0]);
            });
        }
    }
}]);

app.directive("filereadWithName", [function () {
    return {
        scope: {
            filereadWithName: "=",
        },
        link: function (scope, element, attributes) {
            element.bind("change", function (changeEvent) {
                var reader = new FileReader();
                // var valid_formats = ["application/pdf", "image/jpeg", "image/png", "image/jpg"];
                var valid_formats = ["application/pdf","image/jpeg", "image/png", "image/jpg"];
                var file_type = changeEvent.target.files[0].type;
                reader.onload = function (loadEvent) {
                    if ((file_type == valid_formats[0]) || (file_type == valid_formats[1]) || (file_type == valid_formats[2]) || (file_type == valid_formats[3])) {
                        scope.$apply(function () {
                            scope.filereadWithName = {
                                doc: loadEvent.target.result,
                                name: changeEvent.target.files[0].name,
                                type: file_type
                            };
                            // scope.filereadWithName.doc = loadEvent.target.result;
                            // scope.filereadWithName.name = changeEvent.target.files[0].name;
                        });
                    } else {
                        scope.$apply(function () {
                            scope.filereadWithName = false;
                        });
                    }
                }
                reader.readAsDataURL(changeEvent.target.files[0]);
              // console.log('File',scope.filereadWithName);               
            });
        }
    }
}]);

app.directive('fromCalendar', function () {
    return {
        require: 'ngModel',
        link: function (scope, el, attr, ngModel) {
            $(el).datepicker({
                dateFormat: 'yy-mm-dd',
                yearRange:"-90:-18",
                maxDate: -0,
                defaultDate: '1927-01-01',
                autoclose: true,
                changeMonth: true,
                changeYear:true,
                
                onSelect: function (dateText) {

                    scope.$apply(function () {
                        ngModel.$setViewValue(dateText);
                    });
                }
            });
        }
    };
});
app.directive('toCalendar', function () {
    return {
        require: 'ngModel',
        link: function (scope, el, attr, ngModel) {
            $(el).datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: 0,
                defaultDate: 0,
                autoclose: true,
                changeMonth: true,
                changeYear: true,
                onSelect: function (dateText) {
                    scope.$apply(function () {
                        ngModel.$setViewValue(dateText);
                    });
                }
            });
        }
    };
});
app.directive('calender', function () {
    return {
        require: 'ngModel',
        link: function (scope, el, attr, ngModel) {
            $(el).datepicker({
                dateFormat: 'yy-mm-dd',

                autoclose: true,
                changeMonth: true,
                onSelect: function (dateText) {
                    scope.$apply(function () {
                        ngModel.$setViewValue(dateText);
                    });
                }
            });
        }
    };
});

app.directive('autocompleteLocation', ['$http', 'config', function ($http, config) {
    return {
        require: 'ngModel',
        link: function (scope, elem, attr, ngModel) {
            $(elem).autocomplete({
                source: function (request, response) {
                    $http({
                        method: 'GET',
                        url: config.serverUrl + 'bluecollar/buyer-location-search',
                        headers: {
                            'authorization': 'Bearer ' + localStorage.getItem("access_token")
                        },
                        data: {'search': request.term},
                    }).then(function (httpResponse) {
                        //console.log(httpResponse.data.data);
                        response($.map(httpResponse.data.data, function (item) {
                            //console.log(item);
                            var label = item.cur_state_or_city;
                            var val = label;
                            return {
                                label: label,
                                val: val
                            }
                        }));
                    }, function (response) {

                    });
                },
                select: function (event, ui) {
                    scope.$apply(function () {
                        ngModel.$setViewValue(ui.item.val);
                    });
                }
            });
        }
    };
}]);

app.directive('cityAutocomplete', ['$http', 'config', function ($http, config) {
    var cities;
    return {
        scope: {cityAutocomplete: '='},
        link: function (scope, elem, attr) {
            $(elem).autocomplete({
                source: function (request, response) {
                    $http({
                        method: 'POST',
                        url: config.serverUrl + 'bluecollar/city-suggestion',
                        headers: {
                            'authorization': 'Bearer ' + localStorage.getItem("access_token")
                        },
                        data: {'search': request.term},
                    }).then(function (httpResponse) {
                        //console.log(httpResponse.data.data);
                        cities = httpResponse.data;
                        response($.map(httpResponse.data, function (item) {
                            var label = item.city_name;
                            var val = item.id;
                            return {
                                label: label,
                                val: val
                            }
                        }));
                    }, function (response) {

                    });
                },
                select: function (event, ui) {
                    scope.$apply(function () {
                        scope.cityAutocomplete = ui.item.val;
                    });
                },
                change: function (event, ui) {
                    let exists = false;
                    for (c in cities) {
                        if (cities[c].city_name == event.target.value) {
                            exists = true;
                            scope.$apply(function () {
                                scope.cityAutocomplete = cities[c].id;
                            });
                        }
                    }
                    if (!exists) {
                        scope.$apply(function () {
                            scope.cityAutocomplete = '';
                        });
                    }
                }
            });
        }
    };
}
]);

app.directive('mySlider', function () {
    return {
        scope: {mySlider: '='},
        link: function (scope, el, attr) {
            $(el).slider({
                range: true,
                min: scope.mySlider.min,
                max: scope.mySlider.max,
                values: [scope.mySlider.min, scope.mySlider.max],
                stop: function (event, ui) {
                    scope.$apply(function () {
                        scope.mySlider.min = ui.values[0];
                        scope.mySlider.max = ui.values[1];
                    });
                }
            });
        }
    };
});

app.directive('onlyLettersInput', function () {
    return {
        require: 'ngModel',
        link: function (scope, element, attr, ngModelCtrl) {
            function fromUser(text) {
                var transformedInput = text.replace(/[^a-zA-Z]/g, '');
                //console.log(transformedInput);
                if (transformedInput !== text) {
                    ngModelCtrl.$setViewValue(transformedInput);
                    ngModelCtrl.$render();
                }
                return transformedInput;
            }

            ngModelCtrl.$parsers.push(fromUser);
        }
    };
});

app.directive('sanitizedInput', function () {
    return {
        require: 'ngModel',
        link: function (scope, element, attr, ngModelCtrl) {
            function fromUser(text) {
                var transformedInput = text.replace(/[^a-zA-Z0-9\-\/]/g, '');
                //console.log(transformedInput);
                if (transformedInput !== text) {
                    ngModelCtrl.$setViewValue(transformedInput);
                    ngModelCtrl.$render();
                }
                return transformedInput;
            }

            ngModelCtrl.$parsers.push(fromUser);
        }
    };
});

app.directive('numberInput', function () {
    return {
        require: 'ngModel',
        link: function (scope, element, attr, ngModelCtrl) {
            function fromUser(text) {
                var transformedInput = text.replace(/[^0-9]/g, '');
                //console.log(transformedInput);
                if (transformedInput !== text) {
                    ngModelCtrl.$setViewValue(transformedInput);
                    ngModelCtrl.$render();
                }
                return transformedInput;
            }

            ngModelCtrl.$parsers.push(fromUser);
        }
    };
});
