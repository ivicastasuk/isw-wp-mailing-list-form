<?php

declare(strict_types=1);

$rootDir = dirname(__DIR__);
$languagesDir = $rootDir . DIRECTORY_SEPARATOR . 'languages';
$potPath = $languagesDir . DIRECTORY_SEPARATOR . 'isw-wp-mailing-list-form.pot';
$version = detectPluginVersion($rootDir . DIRECTORY_SEPARATOR . 'isw-wp-mailing-list-form.php');

if ( ! is_dir($languagesDir) && ! mkdir($languagesDir, 0777, true) && ! is_dir($languagesDir) ) {
    fwrite(STDERR, "Unable to create languages directory.\n");
    exit(1);
}

if ( wpCliAvailable() ) {
    $command = buildWpCliCommand($rootDir, $potPath);
    exec($command, $output, $exitCode);

    if ( 0 === $exitCode ) {
        fwrite(STDOUT, "Generated {$potPath} using WP-CLI\n");
        exit(0);
    }

    fwrite(STDERR, "WP-CLI POT generation failed, writing placeholder template instead.\n");
}

file_put_contents($potPath, buildPlaceholderPot($version));
fwrite(STDOUT, "Updated {$potPath} with placeholder metadata\n");

function detectPluginVersion(string $pluginFile): string {
    $contents = file_get_contents($pluginFile);
    if ( false !== $contents && preg_match('/^ \* Version:\s+([0-9]+\.[0-9]+\.[0-9]+)/m', $contents, $matches) ) {
        return $matches[1];
    }

    return '1.0.0';
}

function wpCliAvailable(): bool {
    exec('wp --info 2>NUL', $output, $exitCode);

    if ( 0 === $exitCode ) {
        return true;
    }

    exec('wp --info 2>/dev/null', $output, $exitCode);

    return 0 === $exitCode;
}

function buildWpCliCommand(string $rootDir, string $potPath): string {
    $rootArg = escapeshellarg($rootDir);
    $potArg = escapeshellarg($potPath);

    return "wp i18n make-pot {$rootArg} {$potArg} --slug=isw-wp-mailing-list-form --domain=isw-wp-mailing-list-form --exclude=.git,dist,node_modules,vendor,tools";
}

function buildPlaceholderPot(string $version): string {
    $date = gmdate('Y-m-d H:i+0000');

    return <<<POT
msgid ""
msgstr ""
"Project-Id-Version: ISW WP Mailing List Form {$version}\\n"
"Report-Msgid-Bugs-To: \\n"
"POT-Creation-Date: {$date}\\n"
"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n"
"Last-Translator: \\n"
"Language-Team: \\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
"Plural-Forms: nplurals=2; plural=(n != 1);\\n"
"X-Domain: isw-wp-mailing-list-form\\n"

#. Generated placeholder POT file.
#. Install WP-CLI i18n commands and run composer i18n:pot for full string extraction.
POT;
}