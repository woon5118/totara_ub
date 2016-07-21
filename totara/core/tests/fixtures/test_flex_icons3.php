<?php

/*
 * Fixture for totara_core_flex_icon_helper_testcase::test_protected_merge_flex_icons_file().
 *
 * Tests overriding via flex_icons.php
 *
 */

$translations = array(
    'add' => 'minus',
    'remove' => 'plus',
);

$map = array(
    'icon' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-edit ft-state-warning',
                ),
        ),
    'fancy' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' => 'fa fa-circle',
                ),
        ),
);

$deprecated = array(
    'nav_entry' => 'caret-down',
);

$defaults = array(
    'data' => array(
        'template' => 'core/flex_icon2',
    )
);
