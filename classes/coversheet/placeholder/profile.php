<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Handles everything that is needed for coversheet creation.
 *
 * @package    quiz_archiver
 * @copyright  ISB Bayern, 2024
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quiz_archiver\coversheet\placeholder;

use dml_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Handles everything that is needed for coversheet creation.
 *
 * @package    quiz_archiver
 * @copyright  ISB Bayern, 2024
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile {

    // @var userprofilefilds
    var $userprofilefiels = [
        'id',
        'auth',
        'confirmed',
        'policyagreed',
        'deleted',
        'suspended',
        'mnethostid',
        'username',
        'password',
        'idnumber',
        'firstname',
        'lastname',
        'email',
        'emailstop',
        'phone1',
        'phone2',
        'institution',
        'department',
        'address',
        'city',
        'country',
        'lang',
        'calendartype',
        'theme',
        'timezone',
        'firstaccess',
        'lastaccess',
        'lastlogin',
        'currentlogin',
        'lastip',
        'secret',
        'picture',
        'description',
        'descriptionformat',
        'mailformat',
        'maildigest',
        'maildisplay',
        'autosubscribe',
        'trackforums',
        'timecreated',
        'timemodified',
        'trustbitmask',
        'imagealt',
        'lastnamephonetic',
        'firstnamephonetic',
        'middlename',
        'alternatename',
        'moodlenetprofile',
    ];

    /**
     * Get user firstname.
     * @param object $params
     * @return string
     */
    public static function firstname( object $params): string {
        $user = \core_user::get_user($params->userid);
        return $user->firstname;
    }

    /**
     * Get user lastname.
     * @param object $params
     * @return string
     */
    public static function lastname( object $params): string {
        $user = \core_user::get_user($params->userid);
        return $user->lastname;
    }

    /**
     * Get user fullname.
     * @param object $params
     * @return string
     */
    public static function userfullname( object $params): string {
        $user = \core_user::get_user($params->userid);
        return \core_user::get_fullname($user);
    }

    /**
     * Get user institution.
     * @param object $params
     * @return string
     */
    public static function institution( object $params): string {
        $user = \core_user::get_user($params->userid);
        return $user->institution;
    }

    /**
     * Get user department.
     * @param object $params
     * @return string
     */
    public static function department( object $params): string {
        $user = \core_user::get_user($params->userid);
        return $user->department;
    }

    /**
     * Get user address.
     * @param object $params
     * @return string
     */
    public static function address( object $params): string {
        $user = \core_user::get_user($params->userid);
        return $user->address;
    }

    /**
     * Get user city.
     * @param object $params
     * @return string
     */
    public static function city( object $params): string {
        $user = \core_user::get_user($params->userid);
        return $user->city;
    }

    /**
     * Get user country.
     * @param object $params
     * @return string
     */
    public static function country( object $params): string {
        $user = \core_user::get_user($params->userid);
        return $user->country;
    }

    /**
     * Get user language.
     * @param object $params
     * @return string
     */
    public static function language( object $params): string {
        $user = \core_user::get_user($params->userid);
        return $user->lang;
    }
}
