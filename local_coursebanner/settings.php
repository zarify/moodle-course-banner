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

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_coursebanner', get_string('pluginname', 'local_coursebanner'));
    $settings->add(new \local_coursebanner\admin_setting_configtext_with_clear(
        'local_coursebanner/defaulturl',
        get_string('defaulturl', 'local_coursebanner'),
        get_string('defaulturl_desc', 'local_coursebanner'),
        '',
        PARAM_URL,
        80
    ));
    $settings->add(new admin_setting_configtext(
        'local_coursebanner/bannerheight',
        get_string('bannerheight', 'local_coursebanner'),
        get_string('bannerheight_desc', 'local_coursebanner'),
        '80',
        PARAM_INT
    ));
    $ADMIN->add('localplugins', $settings);

    $ADMIN->add('localplugins', new admin_externalpage(
        'local_coursebanner_manage',
        get_string('managebanners', 'local_coursebanner'),
        new moodle_url('/local/coursebanner/manage.php'),
        'local/coursebanner:manage'
    ));
}
