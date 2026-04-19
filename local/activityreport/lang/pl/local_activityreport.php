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
 * Polish language strings for local_activityreport.
 *
 * @package    local_activityreport
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname']     = 'Raport aktywności';
$string['activityreport'] = 'Raport aktywności';
$string['activityname']   = 'Nazwa aktywności';
$string['description']    = 'Opis';
$string['email']          = 'Email';
$string['eventname']      = 'Nazwa zdarzenia';
$string['exportcsv']      = 'Eksportuj CSV';
$string['firstname']      = 'Imię';
$string['lastname']       = 'Nazwisko';
$string['nologsfound']    = 'Nie znaleziono wpisów w logach.';
$string['phone']          = 'Telefon';
$string['timecreated']    = 'Czas';

// Filtry.
$string['filter']             = 'Filtruj';
$string['filter_description'] = 'Opis';
$string['filter_datefrom']    = 'Data od';
$string['filter_dateto']      = 'Data do';
$string['filter_email']       = 'Email';
$string['filter_activityname'] = 'Nazwa aktywności';
$string['filter_eventname']     = 'Nazwa zdarzenia';
$string['filter_eventname_all'] = '— Wszystkie zdarzenia —';
$string['filter_firstname']   = 'Imię';
$string['filter_lastname']    = 'Nazwisko';
$string['filter_phone']       = 'Telefon';
$string['resetfilters']       = 'Resetuj';

// Prywatność.
$string['privacy:metadata'] = 'Wtyczka Raport aktywności nie przechowuje żadnych danych osobowych. Odczytuje jedynie istniejące dane z logów.';

// Opisy zdarzeń.
$string['eventdesc_user_loggedin']                    = 'Użytkownik {$a->user} zalogował się na platformę.';
$string['eventdesc_course_module_viewed']             = 'Użytkownik {$a->user} wyświetlił materiał „{$a->module}" w kursie „{$a->course}".';
$string['eventdesc_course_module_completion_updated'] = 'Użytkownik {$a->user} zaktualizował ukończenie modułu „{$a->module}" w kursie „{$a->course}".';
$string['eventdesc_course_completed']                 = 'Użytkownik {$a->user} ukończył kurs „{$a->course}".';
$string['eventdesc_attempt_started']                  = 'Użytkownik {$a->user} rozpoczął podejście do testu „{$a->module}" w kursie „{$a->course}".';
$string['eventdesc_attempt_submitted']                = 'Użytkownik {$a->user} wysłał podejście do testu „{$a->module}" w kursie „{$a->course}".';
$string['eventdesc_attempt_reviewed']                 = 'Użytkownik {$a->user} przeglądał wynik testu „{$a->module}" w kursie „{$a->course}".';
$string['eventdesc_attempt_viewed']                   = 'Użytkownik {$a->user} wyświetlił podejście do testu „{$a->module}" w kursie „{$a->course}".';
$string['eventdesc_youtube_played']                   = 'Użytkownik {$a->user} uruchomił film YouTube: {$a->videourl}';

// Nazwy zdarzeń.
$string['eventname_user_loggedin']                    = 'Logowanie użytkownika';
$string['eventname_course_module_viewed']             = 'Wyświetlenie modułu kursu';
$string['eventname_course_module_completion_updated'] = 'Aktualizacja ukończenia modułu';
$string['eventname_course_completed']                 = 'Ukończenie kursu';
$string['eventname_attempt_started']                  = 'Rozpoczęcie podejścia do testu';
$string['eventname_attempt_submitted']                = 'Wysłanie podejścia do testu';
$string['eventname_attempt_reviewed']                 = 'Przegląd wyniku testu';
$string['eventname_attempt_viewed']                   = 'Wyświetlenie podejścia do testu';
$string['eventname_youtube_played']                   = 'Odtworzenie filmu YouTube';
