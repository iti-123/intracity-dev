// Use strict
"use strict";
var app = angular.module("lgApp", ["ngRoute", "ui.router", "angular.filter", "oc.lazyLoad","angular.chosen", "ui.bootstrap", "ngStorage", "rzModule", "pusher-angular","rx"]);

app.run(function ($rootScope) {
    
    localStorage.setItem("redirect_url",'');
    
    $rootScope.setAppTitle = function ($state) {
        $rootScope.activeState = ($state.current.name).toUpperCase().substr(0, 1) + ($state.current.name).toLowerCase().substr(1, ($state.current.name).length);
        $("#appTitle").html("Logistiks " + $rootScope.activeState);
    }
})