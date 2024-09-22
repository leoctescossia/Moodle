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
require_once($CFG->dirroot . '/local/greetings/indexapi.php');
require_once($CFG->libdir.'/formslib.php');


class local_greetings_state_form extends moodleform {
    
    public function definition() {
        $mform = $this->_form;

        // Buscar os estados da API do IBGE.
        $states = getStates();

        // Campo dropdown para seleção de estado.
        $options = array();
        foreach ($states as $state) {
            $options[$state['sigla']] = $state['nome'];
        }

        // Adiciona o campo de seleção de estado.
        $mform->addElement('select', 'state', get_string('selectstate', 'local_greetings'), $options);
        $mform->addRule('state', null, 'required', null, 'client');

        // Botões de ação (submit e cancel).
        $this->add_action_buttons(true, get_string('submitstate', 'local_greetings'));
    }

    // Função de validação (se necessário).
    function validation($data, $files) {
        return array(); // Adicione regras de validação personalizadas, se necessário.
    }
}


