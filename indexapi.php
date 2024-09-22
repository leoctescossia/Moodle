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
 * API helper for local_greetings plugin.
 *
 * @package     local_greetings
 * @copyright   2023 Your name <your@email>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Fetches states from IBGE API.
 *
 * @return array|null List of states or null if not found.
 */
function getStates() {
    $url = 'https://servicodados.ibge.gov.br/api/v1/localidades/estados';
    $response = @file_get_contents($url);
    
    if ($response === false) {
        error_log("Falha ao acessar a API do IBGE.");
        return null;
    }

    return json_decode($response, true);
}

/**
 * Fetches cities from IBGE API for a given state.
 *
 * @param string $stateCode The state code (UF) to fetch cities for.
 * @return array|null List of cities or null if not found.
 */
function getCities($stateCode) {
    $url = "https://servicodados.ibge.gov.br/api/v1/localidades/estados/{$stateCode}/municipios";
    $response = @file_get_contents($url);

    if ($response === false) {
        error_log("Falha ao acessar a API do IBGE.");
        return null;
    }

    return json_decode($response, true);
}
