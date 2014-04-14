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
 * Unit tests covering lib.php.
 *
 * @package    local_archivecourse_cleanup
 * @copyright  2014 Jason Peak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once 'lib.php';
require_once $CFG->dirroot.'/course/externallib.php';

class cleanup_testcase extends advanced_testcase {

    /**
     * Set the config setting for threshold in days and store it in instance var.
     * Instantiate the test class.
     * Insert 2 courses into the db.
     */
    public function setup(){

        $this->resetAfterTest();
        set_config('num_days', 7, 'local_archivecourse_cleanup');

        $this->cleanup    = new cleanup();
        $this->threshold  = get_config('local_archivecourse_cleanup', 'num_days');
        $this->course1    = $this->getDataGenerator()->create_course();
        $this->course2    = $this->getDataGenerator()->create_course();
    }

    /**
     * Given the fixture setup, get_old_courses should return nothing
     * because the newly created courses will have 'timecreated' very close to time().
     *
     * Given 2 courses, predate one, and
     * expect that get_old_courses will now find that one.
     * @global type $DB
     */
    public function test_get_courses(){
        global $DB;

        // let's exclude the site default course
        $where = "id != ".SITEID;
        $this->assertEquals(2, count($DB->get_records_select('course', $where)));
        $this->assertEquals(0, count($this->cleanup->get_old_courseids($this->threshold)));

        $this->predate_course($this->course1);
        $this->assertEquals(1, count($this->cleanup->get_old_courseids($this->threshold)));
    }

    /**
     * Given 2 courses, predate one.
     * get_old_courses should find its i, which is then passed to delete_courses.
     *
     * Since delete_courses returns an array containing the ids of courses where deletion failed,
     * we expect it to be empty on success.
     */
    public function test_delete_courses(){
        $this->predate_course($this->course1);

        $courseids = $this->cleanup->get_old_courseids($this->threshold);
        $errors    = $this->cleanup->delete_courses($courseids);
        $this->assertEquals(0, count($errors));
    }

    /**
     * Given 2 courses, predate one.
     * Get the id(s) of the old courses and pass them to confirm_delete;
     * since we have not yet deleted the old courses, confirm delete should
     * NOT return true, rather, it should return an array of course DB rows.
     *
     * Now call the delete function and notice that confirm_delete returns true.
     */
    public function test_confirm_delete(){
        $this->predate_course($this->course1);

        $courseids = $this->cleanup->get_old_courseids($this->threshold);
        $this->assertNotEmpty($this->cleanup->confirm_delete($courseids));

        $this->cleanup->delete_courses($courseids);
        $this->assertTrue($this->cleanup->confirm_delete($courseids));
    }

    public function test_print_status(){
        $nothing_str= "Nothing to do!";
        $this->assertEquals($nothing_str, $this->cleanup->print_status(0, null, null));

        $this->predate_course($this->course1);

        $courseids  = $this->cleanup->get_old_courseids($this->threshold);
        $errors     = $this->cleanup->delete_courses($courseids);
        $count      = count($courseids);
        $this->assertTrue($this->cleanup->confirm_delete($courseids));

        $succes_str = sprintf("Successsfully deleted %d old courses", $count);
        $this->assertEquals($succes_str, $this->cleanup->print_status($count, true, $errors));

        $confirm    = array($this->course1->id => $this->course1);
        $fail_str   = sprintf("Failed to delete courseid %d - %s", $this->course1->id, $this->course1->fullname);
        $this->assertEquals($fail_str, $this->cleanup->print_status($count, $confirm, $errors));

        $failures = array(3);
        $ufail_str= sprintf("UNKNOWN ERROR: Delete failed for courseid %d", $failures[0]);
        $this->assertEquals($ufail_str, $this->cleanup->print_status($count, true, $failures));
    }

    /**
     * Given a course object, set its timecreated value to something earlier than
     * time() - 86400 * $threshold, where $threshold is the integer number of days
     * set in the plugin settings.
     * @global type $DB
     * @param stdClass $course
     */
    public function predate_course($course){
        global $DB;
        $course              = $DB->get_record('course', array('id'=>$course->id));
        $course->timecreated = time() - ($this->threshold * 86400 + 100);

        $DB->update_record('course', $course);
    }
}