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

require_once('../../config.php');
require_once($CFG->dirroot . '/local/greetings/lib.php');
require_once($CFG->dirroot . '/local/greetings/form/form_state.php'); // Formulário para estados e cidades
require_once($CFG->dirroot . '/local/greetings/form/form_csv.php');   // Formulário para upload CSV
require_once($CFG->dirroot . '/enrol/manual/locallib.php'); // Biblioteca para enrolment manual.

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
    // Formulário cancelado, redirecionar.
    redirect(new moodle_url('/local/greetings/index.php'));
} else if ($data = $mform_csv->get_data()) {
    // Verifica se um arquivo foi enviado.
    $file = $mform_csv->get_file_content('userfile');

    if ($file) {
        $lines = explode(PHP_EOL, $file);
        $errors = array();

        foreach ($lines as $line) {
            $data = str_getcsv($line);
            if (count($data) < 5) continue; // Pulando linhas incompletas.

            list($username, $courseid, $role, $group, $enrolstatus) = $data;

            // Verifica se o curso existe (tenta por idnumber primeiro)
            $course = $DB->get_record('course', array('idnumber' => $courseid));
            // Se não encontrou, tenta pelo shortname
            if (!$course) {
                $course = $DB->get_record('course', array('shortname' => $courseid));
            }

            if (!$course) {
                $errors[] = "Curso não encontrado: {$courseid}";
                continue;
            }

            // Verifica se o aluno já existe.
            $user = $DB->get_record('user', array('username' => $username));
            if (!$user) {
                // Se o usuário não existir, criar um novo usuário.
                $user = new stdClass();
                $user->username = $username;
                $user->password = hash_internal_user_password('Senha#2024'); // Senha padrão.
                $user->firstname = ''; // Defina o primeiro nome se disponível.
                $user->lastname = ''; // Defina o sobrenome se disponível.
                $user->email = ''; // Defina o email se disponível.
                $user->auth = 'manual'; // Método de autenticação.
                $user->confirmed = 1;
                $user->mnethostid = $CFG->mnet_localhost_id;

                // Cria o usuário no Moodle.
                $user->id = user_create_user($user);
            }

            // Verifica se o grupo existe.
            $group_record = $DB->get_record('groups', array('name' => $group, 'courseid' => $course->id));
            if (!$group_record) {
                $errors[] = "Grupo não encontrado: {$group}";
                continue;
            }

            // Matricular o aluno no curso.
            $enrol = enrol_get_plugin('manual');
            $instances = enrol_get_instances($course->id, true);
            $manualinstance = null;

            foreach ($instances as $instance) {
                if ($instance->enrol === 'manual') {
                    $manualinstance = $instance;
                    break;
                }
            }

            if ($manualinstance) {
                // Matricula o usuário.
                $enrol->enrol_user($manualinstance, $user->id, 5); // 5 é o ID padrão para a role 'student'.
                // Adiciona o aluno ao grupo
                groups_add_member($group_record->id, $user->id);
            } else {
                $errors[] = "Não foi possível encontrar uma instância de matrícula manual para o curso {$courseid}";
            }
        }

        // Exibir relatório de erros se houver.
        if (!empty($errors)) {
            echo "<h3>Erros encontrados</h3><ul>";
            foreach ($errors as $error) {
                echo "<li>{$error}</li>";
            }
            echo "</ul>";
        } else {
            echo "<h3>Todos os alunos foram matriculados com sucesso!</h3>";
        }
    }
}

$mform_csv->display(); // Exibe o formulário de upload de CSV

echo $OUTPUT->footer();
