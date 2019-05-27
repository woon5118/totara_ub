<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package core_user
 */

namespace core_user;

use \coding_exception;
use \context_course;
use \context_helper;
use \context_user;
use \stdClass;

/**
 * User access controller.
 *
 * A user is a carefully handled thing.
 * There are a lot of checks that go into what one user can view about another (even themselves)
 *
 * This controller helps resolve those checks consistently by providing methods that relate to giving access
 * to specific fields and information.
 *
 * The access controller is instantiated because the checks can be expensive, and as such the results are cached
 * for the life of the instance.
 * If you change anything during execution that would lead to a different outcome then you must redirect or refresh the objects.
 * But really you should redirect if you are changing permissions, roles, etc.
 *
 * It is strongly recommended that if you are working with bulk actions then you do so by iterating user by user.
 * If going field by field is unavoidable then I suggest you cache these objects within your code.
 *
 * @since Totara 13
 */
class access_controller {

    /**
     * The user id.
     * @var int
     */
    private $userid;

    /**
     * True if the user has been deleted, false otherwise.
     * @var bool
     */
    private $userdeleted;

    /**
     * The users mail display setting.
     * @var int
     */
    private $usermaildisplay;

    /**
     * True if the target user and the current user are the same person.
     * @var bool
     */
    private $iscurrentuser = false;

    /**
     * The user context, or false if the user has been deleted.
     * @var context_user|false
     */
    private $context_user = false;

    /**
     * An array of courses the user is enrolled in.
     * Don't directly access this property, call get_enrolled_courses().
     * @var stdClass[]
     */
    private $enrolled_courses;

    /**
     * The course this access controller is specifically focus on.
     * If none are provided then all courses are taken into account.
     * @var null|stdClass
     */
    private $course = null;

    /**
     * An array of already resolved checks for quick lookup.
     * @var bool[]
     */
    private $resolutioncache = [];

    /**
     * An array of hidden fields.
     * Don't access this property directly, call get_hidden_fields()
     * @var string[]
     */
    private $hiddenfields = null;

    /**
     * An array of identity fields.
     * Don't access this property directly, call get_identity_fields()
     * @var string[]
     */
    private $identifyfields = null;

    /**
     * Cache of instantiated instances, limited to INSTANCE_CACHE_MAX_SIZE
     * Please note this cache is not used when running unit tests.
     * @var access_controller[]
     */
    private static $instancecache = [];

    /**
     * An array of keys in the instance cache in the order that they were added.
     * @var string[]
     */
    private static $instancecachekeys = [];

    /**
     * The maximum size for the instance cache.
     */
    private const INSTANCE_CACHE_MAX_SIZE = 10;

    /**
     * Gets a managed user instance for the given user record.
     *
     * It is the responsibility of the called to make sure that all of the required fields are present on the record.
     *
     * @param stdClass|access_controller $user
     * @param int|stdClass|null $courseorid
     * @return access_controller
     */
    public static function for($user, $courseorid = null): access_controller {
        if (empty($user->id) || $user->id <= 0) {
            throw new coding_exception('User access controllers can only be used for real users.');
        }

        $key = (string)$user->id;
        if ($courseorid !== null) {
            $key .= '_';
            $key .= $courseorid->id ?? $courseorid;
        }

        if (!isset(self::$instancecache[$key]) || (defined('PHPUNIT_TEST') && PHPUNIT_TEST)) {
            if (count(self::$instancecache) >= self::INSTANCE_CACHE_MAX_SIZE) {
                // Drop the oldest key.
                unset(self::$instancecache[array_shift(self::$instancecachekeys)]);
            }
            $course = (is_null($courseorid) || is_object($courseorid)) ? $courseorid : get_course($courseorid);
            $controller = new access_controller($user, $course);
            self::$instancecache[$key] = $controller;
            array_push(self::$instancecachekeys, $key);
        }
        return self::$instancecache[$key];
    }

    /**
     * Gets a managed user instance given a user id.
     * @param int $id
     * @param int|stdClass|null $courseorid
     * @return access_controller
     */
    public static function for_user_id(int $id, $courseorid = null): access_controller {
        global $DB;
        if ($id <= 0) {
            throw new coding_exception('Userid does not belong to a real user.');
        }
        return self::for($DB->get_record('user', ['id' => $id], '*', MUST_EXIST), $courseorid);
    }

    /**
     * Gets a managed user object for the current user.
     *
     * @param int|stdClass|null $courseorid
     * @return access_controller
     */
    public static function for_current_user($courseorid = null): access_controller {
        global $USER;
        if (!isloggedin()) {
            throw new \coding_exception('There is no current user');
        }
        return self::for_user_id($USER->id, $courseorid);
    }

    /**
     * Resets the static instance cache when required.
     */
    public static function clear_instance_cache() {
        self::$instancecache = [];
        self::$instancecachekeys = [];
    }

    /**
     * Constructor.
     *
     * @param stdClass $user
     * @param stdClass|null $course
     */
    private function __construct(stdClass $user, stdClass $course = null) {
        global $DB, $USER;
        if (empty($user->id)) {
            throw new coding_exception('User access controllers can only be used for real users.');
        }
        $requiredfields = [
            'id',
            'deleted',
            'maildisplay',
        ];
        $toload = array_filter($requiredfields, function($field) use ($user) {
            return !(isset($user->{$field}) || property_exists($user, $field));
        });
        if (!empty($toload)) {
            $user = $DB->get_record('user', ['id' => $user->id], join(', ', $requiredfields), MUST_EXIST);
        }
        $this->userid = (int)$user->id;
        $this->userdeleted = (bool)$user->deleted;
        $this->usermaildisplay = (int)$user->maildisplay;

        $this->iscurrentuser = ($user->id == $USER->id);
        if (!$this->userdeleted) {
            $this->context_user = context_user::instance($user->id, MUST_EXIST);
        }
        if ($course) {
            $this->course = $course;
        }
    }

    /**
     * Returns true if the current user can view the given field for the tracked user.
     *
     * @param string $field
     * @return bool
     */
    public function can_view_field(string $field): bool {
        switch ($field) {
            case 'id':
                return true;
            case 'username':
            case 'auth':
            case 'confirmed':
            case 'lang':
            case 'theme':
            case 'timezone':
            case 'timecreated':
            case 'timemodified':
            case 'lastnamephonetic':
            case 'firstnamephonetic':
            case 'middlename':
            case 'alternatename':
            case 'mailformat':
                return (
                    $this->iscurrentuser ||
                    $this->has_view_all_details_capability()
                );
            case 'email':
                if ($this->userdeleted) {
                    return false;
                }
                return (
                    $this->usermaildisplay === 1 || // Everyone is allowed to see.
                    $this::is_current_user_an_admin() || // The admin is allowed the users email.
                    $this->iscurrentuser || // Of course the current user is as well.
                    $this->has_course_email_capability() ||  // This is a capability in course context, it will be false in usercontext.
                    $this->has_job_relationship() || // Those with a job relationship are allowed to see.
                    in_array('email', $this->get_identify_fields()) || // It's an identify field.
                     ($this->usermaildisplay === 2 && $this->do_users_share_courses()) // It's available to those who share courses.
                );
            case 'firstname':
            case 'lastname':
                return (
                    $this->iscurrentuser ||
                    $this->has_view_fullnames_capability()
                );
            case 'fullname':
            case 'profileimageurl':
            case 'profileimageurlsmall':
            case 'profileimagealt': // Special case, this is an alias for imagealt.
            case 'imagealt':
                return (
                    $this->iscurrentuser ||
                    $this->can_view_profile()
                );
            case 'address':
                return (
                    $this->iscurrentuser ||
                    $this->can_view_hidden_fields()
                );
            case 'phone1':
            case 'phone2':
                return (
                    $this->iscurrentuser ||
                    $this->can_view_hidden_fields() ||
                    (in_array($field, $this->get_identify_fields()) && $this->can_view_profile())
                );
            case 'country':
            case 'city':
            case 'url':
            case 'icq':
            case 'skype':
            case 'yahoo':
            case 'aim':
            case 'msn':
            case 'suspended':
            case 'firstaccess':
            case 'lastaccess':
                return (
                    $this->iscurrentuser ||
                    (
                        $this->can_view_profile() && (
                            !in_array($field, $this->get_hidden_fields()) ||
                            $this->can_view_hidden_fields()
                        )
                    )
                );
            case 'idnumber':
            case 'institution':
            case 'department':
                return (
                    $this->iscurrentuser ||
                    $this->has_view_all_details_capability() ||
                    (
                        in_array($field, $this->get_identify_fields()) &&
                        $this->can_view_profile()
                    )
                );
            case 'description':
            case 'descriptionformat':
                if ($this->userdeleted) {
                    return false;
                }
                return (
                    $this->iscurrentuser ||
                    $this::is_current_user_an_admin() ||
                    (
                        $this->can_view_profile() && (
                            $this->resolve_profiles_for_enrolled_users_only() &&
                            (
                                !in_array('description', $this->get_hidden_fields()) ||
                                $this->can_view_hidden_fields()
                            )
                        )
                    )
                );

            case 'customfields':
            case 'preferences':
            case 'enrolledcourses':
            case 'interests':
                $method = 'can_view_'.$field;
                return $this->{$method}();

            // The following fields don't have access control - but we know about them so just return false.
            // They aren't there to be displayed, predominantly they are just flags.
            case 'policyagreed':
            case 'deleted':
            case 'mnethostid':
            case 'password':
            case 'secret':
            case 'emailstop':
            case 'calendartype':
            case 'totarasync':
            case 'lastlogin':
            case 'currentlogin':
            case 'lastip':
            case 'picture':
            case 'maildigest':
            case 'maildisplay':
            case 'autosubscribe':
            case 'trackforums':
            case 'trustbitmask':
                return false;
        }

        throw new coding_exception('Unknown user field', $field);
    }

    /**
     * Returns true if the current user can view the tracked users profile.
     *
     * @return bool
     */
    public function can_view_profile() {
        global $CFG;

        if ($this->userdeleted) {
            return false;
        }

        // Do we need to be logged in?
        if (empty($CFG->forceloginforprofiles)) {
            return true;
        } else {
            if (!isloggedin() || (isguestuser() && !$this->iscurrentuser)) {
                // User is not logged in and forceloginforprofile is set, we need to return now.
                // The exception is the guest is allowed to see their own profile.
                return false;
            }
        }

        return (
            $this->iscurrentuser ||
            $this->has_coursecontact_role() ||
            $this->has_view_details_capability() ||
            $this->has_job_relationship()
        );
    }

    /**
     * Returns true if the current user can view the tracked users enrolled courses.
     *
     * @return bool
     */
    public function can_view_enrolledcourses() {
        return (
            $this->iscurrentuser ||
            (
                $this->can_view_profile() && (
                    !in_array('mycourses', $this->get_hidden_fields()) ||
                    $this->can_view_hidden_fields()
                )
            )
        );
    }

    /**
     * Returns true if the current user can view the tracked users preferences.
     *
     * @return bool
     */
    public function can_view_preferences() {
        if (isguestuser($this->userid) || $this->userdeleted) {
            return false;
        }
        return (
            $this->iscurrentuser ||
            // TOTARA: TL-6675 gave the ability for others to edit a users preference.
            has_capability('moodle/user:update', $this->context_user)
        );
    }

    /**
     * Returns true if the current user can view the tracked users custom fields.
     *
     * Please be aware that each individual field still needs to be checked.
     *
     * @return bool
     */
    public function can_view_customfields() {
        return (
            $this->iscurrentuser ||
            $this->can_view_profile()
        );
    }

    /**
     * Returns true if the current user can view the tracked users interests.
     *
     * Interests are tags.
     *
     * @return bool
     */
    public function can_view_interests() {
        return (
            $this->iscurrentuser ||
            $this->can_view_profile()
        );
    }

    /**
     * Returns true if the current user can manage the tracked users files.
     *
     * @return bool
     */
    public function can_manage_files() {
        if (isguestuser($this->userid) || $this->userdeleted) {
            return false;
        }
        return (
            $this->iscurrentuser &&
            has_capability('moodle/user:manageownfiles', $this->context_user)
        );
    }

    /**
     * Determine if the current user can see at least one of the groups of the specified user.
     *
     * @param stdClass $course
     * @return bool
     */
    private function are_course_groups_visible_to_user(stdClass $course) {
        if (!isset($this->resolutioncache[__METHOD__])) {
            $this->resolutioncache[__METHOD__] = groups_user_groups_visible($course, $this->userid);
        }
        return $this->resolutioncache[__METHOD__];
    }

    /**
     * Returns true if the current user can see the tracked users hidden fields.
     *
     * @return bool
     */
    private function can_view_hidden_fields() {
        if (isset($this->resolutioncache[__METHOD__])) {
            return $this->resolutioncache[__METHOD__];
        }
        $this->resolutioncache[__METHOD__] = false;
        if (!$this->context_user) {
            return false; // No context, the user will be deleted.
        }
        if (has_capability('moodle/user:viewhiddendetails', $this->context_user)) {
            $this->resolutioncache[__METHOD__] = true;
            return true;
        }
        foreach ($this->get_enrolled_courses() as $course) {
            $coursecontext = context_course::instance($course->id);
            if (has_capability('moodle/course:viewhiddenuserfields', $coursecontext)) {
                if (!$this->are_course_groups_visible_to_user($course)) {
                    // Not a member of the same group.
                    continue;
                }
                $this->resolutioncache[__METHOD__] = true;
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true if the current user and the tracked user share at least one course.
     *
     * @return bool
     */
    private function do_users_share_courses() {
        global $USER;
        if (!isset($this->resolutioncache[__METHOD__])) {
            $this->resolutioncache[__METHOD__] = enrol_sharing_course($this->userid, $USER->id);
        }
        return $this->resolutioncache[__METHOD__];
    }

    /**
     * Returns all of the courses the tracked user is enrolled in.
     *
     * @return array|stdClass[]
     */
    private function get_enrolled_courses() {
        if (!$this->enrolled_courses) {
            $this->enrolled_courses = [];
            if ($this->course) {
                if (is_enrolled(context_course::instance($this->course->id), $this->userid)) {
                    $this->enrolled_courses = [$this->course];
                }
            } else {
                $this->enrolled_courses = enrol_get_all_users_courses($this->userid);
                array_map(function ($course) {
                    context_helper::preload_from_record($course);
                }, $this->enrolled_courses);
            }
        }
        return $this->enrolled_courses;
    }

    /**
     * Returns an array of hidden fields.
     *
     * @return array
     */
    private function get_hidden_fields(): array {
        global $CFG;
        if ($this->hiddenfields === null) {
            if (empty($CFG->hiddenuserfields)) {
                $fields = [];
            } else {
                $fields = array_flip(explode(',', $CFG->hiddenuserfields));
            }
            // These maps exist because the hidden field name does not match the field on the user record.
            $maps = [
                'webpage' => 'url',
                'icqnumber' => 'icq',
                'skypeid' => 'skype',
                'yahooid' => 'yahoo',
                'aimid' => 'aim',
                'msnid' => 'msn',
            ];
            foreach ($maps as $key => $value) {
                if (in_array($key, $fields)) {
                    $fields[] = $value;
                }
            }
            $this->hiddenfields = $fields;
        }
        return $this->hiddenfields;
    }

    /**
     * Returns an array of user fields.
     *
     * @return array
     */
    private function get_identify_fields(): array {
        if ($this->identifyfields === null) {
            $this->identifyfields = [];
            if ($this->context_user) {
                $this->identifyfields = get_extra_user_fields($this->context_user);
            }
            foreach ($this->get_enrolled_courses() as $course) {
                $fields = get_extra_user_fields(context_course::instance($course->id));
                $this->identifyfields = array_merge($this->identifyfields, $fields);
            }
            $this->identifyfields = array_unique($this->identifyfields);
        }
        return $this->identifyfields;
    }

    /**
     * Returns true if the tracked user is a course contact in at least one course.
     *
     * @return bool
     */
    private function has_coursecontact_role() {
        if (!isset($this->resolutioncache[__METHOD__])) {
            $this->resolutioncache[__METHOD__] = has_coursecontact_role($this->userid);
        }
        return $this->resolutioncache[__METHOD__];
    }

    /**
     * Returns true if the current user holds the moodle/course:useremail in either the
     * tracked course, or at least one course the tracked user is enrolled in.
     *
     * @return bool
     */
    private function has_course_email_capability() {
        if (!isset($this->resolutioncache[__METHOD__])) {
            $this->resolutioncache[__METHOD__] = false;
            foreach ($this->get_enrolled_courses() as $course) {
                $coursecontext = context_course::instance($course->id);
                if (!has_capability('moodle/course:useremail', $coursecontext)) {
                    continue;
                }
                if (!$this->are_course_groups_visible_to_user($course)) {
                    // Not a member of the same group.
                    continue;
                }
                $this->resolutioncache[__METHOD__] = true;
                break;
            }
        }
        return $this->resolutioncache[__METHOD__];
    }

    /**
     * Returns true if the current user and the target user share a job relation.
     */
    private function has_job_relationship() {
        global $USER;
        if ($this->iscurrentuser) {
            return true;
        }
        if (!isset($this->resolutioncache[__METHOD__])) {
            $this->resolutioncache[__METHOD__] = \totara_job\job_assignment::users_share_relation($this->userid, $USER->id);
        }
        return $this->resolutioncache[__METHOD__];
    }

    /**
     * Returns true if the current user has the view all details capability on either the tracked user,
     * the tracked course, or if no tracked course then any course the tracked user is enrolled within.
     *
     * @return bool
     */
    private function has_view_all_details_capability() {
        if (!isset($this->resolutioncache[__METHOD__])) {
            $this->resolutioncache[__METHOD__] = false;
            if (!$this->context_user) {
                return false; // No context, the user will be deleted.
            }
            if (has_capability('moodle/user:viewalldetails', $this->context_user)) {
                // If you've got it against the user then you've got it.
                $this->resolutioncache[__METHOD__] = true;
                return true;
            }
            foreach ($this->get_enrolled_courses() as $course) {
                $coursecontext = context_course::instance($course->id);
                if (!has_capability('moodle/user:viewalldetails', $coursecontext)) {
                    continue;
                }
                if (!$this->are_course_groups_visible_to_user($course)) {
                    // Not a member of the same group.
                    continue;
                }
                $this->resolutioncache[__METHOD__] = true;
                break;
            }
        }
        return $this->resolutioncache[__METHOD__];
    }

    /**
     * Returns true if the current user has the view details capability on either the tracked user,
     * the tracked course, or if no tracked course then any course the tracked user is enrolled within.
     *
     * This method also checks the viewalldetails capability at the same time.
     *
     * @return bool
     */
    private function has_view_details_capability() {
        if (!isset($this->resolutioncache[__METHOD__])) {
            $this->resolutioncache[__METHOD__] = false;
            if (!$this->context_user) {
                return false; // No context, the user has been deleted.
            }
            if (has_any_capability(['moodle/user:viewdetails', 'moodle/user:viewalldetails'], $this->context_user)) {
                // If you've got it against the user then you've got it.
                $this->resolutioncache[__METHOD__] = true;
                return true;
            }
            foreach ($this->get_enrolled_courses() as $course) {
                $coursecontext = context_course::instance($course->id);
                if (!has_any_capability(['moodle/user:viewdetails', 'moodle/user:viewalldetails'], $coursecontext)) {
                    continue;
                }
                if (!$this->are_course_groups_visible_to_user($course)) {
                    // Not a member of the same group.
                    continue;
                }
                $this->resolutioncache[__METHOD__] = true;
                return true;
            }
        }
        return $this->resolutioncache[__METHOD__];
    }

    /**
     * Returns true if the current user the view fullnames capability on either the tracked user,
     * the tracked course, or if no tracked course then any course the tracked user is enrolled within.
     * @return bool
     */
    private function has_view_fullnames_capability() {
        if (!isset($this->resolutioncache[__METHOD__])) {
            $this->resolutioncache[__METHOD__] = false;
            if (!$this->context_user) {
                return false;
            }
            if (has_capability('moodle/site:viewfullnames', $this->context_user)) {
                $this->resolutioncache[__METHOD__] = true;
                return true;
            }
            foreach ($this->get_enrolled_courses() as $course) {
                $coursecontext = context_course::instance($course->id);
                if (!has_capability('moodle/site:viewfullnames', $coursecontext)) {
                    continue;
                }
                if (!$this->are_course_groups_visible_to_user($course)) {
                    // Not a member of the same group.
                    continue;
                }
                $this->resolutioncache[__METHOD__] = true;
                break;
            }
        }
        return $this->resolutioncache[__METHOD__];
    }

    /**
     * Is the current user a site admin.
     *
     * This method is static as the relationship between the tracked user and the current user has no impact on the
     * whether the current user is a site administrator.
     *
     * @return bool
     */
    private static function is_current_user_an_admin() {
        global $USER;
        return is_siteadmin($USER);
    }

    /**
     * Returns true if profilesforenrolledusersonly is off or the tracked user meets the required conditions.
     *
     * The configprofilesforenrolledusersonly string states:
     *    "To prevent misuse by spammers, profile descriptions of users who are not yet enrolled in any course are hidden.
     *     New users must enrol in at least one course before they can add a profile description."
     * @return bool
     */
    private function resolve_profiles_for_enrolled_users_only() {
        global $CFG, $DB;
        if (!isset($this->resolutioncache[__METHOD__])) {
            if (empty($CFG->profilesforenrolledusersonly)) {
                $this->resolutioncache[__METHOD__] = true;
            } else {
                $this->resolutioncache[__METHOD__] = $DB->record_exists('role_assignments', array('userid' => $this->userid));
            }
        }
        return $this->resolutioncache[__METHOD__];
    }
}