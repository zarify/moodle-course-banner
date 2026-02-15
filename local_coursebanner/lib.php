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

/**
 * Render course banner for course view pages.
 *
 * @return string
 */
function local_coursebanner_before_standard_top_of_body_html() {
    global $PAGE, $OUTPUT;

    // Check if we have a valid course ID (don't check $PAGE->course itself due to magic methods)
    if (!isset($PAGE->course->id)) {
        return '';
    }
    
    if ($PAGE->course->id == SITEID) {
        return '';
    }

    // Only show on course view pages - don't use empty() due to magic properties
    $pagetype = $PAGE->pagetype ?? '';
    if ($pagetype === '') {
        return '';
    }
    
    if (strpos($pagetype, 'course-view') !== 0) {
        return '';
    }

    $url = \local_coursebanner\banner_resolver::get_banner_url((int)$PAGE->course->id);
    
    if ($url === '') {
        return '';
    }

    $bannerheight = (int)get_config('local_coursebanner', 'bannerheight');
    if ($bannerheight < 40) {
        $bannerheight = 80;
    }

    $data = [
        'url' => $url,
        'alt' => get_string('banneralt', 'local_coursebanner', format_string($PAGE->course->fullname)),
        'height' => $bannerheight
    ];

    $PAGE->requires->js_call_amd('local_coursebanner/banner', 'init', [$bannerheight]);

    return $OUTPUT->render_from_template('local_coursebanner/banner', $data);
}

/**
 * Add course banner override field to the course edit form.
 *
 * @param MoodleQuickForm $mform
 * @param stdClass $course
 * @param context|null $context
 */
function local_coursebanner_extend_course_edit_form($mform, $course, $context = null) {
    global $DB, $PAGE;

    if (!is_object($mform)) {
        return;
    }

    // Check capability if context is provided.
    if ($context && !has_capability('local/coursebanner:edit', $context)) {
        return;
    }

    $mform->addElement('header', 'local_coursebanner_header', get_string('coursebannerheading', 'local_coursebanner'));
    
    // Create a group with the text input and a static element for the trash icon
    $group = [];
    $group[] = $mform->createElement('static', 'local_coursebanner_url_clear', '', 
        '<button type="button" class="local-coursebanner-clear-btn" data-field-id="id_local_coursebanner_url" ' .
        'title="' . get_string('clearfield', 'local_coursebanner') . '" ' .
        'aria-label="' . get_string('clearfield', 'local_coursebanner') . '">' .
        '<i class="icon fa fa-trash-can fa-fw " aria-hidden="true"></i></button>');
    $group[] = $mform->createElement('text', 'local_coursebanner_url', '', ['size' => 80]);
    
    $mform->addGroup($group, 'local_coursebanner_url_group', get_string('coursebannerurl', 'local_coursebanner'), ' ', false);
    $mform->setType('local_coursebanner_url', PARAM_URL);
    $mform->addHelpButton('local_coursebanner_url_group', 'coursebannerurl', 'local_coursebanner');
    
    // Load the clear field JavaScript
    $PAGE->requires->js_call_amd('local_coursebanner/clear_field', 'init');

    if (!empty($course->id)) {
        $record = $DB->get_record('local_coursebanner_course', ['courseid' => $course->id], 'imageurl', IGNORE_MISSING);
        if ($record && trim($record->imageurl) !== '') {
            $mform->setDefault('local_coursebanner_url', $record->imageurl);
        }
    }
}

/**
 * Save course banner override from the course edit form.
 *
 * @param stdClass $data
 * @param stdClass $course
 */
function local_coursebanner_extend_course_edit_form_save($data, $course) {
    global $DB;

    if (empty($course->id) || !property_exists($data, 'local_coursebanner_url')) {
        return;
    }

    $context = context_course::instance((int)$course->id, IGNORE_MISSING);
    if (!$context || !has_capability('local/coursebanner:edit', $context)) {
        return;
    }

    $url = clean_param(trim((string)$data->local_coursebanner_url), PARAM_URL);
    $record = $DB->get_record('local_coursebanner_course', ['courseid' => $course->id], '*', IGNORE_MISSING);
    $now = time();

    if ($url === '') {
        if ($record) {
            $DB->delete_records('local_coursebanner_course', ['courseid' => $course->id]);
        }
    } else {
        if ($record) {
            $record->imageurl = $url;
            $record->timemodified = $now;
            $DB->update_record('local_coursebanner_course', $record);
        } else {
            $DB->insert_record('local_coursebanner_course', [
                'courseid' => $course->id,
                'imageurl' => $url,
                'timecreated' => $now,
                'timemodified' => $now
            ]);
        }
    }

    // Purge cache for this course.
    \local_coursebanner\banner_resolver::invalidate_course_cache((int)$course->id);
}
