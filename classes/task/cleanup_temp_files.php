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

namespace quiz_archiver\task;

use quiz_archiver\FileManager;

defined('MOODLE_INTERNAL') || die();


/**
 * Scheduled task to periodically clean up temporary files.
 */
class cleanup_temp_files extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_name(): string {
        return get_string('task_cleanup_temp_files', 'quiz_archiver');
    }

    /**
     * Execute the task.
     * @throws \dml_exception
     * @throws \coding_exception
     */
    public function execute(): void {
        echo get_string('task_cleanup_temp_files_start', 'quiz_archiver') . "\n";
        $files_deleted = FileManager::cleanup_temp_files();
        echo get_string('task_cleanup_temp_files_report', 'quiz_archiver', $files_deleted) . "\n";
    }

}