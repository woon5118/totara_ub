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
    'totara_user_validate_password' => array(
        'classname' => 'local_totarahola_external',
        'methodname' => 'totara_user_validate_password',
        'description' => 'Return the if password is correct',
        'type' => 'read',
        'ajax' => true,
        'services' => array(LOCAL_TOTARAHOLA_SERVICES)
    ),
    'local_totarahola_get_course' => array(
        'classname' => 'local_totarahola_external',
        'methodname' => 'get_course',
        'description' => 'Return a match course id',
        'type' => 'read',
        'ajax' => true,
        'services' => array(LOCAL_TOTARAHOLA_SERVICES),
    ),
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
    ),
    'local_totarahola_get_competency' => array(
        'classname' => 'local_totarahola_external',
        'methodname' => 'get_competency',
        'description' => 'Return a competency which match the given id',
        'type' => 'read',
        'ajax' => true,
        'services' => array(LOCAL_TOTARAHOLA_SERVICES),
    ),
    'local_totarahola_get_competency_criteria' => array(
        'classname' => 'local_totarahola_external',
        'methodname' => 'get_competency_criteria',
        'decription' => 'Return list of courses belong assigned to a competency',
        'type' => 'read',
        'ajax' => true,
        'service' => array(LOCAL_TOTARAHOLA_SERVICES),
    ),
    'local_totarahola_get_user_learning_plan' => array(
        'classname' => 'local_totarahola_external',
        'methodname' => 'get_user_learning_plan',
        'description' => 'Return list of leanring plan user',
        'type' => 'read',
        'ajax' => true,
        'service' => array(LOCAL_TOTARAHOLA_SERVICES),
    ),
    'local_totarahola_set_learning_plan' => array(
        'classname' => 'local_totarahola_external',
        'methodname' => 'set_learning_plan',
        'description' => 'Add new user learning plan',
        'type' => 'write',
        'ajax' => true,
        'services' => array(LOCAL_TOTARAHOLA_SERVICES),
    ),
    'local_totarahola_get_learning_plan' => array(
        'classname' => 'local_totarahola_external',
        'methodname' => 'get_learning_plan',
        'description' => 'Return list user learning plan',
        'type' => 'read',
        'ajax' => true, 
        'services' => array(LOCAL_TOTARAHOLA_SERVICES),
    ),
);