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
 * @package    quiz_archiver
 * @copyright  ISB Bayern, 2024
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.php');

global $PAGE, $USER, $DB, $OUTPUT;

$thisurl = new moodle_url('/mod/quiz/report/archiver/settings_coversheet.php');
$PAGE->set_url($thisurl);

$action = optional_param('action', '', PARAM_RAW);

if (!empty($action)) {
    switch ($action) {
        case 'storecoversheet':
            set_config('dynamic_pdf_content', required_param('dynamic_pdf_content', PARAM_RAW), 'quiz_archiver');
            break;
    }
}

$PAGE->set_context(\context_system::instance());
$pagetitle = get_string('define_pdfcoversheet', 'quiz_archiver');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

// No secondary navigation.
// $PAGE->set_secondary_navigation(false);

$templatecontext = [];

$templatecontext['storedhtml'] = '<html>
    <head>
    </head>
    <body>
        <h2>EXAMPLE</h2>
        <p>This is an example, and will not be present in the PDF. As far as you do not save this page :-).</p>
    </body>
</html>';
if(!empty($dynamicpdfcontent = get_config('quiz_archiver', 'dynamic_pdf_content'))) {
    $templatecontext['storedhtml'] = get_config('quiz_archiver', 'dynamic_pdf_content');
}

$templatecontext['placeholderdata'] = \quiz_archiver\coversheet\create_coversheet::get_possible_placeholders();
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('quiz_archiver/define_pdfcoversheet', $templatecontext);
echo $OUTPUT->footer();
