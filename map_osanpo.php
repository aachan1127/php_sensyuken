<!DOCTYPE html>
<html>
<head>
    <title>Nearby Parks</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo$API_KEY?>&&libraries=places"></script>
</head>
<body>
    <h1>Nearby Parks</h1>
    <div id="map" style="height: 500px; width: 100%;"></div>

    <script>
        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 14,
                center: { lat: 33.5902, lng: 130.4017 } // 福岡市の中心座標
            });

            var service = new google.maps.places.PlacesService(map);
            var request = {
                location: map.getCenter(),
                radius: 5000, // 半径5km
                type: ['park'] // 公園のタイプを指定
            };

            service.nearbySearch(request, function(results, status) {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    for (var i = 0; i < results.length; i++) {
                        createMarker(results[i]);
                    }
                }
            });

            function createMarker(place) {
                var marker = new google.maps.Marker({
                    map: map,
                    position: place.geometry.location,
                    title: place.name
                });

                google.maps.event.addListener(marker, 'click', function() {
                    window.location.href = 'park_detail.php?place_id=' + place.place_id;
                });
            }
        }

        google.maps.event.addDomListener(window, 'load', initMap);
    </script>
</body>
</html>
