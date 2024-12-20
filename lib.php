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
 * Library of functions and constants for module labelcollapsed
 *
 * @package mod_labelcollapsed
 * @copyright  2011 Thomas Alsén
 * @copyright  2019 Lancaster University (http://www.lancaster.ac.uk/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Phil Devine <p.devine@lancaster.ac.uk>
 */

use mod_labelcollapsed\output\content_view;

defined('MOODLE_INTERNAL') || die;


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $labelcollapsed
 * @return bool|int
 */
function labelcollapsed_add_instance($labelcollapsed) {
    global $DB;

    $labelcollapsed->timemodified = time();

    return $DB->insert_record("labelcollapsed", $labelcollapsed);
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $labelcollapsed
 * @return bool
 */
function labelcollapsed_update_instance($labelcollapsed) {
    global $DB;

    $labelcollapsed->timemodified = time();
    $labelcollapsed->id = $labelcollapsed->instance;

    return $DB->update_record("labelcollapsed", $labelcollapsed);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id
 * @return bool
 */
function labelcollapsed_delete_instance($id) {
    global $DB;

    if (! $labelcollapsed = $DB->get_record("labelcollapsed", array("id" => $id))) {
        return false;
    }

    $result = true;

    if (! $DB->delete_records("labelcollapsed", array("id" => $labelcollapsed->id))) {
        $result = false;
    }

    return $result;
}


/**
 * Sets the corse module content to be displayed
 * on corse page.
 * @param cm_info $cm
 */
function labelcollapsed_cm_info_view(cm_info $cm): void {
    global $PAGE, $USER;

    if (!$USER->editing) { // Only render content if not editing.
        $output = $PAGE->get_renderer('mod_labelcollapsed');
        $renderable = new content_view($cm->instance, $cm->id);
        $cm->set_content($output->render_content_view($renderable));
    }
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function labelcollapsed_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * @uses FEATURE_IDNUMBER
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool|null True if module supports feature, false if not, null if doesn't know
 */
function labelcollapsed_supports($feature) {
    global $USER;

    switch($feature) {
        case FEATURE_IDNUMBER:
            return false;
        case FEATURE_GROUPS:
            return false;
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return false;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_NO_VIEW_LINK:
            return !($USER->editing || key_exists('wsfunction',$_REQUEST)); // ... validation is done in title.php
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_CONTENT;
        default:
            return null;
    }
}