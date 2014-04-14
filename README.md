local_archivecourse_cleanup
===========================

Deletes courses after a user-configurable interval of time has elapsed since course creation.

##Motivation
This plugin is designed to serve as part of an _archive server_. An archive server gives access to Moodle courses that have been restored from 
semester backups. The archive server is never used for live courses, it exists purely to restore old courses for a short time.
In order to keep the archive server trim, this module removes courses whose `timecreated` value is older than that given in the 
setting described below.

##Settings
* Number of days
   * The number of days that a course may remain in the Moodle instance. This should be an integer less than 999.
* Cron
   * Whether or not to run with cron.

##Warning
At each cron run, this plugin DELETES any course that is older than _n_ days, where _n_ is the number given in the admin setting described above.

##Other Notes
At each cron run, this plugin DELETES courses using the moodlelib function delete_course. There is no going back.
You have been warned.
