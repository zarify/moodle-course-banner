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
 * Privacy API implementation for local_coursebanner.
 *
 * @package    local_coursebanner
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coursebanner\privacy;

use core_privacy\local\metadata\collection;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider for local_coursebanner.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no user data.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'local_coursebanner_cat',
            [
                'categoryid' => 'privacy:metadata:local_coursebanner_cat:categoryid',
                'imageurl' => 'privacy:metadata:local_coursebanner_cat:imageurl',
                'timecreated' => 'privacy:metadata:local_coursebanner_cat:timecreated',
                'timemodified' => 'privacy:metadata:local_coursebanner_cat:timemodified',
            ],
            'privacy:metadata:local_coursebanner_cat'
        );

        $collection->add_database_table(
            'local_coursebanner_course',
            [
                'courseid' => 'privacy:metadata:local_coursebanner_course:courseid',
                'imageurl' => 'privacy:metadata:local_coursebanner_course:imageurl',
                'timecreated' => 'privacy:metadata:local_coursebanner_course:timecreated',
                'timemodified' => 'privacy:metadata:local_coursebanner_course:timemodified',
            ],
            'privacy:metadata:local_coursebanner_course'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return \core_privacy\local\request\contextlist
     */
    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
        // This plugin does not store personal data linked to users.
        $contextlist = new \core_privacy\local\request\contextlist();
        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param \core_privacy\local\request\approved_contextlist $contextlist
     */
    public static function export_user_data(\core_privacy\local\request\approved_contextlist $contextlist) {
        // This plugin does not store personal data linked to users.
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // This plugin does not store personal data linked to users.
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param \core_privacy\local\request\approved_contextlist $contextlist
     */
    public static function delete_data_for_user(\core_privacy\local\request\approved_contextlist $contextlist) {
        // This plugin does not store personal data linked to users.
    }
}
