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

/**
 * Admin setting for URL with clear button.
 *
 * @package    local_coursebanner
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configtext_with_clear extends \admin_setting_configtext {
    
    /**
     * Return the setting with clear button
     *
     * @param mixed $data
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = '') {
        global $PAGE, $OUTPUT;
        
        $default = $this->get_defaultsetting();
        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => $data,
            'size' => $this->size,
        ];

        // Load the clear field JavaScript
        $PAGE->requires->js_call_amd('local_coursebanner/clear_field', 'init');
        
        $cleartext = get_string('clearfield', 'local_coursebanner');
        
        $element = '<div class="local-coursebanner-field-wrapper">';
        $element .= '<button type="button" class="local-coursebanner-clear-btn" data-field-id="' . $context->id . '" ';
        $element .= 'title="' . s($cleartext) . '" aria-label="' . s($cleartext) . '">';
        $element .= '<i class="icon fa fa-trash-can fa-fw " aria-hidden="true"></i>';
        $element .= '</button>';
        $element .= '<input type="text" id="' . $context->id . '" name="' . $context->name . '" ';
        $element .= 'value="' . s($context->value) . '" size="' . $context->size . '" />';
        $element .= '</div>';

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }
}
