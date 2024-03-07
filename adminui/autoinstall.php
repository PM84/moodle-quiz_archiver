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
 * Handler for autoinstall feature from the admin UI of the quiz archiver plugin.
 *
 * @package   quiz_archiver
 * @copyright 2024 Niels Gandraß <niels@gandrass.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */

const NO_OUTPUT_BUFFERING = true;

require_once(__DIR__.'/../../../../../config.php');
require_once("{$CFG->libdir}/moodlelib.php");

use quiz_archiver\form\autoinstall_form;
use quiz_archiver\local\autoinstall;

// Ensure user has permissions
require_login();
$ctx = context_system::instance();
require_capability('moodle/site:config', $ctx);

// Setup page
$PAGE->set_context($ctx);
$PAGE->set_url('/mod/quiz/report/archiver/adminui/autoinstall.php');
$title = get_string('autoinstall_plugin', 'quiz_archiver');
$PAGE->set_title($title);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

// Content
if (autoinstall::plugin_is_unconfigured()) {
    $form = new autoinstall_form();

    if ($form->is_cancelled()) {
        // Cancelled
        echo $OUTPUT->paragraph(get_string('autoinstall_cancelled', 'quiz_archiver'));
        echo $OUTPUT->paragraph(get_string('pleaseclose'));
    } else if ($data = $form->get_data()) {
        // Perform autoinstall
        list($success, $log) = autoinstall::execute(
            $data->wsname,
            $data->rolename,
            $data->username
        );

        // Show result
        echo $OUTPUT->paragraph(get_string('autoinstall_started', 'quiz_archiver'));
        echo $OUTPUT->paragraph(get_string('logs'));
        echo "<pre>{$log}</pre><br/>";

        if ($success) {
            echo $OUTPUT->paragraph(get_string('autoinstall_success', 'quiz_archiver'));
        } else {
            echo $OUTPUT->paragraph(get_string('autoinstall_failure', 'quiz_archiver'));
        }

        echo $OUTPUT->paragraph(get_string('pleaseclose'));
    } else {
        $form->display();
    }
} else {
    echo $OUTPUT->paragraph(get_string('autoinstall_already_configured_long', 'quiz_archiver'));
    echo $OUTPUT->paragraph(get_string('pleaseclose'));
}

// End page
echo $OUTPUT->footer();
