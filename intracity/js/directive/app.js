var app = angular.module("lgApp", []);
app.directive("header", function () {
    return {
        template: "<h1>Made by a directive!</h1>"
    };
});
