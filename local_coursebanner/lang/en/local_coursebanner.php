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

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Course banners';
$string['banneralt'] = 'Banner image for {$a}';
$string['coursebannerheading'] = 'Course banner';
$string['coursebannerurl'] = 'Course banner image URL';
$string['coursebannerurl_help'] = 'Overrides any category banner for this course. Leave blank to inherit from category settings.';
$string['clearfield'] = 'Clear this URL field';
$string['defaulturl'] = 'Default banner image URL';
$string['defaulturl_desc'] = 'Used when neither a course override nor a category banner is configured.';
$string['bannerheight'] = 'Banner height (pixels)';
$string['bannerheight_desc'] = 'Height of banner images in pixels. Must be at least 40px to accommodate the course title overlay. Default is 80px.';
$string['managebanners'] = 'Manage course banners';
$string['managebanners_desc'] = 'Set the global banner height and default URL, plus banner image URLs for categories. Course-specific overrides are configured in course settings.';
$string['categorybannerurl'] = 'Category banner image URL';

// Capabilities.
$string['coursebanner:manage'] = 'Manage global and category banners';
$string['coursebanner:edit'] = 'Edit course banner overrides';

// Privacy.
$string['privacy:metadata:local_coursebanner_cat'] = 'Stores banner image URLs for course categories';
$string['privacy:metadata:local_coursebanner_cat:categoryid'] = 'The course category ID';
$string['privacy:metadata:local_coursebanner_cat:imageurl'] = 'The banner image URL';
$string['privacy:metadata:local_coursebanner_cat:timecreated'] = 'When the banner was configured';
$string['privacy:metadata:local_coursebanner_cat:timemodified'] = 'When the banner was last modified';
$string['privacy:metadata:local_coursebanner_course'] = 'Stores banner image URL overrides for specific courses';
$string['privacy:metadata:local_coursebanner_course:courseid'] = 'The course ID';
$string['privacy:metadata:local_coursebanner_course:imageurl'] = 'The banner image URL';
$string['privacy:metadata:local_coursebanner_course:timecreated'] = 'When the banner override was configured';
$string['privacy:metadata:local_coursebanner_course:timemodified'] = 'When the banner override was last modified';
