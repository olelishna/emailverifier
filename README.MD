# Email Verifier

Verify email addresses without sending an email 

# Requirements

* PHP 8.1

# Installation

* composer require olelishna/emailverifier

# Usage
    <?php
    
    $arrayOfEmails = ['test@example.com'];

    // Default options
    $options = [
        'validFormat' => true,
        'hostExists' => true,
        'mxExists' => true,
        'example' => true,
        'disposable' => true,
        'possMistyped' => true,
    ];

    $verifier = new Olelishna\EmailVerifier\Verifier($arrayOfEmails, $options);

    $resultArray = $verifier->check();