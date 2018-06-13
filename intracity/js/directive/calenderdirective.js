app.directive('fromcalendar', function () {
    return {
        require: 'ngModel',
        link: function (scope, el, attr, ngModel) {
            $(el).datepicker({
               // dateFormat: 'yy-mm-dd',
                dateFormat: 'dd/mm/yy',
                minDate: -0,
                defaultDate: -0,
                autoclose: true,
                changeMonth: true,
                onSelect: function (dateText) {
                    var newDate = $(this).datepicker('getDate');

                    if (newDate) { // Not null
                        newDate.setDate(newDate.getDate());
                        if($(this).val()==""){
                            $(this).next().removeClass("active");
                        }
                        else{
                           $(this).next().addClass("active");
                        }
                    }
                    $('#todate,#hpsellerrateto,#buyerPostValidTo,#returning1,#buyerPostTermValidTo,#buyerPostTermlastDate,#validTodTerm,#last_date_quote_submission,#eventEndDate').datepicker('option', 'minDate', newDate);
                    // $('#buyerPostValidTo,#returning1,#buyerPostTermValidTo,#buyerPostTermlastDate,#validTodTerm,#last_date_quote_submission').val('');
                    
                    scope.$apply(function () {
                        ngModel.$setViewValue(dateText);

                    });
                }
            });
        }
    };
});
app.directive('tocalendar', function () {
    return {
        require: 'ngModel',
        link: function (scope, el, attr, ngModel) {
            $(el).datepicker({
              
                 dateFormat: 'dd/mm/yy',
                minDate: -0,
                defaultDate: -0,
                autoclose: true,
                changeMonth: true,
                onSelect: function (dateText) {
                    var newDate = $(this).datepicker('getDate');

                    if (newDate) { // Not null
                        if($(this).val()==""){
                            $(this).next().removeClass("active");
                        }
                        else{
                           $(this).next().addClass("active");
                        }
                    }
                    scope.$apply(function () {
                        ngModel.$setViewValue(dateText);
                    });
                }
            });
        }
    };
});




app.directive('pastcalender', function () {
    return {
        require: 'ngModel',
        link: function (scope, el, attr, ngModel) {
            $(el).datepicker({
                dateFormat: 'dd/mm/yy',
                maxDate: 0,
                defaultDate: -0,
                autoclose: true,
                changeMonth: true,
                onSelect: function (dateText) {
                    var newDate = $(this).datepicker('getDate');

                    if (newDate) { // Not null
                        if($(this).val()==""){
                            $(this).next().removeClass("active");
                        }
                        else{
                           $(this).next().addClass("active");
                        }
                    }
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
                    var newDate = $(this).datepicker('getDate');

                    if (newDate) { // Not null
                        if($(this).val()==""){
                            $(this).next().removeClass("active");
                        }
                        else{
                           $(this).next().addClass("active");
                        }
                    }
                    scope.$apply(function () {
                        ngModel.$setViewValue(dateText);
                    });
                }
            });
        }
    };
});


app.directive('time-picker', function () {
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


app.directive('noSpecialChar', function() {
    return {
      require: 'ngModel',
      restrict: 'A',
      link: function(scope, element, attrs, modelCtrl) {
        modelCtrl.$parsers.push(function(inputValue) {
          if (inputValue == null)
            return ''
          cleanInputValue = inputValue.replace(/[^\w\s]/gi, '');
          if (cleanInputValue != inputValue) {
            modelCtrl.$setViewValue(cleanInputValue);
            modelCtrl.$render();
          }
          return cleanInputValue;
        });
      }
    }
  });

  app.directive("uploadFile", [function () {
    return {
        scope: {
            uploadFile: "=",
        },
        link: function (scope, element, attributes) {
            element.bind("change", function (changeEvent) {
                var reader = new FileReader();
                // var valid_formats = ["application/pdf", "image/jpeg", "image/png", "image/jpg"];
                var valid_formats = ["application/pdf","application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                "application/msword","image/jpeg", "image/png", "image/jpg"];
                var file_type = changeEvent.target.files[0].type;
                reader.onload = function (loadEvent) {
                    if ((file_type == valid_formats[0]) || (file_type == valid_formats[1]) || (file_type == valid_formats[2]) || (file_type == valid_formats[3])
                        || (file_type == valid_formats[4]) || (file_type == valid_formats[5])) {
                        scope.$apply(function () {
                            scope.uploadFile = {
                                doc: loadEvent.target.result,
                                name: changeEvent.target.files[0].name,
                                type: file_type
                            };
                            console.log("scope.uploadFile::",scope.uploadFile);
                            // scope.filereadWithName.doc = loadEvent.target.result;
                            // scope.filereadWithName.name = changeEvent.target.files[0].name;
                        });
                    } else {
                        scope.$apply(function () {
                            scope.uploadFile = false;
                        });
                    }
                }
                reader.readAsDataURL(changeEvent.target.files[0]);
            });
        }
    }
}]);

 app.directive('ngEnter', function() {
    return function(scope, elem, attrs) {
      elem.bind("keydown keypress", function(event) {
        // 13 represents enter button
        if (event.which === 13) {
          $scope.$apply(function() {
            $scope.$eval(attrs.ngEnter);
          });

          event.preventDefault();
        }
      });
    };
  });


app.directive('selectpicker', ['$parse', function ($parse) {
    return {
      restrict: 'A',
      link: function (scope, element, attrs) {
        element.selectpicker($parse(attrs.selectpicker)());
        element.selectpicker('refresh');
        scope.$watch(attrs.ngModel, function (newVal, oldVal) {
          scope.$parent[attrs.ngModel] = newVal;
         
          scope.$evalAsync(function () {
            if (newVal && newVal.id) {
                if (!attrs.ngOptions || /track by/.test(attrs.ngOptions)) element.val(newVal.id);
                element.selectpicker('refresh');
            } else {
                if (!attrs.ngOptions || /track by/.test(attrs.ngOptions)) element.val(newVal);
                element.selectpicker('refresh');
            }
            element.selectpicker('refresh');

          });
        });

        scope.$on('$destroy', function () {
          scope.$evalAsync(function () {
            element.selectpicker('destroy');
          });
        });
        scope.$on('$recreate', function () {
            scope.$watch(attrs.ngModel, function (newVal, oldVal) {
            scope.$parent[attrs.ngModel] = newVal;
            scope.$evalAsync(function () {

                if (newVal && newVal.id) {
                    if (!attrs.ngOptions || /track by/.test(attrs.ngOptions)) element.val(newVal.id);
                    element.selectpicker('refresh');
                } else {
                    if (!attrs.ngOptions || /track by/.test(attrs.ngOptions)) element.val(newVal);
                    element.selectpicker('refresh');
                }

            });
        });
        });
      }
    };

  }]);



app.directive("jsplaceholder",[function(){
    return {
        link:function(scope,el,attr) {
            el.bind('change',function(event) {
                if($(this).val()=="") { $(this).next().removeClass("active");} 
                else { $(this).next().addClass("active"); }           
            });                    
        }
    }
}]);






