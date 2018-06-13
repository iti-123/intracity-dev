/**
 * Creates a new AuthenticationContext object.
 * Options for AuthenticationContext
 *
 **/
function AuthenticationContext() {
    this._userID = null;
    this._userName = null;
    this._accesstoken = null;
    this._expiresIn = null;
    this._isAuthenticated = false;
    this._stateRenew = false;
    this._userRole = 0;
    this._userEmail = null;
    this._userPhone = null;
    this._sub = null;
    this._iss = null;
    this._iat = null;
    this._nbf = null;
    this._jti = null;
    this._primary_role = null;
    this._secondary_role = null;
    this._active_role = null;
    this._primary_role_id = 0;
    this._secondary_role_id = 0;
    this._active_role_id = 0;
    this.getCachedToken();
}

// adding getter methods
AuthenticationContext.prototype.getUserName = function () {
    return this._userName;
};
AuthenticationContext.prototype.getUserID = function () {
    return this._userID;
};
AuthenticationContext.prototype.getAccessToken = function () {
    return this._accesstoken;
};
AuthenticationContext.prototype.getExpiresIn = function () {
    return this._expiresIn;
};
AuthenticationContext.prototype.isAuthenticated = function () {
    return this._isAuthenticated;
};
AuthenticationContext.prototype.getStateRenew = function () {
    return this._stateRenew;
};
AuthenticationContext.prototype.getUserRole = function () {
    return this._userRole;
};
AuthenticationContext.prototype.getUserSecondaryRole = function () {
    return this._secondary_role;
};
AuthenticationContext.prototype.getUserActiveRole = function () {
    return this._active_role;
};
AuthenticationContext.prototype.getUserActiveRoleId = function () {
    return this._active_role_id;
};
AuthenticationContext.prototype.getUserPrimaryRole = function () {
    return this._primary_role;
};
AuthenticationContext.prototype.getUserPrimaryRoleId = function () {
    return this._primary_role_id;
};
AuthenticationContext.prototype.getUserSecondaryRoleId = function () {
    return this._secondary_role_id;
};
AuthenticationContext.prototype.getUserEmail = function () {
    return this._userEmail;
};
AuthenticationContext.prototype.getUserPhone = function () {
    return this._userPhone;
};
AuthenticationContext.prototype.getSub = function () {
    return this._sub;
};
AuthenticationContext.prototype.getIssuedBy = function () {
    return this._iss;
};
AuthenticationContext.prototype.getIat = function () {
    return this._iat;
};
AuthenticationContext.prototype.getNbf = function () {
    return this._nbf;
};
AuthenticationContext.prototype.getJti = function () {
    return this._jti;
};

/**
 * 
 * Check authentication 
 * 
 */
    try {
        if (localStorage["access_token"] == undefined || localStorage["access_token"] == 0 || localStorage["access_token"] == null || localStorage["access_token"] == false || localStorage["access_token"] == '') {
            window.location.replace("login.html");
        }
    } catch (e) {
    }

// Decoding received JWT From laravel session
AuthenticationContext.prototype._decodeJwt = function (jwtToken) {
    var idTokenPartsRegex = /^([^\.\s]*)\.([^\.\s]+)\.([^\.\s]*)$/;

    var matches = idTokenPartsRegex.exec(jwtToken);
    if (!matches || matches.length < 4) {
        this._logstatus('The returned id_token is not parseable.');
        return null;
    }

    var crackedToken = {
        header: matches[1],
        JWSPayload: matches[2],
        JWSSig: matches[3]
    };

    return crackedToken;
};
// Browser check
AuthenticationContext.prototype._base64DecodeStringUrlSafe = function (base64IdToken) {
    // html5 should support atob function for decoding
    base64IdToken = base64IdToken.replace(/-/g, '+').replace(/_/g, '/');
    if (window.atob) {
        return decodeURIComponent(escape(window.atob(base64IdToken))); // jshint ignore:line
    }

    // TODO add support for this
    console.log('Browser is not supported');
    return null;
};



AuthenticationContext.prototype.getCachedToken = function () {
    var jwtToken = "";
    try {
        if (localStorage["access_token"] == undefined || localStorage["access_token"] == 0 || localStorage["access_token"] == null || localStorage["access_token"] == false || localStorage["access_token"] == '') {
            //localStorage.setItem("access_token", token);
            jwtToken = token;
            //return;
        } else {
            jwtToken = localStorage["access_token"];
        }
    } catch (e) {
    }

    // token token will be decoded to get the username
    var decodedToken = this._decodeJwt(jwtToken);
    if (!decodedToken) {
        return null;
    }
    try {
        var base64IdToken = decodedToken.JWSPayload;
        var base64Decoded = this._base64DecodeStringUrlSafe(base64IdToken);
        if (!base64Decoded) {
            console.log('The returned access_token could not be base64 url safe decoded.');
            return null;
        }

        var responceObj = JSON.parse(base64Decoded);
        // console.log(responceObj);
        if (responceObj.id != "") {
            this._isAuthenticated = true;
        } else {
            this._isAuthenticated = false;
        }
        try {
            this._userRole = responceObj.role;
        } catch (e) {
        }
        try {
            this._primary_role = responceObj.primary_role_name;
        } catch (e) {
        }
        try {
            this._primary_role_id = responceObj.primary_role_id;
        } catch (e) {
        }
        try {
            this._secondary_role = responceObj.secondary_role_name;
        } catch (e) {
        }
        try {
            this._secondary_role_id = responceObj.secondary_role_id;
        } catch (e) {
        }
        try {
            this._active_role = responceObj.active_role_name;
        } catch (e) {
        }
        try {
            this._active_role_id = responceObj.active_role_id;
        } catch (e) {
        }
        try {
            this._userID = responceObj.id;
        } catch (e) {
        }
        try {
            this._userName = responceObj.firstname;
        } catch (e) {
        }
        try {
            this._accesstoken = jwtToken;
        } catch (e) {
        }
        try {
            this._expiresIn = responceObj.exp;
        } catch (e) {
        }
        try {
            this._userEmail = responceObj.email;
        } catch (e) {
        }
        try {
            this._userPhone = responceObj.phone;
        } catch (e) {
        }
        try {
            this._sub = responceObj.sub;
        } catch (e) {
        }
        try {
            this._iss = responceObj.iss;
        } catch (e) {
        }
        try {
            this._iat = responceObj.iat;
        } catch (e) {
        }
        try {
            this._nbf = responceObj.nbf;
        } catch (e) {
        }
        try {
            this._jti = responceObj.jti;
        } catch (e) {
        }
    } catch (err) {
        console.log('The returned access_token could not be decoded: ' + err.stack);
    }
    return null;
};
AuthenticationContext.prototype.refreshToken = function () {
    console.log("I will check the expire time and Refresh");
};