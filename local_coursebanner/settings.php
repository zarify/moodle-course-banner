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

if (has_capability('local/coursebanner:manage', context_system::instance())) {
    $parent = 'localplugins';
    if (!$ADMIN->locate($parent)) {
        $parent = 'courses';
    }

    if ($ADMIN->locate($parent)) {
        $ADMIN->add($parent, new admin_externalpage(
        'local_coursebanner_manage',
        get_string('managebanners', 'local_coursebanner'),
        new moodle_url('/local/coursebanner/manage.php'),
        'local/coursebanner:manage'
        ));
    }
}
