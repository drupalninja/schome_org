<?php /* Static Name: Map */ ?>
<style type="text/css">
    /* Set a size for our map container, the Google Map will take up 100% of this container */
    #map {
        width: 100%;
        height: 486px;
    }
</style>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDYjQ8K58wMUoGYB5pvm6nbZUk_BL7NgCU&amp;sensor=false"></script>
<script type="text/javascript">
    // When the window has finished loading create our google map below
    google.maps.event.addDomListener(window, 'load', init);

    function init() {

    	// The latitude and longitude to center the map (always required)
    	var myLatlng = new google.maps.LatLng(<?php echo of_get_option('latitude'); ?>, <?php echo of_get_option('longitude'); ?>);

        // Basic options for a simple Google Map
        // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
        var mapOptions = {
            // How zoomed in you want the map to start at (always required)
            zoom: <?php echo of_get_option('zoom'); ?>,

            // The latitude and longitude to center the map (always required)
            center: myLatlng, // Santa Barbara

            // How you would like to style the map.
            // This is where you would paste any style found on Snazzy Maps.
            styles:
            [
                {"featureType":"water","stylers":[{"color":"#c6c2bf"}]},

                {"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#3b3734"}]},
                {"featureType":"administrative","elementType":"labels.text.stroke","stylers":[{"color":"transparent"}]},

                {"featureType":"administrative.province","elementType":"labels.text.fill","stylers":[{"color":"#aaa6a3"}]},

                {"featureType":"road","elementType":"labels","stylers":[{"saturation": -93}]},
                {"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#5c5855"}]},
                {"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"transparent"}]},

                {"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#a6a29f"}]},
                {"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#afaba8"}]},

                {"featureType":"road.arterial","elementType":"geometry.stroke","stylers":[{"color":"#c9c5c2"}]},
                {"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},

                {"featureType":"road.local","elementType":"geometry.fill","stylers":[{"color":"#c1bdba"}]},

                {"featureType":"transit","elementType":"geometry.fill","stylers":[{"color":"#c3bfbc"}]},

                {"featureType":"landscape.man_made","elementType":"geometry.fill","stylers":[{"color":"#e8e4e1"}]},
                {"featureType":"landscape.natural","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#f0ece9"}]},

                {"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#dcd8d5"}]},
                {"featureType":"poi.business","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#c9c5c2"}]},

                {"featureType":"poi.park","elementType":"labels","stylers":[{"saturation": -93},]},
                {"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#d6d2cf"}]},
                {"featureType":"poi","stylers":[{"visibility":"off"}]},
                {"featureType":"poi.park","stylers":[{"visibility":"on"}]},
                {"featureType":"poi.sports_complex","stylers":[{"visibility":"on"}]},
                {"featureType":"poi.medical","stylers":[{"visibility":"on"}]},
                {"featureType":"poi.business","stylers":[{"visibility":"simplified"}]},

                {"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#3b3734"}]},
                {"featureType":"poi","elementType":"labels.text.stroke","stylers":[{"color":"transparent"}]},
            ],

            scrollwheel: false,

            mapTypeControl: false,

            zoomControl: false,

            streetViewControl: false,

            panControl: false

        };

        // Create the Google Map using out element and options defined above
        var map = new google.maps.Map(document.getElementById('map'), mapOptions);

        var image = '<?php echo get_stylesheet_directory_uri(); ?>/images/marker.png';

        var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            icon: image
        });

        // To add the marker to the map, call setMap();
        marker.setMap(map);

    }
</script>
<div id="map"></div>