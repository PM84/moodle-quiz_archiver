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

namespace quiz_archiver\local;

/**
 * Custom util functions
 *
 * @package   quiz_archiver
 * @copyright  2024, ISB Bayern
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coversheet {

    /**
     * Collects all information needed for the coversheet and returns the resulting html.
     * @param int $attemptid
     * @return string
     */
    public static function get_coversheet(int $attemptid):string {
        $html = "";
        return $html;
    }




// foreach (core_component::get_plugin_list('local') as $plugin => $plugindir) {
//     if (get_string_manager()->string_exists('pluginname', 'local_' . $plugin)) {
//         $strpluginname = get_string('pluginname', 'local_' . $plugin);
//     } else {
//         $strpluginname = $plugin;
//     }
//     $plugins[$plugin] = $strpluginname;
// }

}
