<?php

declare(strict_types=1);

namespace Olelishna\EmailVerifier;

use Olelishna\EmailVerifier\Data\{DomainsExample, DomainsDisposable, DomainsTypo};

class Verifier
{
    public static function check(array $arrayOfEmails = []): array
    {
        return array_map('self::isValid', $arrayOfEmails);
    }

    private static function isValid(string $address): array
    {
        $email_data = [
            'address' => $address,
            'username' => '',
            'domain' => '',
        ];

        $parts = self::getMailParts($address);

        if ($parts !== false) {
            $email_data['username'] = $parts['username'];
            $email_data['domain'] = $parts['domain'];
        }

        $email_data['validFormat'] = self::isValidFormat($address);
        $email_data['hostExists'] = self::hostExists($email_data['domain']);
        $email_data['mxExists'] = self::mxExists($email_data['domain']);
        $email_data['example'] = self::isExampleDomain($email_data['domain']);
        $email_data['disposable'] = self::isTemporaryDomain($email_data['domain']);
        $email_data['possMistyped'] = self::isPossibleTypoInDomain($email_data['domain']);

        return $email_data;
    }

    private static function getMailParts(string $address = ''): bool|array
    {
        if ($address === '') {
            return false;
        }

        $address = str_replace(['.', '＠'], ['.', '@',], $address);

        if (!str_contains($address, '@')) {
            return false;
        }

        if (!preg_match('/^(?<local>.+)@(?<domain>.+)$/', $address, $parts)) {
            return false;
        }

        return [
            'username' => $parts['local'] ?? '',
            'domain' => $parts['domain'] ?? '',
        ];
    }

    private static function isValidFormat(string $address): bool
    {
        // In general, this validates e-mail addresses against the addr-specsyntax in » RFC 822, with the exceptions
        // that comments and whitespace folding and dotless domain names are not supported.
        if (filter_var($address, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

    private static function mxExists(string $domain = ''): bool
    {
        if ($domain === '') {
            return false;
        }

        return checkdnsrr($domain.'.');
    }

    private static function hostExists(string $domain = ''): bool
    {
        if ($domain === '') {
            return false;
        }

        return checkdnsrr($domain.'.', 'A');
    }

    private static function isExampleDomain(string $domain = ''): bool
    {
        if ($domain === '') {
            return false;
        }

        if (in_array($domain, DomainsExample::DOMAINS, true)) {
            return true;
        }

        return false;
    }

    private static function isTemporaryDomain(string $domain = ''): bool
    {
        if ($domain === '') {
            return false;
        }

        if (in_array($domain, DomainsDisposable::DOMAINS, true)) {
            return true;
        }

        return false;
    }

    private static function isPossibleTypoInDomain(string $domain): bool
    {
        if ($domain === '') {
            return false;
        }

        if (in_array($domain, DomainsTypo::DOMAINS, true)) {
            return true;
        }

        return false;
    }

}
