app.service('GoogleMapService', function ($q, $http) {
    return {
        calculateDistance: function (fromLocation, toLocation) {
            var deferred = $q.defer();

            // console.log("fromLocation::",fromLocation);
            // console.log("toLocation::",toLocation);
            

            var directionsService = new google.maps.DirectionsService();

            var start = fromLocation;
            var end = toLocation;

            console.log("Start::" + start);
            console.log("End::" + end);

            var waypts = [];
            var checkboxArray = [];
            checkboxArray = document.getElementById('waypoints');

            // for (var i = 0; i < checkboxArray.length; i++) {
            //     if (checkboxArray.options[i].selected == true) {
            //         waypts.push({
            //             location: checkboxArray[i].value,
            //             stopover: true
            //         });
            //     }
            // }

            // console.log("Way Points::", waypts);


            var request = {
                origin: start,
                destination: end,
                waypoints: waypts,
                travelMode: google.maps.TravelMode.DRIVING
            };
            directionsService.route(request, function (response, status) {
                console.log("Status::", status);
                var tempRoute = [];
                  var routes = [];
                if (status == google.maps.DirectionsStatus.OK) {
                    route = response.routes[0];
                 for (var i = 0; i < route.legs.length; i++) {
                        tempRoute = {
                            'routeSegment': i + 1,
                            'start_address': route.legs[i].start_address,
                            'end_address': route.legs[i].end_address,
                            'duration': route.legs[i].duration.text,
                            'distance': route.legs[i].distance.text
                        };
                        routes.push(tempRoute);
                    }
                    deferred.resolve(routes);
                }/*else {
                    deferred.resolve(routes);
                }*/

            });

            return deferred.promise;
        },
        getDistanceInKM: function(distance) {
            var deferred = $q.defer();
            let distanceInKm;
            let d = distance.split(" ");
            try {
                if(d[1] === 'km') {
                    distanceInKm = d[0]
                } else if(d[1] === 'm') {
                    distanceInKm = d[0]/1000;
                }
                deferred.resolve(distanceInKm);
            } catch(err) {
                deferred.reject(err);
            }
                
            return deferred.promise;
        },
        getDistanceInMinutes: function(duration) {
            var deferred = $q.defer();
            let distanceInMinutes;
            let t = duration.split(" ");
            try {
                if(t[1] === 'hours') {
                    distanceInMinutes = t[0]*60 + t[2];
                } else if(t[1] == 'mins') {
                    distanceInMinutes =  t[0];
                }
                deferred.resolve(distanceInMinutes);
            } catch(err) {
                deferred.reject(err);
            }

            return deferred.promise;
        }



    }
});