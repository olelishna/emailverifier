# Email Verifier

Verify email addresses without sending an email 

# Requirements

* PHP 8.1

# Installation

* composer require olelishna/emailverifier

# Usage
    <?php
    
    $arrayOfEmails = ['test@example.com'];

    $resultArray = Olelishna\EmailVerifier\Verifier::check($arrayOfEmails);