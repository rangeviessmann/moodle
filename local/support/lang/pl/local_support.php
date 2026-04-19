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
 * Polish strings for local_support.
 *
 * @package    local_support
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Wsparcie plikow niestandardowych';
$string['fallbackemail'] = 'Zapasowy e-mail do resetowania hasła';
$string['fallbackemail_desc'] = 'Jeśli użytkownik poda adres e-mail, który nie istnieje w systemie podczas resetowania hasła, wiadomość zostanie wysłana na ten adres. Pozostaw puste, aby wyłączyć.';
$string['peselnotfound'] = 'Użytkownik o takim numerze PESEL nie istnieje.';
$string['fallbackemailsubject'] = 'Proba resetowania hasla dla nieznanego adresu email: {$a}';
$string['fallbackemailbody'] = 'Ktos probowal zresetowac haslo podajac adres email: {$a}, ale ten adres nie istnieje w systemie.';
$string['fallbackemailbody_html'] = '<p>Ktos probowal zresetowac haslo podajac adres email: <strong>{$a}</strong>, ale ten adres nie istnieje w systemie.</p>';
$string['emailpasswordconfirmmaybesent'] = 'Jesli istnieje konto powiazane z tymi danymi, wyslano wiadomosc z instrukcjami.';
$string['blockedurls'] = 'Zablokowane adresy URL dla zwyklych uzytkownikow';
$string['blockedurls_desc'] = 'Wpisz jedna sciezke URL na linie. Uzytkownicy nie bedacy administratorami odwiedzajacy te strony zostana przekierowani na /my/. Uzyj czesciowych sciezek, np. /grade/report/overview/index.php';
$string['sms_heading'] = 'Ustawienia SMS (SerwerSMS.pl)';
$string['sms_heading_desc'] = 'Konfiguracja serwisu wysylki SMS przez API SerwerSMS.pl.';
$string['sms_api_token'] = 'Token API';
$string['sms_api_token_desc'] = 'Token Bearer API z SerwerSMS.pl (Panel Klienta > Ustawienia interfejsow > HTTPS API > Tokeny API).';
$string['sms_sender'] = 'Nazwa nadawcy SMS';
$string['sms_sender_desc'] = 'Nazwa nadawcy wyswietlana w SMS (musi byc zarejestrowana w SerwerSMS.pl).';
$string['event_sms_sent'] = 'Wyslano SMS';
$string['wp_sync_heading'] = 'Synchronizacja z WordPressem';
$string['wp_sync_heading_desc'] = 'Ustawienia wysylki danych uzytkownika do WordPressa przy zmianie flagi deklaracji.';
$string['wp_sync_endpoint'] = 'Adres URL endpointu WordPress';
$string['wp_sync_endpoint_desc'] = 'Pelny adres URL endpointu WordPress REST API, ktory odbiera dane uzytkownika (np. https://example.com/wp-json/moodle-sync/v1/create-user).';
$string['wp_sync_token'] = 'Tajny token HMAC WordPress';
$string['wp_sync_token_desc'] = 'Tajny token uzywany do podpisywania zadania algorytmem HMAC-SHA256. Musi byc taki sam jak token skonfigurowany po stronie WordPressa.';
$string['event_wp_sync_sent'] = 'Wyslano dane uzytkownika do WordPressa';
$string['internaltest_notdone'] = 'Niezrobiony';
$string['internaltest_passed'] = 'Zaliczony';
$string['internaltest_failed'] = 'Niezaliczony';
$string['internaltest_active'] = 'Aktywny';
$string['internaltest_inactive'] = 'Nieaktywny';
$string['red_brand_heading'] = 'Motyw czerwony – identyfikacja wizualna';
$string['red_brand_heading_desc'] = 'Wgraj własne logo i faviconę, które będą wyświetlane użytkownikom, których aktywny kierunek używa motywu czerwonego. Pozostaw puste, aby używać domyślnych ustawień platformy.';
$string['red_logo'] = 'Logo (motyw czerwony)';
$string['red_logo_desc'] = 'Logo wyświetlane na pasku nawigacji dla użytkowników motywu czerwonego. Zalecane: PNG lub SVG, przezroczyste tło.';
$string['red_favicon'] = 'Favicona (motyw czerwony)';
$string['red_favicon_desc'] = 'Ikona karty przeglądarki dla użytkowników motywu czerwonego. Akceptowane formaty: .ico, .png, .svg, .jpg, .jpeg.';
$string['gardener_brand_heading'] = 'Ogrodnik (motyw zielony) – identyfikacja wizualna';
$string['gardener_brand_heading_desc'] = 'Wgraj własne logo i faviconę, które będą wyświetlane użytkownikom, których aktywny kierunek używa motywu Ogrodnik (zielony). Pozostaw puste, aby używać domyślnych ustawień platformy.';
$string['green_logo'] = 'Logo (motyw zielony)';
$string['green_logo_desc'] = 'Logo wyświetlane na pasku nawigacji dla użytkowników motywu Ogrodnik. Zalecane: PNG lub SVG, przezroczyste tło.';
$string['green_favicon'] = 'Favicona (motyw zielony)';
$string['green_favicon_desc'] = 'Ikona karty przeglądarki dla użytkowników motywu Ogrodnik. Akceptowane formaty: .ico, .png, .svg, .jpg, .jpeg.';
$string['gardener_smtp_heading'] = 'Ogrodnik (motyw zielony) – wychodząca poczta (SMTP)';
$string['gardener_smtp_heading_desc'] = 'Oddzielny serwer SMTP używany do wysyłki emaili do użytkowników, których aktywny kierunek ma motyw „Ogrodnik (zielony)". Pozostaw pole serwera SMTP puste, aby używać domyślnego serwera platformy.';
$string['gardener_smtphosts'] = 'Serwer SMTP';
$string['gardener_smtphosts_desc'] = 'Nazwa hosta lub IP serwera SMTP (np. smtp.example.com). Pozostaw puste, aby używać domyślnego serwera platformy.';
$string['gardener_smtpport'] = 'Port SMTP';
$string['gardener_smtpport_desc'] = 'Numer portu (zazwyczaj 25, 465 dla SSL, 587 dla TLS).';
$string['gardener_smtpsecure'] = 'Zabezpieczenie SMTP';
$string['gardener_smtpsecure_desc'] = 'Protokół szyfrowania połączenia SMTP.';
$string['gardener_smtpsecure_none'] = 'Brak';
$string['gardener_smtpuser'] = 'Nazwa użytkownika SMTP';
$string['gardener_smtpuser_desc'] = 'Nazwa użytkownika do uwierzytelniania SMTP. Pozostaw puste, jeśli uwierzytelnianie nie jest wymagane.';
$string['gardener_smtppass'] = 'Hasło SMTP';
$string['gardener_smtppass_desc'] = 'Hasło do uwierzytelniania SMTP.';
$string['gardener_smtp_fromemail'] = 'Adres nadawcy';
$string['gardener_smtp_fromemail_desc'] = 'Adres e-mail nadawcy używany przy wysyłce przez serwer SMTP Ogrodnika.';
$string['gardener_smtp_fromname'] = 'Nazwa nadawcy';
$string['gardener_smtp_fromname_desc'] = 'Nazwa nadawcy wyświetlana w polu Od dla emaili Ogrodnika.';
