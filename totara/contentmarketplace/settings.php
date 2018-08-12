<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Sergey Vidusov <sergey.vidusov@androgogic.com>
 * @package totara_contentmarketplace
 */

defined('MOODLE_INTERNAL') || die;

use core_course\workflow_manager\coursecreate;
use totara_contentmarketplace\workflow_manager\exploremarketplace;

$context = context_system::instance();

if ($hassiteconfig or has_any_capability(array('totara/contentmarketplace:config', 'totara/contentmarketplace:add'), $context)) {

    $ADMIN->add('root', new admin_category(
            'contentmarketplace',
            get_string('contentmarketplace', 'totara_contentmarketplace')
        ),
        'appearance'
    );

    // Check for explicit disable via config.php
    $forcedisabled = (!\totara_contentmarketplace\local::is_enabled() && array_key_exists('enablecontentmarketplaces', $CFG->config_php_settings));
    // Check if enabled and configured.
    $alreadysetup = (\totara_contentmarketplace\local::is_enabled() && !\totara_contentmarketplace\local::should_show_admin_setup_intro());

    // Hide if force disabled or already setup.
    $setuphidden = ($forcedisabled || $alreadysetup);
    $ADMIN->add('contentmarketplace', new admin_externalpage(
        'setup_content_marketplaces',
        get_string('setup_content_marketplaces', 'totara_contentmarketplace'),
        $CFG->wwwroot.'/totara/contentmarketplace/setup.php',
        'totara/contentmarketplace:config',
        $setuphidden
    ));

    // Hide unless marketplaces are already setup.
    $managehidden = !$alreadysetup;
    $ADMIN->add('contentmarketplace', new admin_externalpage(
        'manage_content_marketplaces',
        get_string('manage_content_marketplaces', 'totara_contentmarketplace'),
        $CFG->wwwroot.'/totara/contentmarketplace/marketplaces.php',
        'totara/contentmarketplace:config',
        $managehidden
    ));

    $beforesibling = $ADMIN->locate('addcategory') ? 'addcategory' : '';
    $wm = new exploremarketplace();
    $explorehidden = !\totara_contentmarketplace\local::is_enabled() || !$wm->workflows_available();
    $url = $wm->get_url();
    $ADMIN->add(
        'courses',
        new admin_externalpage(
            'exploremarketplaces',
            new lang_string('explore_totara_content', 'totara_contentmarketplace'),
            $url,
            array('totara/contentmarketplace:add'),
            $explorehidden
        ),
        $beforesibling
    );

}
