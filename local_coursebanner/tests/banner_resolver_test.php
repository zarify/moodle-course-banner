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

/**
 * Tests for banner resolver.
 */
class banner_resolver_test extends advanced_testcase {
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        $this->reset_resolver_cache();
    }

    public function test_returns_default_when_no_overrides(): void {
        set_config('defaulturl', 'https://example.test/default.png', 'local_coursebanner');

        $course = $this->getDataGenerator()->create_course();

        $url = banner_resolver::get_banner_url((int)$course->id);
        $this->assertSame('https://example.test/default.png', $url);
    }

    public function test_category_banner_overrides_default(): void {
        set_config('defaulturl', 'https://example.test/default.png', 'local_coursebanner');

        $category = $this->getDataGenerator()->create_category(['name' => 'Cat A']);
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);

        $this->insert_category_banner($category->id, 'https://example.test/cat.png');

        $url = banner_resolver::get_banner_url((int)$course->id);
        $this->assertSame('https://example.test/cat.png', $url);
    }

    public function test_parent_category_banner_is_used_when_subcategory_missing(): void {
        $parent = $this->getDataGenerator()->create_category(['name' => 'Parent']);
        $child = $this->getDataGenerator()->create_category(['name' => 'Child', 'parent' => $parent->id]);
        $course = $this->getDataGenerator()->create_course(['category' => $child->id]);

        $this->insert_category_banner($parent->id, 'https://example.test/parent.png');

        $url = banner_resolver::get_banner_url((int)$course->id);
        $this->assertSame('https://example.test/parent.png', $url);
    }

    public function test_course_override_takes_precedence(): void {
        $category = $this->getDataGenerator()->create_category(['name' => 'Cat']);
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);

        $this->insert_category_banner($category->id, 'https://example.test/cat.png');
        $this->insert_course_override($course->id, 'https://example.test/course.png');

        $url = banner_resolver::get_banner_url((int)$course->id);
        $this->assertSame('https://example.test/course.png', $url);
    }

    public function test_returns_empty_when_no_default_or_overrides(): void {
        $course = $this->getDataGenerator()->create_course();

        $url = banner_resolver::get_banner_url((int)$course->id);
        $this->assertSame('', $url);
    }

    public function test_returns_default_for_missing_course_when_configured(): void {
        set_config('defaulturl', 'https://example.test/default.png', 'local_coursebanner');

        $url = banner_resolver::get_banner_url(999999);
        $this->assertSame('https://example.test/default.png', $url);
    }

    public function test_grandparent_category_banner_is_used_when_descendants_missing(): void {
        $grandparent = $this->getDataGenerator()->create_category(['name' => 'Grandparent']);
        $parent = $this->getDataGenerator()->create_category(['name' => 'Parent', 'parent' => $grandparent->id]);
        $child = $this->getDataGenerator()->create_category(['name' => 'Child', 'parent' => $parent->id]);
        $course = $this->getDataGenerator()->create_course(['category' => $child->id]);

        $this->insert_category_banner($grandparent->id, 'https://example.test/grandparent.png');

        $url = banner_resolver::get_banner_url((int)$course->id);
        $this->assertSame('https://example.test/grandparent.png', $url);
    }

    public function test_resolved_urls_are_trimmed(): void {
        set_config('defaulturl', "   https://example.test/default-trimmed.png  \n", 'local_coursebanner');
        $category = $this->getDataGenerator()->create_category(['name' => 'Cat']);
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);

        $url = banner_resolver::get_banner_url((int)$course->id);
        $this->assertSame('https://example.test/default-trimmed.png', $url);

        $this->insert_category_banner($category->id, "\n https://example.test/category-trimmed.png \t");
        \local_coursebanner\banner_resolver::invalidate_course_cache((int)$course->id);

        $url = banner_resolver::get_banner_url((int)$course->id);
        $this->assertSame('https://example.test/category-trimmed.png', $url);

        $this->insert_course_override($course->id, "  https://example.test/course-trimmed.png   ");
        \local_coursebanner\banner_resolver::invalidate_course_cache((int)$course->id);

        $url = banner_resolver::get_banner_url((int)$course->id);
        $this->assertSame('https://example.test/course-trimmed.png', $url);
    }

    private function insert_category_banner(int $categoryid, string $url): void {
        global $DB;

        $now = time();
        $DB->insert_record('local_coursebanner_cat', [
            'categoryid' => $categoryid,
            'imageurl' => $url,
            'timecreated' => $now,
            'timemodified' => $now
        ]);

        $this->reset_resolver_cache();
    }

    private function insert_course_override(int $courseid, string $url): void {
        global $DB;

        $now = time();
        $DB->insert_record('local_coursebanner_course', [
            'courseid' => $courseid,
            'imageurl' => $url,
            'timecreated' => $now,
            'timemodified' => $now
        ]);

        $this->reset_resolver_cache();
    }

    private function reset_resolver_cache(): void {
        $ref = new \ReflectionClass(banner_resolver::class);

        $coursecache = $ref->getProperty('coursecache');
        $coursecache->setAccessible(true);
        $coursecache->setValue(null, []);

        $categorycache = $ref->getProperty('categorycache');
        $categorycache->setAccessible(true);
        $categorycache->setValue(null, null);
    }
}
