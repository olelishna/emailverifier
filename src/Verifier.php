<?php

declare(strict_types=1);

namespace Olelishna\EmailVerifier;

use Olelishna\EmailVerifier\Data\{DomainsExample, DomainsDisposable, DomainsTypo};

class Verifier
{
    protected static array $defaultOptions = [
        'validFormat' => true,
        'hostExists' => true,
        'mxExists' => true,
        'example' => true,
        'disposable' => true,
        'possMistyped' => true,
    ];

    /**
     * @param string[] $emails
     */
    public function __construct(public array $emails = [], public array $options = [])
    {
        $this->options = array_merge(self::$defaultOptions, $this->options);
    }

    public function check(): array
    {
        return array_map([$this, 'isValid'], $this->emails);
    }

    private function isValid(string $address): array
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

        if ($this->options['validFormat']) {
            $email_data['validFormat'] = self::isValidFormat($address);
        }

        if ($this->options['hostExists']) {
            $email_data['hostExists'] = self::hostExists($email_data['domain']);
        }

        if ($this->options['mxExists']) {
            $email_data['mxExists'] = self::mxExists($email_data['domain']);
        }

        if ($this->options['example']) {
            $email_data['example'] = self::isExampleDomain($email_data['domain']);
        }

        if ($this->options['disposable']) {
            $email_data['disposable'] = self::isTemporaryDomain($email_data['domain']);
        }

        if ($this->options['possMistyped']) {
            $email_data['possMistyped'] = self::isPossibleTypoInDomain($email_data['domain']);
        }

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
