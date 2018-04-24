<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * Totata navigation library page
 *
 * @package    totara
 * @subpackage navigation
 * @author     Oleg Demeshev <oleg.demeshev@totaralms.com>
 * @author     Chris Wharton <chris.wharton@catalyst-eu.net>
 */
namespace totara_core\totara\menu;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to store, render and manage totara navigation category
 *
 * @property-read int $id
 * @property-read int $parentid
 * @property-read string $title
 * @property-read string $url
 * @property-read string $classname
 * @property-read int $sortorder
 * @property-read int $depth
 * @property-read string $path
 * @property-read int $custom
 * @property-read int $customtitle
 * @property-read int $visibility
 * @property-read int $timemodified
 *
 * @package    totara
 * @subpackage navigation
 */
class menu implements \renderable, \IteratorAggregate {

    // Custom field values.
    // Totara menu default item - delete is forbidden
    const DEFAULT_ITEM = 0;
    // Totaramenu default classname - add sting to database
    const DEFAULT_CLASSNAME = '\totara_core\totara\menu\item';
    // Database menu item - delete is allowed
    const DB_ITEM = 1;

    // Visibility values.
    const HIDE_ALWAYS = 0;
    const SHOW_ALWAYS = 1;
    const SHOW_WHEN_REQUIRED = 2;
    const SHOW_CUSTOM = 3;

    // Maximum number of levels of menu items.
    // If increasing this number, additional .totara_item_depthX css styles need to be implemented to ensure
    // that the top navigation menu editor table is formatted correctly. Also, the "path" db column has max
    // length of 50, so keep an eye on that. Make sure to extend test_update_descendant_paths.
    const MAX_DEPTH = 3;

    /**
     * Any access rule operator.
     * @const AGGREGATION_ANY One or more are required.
     */
    const AGGREGATION_ANY = 0;

    /**
     * All access rule operator.
     * @const AGGREGATION_ALL All are required.
     */
    const AGGREGATION_ALL = 1;

    // The target attribute specifies where to open the linked document.
    // Default _self, no target attribute
    const TARGET_ATTR_SELF = '_self';
    const TARGET_ATTR_BLANK = '_blank';

    /**
    * @var \totara_core\totara\menu\menu stores pseudo category with id=0.
    * Use totara_core_menu::get(0) to retrieve.
    */
    protected static $menucat0;

    /**
    * @var array list of all fields and their short name and reserve value.
    */
    protected static $menufields = array(
        'id' => array('id', 0),
        'parentid' => array('pa', 0),
        'title' => array('ti', ''),
        'url' => array('ur', null),
        'classname' => array('cl', ''),
        'sortorder' => array('so', 0),
        'depth' => array('dh', 1),
        'path' => array('ph', null),
        'custom' => array('de', self::DB_ITEM),
        'customtitle' => array('ct', 0),
        'visibility' => array('vi', self::SHOW_ALWAYS),
        'visibilityold' => array('vo', self::SHOW_ALWAYS),
        'targetattr' => array('ta', self::TARGET_ATTR_SELF),
        'timemodified' => null, // Not cached.
    );

    /** @var int */
    protected $id = 0;

    /** @var int */
    protected $parentid = 0;

    /** @var string */
    protected $title = '';

    /** @var string */
    protected $url = '';

    /** @var string */
    protected $classname = '';

    /** @var int */
    protected $sortorder = 0;

    /** @var int */
    protected $depth = 0;

    /** @var string */
    protected $path = '';

    /** @var bool */
    protected $custom = self::DB_ITEM;

    /** @var int */
    protected $customtitle = 1;

    /** @var int */
    protected $visibility = self::SHOW_ALWAYS;

    /** @var int stores the last visibility state */
    protected $visibilityold = null;

    /** @var int */
    protected $targetattr = self::TARGET_ATTR_SELF;

    /** @var int */
    protected $timemodified = null;

    /**
     * Magic method getter, redirects to read values. Queries from DB the fields that were not cached
     *
     * @global \moodle_database $DB
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        global $DB;
        if (array_key_exists($name, self::$menufields)) {
            if ($this->$name === false) {
                // Property was not retrieved from DB, retrieve all not retrieved fields.
                $notretrievedfields = array_diff_key(self::$menufields, array_filter(self::$menufields));
                $rs = $DB->get_record('totara_navigation', array('id' => $this->id), join(',', array_keys($notretrievedfields)), MUST_EXIST);
                foreach ($rs as $key => $value) {
                    $this->$key = $value;
                }
            }
            return $this->$name;
        }
        debugging('Invalid totara_core_menu property accessed! ' . $name, DEBUG_DEVELOPER);
        return null;
    }

    /**
     * Full support for isset on magic read properties.
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        if (array_key_exists($name, self::$menufields)) {
            return isset($this->$name);
        }
        return false;
    }

    /**
     * Create an iterator because magic vars can't be seen by 'foreach'.
     * Implementing method from interface IteratorAggregate.
     *
     * @return \ArrayIterator
     */
    public function getiterator() {
        $ret = array();
        foreach (self::$menufields as $property => $unused) {
            if ($this->$property !== false) {
                $ret[$property] = $this->$property;
            }
        }
        return new \ArrayIterator($ret);
    }

    public function get_property() {
        return (array)$this->getiterator();
    }

    /**
     * Constructor.
     * Constructor is protected, use totara_core_menu::get($id) to retrieve category.
     *
     * @param \stdClass $record from DB (may not contain all fields)
     */
    protected function __construct(\stdClass $record) {
        foreach ($record as $key => $val) {
            if (array_key_exists($key, self::$menufields)) {
                $this->$key = $val;
            }
        }
    }

    /**
     * Returns totara_core_menu object for requested category.
     * If id is 0, the pseudo object for root category is returned (convenient
     * for calling other functions such as get_children()).
     *
     * @param int $id category id
     * @return null|menu
     * @throws \moodle_exception
     */
    public static function get($id = 0) {
        if (!$id) {
            if (!isset(self::$menucat0)) {
                $record = new \stdClass();
                $record->id = 0;
                $record->parentid = 0;
                $record->title = '';
                $record->url = '';
                $record->classname = '';
                $record->sortorder = 0;
                $record->depth = 0;
                $record->path = '';
                $record->custom = self::DB_ITEM;
                $record->customtitle = 1;
                $record->visibility = self::SHOW_ALWAYS;
                $record->visibilityold = null;
                $record->targetattr = self::TARGET_ATTR_SELF;
                $record->timemodified = 0;
                self::$menucat0 = new menu($record);
            }
            return self::$menucat0;
        }

        if ($rs = self::get_records('tn.id = :id', array('id' => (int)$id))) {
            $record = reset($rs);
            return new menu($record);
        } else {
            throw new \moodle_exception('unknowcategory');
        }
    }

    /**
     * Creates a new category from raw data.
     *
     * Data sample
     * @global \moodle_database $DB
     * @param array|\stdClass $data
     * $data->title = 'New parent|child node title';
     * $data->url = '/totara/core/tm_sync4.php';
     * $data->parentid = 0|19;
     * $data->classname = null|'your_class_name';
     * $data->custom = 0;
     * $data->customtitle = 0;
     * $data->visibility = 2;
     * $data->targetattr = _self;
     * @return true
     */
    public static function sync($data) {
        global $DB;

        $data = (object)$data;

        // Check if a parentid not moved as child node to other parent node.
        // OR if it exists at all.
        try {
            $parent = self::get($data->parentid);
        } catch (\moodle_exception $e) {
            // For somereason parent is disappeared.
            // Get the pseudo object for root category.
            $parent = self::get();
        }
        $data->parentid = $parent->id;

        // Lets sort out a depth.
        $data->depth = $parent->depth + 1;
        // Other data.
        $data->custom = (!isset($data->custom) ? self::DEFAULT_ITEM : (int)$data->custom);
        $data->url    = ($data->custom == self::DB_ITEM) ? $data->url : null;
        $data->customtitle  = (!isset($data->customtitle) ? 1 : (int)$data->customtitle);
        $data->visibility   = (!isset($data->visibility) ? self::HIDE_ALWAYS : (int)$data->visibility);
        $data->targetattr   = (!isset($data->targetattr) ? self::TARGET_ATTR_SELF : $data->targetattr);
        $sortorder = isset($data->sortorder) ? $data->sortorder : null;
        $data->sortorder = self::calculate_item_sortorder($sortorder, $parent->id);
        $data->timemodified = time();

        $data->id = $DB->insert_record('totara_navigation', $data);

        // Update path (only possible after we know the category id.
        $data->path = $parent->path . '/' . $data->id;
        $DB->set_field('totara_navigation', 'path', $data->path, array('id' => $data->id));
        // Add to log.
        \totara_core\event\menuitem_sync::create_from_item($data->id)->trigger();

        return $data;
    }

    /**
     * Creates a new category either from form data or from raw data
     *
     * @global \moodle_database $DB
     * @param array|\stdClass $data
     * @return true
     * @throws \moodle_exception
     */
    public function create($data) {
        global $DB;

        $data = (object)$data;
        if (empty($data->parentid)) {
            $parent = self::get();
        } else {
            $parent = self::get($data->parentid);
        }
        $data->parentid  = $parent->id;
        $data->classname = (!isset($data->classname) ? self::DEFAULT_CLASSNAME : $data->classname);
        $data->depth  = $parent->depth + 1;

        if (!$this->can_set_depth($data->depth)) {
            // You cannot move this item to the selected parent because it has children. Please move this item's children first.
            throw new \coding_exception('Tried to create a menu item that was deeper than the maximum depth');
        }

        $data->custom = (!isset($data->custom) ? self::DB_ITEM : (int)$data->custom);
        $data->url    = ($data->custom == self::DB_ITEM) ? $data->url : null;
        $data->customtitle = (!isset($data->customtitle) ? 1 : (int)$data->customtitle);
        $data->visibility  = (!isset($data->visibility) ? self::HIDE_ALWAYS : (int)$data->visibility);
        $data->timemodified= time();
        $sortorder = isset($data->sortorder) ? $data->sortorder : null;
        $data->sortorder = self::calculate_item_sortorder($sortorder, $parent->id);
        $data->id = $DB->insert_record('totara_navigation', $data);
        // Update path (only possible after we know the category id.
        $data->path = $parent->path . '/' . $data->id;
        $DB->set_field('totara_navigation', 'path', $data->path, array('id' => $data->id));

        \totara_core\event\menuitem_created::create_from_item($data->id)->trigger();

        return $data;
    }

    /**
     * Returns the maximum depth of all of this node's children, relative to this node's depth. E.g.
     * - actual depth 2, with no children: 0
     * - actual depth 2, just one child who is themselves childless: 1
     * - a tree of descendants, where this node is depth 3 and the deepest descendant is depth 7: 4
     *
     * @return int
     */
    public function max_relative_descendant_depth() {
        global $DB;

        // If it doesn't exist in the database then it can't have descendants.
        if (empty($this->id)) {
            return 0;
        }

        $where = "path LIKE '{$DB->sql_like_escape($this->path . '/')}%'";

        $depth = $DB->get_field_select('totara_navigation',  'MAX(depth)', $where, [], IGNORE_MISSING);

        if ($depth === false || is_null($depth)) {
            return 0;
        } else {
            return $depth - $this->depth;
        }
    }

    /**
     * Determines if the specified depth is valid, given the desired depth, the depth of descendants and the
     * absolute maximum depth supported by the tree.
     *
     * @param int $depth
     * @return bool
     */
    public function can_set_depth(int $depth) {
        if (empty($this->id)) {
            // If it doesn't exist in the database then it can't have descendants.
            $maxrelativedescendantdepth = 0;
        } else {
            $maxrelativedescendantdepth = $this->max_relative_descendant_depth();
        }

        return 1 <= $depth && $depth + $maxrelativedescendantdepth <= self::MAX_DEPTH;
    }

    /**
     * Updates the record with either form data or raw data
     *
     * @global \moodle_database $DB
     * @param array|\stdClass $data
     * @return true
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function update($data) {
        global $DB;

        if (!$this->id) {
            // There is no actual DB record associated with root category.
            throw new \moodle_exception('error:findingmenuitem', 'totara_core');
        }

        $data = (object)$data;
        $data->id = $this->id;

        if ($this->custom == self::DEFAULT_ITEM) {
            // If Totara default menu title changed.
            if ($this->title !== $data->title) {
                $data->customtitle = 1;
            } else {
                $data->title = $this->title;
            }
        }
        $data->timemodified = time();

        // Sort out depth and path.
        if ((int)$data->parentid > 0) {
            $parent = self::get($data->parentid);

            $data->depth = $parent->depth + 1;
            $data->path = $parent->path . '/' . $data->id;

            // If there is a parent then make sure that the item's children won't end up too deep.
            if (!$this->can_set_depth($data->depth)) {
                if ($data->depth > self::MAX_DEPTH) {
                    throw new \coding_exception('Tried to create a menu item that was deeper than the maximum depth');
                } else {
                    throw new \moodle_exception('error:itemhasdescendants', 'totara_core');
                }
            }
        } else {
            // Top level item. Can't have child depth problems because it can only have moved up (if at all).
            $data->depth = 1;
            $data->path = '/' . $data->id;
        }

        $transaction = $DB->start_delegated_transaction();

        // Update the depth and path of all children.
        $depthchange = $data->depth - $this->depth;
        list($replacesql, $params) = $DB->sql_text_replace('path', $this->path, $data->path, SQL_PARAMS_NAMED);
        $sql = "UPDATE {totara_navigation}
                   SET depth = depth + :depthchange,
                       timemodified = :timemodified,
                       {$replacesql}
                 WHERE path LIKE '{$DB->sql_like_escape($this->path . '/')}%'";
        $params['depthchange'] = $depthchange;
        $params['timemodified'] = $data->timemodified;
        $DB->execute($sql, $params);

        $DB->update_record('totara_navigation', $data);

        $transaction->allow_commit();

        \totara_core\event\menuitem_updated::create_from_item($data->id)->trigger();

        return true;
    }

    /**
     * Validate node data
     *
     * @param object $data
     * @return array $errors
     */
    public static function validation($data) {
        global $CFG;

        $errors = array();

        if (isset($data->title) && empty($data->title)) {
            $errors['title'] = get_string('error:menuitemtitlerequired', 'totara_core');
        }
        if (\core_text::strlen($data->title) > 1024) {
            $errors['title'] = get_string('error:menuitemtitletoolong', 'totara_core');
        }

        if ($data->custom == self::DB_ITEM) {
            if (\core_text::strlen($data->url) > 255) {
                $errors['url'] = get_string('error:menuitemurltoolong', 'totara_core');
            }
            if (!empty($data->url)) {
                $url = $data->url;
                if ($url[0] === '/') {
                    $url = $CFG->wwwroot . $url;
                }
                $url = self::replace_url_parameter_placeholders($url);
                if (!filter_var($url, FILTER_VALIDATE_URL)) {
                    $errors['url'] = get_string('error:menuitemurlinvalid', 'totara_core');
                }
            }
        }

        if (isset($data->classname)) {
            if (\core_text::strlen($data->classname) > 255) {
                $errors['classname'] = get_string('error:menuitemclassnametoolong', 'totara_core');
            }
        }

        if (isset($data->targetattr)) {
            if (\core_text::strlen($data->targetattr) > 100) {
                $errors['targetattr'] = get_string('error:menuitemtargetattrtoolong', 'totara_core');
            }
        }

        return $errors;

    }

    /**
     * Replace url placeholders in custom menu items.
     *
     * @param string $url
     * @return string
     */
    public static function replace_url_parameter_placeholders($url) {
        global $USER, $COURSE;

        $search = array(
            '##userid##',
            '##username##',
            '##useremail##',
            '##courseid##',
        );
        $replace = array(
            isset($USER->id) ? $USER->id : 0,
            isset($USER->username) ? urlencode($USER->username) : '',
            isset($USER->email) ? urlencode($USER->email) : '',
            isset($COURSE->id) ? $COURSE->id : SITEID,
        );

        return str_replace($search, $replace, $url);
    }

    /**
     * Retrieves number of records from totara_navigation table
     *
     * @global \moodle_database $DB
     * @param string $whereclause
     * @param array $params
     * @return array of stdClass objects
     */
    public static function get_records($whereclause, $params = array()) {
        global $DB;

        $fields = array_keys(array_filter(self::$menufields));
        $sql = "SELECT tn.". join(',tn.', $fields). " FROM {totara_navigation} tn WHERE ". $whereclause." ORDER BY tn.sortorder";
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Retrieves number of records from totara_navigation table where visibility true
     *
     * @global \moodle_database $DB
     * @return array of stdClass objects
     */
    public static function get_nodes() {
        global $DB;

        $fields = array_keys(array_filter(self::$menufields));
        $sql = "SELECT
                    tn.". join(',tn.', $fields). ",
                    (SELECT classname FROM {totara_navigation} WHERE tn.parentid = id) AS parent,
                    (SELECT visibility FROM {totara_navigation} WHERE tn.parentid = id) AS parentvisibility
                FROM
                    {totara_navigation} tn
                WHERE
                    tn.visibility > :hidealways
                ORDER BY
                    tn.parentid, tn.sortorder";
        return $DB->get_records_sql($sql, array('hidealways' => self::HIDE_ALWAYS));
    }

    /**
     * Returns array of children categories
     *
     * @global \moodle_database $DB
     * @return \totara_core\totara\menu\menu[] Array of totara_core_menu objects indexed by category id
     */
    public function get_children() {
        // We need to retrieve all children by sort.
        $rs = self::get_records('tn.parentid = :parentid', array('parentid' => $this->id));
        if (empty($rs)) {
            return array();
        }
        $rv = array();
        foreach ($rs as $id => $item) {
            $rv[$id] = new menu($item);
        }
        return $rv;
    }

    /**
     * Deletes a category and all children
     *
     * @return boolean
     */
    public function delete() {
        global $DB;

        // If category is from TotaraMenu throw exception, can't delete it.
        if (!$this->custom) {
            throw new \moodle_exception('error:menuitemcannotberemoved', 'totara_core', null, $this->title);
        }

        $transaction = $DB->start_delegated_transaction();

        // If category has children.
        $children = $this->get_children();
        if ($children) {
            foreach ($children as $subitem) {
                // Delete all children.
                $subitem->delete();
            }
        }
        // Delete the associated settings.
        $DB->delete_records('totara_navigation_settings', array('itemid' => $this->id));
        // Finally delete the category and it's context.
        $DB->delete_records('totara_navigation', array('id' => $this->id));
        $transaction->allow_commit();

        // Add to log.
        \totara_core\event\menuitem_deleted::create_from_item($this->id)->trigger();

        return true;
    }

    /**
     * Returns default visibility list.
     *
     * @return array array(HIDE_ALWAYS, SHOW_ALWAYS, SHOW_WHEN_REQUIRED)
     */
    public static function get_visibility_list() {
        return array(
            self::HIDE_ALWAYS => get_string('menuitem:hide', 'totara_core'),
            self::SHOW_ALWAYS => get_string('menuitem:show', 'totara_core'),
            self::SHOW_WHEN_REQUIRED => get_string('menuitem:showwhenrequired', 'totara_core'),
            self::SHOW_CUSTOM => get_string('menuitem:showcustom', 'totara_core'),
        );
    }

    /**
     * Returns node visibility if exists in visibility list or empty value.
     *
     * @param string $visibility
     * @return string empty|visibility
     */
    public static function get_visibility($visibility) {
        $visibilitylist = self::get_visibility_list();
        if (isset($visibilitylist[$visibility])) {
            return $visibilitylist[$visibility];
        }
        return '';
    }

    /**
     * Set child node as a parent during totara_upgrade_menu().
     *
     * @global \moodle_database $DB
     */
    public function set_parent() {
        global $DB;

        $DB->set_field('totara_navigation', 'parentid', '0', array('id' => (int)$this->id));
        // Add to log.
        \totara_core\event\menuitem_setparent::create_from_item($this->id)->trigger();
    }

    /*
     * Change $custom property to delete a totara menu item during totara_upgrade_menu().
     */
    public function set_custom($custom = menu::DEFAULT_ITEM) {
        $this->custom = $custom;
    }

    /**
     * This function returns a list representing category parent tree
     * for display or to use in a form <select> element
     *
     * @global \moodle_database $DB
     * @param integer $excludeid Exclude this category and its children from the lists built.
     * @param integer $parentid The node to add to the result, and all children to be called recursively.
     * @return array of strings
     */
    public static function make_menu_list($excludeid = 0, int $parentid = 0) {
        global $DB;

        $nodes = [];

        if ($parentid == 0) {
            $nodes[0] = get_string('top');
            $depth = 0;
        } else {
            $node = $DB->get_record('totara_navigation', ['id' => $parentid]);
            $indent = '';
            $depth = $node->depth;
            for ($i = 1; $i < $node->depth; $i++) {
                $indent .= '&nbsp;&nbsp;';
            }
            $nodes[$parentid] = $indent . '-&nbsp;' . format_string($node->title, true);
        }

        if ($depth < self::MAX_DEPTH - 1) {
            $select = "id <> :excludedid AND parentid = :parentid";
            $params = ['excludedid' => $excludeid, 'parentid' => $parentid];
            $children = $DB->get_records_select('totara_navigation', $select, $params, 'sortorder');

            foreach ($children as $child) {
                $descendants = self::make_menu_list($excludeid, $child->id);
                $nodes = $nodes + $descendants;
            }
        }

        return $nodes;
    }

    /**
     * Changes the sort order of this categories parent shifting this category up or down one.
     *
     * @global \moodle_database $DB
     * @param int $id category
     * @param bool $up If set to true the category is shifted up one spot, else its moved down.
     * @return bool true on success, false otherwise.
     */
    public static function change_sortorder($id = 0, $up = false) {
        global $DB;

        $data = self::get((int)$id);
        $params = array($data->sortorder, $data->parentid);
        $select = ($up ? 'sortorder < ? AND parentid = ?' : 'sortorder > ? AND parentid = ?');
        $sort   = ($up ? 'sortorder DESC' : 'sortorder ASC');
        $action = ($up ? 'moveup' : 'movedown');
        $swapcategory = $DB->get_records_select('totara_navigation', $select, $params, $sort, '*', 0, 1);
        $swapcategory = reset($swapcategory);
        if (!$swapcategory) {
            return false;
        }

        $DB->set_field('totara_navigation', 'sortorder', $swapcategory->sortorder, array('id' => $data->id));
        $DB->set_field('totara_navigation', 'timemodified', time(), array('id' => $data->id));
        $DB->set_field('totara_navigation', 'sortorder', $data->sortorder, array('id' => $swapcategory->id));
        $DB->set_field('totara_navigation', 'timemodified', time(), array('id' => $swapcategory->id));

        \totara_core\event\menuitem_sortorder::create_from_item($data->id, $action)->trigger();
        return true;
    }

    /**
     * Changes menu item visibility.
     *
     * @global \moodle_database $DB
     * @param int menu item id
     * @param bool $hide
     * @return bool True on success, false otherwise.
     */
    public static function change_visibility($id = 0, $hide = false) {
        global $DB;

        $itemid = (int)$id;
        $update = new \stdClass;
        $update->id = $itemid;
        // Change visibility to hide
        if ($hide) {
            $visibility = $DB->get_field('totara_navigation', 'visibility', array('id' => $itemid), MUST_EXIST);
            $update->visibility = self::HIDE_ALWAYS;
            $update->visibilityold = $visibility;
            $DB->update_record('totara_navigation', $update);
        } else {
            // Change visibility to show
            $item = self::get($itemid);
            $visibility = $item->visibilityold;
            if ($visibility === null) {
                if ($item->custom == self::DB_ITEM) {
                    $visibility = self::SHOW_ALWAYS;
                } else {
                    $visibility = self::SHOW_WHEN_REQUIRED;
                }
            }
            $update->visibility = $visibility;
            $update->visibilityold = null;
        }
        $DB->update_record('totara_navigation', $update);

        \totara_core\event\menuitem_visibility::create_from_item($itemid, $hide)->trigger();

        return true;
    }

    /**
     * Resets the menu to the default state as determined by the code.
     */
    public static function reset_menu() {
        global $DB;
        // Truncate the menu table.
        $DB->delete_records('totara_navigation');
        // And the menu settings.
        $DB->delete_records('totara_navigation_settings');
        // Then recreate the defaults.
        totara_upgrade_menu();
    }

    /**
     * Load new node class if exists. For custom items this will be
     * the class \totara_core\totara\menu\item, for other items it is
     * the item classname.
     *
     * Returns false if the classname provided is not found.
     *
     * @param object item
     * @return \totara_core\totara\menu\item|bool - new instance or false if not found.
     */
    public static function node_instance($item) {
        if (is_array($item)) {
            $item = (object)$item;
        }
        if ($item->custom) {
            return new \totara_core\totara\menu\item($item);
        }
        $classname = $item->classname;
        if (class_exists($classname)) {
            return new $classname($item);
        } else {
            return false;
        }
    }

    /**
     * Given an item's preferred sortorder (or null if no preference),
     * calculate the best real sortorder for that item. We need to
     * check to see which values have already been used to identify this.
     *
     * @param int|null $defaultsort Preferred sort order for this item (or null).
     * @param int $parentid ID of parent node. Used to calculate position if no default sort given.
     * @return int
     */
    private static function calculate_item_sortorder($defaultsort, $parentid = 0) {
        global $DB;
        $existingsortorders = $DB->get_records_menu('totara_navigation', null, 'sortorder DESC', 'id,sortorder');

        // Base position on default sort if it is given.
        if (!is_null($defaultsort)) {
            // Is preferred sort already used?
            // If it is, keep incrementing sort until we find one that is available.
            while (in_array($defaultsort, $existingsortorders)) {
                $defaultsort++;
            }
            return $defaultsort;
        }

        // No default given, let's try to pick a smart default.

        // Item has a parent, let's set the default sort so it appears as a child.
        if ($parentid > 0) {
            $parentsort = $existingsortorders[$parentid];
            $existingchildren = $DB->get_records_menu('totara_navigation', array('parentid' => $parentid), 'sortorder DESC', 'id,sortorder');

            // Add after last child, or immediately after parent if there are no children.
            $minvalue = !empty($existingchildren) ? reset($existingchildren) : $parentsort;

            // Add before the next top level item.
            $toplevelitems = $DB->get_records_menu('totara_navigation', array('parentid' => 0), 'sortorder DESC', 'id,sortorder');
            $maxvalue = false;
            foreach ($toplevelitems as $id => $sort) {
                if ($id == $parentid) {
                    break;
                }
                $maxvalue = $sort;
            }

            // Sanity check.
            if ($maxvalue && $minvalue < $maxvalue) {

                // Ideally we can put this right in the middle.
                $preferredsort = ceil(($minvalue + $maxvalue)/2);
                if (!in_array($preferredsort, $existingsortorders)) {
                    return $preferredsort;
                }

                // If that's taken let's try every possible space between the two.
                $preferredsort = $minvalue + 1;
                while ($preferredsort < $maxvalue) {
                    if (!in_array($preferredsort, $existingsortorders)) {
                        return $preferredsort;
                    }
                    $preferredsort++;
                }
            }
        }

        // Last resort is to add the new item to the very end.
        $highestsort = reset($existingsortorders);
        return (int)$highestsort + 10000;

    }

    /**
     * Method for obtaining a item setting.
     *
     * @param string $type Identifies the class using the setting.
     * @param string $name Identifies the particular setting.
     * @return mixed The value of the setting $name or null if it doesn't exist.
     */
    public function get_setting($type, $name) {
        global $DB;
        return $DB->get_field('totara_navigation_settings', 'value',
            array('itemid' => $this->id, 'type' => $type, 'name' => $name));
    }

    /**
     * Returns all of the settings associated with this item.
     *
     * @return array
     */
    public function get_settings() {
        global $DB;
        $settings = array();
        foreach ($DB->get_records('totara_navigation_settings', array('itemid' => $this->id)) as $setting) {
            if (!isset($settings[$setting->type])) {
                $settings[$setting->type] = array();
            }
            $settings[$setting->type][$setting->name] = $setting;
        }
        return $settings;
    }

    /**
     * Insert or update a single rule for a menu item.
     *
     * @param string $type Identifies the class using the setting.
     * @param string $name Identifies the particular setting.
     * @param string $value New value for the setting.
     */
    public function update_setting($type, $name, $value) {
        global $DB;

        $existing = $DB->get_record('totara_navigation_settings', array('itemid' => $this->id, 'type' => $type, 'name' => $name));

        $record = new \stdClass();
        $record->timemodified = time();
        if ($existing && $existing->value !== $value) {
            // Its an existing setting whose value has changed.
            $record->id = $existing->id;
            $record->value = $value;
            $DB->update_record('totara_navigation_settings', $record);
        } else if (!$existing) {
            // Its a new setting, insert it.
            $record->itemid = $this->id;
            $record->type = $type;
            $record->name = $name;
            $record->value = $value;
            $DB->insert_record('totara_navigation_settings', $record);
        }
    }

    /**
     * Update several settings at once.
     *
     * The $settings array is expected to be an array of arrays.
     * Each sub array should associative with three keys: type, name, value.
     *
     * @param array[] $settings
     */
    public function update_settings(array $settings) {
        global $DB;

        // Build an array of existing settings so that we don't need to fetch each one individually as we look
        // at the setting data.
        $now = time();
        $existing = $this->get_settings();

        // Look at each setting and either update it, insert it, or skip it (if it has not changed).
        foreach ($settings as $setting) {
            $type = $setting['type'];
            $name = $setting['name'];
            $value = $setting['value'];
            if (isset($existing[$type][$name])) {
                // Its an existing setting, lets check if its actually changed.
                if ($value === $existing[$type][$name]->value) {
                    // Nothing to do here, the setting has not changed.
                    continue;
                }
                // The value has changed update the setting.
                $record = new \stdClass;
                $record->id = $existing[$type][$name]->id;
                $record->timemodified = $now;
                $record->value = $value;
                $DB->update_record('totara_navigation_settings', $record);
            } else {
                // Its the first time this setting has been set.
                $record = new \stdClass;
                $record->timemodified = $now;
                $record->itemid = $this->id;
                $record->type = $type;
                $record->name = $name;
                $record->value = $value;
                $DB->insert_record('totara_navigation_settings', $record);
            }
        }
    }

    /**
     * The list of available preset rules to select from.
     *
     * @return array $choices Rules to select from.
     */
    public static function get_preset_rule_choices() {
        $choices = array(
            'is_logged_in'              => get_string('menuitem:rulepreset_is_logged_in', 'totara_core'),
            'is_not_logged_in'          => get_string('menuitem:rulepreset_is_not_logged_in', 'totara_core'),
            'is_guest'                  => get_string('menuitem:rulepreset_is_guest', 'totara_core'),
            'is_not_guest'              => get_string('menuitem:rulepreset_is_not_guest', 'totara_core'),
            'is_site_admin'             => get_string('menuitem:rulepreset_is_site_admin', 'totara_core'),
            'can_view_required_learning'=> get_string('menuitem:rulepreset_can_view_required_learning', 'totara_core'),
            'can_view_my_team'          => get_string('menuitem:rulepreset_can_view_my_team', 'totara_core'),
            'can_view_my_reports'       => get_string('menuitem:rulepreset_can_view_my_reports', 'totara_core'),
            'can_view_certifications'   => get_string('menuitem:rulepreset_can_view_certifications', 'totara_core'),
            'can_view_programs'         => get_string('menuitem:rulepreset_can_view_programs', 'totara_core'),
            'can_view_allappraisals'    => get_string('menuitem:rulepreset_can_view_allappraisals', 'totara_core'),
            'can_view_latestappraisal'  => get_string('menuitem:rulepreset_can_view_latest_appraisal', 'totara_core'),
            'can_view_appraisal'        => get_string('menuitem:rulepreset_can_view_appraisal', 'totara_core'),
            'can_view_feedback_360s'    => get_string('menuitem:rulepreset_can_view_feedback_360s', 'totara_core'),
            'can_view_my_goals'         => get_string('menuitem:rulepreset_can_view_my_goals', 'totara_core'),
            'can_view_learning_plans'   => get_string('menuitem:rulepreset_can_view_learning_plans', 'totara_core'),
        );

        return $choices;
    }

}
