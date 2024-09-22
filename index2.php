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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Index page for local_greetings plugin.
 *
 * @package     local_greetings
 * @copyright   2023 Your name <your@email>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/local/greetings/lib.php');
require_once($CFG->dirroot . '/local/greetings/form/form_state.php'); // Formulário para estados e cidades
require_once($CFG->dirroot . '/local/greetings/form/form_csv.php');   // Formulário para upload CSV

require_login();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/greetings/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_greetings'));
$PAGE->set_heading(get_string('pluginname', 'local_greetings'));

echo $OUTPUT->header();

// ==========================
// Formulário 1: Selecionar Estados e Exibir Cidades
// ==========================
$mform_state = new local_greetings_state_form(); // Formulário para estados e cidades

if ($mform_state->is_cancelled()) {
    // O formulário foi cancelado.
} else if ($data = $mform_state->get_data()) {
    // Processar seleção de estado e exibir cidades
    $selectedState = $data->state;
    $cities = getCities($selectedState);

    if ($cities) {
        echo '<h3>Lista de Cidades</h3>';
        echo '<ul>';
        foreach ($cities as $city) {
            echo '<li>' . $city['nome'] . '</li>';
        }
        echo '</ul>';
    }
}

$mform_state->display(); // Exibe o formulário de estados

// ==========================
// Formulário 2: Upload de Arquivo CSV
// ==========================
$mform_csv = new local_greetings_csv_form(); // Formulário para upload de CSV

if ($mform_csv->is_cancelled()) {
    // O formulário foi cancelado.
} 
else if ($data = $mform_csv->get_data()) {
    // Processar upload de arquivo CSV
    $file_content = $mform_csv->get_file_content('csvfile');
    
    // Lógica para processar o arquivo CSV...
    // Exemplo: Fazer o parsing do CSV e exibir seu conteúdo.
    echo '<h3>Arquivo CSV Enviado</h3>';
    echo '<pre>' . $file_content . '</pre>';
}

$mform_csv->display(); // Exibe o formulário de upload de CSV

echo $OUTPUT->footer();
