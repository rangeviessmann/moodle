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
 * Polish strings for local_recruitment.
 *
 * @package    local_recruitment
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Zarządzanie rekrutacjami';
$string['recruitments'] = 'Rekrutacje';
$string['addrecruitment'] = 'Dodaj rekrutację';
$string['editrecruitment'] = 'Edytuj rekrutację';
$string['deleterecruitment'] = 'Usuń rekrutację';
$string['recruitmentname'] = 'Nazwa';
$string['recruitmentdate'] = 'Data zakończenia rekrutacji';
$string['basecategory'] = 'Kategoria bazowa';
$string['archivecourse'] = 'Kurs archiwum';
$string['preparationcourse'] = 'Kurs przygotowania';
$string['quizescourse'] = 'Kurs testów wewnętrznych';
$string['cohort'] = 'Kohorta';
$string['choose'] = '-- Wybierz --';
$string['confirmdelete'] = 'Czy na pewno chcesz usunąć rekrutację "{$a}"? Wszystkie kursy (kierunki) i ich kohorty zostaną usunięte.';
$string['recruitmentsaved'] = 'Rekrutacja została zapisana.';
$string['recruitmentdeleted'] = 'Rekrutacja została usunięta.';
$string['norecruitments'] = 'Nie znaleziono rekrutacji.';
$string['cohortfor'] = 'Kohorta dla rekrutacji: {$a}';
$string['cohortfor_direction'] = 'Kohorta dla: {$a->recruitment} - {$a->direction}';
$string['privacy:metadata'] = 'Wtyczka rekrutacji nie przechowuje danych osobowych bezpośrednio. Przypisania użytkowników są zarządzane przez członkostwo w kohortach.';
$string['eventrecruitmentcreated'] = 'Rekrutacja utworzona';
$string['eventrecruitmentupdated'] = 'Rekrutacja zaktualizowana';
$string['eventrecruitmentdeleted'] = 'Rekrutacja usunięta';
$string['assignedcourses'] = 'Przypisane kursy';
$string['enter'] = 'Wejdź';
$string['directions'] = 'Kursy (kierunki)';
$string['directionname'] = 'Nazwa kursu (kierunku)';
$string['adddirection'] = 'Dodaj kurs';
$string['editdirection'] = 'Edytuj kurs (kierunek)';
$string['deletedirection'] = 'Usuń kurs (kierunek)';
$string['confirmdeletedirection'] = 'Czy na pewno chcesz usunąć kurs (kierunek) "{$a}" i jego kohortę?';
$string['directionsaved'] = 'Kurs (kierunek) został zapisany.';
$string['directiondeleted'] = 'Kurs (kierunek) został usunięty.';
$string['backtorecruitments'] = 'Powrót do rekrutacji';
$string['copystatus'] = 'Status kopiowania';
$string['copyinprogress'] = 'Kopiowanie w toku...';
$string['copydone'] = 'Gotowe';
$string['users'] = 'Użytkownicy';
$string['importusers'] = 'Importuj użytkowników';
$string['downloadcsvsample'] = 'Pobierz przykładowy CSV';
$string['declaration'] = 'Deklaracja';
$string['declarationyes'] = 'Tak';
$string['declarationno'] = 'Nie';
$string['toggledeclaration'] = 'Przełącz deklarację';
$string['removeuser'] = 'Usuń użytkownika z rekrutacji';
$string['userremoved'] = 'Użytkownik został usunięty z tego kierunku.';
$string['confirmremoveuser'] = 'Czy na pewno chcesz usunąć użytkownika "{$a}" z tego kierunku?';
$string['userimported'] = 'Użytkownicy zaimportowani. Utworzono: {$a->created}, Zaktualizowano: {$a->updated}, Błędy: {$a->errors}, Powiadomiono: {$a->notified}.';
$string['importerror'] = 'Błąd w wierszu {$a->line}: {$a->message}';
$string['backtousers'] = 'Powrót do użytkowników';
$string['csvfilesemicolon'] = 'Plik CSV (wartości rozdzielone średnikiem)';
$string['csvfileonly'] = 'Dozwolone są tylko pliki z rozszerzeniem .csv';
$string['csvformat'] = 'Format CSV: username;firstname;lastname;email;phone;declaration (tak/nie)';
$string['exportusers'] = 'Eksportuj użytkowników';
$string['setdeclaration'] = 'Zaznacz deklarację';
$string['confirmsetdeclaration'] = 'Czy na pewno chcesz oznaczyć deklarację dla użytkownika "{$a}"? Ta akcja jest nieodwracalna i spowoduje wysłanie powiadomienia.';
$string['declarationset'] = 'Deklaracja została oznaczona i powiadomienie wysłane.';
$string['filter'] = 'Filtruj';
$string['resetfilters'] = 'Resetuj';
$string['deleteuser'] = 'Usuń użytkownika z platformy';
$string['userdeleted'] = 'Użytkownik został trwale usunięty z platformy.';
$string['confirmuserdelete'] = 'Czy na pewno chcesz trwale usunąć tego użytkownika z platformy? Tej operacji nie można cofnąć. Użytkownik zostanie usunięty ze wszystkich kursów i rekrutacji.';
$string['notificationstatus'] = 'Powiadomienie';
$string['notifiedyes'] = 'Wysłano';
$string['notifiedno'] = 'Nie wysłano';
$string['examregistrationsubject'] = 'Zapis na egzamin jest już dostępny';
$string['examregistrationbody'] = 'Dzień dobry,<br> zapis na egzamin dla kursu {$a->direction} (rekrutacja: {$a->recruitment}) jest już dostępny.<br><br> Zaloguj się do platformy: {$a->loginurl}';
$string['examregistrationsms'] = 'Zapis na egzamin dla {$a->direction} ({$a->recruitment}) jest juz dostepny. Zaloguj sie do platformy.';
$string['messageprovider:exam_registration'] = 'Powiadomienie o zapisie na egzamin';
$string['newaccountsubject'] = 'Twoje konto na platformie zostało utworzone';
$string['newaccountbody'] = 'Dzień dobry {$a->firstname} {$a->lastname},<br>
<br>
Twoje konto na platformie zostało utworzone.<br>
<br>
Login (PESEL): {$a->username}<br>
Hasło: {$a->password}<br>
<br>
Zaloguj się do platformy: {$a->loginurl}<br>
<br>
Zalecamy zmianę hasła po pierwszym logowaniu.';
$string['archives_overview'] = 'Archiwum wykładów';
$string['preparation_overview'] = 'Przygotowanie do egzaminu';
$string['internaltests_overview'] = 'Zaliczenia';
$string['basecourse'] = 'Kurs bazowy';
$string['basecourse_label'] = 'Kurs bazowy';
$string['basecourses'] = 'Kursy bazowe';
$string['directioncourses'] = 'Kursy kierunkowe';
$string['progressreport'] = 'Wyniki';
$string['progressreport_btn'] = 'Przejdź';
$string['kategoriezowe_group'] = 'Kategorie bazowe';
$string['kategorieutwortzone_group'] = 'Utworzone kierunki';
$string['theme'] = 'Motyw';
$string['theme_red'] = 'SPV (czerwony)';
$string['theme_green'] = 'Ogrodnik (zielony)';
$string['task_resend_account_emails'] = 'Ponowne wysyłanie maili z danymi logowania';
$string['task_send_quiz_notifications'] = 'Wysyłanie powiadomień o zaliczeniach';
$string['send_notifications'] = 'Wyślij powiadomienia przypominające';
$string['send_notifications_help'] = 'Jeśli zaznaczono, użytkownicy otrzymają powiadomienia e-mail na 7 dni przed otwarciem zaliczenia, w chwili otwarcia oraz na 24 godziny przed zamknięciem.';
$string['notification_subject_7days'] = 'Zaliczenie "{$a->quizname}" otwiera się za 7 dni';
$string['notification_subject_open'] = 'Zaliczenie "{$a->quizname}" jest już dostępne';
$string['notification_subject_closing'] = 'Zaliczenie "{$a->quizname}" zamyka się za 24 godziny';
$string['notification_body_7days'] = 'Dzień dobry, zaliczenie "{$a->quizname}" w kursie {$a->direction} (rekrutacja: {$a->recruitment}) otworzy się dnia {$a->date}. Zaloguj się do platformy: {$a->loginurl}';
$string['notification_body_open'] = 'Dzień dobry, zaliczenie "{$a->quizname}" w kursie {$a->direction} (rekrutacja: {$a->recruitment}) jest już dostępne. Zaloguj się do platformy: {$a->loginurl}';
$string['notification_body_closing'] = 'Dzień dobry, zaliczenie "{$a->quizname}" w kursie {$a->direction} (rekrutacja: {$a->recruitment}) zamyka się za 24 godziny. Zaloguj się do platformy: {$a->loginurl}';
$string['notification_sms_7days'] = 'Zaliczenie "{$a->quizname}" otwiera sie za 7 dni. Zaloguj sie do platformy.';
$string['notification_sms_open'] = 'Zaliczenie "{$a->quizname}" jest juz dostepne. Zaloguj sie do platformy.';
$string['notification_sms_closing'] = 'Zaliczenie "{$a->quizname}" zamyka sie za 24 godziny. Zaloguj sie do platformy.';
$string['messageprovider:quiz_notification'] = 'Powiadomienie o zaliczeniu';
