<?php
/*
*
*
*
*/
define('LOCAL_TOTARAHOLA_SERVICES', 'local_totarahola_services');
define('TOTARA_HOLA_SERVICES', 'totara-hola');

// define the web service functions to install
$functions = array(
    'local_totarahola_get_popular_courses' => array(
        'classname' => 'local_totarahola_external',
        'methodname' => 'get_popular_courses',
        'description' => 'Return the most popular courses',
        'type' => 'read',
        'ajax' => true,
        'services' => array(LOCAL_TOTARAHOLA_SERVICES)
    ),
    // 'local_totarahola_get_competencies' => array(
    //     'classname' => 'local_totarahola_external',
    //     'methodname' => 'get_competencies',
    //     'description' => 'Return list of competencies belong to a frameworks',
    //     'type' => 'read',
    //     'ajax' => true,
    //     'services' => array(LOCAL_TOTARAHOLA_SERVICES)
    // ),
    'local_totarahola_get_competency_frameworks' => array(
        'classname' => 'local_totarahola_external',
        'methodname' => 'get_competency_frameworks',
        'description' => 'Return frameworks list',
        'type' => 'read',
        'ajax' => true,
        'services' => array(LOCAL_TOTARAHOLA_SERVICES, TOTARA_HOLA_SERVICES),
    ),
    'local_totarahola_get_competency_with_more_courses' => array(
        'classname' => 'local_totarahola_external',
        'methodname' => 'get_competency_with_more_courses',
        'description' => 'Return list of competency with more courses',
        'type' => 'read',
        'ajax' => true,
        'services' => array(LOCAL_TOTARAHOLA_SERVICES),
    ),
    'local_totarahola_get_framework_competencies' => array(
        'classname' => 'local_totarahola_external',
        'methodname' => 'get_framework_competencies',
        'description' => 'Return list of competencies belong to a framework',
        'type' => 'read',
        'ajax' => true,
        'services' => array(LOCAL_TOTARAHOLA_SERVICES),
    )
);