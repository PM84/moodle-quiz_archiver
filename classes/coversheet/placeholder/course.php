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
 * Handles course callback to get corse info.
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
 * Handles course callback to get corse info.
 *
 * @package    quiz_archiver
 * @copyright  ISB Bayern, 2024
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course {

    /**
     * Get course fullname.
     * @param object $params
     * @return string
     */
    public static function fullname(object $params): string {
        $course = get_course($params->courseid);
        return $course->fullname;
    }

    /**
     * Get course shortname.
     * @param object $params
     * @return string
     */
    public static function shortname(object $params): string {
        $course = get_course($params->courseid);
        return $course->shortname;
    }

    /**
     * Get course summary.
     * @param object $params
     * @return string
     */
    public static function summary(object $params): string {
        $course = get_course($params->courseid);
        return $course->summary;
    }

    /**
     * Get course format.
     * @param object $params
     * @return string
     */
    public static function format(object $params): string {
        $course = get_course($params->courseid);
        return $course->format;
    }

    /**
     * Get course startdate.
     * @param object $params
     * @return string
     */
    public static function startdate(object $params): string {
        $course = get_course($params->courseid);
        return $course->startdate;
    }

    /**
     * Get course enddate.
     * @param object $params
     * @return string
     */
    public static function enddate(object $params): string {
        $course = get_course($params->courseid);
        return $course->enddate;
    }
}
