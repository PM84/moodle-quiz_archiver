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

namespace quiz_archiver\coversheet;

use admin_setting_heading;
use coding_exception;
use quiz_archiver\external\get_attempts_metadata;

defined('MOODLE_INTERNAL') || die();

/**
 * Handles everything that is needed for coversheet creation.
 *
 * @package    quiz_archiver
 * @copyright  ISB Bayern, 2024
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create_coversheet {

    /**
     * Create the coversheet.
     * @param int $attemptid
     * @return string
     */
    public static function get_coversheet(int $attemptid): string {
        global $OUTPUT;

        $config = get_config('quiz_archiver');

        if (empty($config->enable_pdf_coversheet)) {
            return '';
        }

        // Get all needed attempt metadata. E.g. User, timestarted, timefinished etc.
        $attemptmetadata = self::get_attempt_metadata($attemptid);

        // Find all placeholders.
        preg_match_all('/{{(.*)_(.*)}}/', $config->dynamic_pdf_content, $matches);

        // Now replace all placeholders.
        $html = $config->dynamic_pdf_content;
        foreach ($matches[0] as $key => $placeholder) {
            $classpath = '\quiz_archiver\coversheet\placeholder\\' . $matches[1][$key];
            $method = $matches[2][$key];
            $replacement = self::check_class_and_method($classpath, $method, $attemptmetadata);
            $html = preg_replace('/' . $placeholder . '/', $replacement, $html);
        }
        \local_debugger\performance\debugger::print_debug('test', 'html', $html);

        $filename = !empty($config->pdfcoversheetbackgroundimage) ? $config->pdfcoversheetbackgroundimage : null;
        $fs = get_file_storage();
        $context = \context_system::instance();

        $templatecontext = [];

        $backgroundimage = $fs->get_file($context->id, 'quiz_archiver', 'pdfcoversheetbackgroundimage', 0, '/', $filename);
        if (!empty($backgroundimage)) {
            $imgdata64 = base64_encode($backgroundimage->get_content());
            $templatecontext['backgroundimage64'] = 'data:image/png;base64,' . $imgdata64;
        }
        $templatecontext['html'] = $html;
        $templatecontext['styles'] = 'page-break-after: always; width: 100%; height: 100vh;';

        $html = $OUTPUT->render_from_template('quiz_archiver/pdfcoversheet_html_sceleton', $templatecontext);
        // $html = '<div style="' . join(' ', $styles) . '">' . $html . '</div>';
        return $html;
    }

    /**
     * Gets the metadata of all attempts made inside this quiz, excluding previews.
     *
     * @param array|null $filter_attemptids If given, only attempts with the given
     * IDs will be returned.
     *
     * @return object
     * @throws \dml_exception
     */
    private static function get_attempt_metadata(int $attemptid): object {
        global $DB;

        $fields = [
            'qa.id AS attemptid',
            'qa.userid',
            'qa.quiz as quizinstance',
            'qa.attempt as attemptnumber',
            'qa.state',
            'qa.timestart',
            'qa.timefinish',
            'q.course as courseid',
        ];

        $sql = "SELECT " . join(", ", $fields) .
            " FROM {quiz_attempts} qa " .
            "LEFT JOIN {user} u ON qa.userid = u.id " .
            "LEFT JOIN {quiz} q on q.id = qa.quiz " .
            "WHERE qa.id = :qaid";

        // Get all requested attempt
        return $DB->get_record_sql($sql, ["qaid" => $attemptid]);
    }

    /**
     * Checks and executes the callback method.
     * @param string $classpath
     * @param string $method
     * @param string|int|object|array $params
     * @return string
     */
    private static function check_class_and_method(string $classpath, string $method, string|int|object|array $params): string {

        if (!class_exists($classpath)) {
            return 'Class ' . $classpath . ' not found.';
        }

        $class = new $classpath();
        if (!method_exists($class, $method)) {
            return 'Placeholder for ' . $method . ' not found.';
        }

        return $class::$method($params);
    }

    /**
     * Get all possible placeholders in a mustache context format.
     *
     * @return array
     */
    public static function get_possible_placeholders(): array {
        global $CFG;
        $placeholders = [];
        $dir = $CFG->dirroot . "/mod/quiz/report/archiver/classes/coversheet/placeholder";
        $basenames = self::get_all_files_in_directory($dir);
        foreach ($basenames as $basename) {
            $placeholders[] = [
                'placeholders' => self::get_placeholders($basename, "\quiz_archiver\coversheet\placeholder\\$basename"),
                'metadata' => [
                    'tabid' => 'qa_' . $basename . '_tab',
                    'tab' => get_string($basename, 'quiz_archiver'),
                ],
            ];
        }

        return $placeholders;
    }

    /**
     * Get the array of the placeholders.
     *
     * @param string $basename
     * @param string $classname
     * @return array
     */
    private static function get_placeholders(string $basename, string $classname): array {
        $methods = get_class_methods($classname);
        $placeholders = [];
        foreach ($methods as $method) {
            $placeholders[] = $basename . "_" . $method;
        }
        return $placeholders;
    }

    /**
     * Get all basenames of files in a specific directory
     *
     * @param string $dir
     * @return array
     * @throws coding_exception
     */
    private static function get_all_files_in_directory(string $dir): array {
        $files = scandir($dir);
        $basenames = [];
        foreach ($files as $file) {
            if (is_file($dir . '/' . $file)) {
                $basenames[] = basename($file, '.php');
            }
        }
        return $basenames;
    }
}
