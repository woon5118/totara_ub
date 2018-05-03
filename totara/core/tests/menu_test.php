<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2015 onwards Totara Learning Solutions LTD
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
 * @author Petr Skoda <petr.skoda@totaralms.com>
 * @package totara_core
 */

defined('MOODLE_INTERNAL') || die();

use totara_core\totara\menu\menu;

/**
 * To test, run this from the command line from the $CFG->dirroot.
 * vendor/bin/phpunit --verbose totara_core_menu_testcase totara/core/tests/menu_test.php
 */
class totara_core_menu_testcase extends advanced_testcase {
    public function test_url_replace() {
        global $COURSE;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        $this->setUser($user);
        $COURSE = $course;

        $url = 'http://example.com/##username##/index.php?id=##userid##&course=##courseid###xxx##useremail##';
        $result = \totara_core\totara\menu\menu::replace_url_parameter_placeholders($url);
        $encodedemail = urlencode($user->email);
        $encodedusername = urlencode($user->username);
        $this->assertSame("http://example.com/{$encodedusername}/index.php?id={$user->id}&course={$course->id}#xxx{$encodedemail}", $result);
    }

    public function test_validate() {
        $this->resetAfterTest(true);

        $data = new stdClass();
        $data->title = 'Some title';
        $data->custom = '1';
        $data->url = 'http://example.com/##username##/index.php?id=##userid##&course=##courseid###xxx##useremail##';
        $data->classname = 'someclass';
        $data->targetattr = '_blank';
        $errors = \totara_core\totara\menu\menu::validation($data);
        $this->assertSame(array(), $errors);

        $data = new stdClass();
        $data->title = 'Some title';
        $data->custom = '0';
        $data->classname = 'someclass';
        $data->targetattr = '_blank';
        $errors = \totara_core\totara\menu\menu::validation($data);
        $this->assertSame(array(), $errors);

        $data = new stdClass();
        $data->title = str_pad('sometitle', 1025, 'x');
        $data->custom = '1';
        $data->url = str_pad('/', 256, 'x');
        $data->classname = str_pad('someclass', 256, 'x');
        $data->targetattr = str_pad('_blank', 101, '_');
        $errors = \totara_core\totara\menu\menu::validation($data);
        $this->assertCount(4, $errors);
        $this->assertArrayHasKey('title', $errors);
        $this->assertArrayHasKey('url', $errors);
        $this->assertArrayHasKey('classname', $errors);
        $this->assertArrayHasKey('targetattr', $errors);

        $data = new stdClass();
        $data->title = '';
        $data->custom = '1';
        $data->url = '';
        $data->classname = '';
        $data->targetattr = '';
        $errors = \totara_core\totara\menu\menu::validation($data);
        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('title', $errors);

        $data = new stdClass();
        $data->title = 'Some title';
        $data->custom = '1';
        $data->url = 'http:/xxx';
        $errors = \totara_core\totara\menu\menu::validation($data);
        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('url', $errors);
    }

    public function setup_tree_data() {
        $this->resetAfterTest();

        $data = new class() {
            /** @var array */
            public $noderecords = [];
        };

        /** @var totara_core_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_core');

        // Make some test data.
        // 1a => 2a => 3a
        //          => 3b
        //    => 2b => 3c
        //          => 3d
        //    => 2c
        // 1b => 2d
        // 1c
        // 1d
        $data->noderecords['node1a'] = $generator->create_menu_item(
            ['title' => 'node1a', 'visibility' => menu::SHOW_ALWAYS, 'url' => '.']);
        $data->noderecords['node1b'] = $generator->create_menu_item(
            ['title' => 'node1b', 'visibility' => menu::SHOW_ALWAYS, 'url' => '.']);
        $data->noderecords['node1c'] = $generator->create_menu_item(
            ['title' => 'node1c', 'visibility' => menu::SHOW_ALWAYS, 'url' => '.']);
        $data->noderecords['node1d'] = $generator->create_menu_item(
            ['title' => 'node1d', 'visibility' => menu::SHOW_ALWAYS, 'url' => '.']);

        $data->noderecords['node2a'] = $generator->create_menu_item(
            ['title' => 'node2a', 'visibility' => menu::SHOW_ALWAYS, 'url' => '.', 'parentid' => $data->noderecords['node1a']->id]);
        $data->noderecords['node2b'] = $generator->create_menu_item(
            ['title' => 'node2b', 'visibility' => menu::SHOW_ALWAYS, 'url' => '', 'parentid' => $data->noderecords['node1a']->id]);
        $data->noderecords['node2c'] = $generator->create_menu_item(
            [
                'title' => 'node2c',
                'visibility' => menu::SHOW_ALWAYS,
                'url' => '',
                'classname' => '\totara_core\totara\menu\home',
                'custom' => menu::DEFAULT_ITEM,
                'parentid' => $data->noderecords['node1a']->id,
        ]);
        $data->noderecords['node2d'] = $generator->create_menu_item(
            ['title' => 'node2d', 'visibility' => menu::HIDE_ALWAYS, 'url' => '.', 'parentid' => $data->noderecords['node1b']->id]);

        $data->noderecords['node3a'] = $generator->create_menu_item(
            ['title' => 'node3a', 'visibility' => menu::SHOW_ALWAYS, 'url' => '.', 'parentid' => $data->noderecords['node2a']->id]);
        $data->noderecords['node3b'] = $generator->create_menu_item(
            ['title' => 'node3b', 'visibility' => menu::SHOW_ALWAYS, 'url' => '.', 'parentid' => $data->noderecords['node2a']->id]);
        $data->noderecords['node3c'] = $generator->create_menu_item(
            ['title' => 'node3c', 'visibility' => menu::SHOW_ALWAYS, 'url' => '', 'parentid' => $data->noderecords['node2b']->id]);
        $data->noderecords['node3d'] = $generator->create_menu_item(
            [
                'title' => 'node3d',
                'visibility' => menu::SHOW_ALWAYS,
                'url' => '.',
                'classname' => '\tool_sitepolicy\totara\menu\userpolicy',
                'custom' => menu::DEFAULT_ITEM,
                'parentid' => $data->noderecords['node2b']->id,
        ]);

        return $data;
    }

    public function test_max_relative_descendant_depth() {
        $data = $this->setup_tree_data();

        $node1a = menu::get($data->noderecords['node1a']->id);
        $this->assertEquals(2, $node1a->max_relative_descendant_depth());

        $node1c = menu::get($data->noderecords['node1c']->id);
        $this->assertEquals(0, $node1c->max_relative_descendant_depth());

        $node2a = menu::get($data->noderecords['node2a']->id);
        $this->assertEquals(1, $node2a->max_relative_descendant_depth());

        $node2d = menu::get($data->noderecords['node2d']->id);
        $this->assertEquals(0, $node2d->max_relative_descendant_depth());

        $node3a = menu::get($data->noderecords['node3a']->id);
        $this->assertEquals(0, $node3a->max_relative_descendant_depth());

        $unsavedemptyitem = menu::get();
        $this->assertEquals(0, $unsavedemptyitem->max_relative_descendant_depth());
    }

    public function test_can_set_depth() {
        $data = $this->setup_tree_data();

        // Test with a real, saved node.
        $node2a = menu::get($data->noderecords['node2a']->id); // Has one level of descendants.

        // Test with a node which already exists (e.g. when updating).
        $this->assertFalse($node2a->can_set_depth(0));
        $this->assertTrue($node2a->can_set_depth(1));
        $this->assertTrue($node2a->can_set_depth(menu::MAX_DEPTH - 1));
        $this->assertFalse($node2a->can_set_depth(menu::MAX_DEPTH)); // Child would be MAX_DEPTH + 1.
        $this->assertFalse($node2a->can_set_depth(menu::MAX_DEPTH + 1));

        // Test with a node which hasn't been saved yet.
        $unsavedemptyitem = menu::get();
        $this->assertFalse($unsavedemptyitem->can_set_depth(0));
        $this->assertTrue($unsavedemptyitem->can_set_depth(1));
        $this->assertTrue($unsavedemptyitem->can_set_depth(menu::MAX_DEPTH - 1));
        $this->assertTrue($unsavedemptyitem->can_set_depth(menu::MAX_DEPTH)); // No children, so ok.
        $this->assertFalse($unsavedemptyitem->can_set_depth(menu::MAX_DEPTH + 1));
    }

    public function test_create_depth() {
        $data = $this->setup_tree_data();

        /** @var totara_core_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_core');

        // Setup already contains valid data using 'create()' (via the generator), so we just need to check the exception.
        // Note that if the generator is modified then this test will most likely fail.
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Tried to create a menu item that was deeper than the maximum depth');
        $generator->create_menu_item(['url' => '.', 'parentid' => $data->noderecords['node3a']->id]);
    }

    public function test_update_depth() {
        $data = $this->setup_tree_data();

        // Move a level 2 node up to level 1.
        $node2a = menu::get($data->noderecords['node2a']->id); // Has one level of descendants.
        $this->assertEquals(2, $node2a->depth);
        $this->assertTrue($node2a->update(['parentid' => 0]));
        $node2a = menu::get($node2a->id); // Reload the object.
        $this->assertEquals(1, $node2a->depth);
        unset($node2a); // Prevent these objects being used below.

        // Move a level 3 node up to level 2.
        $node3c = menu::get($data->noderecords['node3c']->id); // Has one level of descendants.
        $this->assertEquals(3, $node3c->depth);
        $node1c = menu::get($data->noderecords['node1c']->id);
        $this->assertEquals(1, $node1c->depth);
        $this->assertTrue($node3c->update(['parentid' => $node1c->id]));
        $node3c = menu::get($node3c->id); // Reload the object.
        $this->assertEquals(2, $node3c->depth);
        unset($node3c, $node1c); // Prevent these objects being used below.

        // Move a level 1 node without descendants down to level 3.
        $node1d = menu::get($data->noderecords['node1d']->id);
        $this->assertEquals(1, $node1d->depth);
        $node2c = menu::get($data->noderecords['node2c']->id);
        $this->assertEquals(2, $node2c->depth);
        $this->assertTrue($node1d->update(['parentid' => $node2c->id]));
        $node1d = menu::get($node1d->id); // Reload the object.
        $this->assertEquals(3, $node1d->depth);
        unset($node1d, $node2c); // Prevent these objects being used below.

        // Fail to move a level 2 node with descendants down to level 3.
        $node2b = menu::get($data->noderecords['node2b']->id);
        $this->assertEquals(2, $node2b->depth);
        $node2d = menu::get($data->noderecords['node2d']->id);
        $this->assertEquals(2, $node2d->depth);

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage('You cannot move this item to the selected parent because it has descendants. Please move this item\'s descendants first.');
        $this->assertTrue($node2b->update(['parentid' => $node2d->id]));
    }

    public function test_update_descendant_depths() {
        $data = $this->setup_tree_data();

        // Move a level 2 node up to level 1. Descendants should now be level 2.
        $node2a = menu::get($data->noderecords['node2a']->id); // Node to move.
        $node3a = menu::get($data->noderecords['node3a']->id); // Descendant.
        $node3b = menu::get($data->noderecords['node3b']->id); // Descendant.
        $this->assertEquals(2, $node2a->depth);
        $this->assertEquals(3, $node3a->depth);
        $this->assertEquals(3, $node3b->depth);

        $this->assertTrue($node2a->update(['parentid' => 0]));

        $node3a = menu::get($data->noderecords['node3a']->id); // Descendant moved.
        $node3b = menu::get($data->noderecords['node3b']->id); // Descendant moved.
        $this->assertEquals(2, $node3a->depth);
        $this->assertEquals(2, $node3b->depth);
        unset($node2a, $node3a, $node3b); // Prevent these objects being used below.

        // Move a level 1 node down to level 2. Descendants should now be level 2.
        $node1b = menu::get($data->noderecords['node1b']->id); // Node to move.
        $node2d = menu::get($data->noderecords['node2d']->id); // Descendant.
        $node1a = menu::get($data->noderecords['node1a']->id); // New parent.
        $this->assertEquals(1, $node1b->depth);
        $this->assertEquals(2, $node2d->depth);
        $this->assertEquals(1, $node1a->depth);

        $this->assertTrue($node1b->update(['parentid' => $node1a->id]));

        $node2d = menu::get($data->noderecords['node2d']->id); // Descendant moved.
        $this->assertEquals(3, $node2d->depth);
    }

    public function test_update_descendant_paths() {
        $data = $this->setup_tree_data();

        // Move a level 2 node up to level 1. Descendants should now be level 2.
        $node1a = menu::get($data->noderecords['node1a']->id); // Top node.
        $node2a = menu::get($data->noderecords['node2a']->id); // Node to move.
        $node3a = menu::get($data->noderecords['node3a']->id); // Descendant.
        $node3b = menu::get($data->noderecords['node3b']->id); // Descendant.
        $this->assertEquals('/' . $node1a->id, $node1a->path);
        $this->assertEquals($node1a->path . '/' . $node2a->id, $node2a->path);
        $this->assertEquals($node2a->path . '/' . $node3a->id, $node3a->path);
        $this->assertEquals($node2a->path . '/' . $node3b->id, $node3b->path);

        $this->assertTrue($node2a->update(['parentid' => 0]));

        $node2a = menu::get($data->noderecords['node2a']->id); // Moved.
        $node3a = menu::get($data->noderecords['node3a']->id); // Descendant moved.
        $node3b = menu::get($data->noderecords['node3b']->id); // Descendant moved.
        $this->assertEquals('/' . $node2a->id, $node2a->path);
        $this->assertEquals('/' . $node2a->id . '/' . $node3a->id, $node3a->path);
        $this->assertEquals('/' . $node2a->id . '/' . $node3b->id, $node3b->path);
        unset($node1a, $node2a, $node3a, $node3b); // Prevent these objects being used below.

        // Move a level 1 node down to level 2. Descendants should now be level 2.
        $node1a = menu::get($data->noderecords['node1a']->id); // New parent and top node.
        $node1b = menu::get($data->noderecords['node1b']->id); // Node to move.
        $node2d = menu::get($data->noderecords['node2d']->id); // Descendant.
        $this->assertEquals('/' . $node1a->id, $node1a->path);
        $this->assertEquals('/' . $node1b->id, $node1b->path);
        $this->assertEquals($node1b->path . '/' . $node2d->id, $node2d->path);

        $this->assertTrue($node1b->update(['parentid' => $node1a->id]));

        $node1a = menu::get($data->noderecords['node1a']->id); // Top node.
        $node1b = menu::get($data->noderecords['node1b']->id); // Moved.
        $node2d = menu::get($data->noderecords['node2d']->id); // Descendant moved.
        $this->assertEquals($node1a->path . '/' . $node1b->id, $node1b->path);
        $this->assertEquals($node1b->path . '/' . $node2d->id, $node2d->path);
    }

    public function test_make_menu_list() {
        global $DB;

        // Remove the default tree, so we're just looking at the test data.
        $DB->delete_records('totara_navigation');

        $data = $this->setup_tree_data();

        // Matches the top two levels in set up in setup_tree_data(). Excludes third level items.
        $expected = [
            0 => 'Top',
            $data->noderecords['node1a']->id => '-&nbsp;node1a',
            $data->noderecords['node2a']->id => '&nbsp;&nbsp;-&nbsp;node2a',
            $data->noderecords['node2b']->id => '&nbsp;&nbsp;-&nbsp;node2b',
            $data->noderecords['node2c']->id => '&nbsp;&nbsp;-&nbsp;node2c',
            $data->noderecords['node1b']->id => '-&nbsp;node1b',
            $data->noderecords['node2d']->id => '&nbsp;&nbsp;-&nbsp;node2d',
            $data->noderecords['node1c']->id => '-&nbsp;node1c',
            $data->noderecords['node1d']->id => '-&nbsp;node1d',
        ];

        $menu = menu::make_menu_list(0, 0);

        $this->assertEquals($expected, $menu);
    }

    public function test_totara_build_menu_descendants() {
        $data = $this->setup_tree_data();

        $allrecords = menu::get_nodes();

        // totara_build_menu_descendants can handle records which are menu::HIDE_ALWAYS (in $node->get_visibility()),
        // but menu::get_nodes() should exclude those records anyway.
        foreach ($allrecords as $record) {
            $this->assertNotEquals('node2d', $record->title);
        }

        $result = totara_build_menu_descendants($data->noderecords['node1a']->id, $allrecords);

        // 1a => 2a => 3a
        //          => 3b
        //    => 2b => 3c
        //          => 3d
        //    => 2c
        // 3c is excluded because it is a leaf without a url.
        // 3d is excluded because the feature is disabled.
        // 2b is excluded because it is a group without visibile descendants and without a URL.
        // 2c in included because, even though it has no url, the class provides one.
        $this->assertEquals('node2a', $result[0]->linktext);
        $this->assertEquals('node3a', $result[1]->linktext);
        $this->assertEquals('node3b', $result[2]->linktext);
        $this->assertEquals('node2c', $result[3]->linktext);

        // Make sure that the loop prevention is working (we wouldn't want sites to be completely broken).
        // Just hack it, by suggesting that the parent is already at depth 2 when we ask for descendants.
        $result = totara_build_menu_descendants($data->noderecords['node1a']->id, $allrecords, 2);

        // There are four debugging messages, one for each node that would be too deep to add.
        $debuggingmessage = 'Tried to construct a menu tree which is deeper than the maximum allowed or contains a cycle: ';
        $this->assertDebuggingCalledCount(4, [
            $debuggingmessage . 'node3a',
            $debuggingmessage . 'node3b',
            $debuggingmessage . 'node3c',
            $debuggingmessage . 'node3d',
        ]);

        $this->assertEquals('node2a', $result[0]->linktext);
        $this->assertEquals('node2c', $result[1]->linktext);
    }
}
