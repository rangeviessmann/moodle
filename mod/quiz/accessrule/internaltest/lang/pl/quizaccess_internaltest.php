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
 * Polish strings for quizaccess_internaltest.
 *
 * @package    quizaccess_internaltest
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Zaliczenia';
$string['internaltest'] = 'Zaliczenie wewnętrzne';
$string['internaltest_help'] = 'Po włączeniu odpowiedzi z zaliczenia są zapisywane jako JSON po złożeniu podejścia. Powiadomienia e-mail są wysyłane do zapisanych użytkowników przed otwarciem i zamknięciem zaliczenia.';
$string['privacy:metadata:quizaccess_inttest_results'] = 'Przechowuje dane JSON z odpowiedziami użytkownika na zaliczenia wewnętrzne.';
$string['privacy:metadata:quizaccess_inttest_results:userid'] = 'ID użytkownika, który złożył podejście.';
$string['privacy:metadata:quizaccess_inttest_results:jsondata'] = 'Dane JSON zawierające pytania i odpowiedzi użytkownika.';
$string['privacy:metadata:quizaccess_inttest_results:timecreated'] = 'Czas zapisania wyniku.';
$string['messageprovider:internaltest_reminder'] = 'Przypomnienia o zaliczeniach';
$string['notification_subject_7days'] = 'Testy wewnętrzne będą dostępne za 7 dni';
$string['notification_subject_open'] = 'Testy wewnętrzne są już dostępne';
$string['notification_subject_closing'] = 'Testy wewnętrzne będą dostępne jeszcze przez 24 godziny';
$string['notification_body_7days'] = 'Informujemy, że testy wewnętrzne na zaliczenie Kwalifikacyjnego Kursu Zawodowego będą dostępne za 7 dni w panelu słuchacza. Zaloguj się: {$a->loginurl}
Testy są obowiązkowe dla wszystkich słuchaczy. Należy podejść do nich przed {$a->closedate} oraz uzyskać minimum {$a->gradepass}, aby zaliczyć kurs.';
$string['notification_body_open'] = 'Informujemy, że testy wewnętrzne na zaliczenie Kwalifikacyjnego Kursu Zawodowego są już dostępne w panelu słuchacza. Zaloguj się: {$a->loginurl}
Testy są obowiązkowe dla wszystkich słuchaczy. Należy podejść do nich przed {$a->closedate} oraz uzyskać minimum {$a->gradepass}, aby zaliczyć kurs.';
$string['notification_body_closing'] = 'Informujemy, że testy wewnętrzne na zaliczenie Kwalifikacyjnego Kursu Zawodowego będą dostępne w panelu słuchacza jeszcze przez 24h. Zaloguj się: {$a->loginurl}
Testy są obowiązkowe dla wszystkich słuchaczy. Należy podejść do nich przed {$a->closedate} oraz uzyskać minimum {$a->gradepass}, aby zaliczyć kurs.';
$string['taskname'] = 'Wysyłanie powiadomień o zaliczeniach';
$string['sms_7days'] = 'Testy wewnętrzne na zaliczenie Kwalifikacyjnego Kursu Zawodowego beda dostepne za 7 dni. Zaloguj sie: {$a->loginurl}';
$string['sms_open'] = 'Testy wewnętrzne na zaliczenie Kwalifikacyjnego Kursu Zawodowego sa juz dostepne w panelu sluchacza. Zaloguj sie: {$a->loginurl} Zdaj testy przed {$a->closedate}';
$string['sms_open_noclose'] = 'Testy wewnętrzne na zaliczenie Kwalifikacyjnego Kursu Zawodowego sa juz dostepne w panelu sluchacza. Zaloguj sie: {$a->loginurl}';
$string['sms_closing'] = 'Testy wewnętrzne na zaliczenie Kwalifikacyjnego Kursu Zawodowego beda dostepne jeszcze przez 24h. Zaloguj sie: {$a->loginurl}';
