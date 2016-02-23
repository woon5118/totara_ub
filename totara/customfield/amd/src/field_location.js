/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Ryan Adams <ryana@learningpool.com>
 * @package totara_customfield
 */

define(['jquery'], function ($) {
    var Location = function(args) {
        this.addressinfo = {};
        this.formprefix = '';
        var customfield = this;

        this.formprefix = (args.formprefix !== '' && typeof args.formprefix !== "undefined") ? args.formprefix : '';

        this.geocode_address = function () {
            var address = this.url_encode_address(this.addressinfo.address);
            if (address === '') {
                return;
            }

            $.ajax({
                url: 'https://maps.googleapis.com/maps/api/geocode/json?address=' + address
                + '&region=' + this.addressinfo.locationdefaults.regionbias,
                type: 'GET',
                success: function (data, response) {
                    if (response == 'success' && data.results.length > 0) {
                        customfield.addressinfo.address = data.results[0].formatted_address;
                        customfield.addressinfo.latlng = data.results[0].geometry.location;
                        customfield.load_map();
                    }
                },
                error: function (err, x) {

                }
            });
        };

        this.url_encode_address = function (address) {
            return address.replace(/\s+/g, '+');
        };

        this.load_map = function (forDisplay) {
            // This flag is used to differentiate between the two types of map (to reduce repetition) i.e. one for display
            // and one used on the edit page.
            forDisplay = typeof forDisplay !== 'undefined' ? forDisplay : true;

            if (this.addressinfo.latlng == 'undefined') {
                return;
            }

            var mapOptions = {
                center: {
                    lat: this.addressinfo.latlng.lat,
                    lng: this.addressinfo.latlng.lng
                },
                zoomControl: true,
                mapTypeControl: false,
                scaleControl: false,
                streetViewControl: true,
                rotateControl: false,
                mapTypeId: this.addressinfo.view,
                zoom: this.get_zoom_level()
            };

            this.addressinfo.map = new google.maps.Map(
                document.getElementById(this.formprefix + 'location_map'), mapOptions
            );
            if (!forDisplay) {
                this.set_map_view();
            }

            this.addressinfo.marker = new google.maps.Marker({
                position: {
                    lat: this.addressinfo.latlng.lat,
                    lng: this.addressinfo.latlng.lng
                },
                map: this.addressinfo.map,
                draggable: !forDisplay
            });

            $('#' + this.formprefix + 'latitude').val(this.addressinfo.latlng.lat);
            $('#' + this.formprefix + 'longitude').val(this.addressinfo.latlng.lng);

            if (!forDisplay) {
                this.addressinfo.marker.addListener('drag', function (e) {
                    $('#' + customfield.formprefix + 'latitude').val(e.latLng.lat());
                    $('#' + customfield.formprefix + 'longitude').val(e.latLng.lng());
                });
            } else {
                var contentString = "";

                if (this.addressinfo.address !== 'undefined' && this.addressinfo.address.length > 0) {
                    contentString = this.addressinfo.address;
                } else {
                    return;
                }

                var infowindow = new google.maps.InfoWindow({
                    content: contentString
                });

                this.addressinfo.marker.addListener('click', function () {
                    infowindow.open(customfield.addressinfo.map, customfield.addressinfo.marker);
                });
            }
        };

        this.set_map_view = function () {

            var selectedradio = $('.radio_view:checked');

            selectedradio.each( function( index, element ) {
                if (element.id.indexOf(customfield.formprefix) > -1) {
                    switch (element.value) {
                        case 'hybrid':
                            customfield.addressinfo.map.setMapTypeId(window.google.maps.MapTypeId.HYBRID);
                            customfield.addressinfo.view = window.google.maps.MapTypeId.HYBRID;
                            break;
                        case 'satellite':
                            customfield.addressinfo.map.setMapTypeId(window.google.maps.MapTypeId.SATELLITE);
                            customfield.addressinfo.view = window.google.maps.MapTypeId.SATELLITE;
                            break;
                        default:
                            customfield.addressinfo.map.setMapTypeId(window.google.maps.MapTypeId.ROADMAP);
                            customfield.addressinfo.view = window.google.maps.MapTypeId.ROADMAP;
                            break;
                    }
                }
            });
        };

        this.get_zoom_level = function () {
            if (typeof this.addressinfo.locationdefaults !== 'undefined') {
                return parseInt(this.addressinfo.locationdefaults.defaultzoomlevel);
            } else {
                return 12;
            }
        };


        $('#id_' + this.formprefix + 'searchaddress_btn').on('click', function (e) {
            e.preventDefault();
            customfield.addressinfo.address = $('#id_' + customfield.formprefix + 'addresslookup').val().trim();
            customfield.geocode_address();
        });

        $('#id_' + this.formprefix + 'useaddress_btn').on('click', function (e) {
            e.preventDefault();
            customfield.addressinfo.address = $('#id_' + customfield.formprefix + 'address').val().trim();
            customfield.geocode_address();
        });

        $('.radio_view').on('change', function (e) {
            if (this.id.indexOf(customfield.formprefix) > -1) {
                customfield.set_map_view();
                customfield.load_map(true);
            }
        });

        var map = $('#' + this.formprefix + 'location_map');

        if (!map.length) {
            return;
        }

        var forDisplay = args.fordisplay;

        this.addressinfo.locationdefaults = args;
        this.addressinfo.latlng = {};

        if ($('#' + this.formprefix + 'latitude').length) {
            this.addressinfo.latlng.lat = parseFloat($('#' + this.formprefix + 'latitude').val());
        } else {
            map.hide();
            return;
        }

        if ($('#' + this.formprefix + 'longitude').length) {
            this.addressinfo.latlng.lng = parseFloat($('#' + this.formprefix + 'longitude').val());
        } else {
            map.hide();
            return;
        }

        if ($('#' + this.formprefix + 'address').length) {
            this.addressinfo.address = $('#' + this.formprefix + 'address').val().trim();
        } else {
            this.addressinfo.address = "";
        }

        if ($('#' + this.formprefix + 'room-location-view').length) {
            if ($('#' + this.formprefix + 'room-location-view').val() == "map") {
                this.addressinfo.view = google.maps.MapTypeId.ROADMAP;
            } else if ($('#' + location.formprefix + 'room-location-view').val() == "satellite") {
                this.addressinfo.view = google.maps.MapTypeId.SATELLITE;
            } else {
                this.addressinfo.view = google.maps.MapTypeId.HYBRID;
            }
        } else {
            this.addressinfo.view = google.maps.MapTypeId.ROADMAP;
        }

        this.load_map(forDisplay);

    };

    return {
        init: function (args) {
            return new Location(args);
        }
    };
});