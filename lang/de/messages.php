<?php

return [
    // Standard Laravel Nachrichten
    'whoops' => 'Ups!',
    'hello' => 'Hallo!',
    'regards' => 'Beste Grüße',
    
    // E-Mail-Verifizierung
    'verify_email_subject' => 'E-Mail-Adresse bestätigen',
    'verify_email_greeting' => 'Hallo!',
    'verify_email_line_1' => 'Bitte klicken Sie auf den Button unten, um Ihre E-Mail-Adresse zu bestätigen.',
    'verify_email_action' => 'E-Mail-Adresse bestätigen',
    'verify_email_line_2' => 'Falls Sie kein Konto erstellt haben, sind keine weiteren Maßnahmen erforderlich.',
    
    // Passwort-Reset (zusätzlich zu passwords.php)
    'reset_password_notification' => [
        'subject' => 'Passwort zurücksetzen',
        'greeting' => 'Hallo!',
        'line_1' => 'Sie erhalten diese E-Mail, weil wir eine Passwort-Reset-Anfrage für Ihr Konto erhalten haben.',
        'action' => 'Passwort zurücksetzen',
        'line_2' => 'Dieser Link zum Zurücksetzen des Passworts läuft in :count Minuten ab.',
        'line_3' => 'Falls Sie kein Passwort-Reset angefordert haben, sind keine weiteren Maßnahmen erforderlich.',
    ],
    
    // E-Mail-Verifizierung Notification
    'verify_email_notification' => [
        'subject' => 'E-Mail-Adresse bestätigen',
        'greeting' => 'Hallo!',
        'line_1' => 'Bitte klicken Sie auf den Button unten, um Ihre E-Mail-Adresse zu bestätigen.',
        'action' => 'E-Mail-Adresse bestätigen',
        'line_2' => 'Falls Sie kein Konto erstellt haben, sind keine weiteren Maßnahmen erforderlich.',
    ],
    
    // Allgemeine Button-Texte
    'button_text' => [
        'reset_password' => 'Passwort zurücksetzen',
        'verify_email' => 'E-Mail-Adresse bestätigen',
        'login' => 'Anmelden',
        'register' => 'Registrieren',
    ],
    
    // Salutation
    'salutation' => 'Beste Grüße,<br>Das MystiDraw Team',
];
