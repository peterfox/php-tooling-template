#!/usr/bin/env php
<?php

function ask(string $question, string $default = ''): string
{
    $answer = readline($question.($default ? " ({$default})" : null).': ');

    if (! $answer) {
        return $default;
    }

    return $answer;
}

function askWithOptions(string $question, array $options, string $default = ''): string
{
    $suggestions = implode('/', array_map(
        fn (string $option) => $option === $default ? strtoupper($option) : $option,
        $options,
    ));

    $answer = ask("{$question} ({$suggestions})");

    $validOptions = implode(', ', $options);

    while (! in_array($answer, $options)) {
        if ($default && $answer === '') {
            $answer = $default;

            break;
        }

        writeln(PHP_EOL."Please pick one of the following options: {$validOptions}");

        $answer = ask("{$question} ({$suggestions})");
    }

    if (! $answer) {
        $answer = $default;
    }

    return $answer;
}

function confirm(string $question, bool $default = false): bool
{
    $answer = ask($question.' ('.($default ? 'Y/n' : 'y/N').')');

    if (! $answer) {
        return $default;
    }

    return strtolower($answer) === 'y';
}

function writeln(string $line): void
{
    echo $line.PHP_EOL;
}

function run(string $command): string
{
    return trim(shell_exec($command) ?? '');
}

function str_after(string $subject, string $search): string
{
    $pos = strrpos($subject, $search);

    if ($pos === false) {
        return $subject;
    }

    return substr($subject, $pos + strlen($search));
}

function slugify(string $subject): string
{
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $subject), '-'));
}

function title_case(string $subject): string
{
    return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $subject)));
}

function replace_in_file(string $file, array $replacements): void
{
    $contents = file_get_contents($file);

    file_put_contents(
        $file,
        str_replace(
            array_keys($replacements),
            array_values($replacements),
            $contents
        )
    );
}

function removeReadmeParagraphs(string $file): void
{
    $contents = file_get_contents($file);

    file_put_contents(
        $file,
        preg_replace('/<!--delete-->.*<!--\/delete-->/s', '', $contents) ?: $contents
    );
}

function determineSeparator(string $path): string
{
    return str_replace('/', DIRECTORY_SEPARATOR, $path);
}

function replaceForWindows(): array
{
    return preg_split('/\\r\\n|\\r|\\n/', run('dir /S /B * | findstr /v /i .git\ | findstr /v /i vendor | findstr /v /i '.basename(__FILE__).' | findstr /r /i /M /F:/ ":author :vendor :package VendorName skeleton vendor_name vendor_slug author@domain.com"'));
}

function replaceForAllOtherOSes(): array
{
    return explode(PHP_EOL, run('grep -E -r -l -i ":author|:vendor|:package|VendorName|skeleton|vendor_name|vendor_slug|author@domain.com" --exclude-dir=vendor ./* ./.github/* | grep -v '.basename(__FILE__)));
}

function setupTestingLibrary(): void
{
    replace_in_file(__DIR__.'/composer.json', [
        ':require_dev_testing' => '"phpunit/phpunit": "^10.3.2"',
        ':scripts_testing' => '"test": "vendor/bin/phpunit",
        "test-coverage": "vendor/bin/phpunit --coverage"',
        ':plugins_testing,' => '', // We need to remove the comma here as well, since there's nothing to add
    ]);
}

function setupCodeStyleLibrary(): void
{
    rename(
        from: __DIR__.'/.github/workflows/fix-php-code-style-issues.yml',
        to: __DIR__.'/.github/workflows/fix-php-code-style-issues.yml'
    );

    replace_in_file(__DIR__.'/composer.json', [
        ':require_dev_codestyle' => '"friendsofphp/php-cs-fixer": "^3.21.1"',
        ':scripts_codestyle' => '"format": "vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --allow-risky=yes"',
        ':plugins_testing' => '',
    ]);
}

function setupPhpStanLibrary(): void
{
    replace_in_file(__DIR__.'/composer.json', [
        ':require_dev_testing' => '"phpunit/phpunit": "^10.3.2"',
        ':scripts_testing' => '"test": "vendor/bin/phpunit",
        "test-coverage": "vendor/bin/phpunit --coverage"',
        ':plugins_testing,' => '', // We need to remove the comma here as well, since there's nothing to add
    ]);
}

$gitName = run('git config user.name');
$authorName = ask('Author name', $gitName);

$gitEmail = run('git config user.email');
$authorEmail = ask('Author email', $gitEmail);

$usernameGuess = explode(':', run('git config remote.origin.url'))[1] ?? '';
$usernameGuess = dirname($usernameGuess);
$usernameGuess = basename($usernameGuess);
$authorUsername = ask('Author username', $usernameGuess);

$vendorName = ask('Vendor name', $authorUsername);
$vendorSlug = slugify($vendorName);
$vendorNamespace = ucwords($vendorName);
$vendorNamespace = ask('Vendor namespace', $vendorNamespace);

$packageName = ask('Package name', 'tooling');
$packageSlug = slugify($packageName);

$className = title_case($packageName);
$className = ask('Package name', $className);

writeln('------');
writeln("Author     : {$authorName} ({$authorUsername}, {$authorEmail})");
writeln("Vendor     : {$vendorName} ({$vendorSlug})");
writeln("Package    : {$packageSlug}");
writeln("Namespace  : {$vendorNamespace}\\{$className}");
writeln("Package name : {$className}");
writeln('------');

writeln('This script will replace the above values in all relevant files in the project directory.');

if (! confirm('Modify files?', true)) {
    exit(1);
}

$files = (str_starts_with(strtoupper(PHP_OS), 'WIN') ? replaceForWindows() : replaceForAllOtherOSes());

foreach ($files as $file) {
    replace_in_file($file, [
        ':author_name' => $authorName,
        ':author_username' => $authorUsername,
        'author@domain.com' => $authorEmail,
        ':vendor_name' => $vendorName,
        ':vendor_slug' => $vendorSlug,
        'VendorName' => $vendorNamespace,
        ':package_name' => $packageName,
        ':package_slug' => $packageSlug,
        'Skeleton' => $className,
    ]);

    match (true) {
        str_contains($file, 'README.md') => removeReadmeParagraphs($file),
        default => [],
    };
}

setupTestingLibrary();
setupCodeStyleLibrary();

confirm('Execute `composer install` and run tests?') && run('composer install && composer test');

confirm('Let this script delete itself?', true) && unlink(__FILE__);
