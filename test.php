<?php

declare(strict_types=1);

require_once __DIR__."/vendor/autoload.php";

$arrayOfEmails = ['test@example.com', 'olelishna@gmail.com', 'olga@hello.com', 'olga@hello,com', 'olga@gmai.com'];

$resultArray = Olelishna\EmailVerifier\Verifier::check($arrayOfEmails);

var_export($resultArray);