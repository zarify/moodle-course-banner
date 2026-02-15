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

require_once(__DIR__ . '/../lib.php');

class hook_callbacks {
    /**
     * Inject the banner HTML at the start of the body.
     *
     * @param \core\hook\output\before_standard_top_of_body_html_generation $hook
     */
    public static function before_standard_top_of_body_html_generation(
        \core\hook\output\before_standard_top_of_body_html_generation $hook
    ): void {
        $hook->add_html(local_coursebanner_before_standard_top_of_body_html());
    }

    /**
     * Add course banner fields to the course edit form.
     *
     * @param \core_course\hook\after_form_definition $hook
     */
    public static function after_form_definition(\core_course\hook\after_form_definition $hook): void {
        $formwrapper = $hook->formwrapper;
        local_coursebanner_extend_course_edit_form(
            $hook->mform,
            $formwrapper->get_course(),
            $formwrapper->get_context()
        );
    }

    /**
     * Save course banner overrides from the course edit form.
     *
     * @param \core_course\hook\after_form_submission $hook
     */
    public static function after_form_submission(\core_course\hook\after_form_submission $hook): void {
        $data = $hook->get_data();
        // The course data is in $data itself after form submission.
        local_coursebanner_extend_course_edit_form_save($data, $data);
    }
}
