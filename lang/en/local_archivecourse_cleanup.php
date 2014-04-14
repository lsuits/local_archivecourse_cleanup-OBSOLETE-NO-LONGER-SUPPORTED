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
 * Lang strings.
 *
 * @package    local_archivecourse_cleanup
 * @copyright  2014 Jason Peak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname']       = 'Archive Course Cleanup Utility';
$string['frankenname']      = 'local_archivecourse_cleanup';
$string['settings']         = 'Cleanup Settings';
$string['num_days']         = "Days";
$string['num_days_desc']    = "Number of days to keep courses";
$string['unknown_err']      = 'UNKNOWN ERROR: Delete failed for courseid {$a}';
$string['donothing']        = 'Nothing to do!';
$string['deletesuccess']    = 'Successsfully deleted {$a} old courses';
$string['noconfirm']        = 'Failed to delete courseid {$a->id} - {$a->fullname}';
$string['catchall_err']     = 'Something went wrong.';
$string['cron_enable']      = 'Cron';
$string['cron_enable_desc'] = "Should this plugin run at cron and delete all courses older than the Number of Days specified above?";