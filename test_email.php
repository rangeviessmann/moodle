<?php
// Test email sending with different From/Reply-To addresses.
// Usage: /test_email.php (browser, logged in as admin)
//
// Enter 3 email addresses and send test messages with different sender configs.

require_once(__DIR__ . '/config.php');
require_login();
require_capability('moodle/site:config', context_system::instance());

$PAGE->set_url('/test_email.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Test Email');
$PAGE->set_pagelayout('admin');

$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && confirm_sesskey()) {
    $email1 = trim($_POST['email1'] ?? '');
    $email2 = trim($_POST['email2'] ?? '');
    $email3 = trim($_POST['email3'] ?? '');

    $emails = array_filter([$email1, $email2, $email3], function($e) {
        return !empty($e) && validate_email($e);
    });

    if (empty($emails)) {
        $results[] = ['error', 'Nie podano poprawnych adresów email.'];
    } else {
        foreach ($emails as $idx => $email) {
            $testnum = $idx + 1;

            // Create a fake user object for the recipient.
            $recipient = new stdClass();
            $recipient->id = -1;
            $recipient->email = $email;
            $recipient->firstname = 'Test';
            $recipient->lastname = "Odbiorca {$testnum}";
            $recipient->maildisplay = 1;
            $recipient->mailformat = 1;
            $recipient->auth = 'manual';
            $recipient->suspended = 0;
            $recipient->deleted = 0;
            $recipient->emailstop = 0;
            $recipient->username = 'testrecipient' . $testnum;

            switch ($testnum) {
                case 1:
                    // Test 1: Default noreply sender.
                    $from = core_user::get_noreply_user();
                    $replyto = '';
                    $replytoname = '';
                    $desc = "Nadawca: noreply ({$from->email}), brak reply-to";
                    break;

                case 2:
                    // Test 2: Custom sender object.
                    $from = new stdClass();
                    $from->id = -99;
                    $from->email = $CFG->noreplyaddress;
                    $from->firstname = 'Sekretariat';
                    $from->lastname = 'Testowy';
                    $from->maildisplay = 1;
                    $replyto = '';
                    $replytoname = '';
                    $desc = "Nadawca: Sekretariat Testowy ({$from->email}), brak reply-to";
                    break;

                case 3:
                    // Test 3: Noreply sender + custom reply-to.
                    $from = core_user::get_noreply_user();
                    $replyto = $email; // reply-to = sam odbiorca (dla testu)
                    $replytoname = 'Reply Test';
                    $desc = "Nadawca: noreply ({$from->email}), reply-to: {$replyto}";
                    break;
            }

            $subject = "Test email #{$testnum} - " . date('Y-m-d H:i:s');
            $messagetext = "To jest testowa wiadomosc #{$testnum}.\n\nKonfiguracja: {$desc}\n\nCzas: " . date('Y-m-d H:i:s');
            $messagehtml = "<h3>Test email #{$testnum}</h3><p>To jest testowa wiadomość.</p><p><strong>Konfiguracja:</strong> {$desc}</p><p>Czas: " . date('Y-m-d H:i:s') . "</p>";

            try {
                $result = email_to_user(
                    $recipient,
                    $from,
                    $subject,
                    $messagetext,
                    $messagehtml,
                    '', // attachment
                    '', // attachname
                    true, // usetrueaddress
                    $replyto,
                    $replytoname
                );

                if ($result) {
                    $results[] = ['success', "Email #{$testnum} wysłany do {$email} — {$desc}"];
                } else {
                    $results[] = ['error', "Email #{$testnum} do {$email} — BŁĄD wysyłki — {$desc}"];
                }
            } catch (Exception $e) {
                $results[] = ['error', "Email #{$testnum} do {$email} — WYJĄTEK: " . $e->getMessage()];
            }
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading('Test wysyłki email');

// Show results.
if (!empty($results)) {
    foreach ($results as [$type, $msg]) {
        if ($type === 'success') {
            echo $OUTPUT->notification($msg, 'success');
        } else {
            echo $OUTPUT->notification($msg, 'error');
        }
    }
}

// Show config info.
echo html_writer::tag('p', '<strong>SMTP Host:</strong> ' . ($CFG->smtphosts ?? '(nie ustawiony — mail())'));
echo html_writer::tag('p', '<strong>Noreply:</strong> ' . ($CFG->noreplyaddress ?? '(brak)'));

// Form.
echo html_writer::start_tag('form', ['method' => 'post', 'action' => 'test_email.php', 'class' => 'mt-3']);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);

for ($i = 1; $i <= 3; $i++) {
    $val = $_POST["email{$i}"] ?? '';
    echo html_writer::start_div('form-group mb-3');
    echo html_writer::label("Email #{$i}:", "email{$i}");
    echo html_writer::empty_tag('input', [
        'type' => 'email',
        'name' => "email{$i}",
        'id' => "email{$i}",
        'value' => s($val),
        'class' => 'form-control',
        'placeholder' => "adres{$i}@example.com",
    ]);

    $descriptions = [
        1 => 'Nadawca: noreply (domyślny)',
        2 => 'Nadawca: "Sekretariat Testowy" (custom from)',
        3 => 'Nadawca: noreply + custom reply-to',
    ];
    echo html_writer::tag('small', $descriptions[$i], ['class' => 'form-text text-muted']);
    echo html_writer::end_div();
}

echo html_writer::tag('button', 'Wyślij 3 testowe emaile', [
    'type' => 'submit',
    'class' => 'btn btn-primary',
]);
echo html_writer::end_tag('form');

echo $OUTPUT->footer();
