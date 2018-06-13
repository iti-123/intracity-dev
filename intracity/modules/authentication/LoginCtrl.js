'use strict';
$(document).ready(function () {
    $("#myLoginModal").modal('show');

    $(".login.loginmodal-submit").on('click', function (e) {
        e.preventDefault();
        var user = {};
        user.email = $("input[type=text]").val();
        user.password = $("input[type=password]").val();

        var empty = $('form#loginForm').find("input").filter(function () {
            return this.value === "";
        });
        console.log(empty.length);
        if (!empty.length) {
            logintNow(user);
        } else {
            alert('Enter User name and Password');
        }
    });


    function logintNow(user) {
        var formattedString = "login?email=" + user.email + "&password=" + user.password;
        $("#loginProgress").text("Login progress......For User ");
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASE_URL + formattedString,
            contentType: "application/json",
            success: function (data) {
            },
            error: function (error) {
                console.log(error.responseText);
                if (error.responseText.indexOf('error') > 0) {
                    alert('invalid_credentials');
                    $("#loginProgress").text("Login Failed!");                    
                } else {
                    localStorage.setItem("access_token", error.responseText);
                    var redirectUrl = localStorage.getItem('redirect_url');

                     console.log("NotNullredirectUrl::",typeof redirectUrl);

                    if(redirectUrl === '' || redirectUrl === null) {
                        window.location.replace("index.html");
                        console.log("redirectUrl::",redirectUrl);
                    } else {
                        localStorage.setItem('redirect_url','');
                        window.location = redirectUrl;
                        console.log("redirectUrlNotNull::",redirectUrl);
                    }                   
                    
                }
            }
        });
    }

});
