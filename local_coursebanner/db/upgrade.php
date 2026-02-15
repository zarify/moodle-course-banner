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
 * Upgrade script for local_coursebanner.
 *
 * @package    local_coursebanner
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the local_coursebanner plugin.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_coursebanner_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026021100) {
        // Add foreign key constraints.
        
        $table = new xmldb_table('local_coursebanner_cat');
        $key = new xmldb_key('categoryid_fk', XMLDB_KEY_FOREIGN, ['categoryid'], 'course_categories', ['id']);
        if (!$dbman->find_key_name($table, $key)) {
            $dbman->add_key($table, $key);
        }

        $table = new xmldb_table('local_coursebanner_course');
        $key = new xmldb_key('courseid_fk', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);
        if (!$dbman->find_key_name($table, $key)) {
            $dbman->add_key($table, $key);
        }

        upgrade_plugin_savepoint(true, 2026021100, 'local', 'coursebanner');
    }

    return true;
}
