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

// $courseid = required_param('courseid', PARAM_INT);

$thisurl = new moodle_url('/mod/quiz/report/archiver/settings_coversheet.php');
$PAGE->set_url($thisurl);
// $PAGE->set_pagelayout('incourse');

$action = optional_param('action', '', PARAM_RAW);

if (!empty($action)) {
    switch ($action) {
        case 'storecoversheet':
            set_config('dynamic_pdf_content', required_param('dynamic_pdf_content', PARAM_RAW), 'quiz_archiver');
            break;
    }
}

// $category = $DB->get_record('course_categories', array('id' => $ccatid), '*', MUST_EXIST);
// $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));

// require_login($courseid, false);
// $coursecontext = context_course::instance($courseid);

// $template = $DB->get_record('block_mbsteachshare_template', array('courseid' => $courseid), '*', MUST_EXIST);

$PAGE->set_context(\context_system::instance());
$pagetitle = get_string('define_pdfcoversheet', 'quiz_archiver');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

// No secondary navigation.
$PAGE->set_secondary_navigation(false);

$templatecontext = [];

// $helper = new quiz_archiver\helper();
// $plugininfo = new quiz_archiver\plugininfo\aitool();
// $enabledtools = $plugininfo->get_enabled_plugins();

// // $options = ['' => get_string('pleaseselect', 'quiz_archiver')];
// $options = [];
// foreach ($enabledtools as $tool) {
//     $options[] = ['tool' => $tool, 'toolname' => get_string('pluginname', 'aitool_' . $tool), 'apikey' => "testkey"];
// }

// $purposes = $helper->get_all_purposes();
// foreach ($purposes as $purpose) {

//     $templatecontext['matching']['purposes'][] = [
//         'purpose' => $purpose,
//         'purposename' => get_string('purpose_' . $purpose, 'quiz_archiver'),
//         'selectoptions' => $options,
//     ];
// }

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
// print_r(get_config('quiz_archiver', 'dynamic_pdf_content'));die;
echo $OUTPUT->header();

// $renderer = $PAGE->get_renderer('block_mbsteachshare');
// template::add_template_management_info($template);

echo $OUTPUT->render_from_template('quiz_archiver/define_pdfcoversheet', $templatecontext);
// $logdata = log::get_template_history($template->id);
// echo $renderer->render_template_history($template, $logdata);

echo $OUTPUT->footer();
