<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/**
 * Index page for local_greetings plugin.
 *
 * @package     local_greetings
 * @copyright   2023 Your name <your@email>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/local/greetings/lib.php');

require_once($CFG->libdir.'/formslib.php');


class local_greetings_csv_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        // Campo de upload de arquivo CSV.
        $mform->addElement('filepicker', 'userfile', get_string('uploadcsv', 'local_greetings'), null, array('accepted_types' => '.csv'));
        $mform->addRule('userfile', null, 'required', null, 'client');
        
        // Botão de submissão.
        $mform->addElement('submit', 'submitbutton', get_string('submit'));
    }
}
