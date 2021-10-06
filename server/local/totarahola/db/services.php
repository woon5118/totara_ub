<?php
/*
*
*
*
*/
define('LOCAL_TOTARAHOLA_SERVICES', 'local_totarahola_services');

// define the web service functions to install
$functions = array(
    'local_totarahola_get_popular_courses' => array(
        'classname' => 'local_totarahola_external',
        'methodname' => 'get_popular_courses',
        'description' => 'Return the most popular courses',
        'type' => 'read',
        'ajax' => true,
        'services' => array(LOCAL_TOTARAHOLA_SERVICES)
    )
);