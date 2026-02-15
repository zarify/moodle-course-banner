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

use cache;

defined('MOODLE_INTERNAL') || die();

class banner_resolver {
    /** @var array<int,string> */
    private static $coursecache = [];

    /** @var array<int,string>|null */
    private static $categorycache = null;

    /**
     * Resolve banner URL for a course.
     *
     * @param int $courseid
     * @return string
     */
    public static function get_banner_url(int $courseid): string {
        global $DB;

        // Check MUC cache first.
        $cache = cache::make('local_coursebanner', 'resolvedbanners');
        $cachekey = 'course_' . $courseid;
        $cached = $cache->get($cachekey);
        if ($cached !== false) {
            return $cached;
        }

        // Check static cache.
        if (array_key_exists($courseid, self::$coursecache)) {
            return self::$coursecache[$courseid];
        }

        $url = '';

        $override = $DB->get_record('local_coursebanner_course', ['courseid' => $courseid], 'imageurl', IGNORE_MISSING);
        if ($override && trim($override->imageurl) !== '') {
            $url = trim($override->imageurl);
            self::$coursecache[$courseid] = $url;
            $cache->set($cachekey, $url);
            return $url;
        }

        $course = $DB->get_record('course', ['id' => $courseid], 'id,category', IGNORE_MISSING);
        if ($course && !empty($course->category)) {
            $categoryurls = self::get_category_urls();
            $category = \core_course_category::get($course->category, IGNORE_MISSING);
            while ($category) {
                $categoryid = $category->id;
                if (!empty($categoryurls[$categoryid])) {
                    $url = trim($categoryurls[$categoryid]);
                    break;
                }
                if (empty($category->parent)) {
                    break;
                }
                $category = \core_course_category::get($category->parent, IGNORE_MISSING);
            }
        }

        if ($url === '') {
            $defaulturl = get_config('local_coursebanner', 'defaulturl');
            if (!empty($defaulturl)) {
                $url = trim($defaulturl);
            }
        }

        self::$coursecache[$courseid] = $url;
        $cache->set($cachekey, $url);
        return $url;
    }

    /**
     * Invalidate resolver cache for one course.
     *
     * @param int $courseid
     * @return void
     */
    public static function invalidate_course_cache(int $courseid): void {
        unset(self::$coursecache[$courseid]);

        $cache = cache::make('local_coursebanner', 'resolvedbanners');
        $cache->delete('course_' . $courseid);
    }

    /**
     * Invalidate all resolver caches.
     *
     * @return void
     */
    public static function invalidate_all_cache(): void {
        self::$coursecache = [];
        self::$categorycache = null;

        $cache = cache::make('local_coursebanner', 'resolvedbanners');
        $cache->purge();
    }

    /**
     * @return array<int,string>
     */
    private static function get_category_urls(): array {
        global $DB;

        if (self::$categorycache === null) {
            self::$categorycache = $DB->get_records_menu('local_coursebanner_cat', null, '', 'categoryid,imageurl');
        }

        return self::$categorycache;
    }
}
