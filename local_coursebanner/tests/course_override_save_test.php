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

namespace local_coursebanner;

defined('MOODLE_INTERNAL') || die();

use advanced_testcase;

require_once(__DIR__ . '/../lib.php');

/**
 * Tests for course override save flow.
 */
class course_override_save_test extends advanced_testcase {
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        banner_resolver::invalidate_all_cache();
    }

    public function test_save_rejected_without_edit_capability(): void {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($student);
        \local_coursebanner_extend_course_edit_form_save((object)[
            'local_coursebanner_url' => 'https://example.test/blocked.png'
        ], (object)[
            'id' => $course->id
        ]);

        $this->assertFalse($DB->record_exists('local_coursebanner_course', ['courseid' => $course->id]));
    }

    public function test_save_and_clear_allowed_with_edit_capability(): void {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        $this->setUser($teacher);
        \local_coursebanner_extend_course_edit_form_save((object)[
            'local_coursebanner_url' => 'https://example.test/allowed.png'
        ], (object)[
            'id' => $course->id
        ]);

        $record = $DB->get_record('local_coursebanner_course', ['courseid' => $course->id], 'imageurl', MUST_EXIST);
        $this->assertSame('https://example.test/allowed.png', $record->imageurl);

        \local_coursebanner_extend_course_edit_form_save((object)[
            'local_coursebanner_url' => ''
        ], (object)[
            'id' => $course->id
        ]);

        $this->assertFalse($DB->record_exists('local_coursebanner_course', ['courseid' => $course->id]));
    }

    public function test_save_invalidates_resolver_static_and_muc_cache(): void {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        $now = time();
        $DB->insert_record('local_coursebanner_course', [
            'courseid' => $course->id,
            'imageurl' => 'https://example.test/original.png',
            'timecreated' => $now,
            'timemodified' => $now
        ]);

        $first = banner_resolver::get_banner_url((int)$course->id);
        $this->assertSame('https://example.test/original.png', $first);

        $this->setUser($teacher);
        \local_coursebanner_extend_course_edit_form_save((object)[
            'local_coursebanner_url' => 'https://example.test/updated.png'
        ], (object)[
            'id' => $course->id
        ]);

        $second = banner_resolver::get_banner_url((int)$course->id);
        $this->assertSame('https://example.test/updated.png', $second);
    }
}
