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
 * Library functions / classes.
 *
 * @package    local_archivecourse_cleanup
 * @copyright  2014 Jason Peak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once $CFG->libdir.'/moodlelib.php';

/**
 * Delete courses created before a configured time value.
 *
 * The cleanup class, defined below, does the major lifting.
 * We fetch the configured time value, in days, and use it
 * to fetch courseids for deletion.
 */
function local_archivecourse_cleanup_cron(){

    $cleanup    = new cleanup();
    $threshold  = get_config('local_archivecourse_cleanup', 'num_days');
    $courseids  = $cleanup->get_old_courseids($threshold);
    $count      = count($courseids);
    $errors     = null;
    $confirm    = null;

    if($count > 0){
        $errors  = $cleanup->delete_courses($courseids);
        $confirm = $cleanup->confirm_delete($courseids);
    }

    mtrace($cleanup->print_status($count, $confirm, $errors));
}

class cleanup {

    /**
     * Get the courseids of courses considered old.
     *
     * @global type $DB
     * @param int $threshold number, in days, representing how far back to look
     * for courses considered 'old'
     * @return int[]
     */
    public function get_old_courseids($threshold){
        global $DB;
        $limit      = time() - 86400 * clean_param($threshold, PARAM_INT);
        $where      = sprintf("timecreated < %s AND id != %d", $limit, SITEID);
        $courses    = $DB->get_records_select('course',$where);
        return array_keys($courses);
    }

    /**
     * Use the moodlelib fn to delete courses one-by-one.
     *
     * @param int[] $courseids ids of courses to be deleted
     * @return int number of successful deletions
     */
    public function delete_courses($courseids) {
        $errors = array();
        foreach($courseids as $id){
            if(!delete_course($id, false)){
                $errors = ($id);
            }
        }
        return $errors;
    }

    /**
     * Confirm that none of the courseids exist in the DB.
     *
     * @global type $DB
     * @param int[] $courseids
     * @return true|stdClass[] true if nothing is found, the found rows, otherwise.
     */
    public function confirm_delete($courseids){
        global $DB;
        $rows = $DB->get_records_list('course', 'id', $courseids);
        return empty($rows) ? true : $rows;
    }

    /**
     * Given the result of confirm_delete and of delete_courses, print status.
     * @param type $confirm
     * @param type $errors
     * @return type
     */
    public function print_status($count, $confirm = null, $errors = null){
        $status = "";

        $_s = function($k, $a = null){
            return get_string($k, 'local_archivecourse_cleanup', $a);
        };

        if($count == 0){
            $status = $_s('donothing');

        }elseif($confirm === true && count($errors) == 0){
            $status .= $_s('deletesuccess', $count);

        }elseif(is_array($confirm)){
            foreach($confirm as $failid => $fail){
                $status .= $_s("noconfirm", $fail);
            }

        }elseif(!empty($errors)){
            // should never happen
            foreach($errors as $err){
                $status .= $_s("unknown_err", $err);
            }

        }else{
            // should really never happen
            $status = $_s('catchall_err');
        }

        return $status;
    }
}