/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var hyperlocalapp = angular.module("hyperlocal", ['ui.bootstrap']);

hyperlocalapp.controller("searching", function ($scope) {

    $scope.searchFormSubmit = "Search";

    $scope.StartSearch = function () {

        if (!$("#searchForm").data('bootstrapValidator').validate().isValid()) {

            return false;
        }

        $scope.searchFormSubmit = "Processing...";
        $scope.$apply();

    };
});