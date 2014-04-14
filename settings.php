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
 * Settings defined.
 *
 * @package    local_archivecourse_cleanup
 * @copyright  2014 Jason Peak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$_s = function($k, $a = null){
    return get_string($k, 'local_archivecourse_cleanup', $a);
};

if ($hassiteconfig) {

    $settings = new admin_settingpage('local_archivecourse_cleanup', $_s('pluginname'));

    $settings->add(
            new admin_setting_configtext('local_archivecourse_cleanup/num_days', $_s('num_days'), $_s('num_days_desc'), 7, PARAM_INT, 3));
    $settings->add(
            new admin_setting_configcheckbox('local_archivecourse_cleanup/cron_enable', $_s('cron_enable'), $_s('cron_enable_desc'), 0));

    $ADMIN->add('localplugins', $settings);
}