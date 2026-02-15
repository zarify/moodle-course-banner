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

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
$systemcontext = context_system::instance();
require_capability('local/coursebanner:manage', $systemcontext);

if (has_capability('moodle/site:config', $systemcontext)) {
    admin_externalpage_setup('local_coursebanner_manage');
} else {
    $PAGE->set_pagelayout('admin');
}

$categories = core_course_category::make_categories_list();
$current = $DB->get_records_menu('local_coursebanner_cat', null, '', 'categoryid,imageurl');

if (data_submitted() && confirm_sesskey()) {
    $bannerheight = optional_param('bannerheight', 80, PARAM_INT);
    if ($bannerheight < 40) {
        $bannerheight = 80;
    }
    set_config('bannerheight', $bannerheight, 'local_coursebanner');

    $defaulturl = clean_param(optional_param('defaulturl', '', PARAM_RAW_TRIMMED), PARAM_URL);
    set_config('defaulturl', $defaulturl, 'local_coursebanner');

    $caturls = optional_param_array('caturl', [], PARAM_RAW_TRIMMED);
    $now = time();

    foreach ($caturls as $categoryid => $url) {
        $categoryid = (int)$categoryid;
        if ($categoryid <= 0 || !array_key_exists($categoryid, $categories)) {
            continue;
        }

        $cleanurl = clean_param(trim($url), PARAM_URL);
        $existing = $DB->get_record('local_coursebanner_cat', ['categoryid' => $categoryid], '*', IGNORE_MISSING);

        if ($cleanurl === '') {
            if ($existing) {
                $DB->delete_records('local_coursebanner_cat', ['categoryid' => $categoryid]);
            }
            continue;
        }

        if ($existing) {
            $existing->imageurl = $cleanurl;
            $existing->timemodified = $now;
            $DB->update_record('local_coursebanner_cat', $existing);
        } else {
            $DB->insert_record('local_coursebanner_cat', [
                'categoryid' => $categoryid,
                'imageurl' => $cleanurl,
                'timecreated' => $now,
                'timemodified' => $now
            ]);
        }
    }

    // Purge all resolved banner caches since category mappings changed.
    \local_coursebanner\banner_resolver::invalidate_all_cache();

    redirect(new moodle_url('/local/coursebanner/manage.php'), get_string('changessaved'));
}

$bannerheight = (int)get_config('local_coursebanner', 'bannerheight');
if ($bannerheight < 40) {
    $bannerheight = 80;
}
$defaulturl = get_config('local_coursebanner', 'defaulturl');

$PAGE->set_url(new moodle_url('/local/coursebanner/manage.php'));
$PAGE->set_context($systemcontext);
$PAGE->requires->js_call_amd('local_coursebanner/clear_field', 'init');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managebanners', 'local_coursebanner'));

echo html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);

echo html_writer::start_div('local-coursebanner-settings');

echo html_writer::tag('p', get_string('managebanners_desc', 'local_coursebanner'));

echo html_writer::start_div('local-coursebanner-height');
echo html_writer::tag('label', get_string('bannerheight', 'local_coursebanner'), ['for' => 'bannerheight']);
echo html_writer::empty_tag('input', [
    'type' => 'number',
    'id' => 'bannerheight',
    'name' => 'bannerheight',
    'value' => $bannerheight,
    'min' => 40,
    'step' => 1
]);
echo html_writer::tag('div', get_string('bannerheight_desc', 'local_coursebanner'), ['class' => 'form-text text-muted']);
echo html_writer::end_div();

echo html_writer::start_div('local-coursebanner-default');
echo html_writer::tag('label', get_string('defaulturl', 'local_coursebanner'), ['for' => 'defaulturl']);
echo html_writer::start_div('local-coursebanner-field-wrapper');
echo html_writer::tag('button', 
    html_writer::tag('i', '', ['class' => 'icon fa fa-trash-can fa-fw ', 'aria-hidden' => 'true']), [
    'type' => 'button',
    'class' => 'local-coursebanner-clear-btn',
    'data-field-id' => 'defaulturl',
    'title' => get_string('clearfield', 'local_coursebanner'),
    'aria-label' => get_string('clearfield', 'local_coursebanner')
]);
echo html_writer::empty_tag('input', [
    'type' => 'url',
    'id' => 'defaulturl',
    'name' => 'defaulturl',
    'value' => $defaulturl,
    'size' => 80
]);
echo html_writer::end_div();
echo html_writer::end_div();

$table = new html_table();
$table->head = [
    get_string('category'),
    get_string('categorybannerurl', 'local_coursebanner')
];
$table->data = [];

foreach ($categories as $categoryid => $categoryname) {
    $value = $current[$categoryid] ?? '';
    $fieldid = 'caturl_' . $categoryid;
    
    $wrapper = html_writer::start_div('local-coursebanner-field-wrapper');
    $wrapper .= html_writer::tag('button', 
        html_writer::tag('i', '', ['class' => 'icon fa fa-trash-can fa-fw ', 'aria-hidden' => 'true']), [
        'type' => 'button',
        'class' => 'local-coursebanner-clear-btn',
        'data-field-id' => $fieldid,
        'title' => get_string('clearfield', 'local_coursebanner'),
        'aria-label' => get_string('clearfield', 'local_coursebanner')
    ]);
    $wrapper .= html_writer::empty_tag('input', [
        'type' => 'url',
        'id' => $fieldid,
        'name' => 'caturl[' . $categoryid . ']',
        'value' => $value,
        'size' => 80
    ]);
    $wrapper .= html_writer::end_div();
    
    $table->data[] = [format_string($categoryname), $wrapper];
}

echo html_writer::table($table);

echo html_writer::empty_tag('input', [
    'type' => 'submit',
    'class' => 'btn btn-primary',
    'value' => get_string('savechanges')
]);

echo html_writer::end_div();
echo html_writer::end_tag('form');

echo $OUTPUT->footer();
