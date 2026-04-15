<?php

declare(strict_types=1);

$allowedBumps = array('patch', 'minor', 'major');
$bumpType = $argv[1] ?? '';

if ( ! in_array($bumpType, $allowedBumps, true) ) {
    fwrite(STDERR, "Usage: php tools/bump-version.php [patch|minor|major]\n");
    exit(1);
}

$rootDir = dirname(__DIR__);
$pluginFile = $rootDir . DIRECTORY_SEPARATOR . 'isw-wp-mailing-list-form.php';
$readmeFile = $rootDir . DIRECTORY_SEPARATOR . 'readme.txt';
$potFile = $rootDir . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . 'isw-wp-mailing-list-form.pot';

$currentVersion = readCurrentVersion($pluginFile);
$newVersion = bumpVersion($currentVersion, $bumpType);

$updates = array();

$updates[$pluginFile] = buildUpdatedContents($pluginFile, array(
    '/^(\s*\*\s*Version:\s*)([0-9]+\.[0-9]+\.[0-9]+)\s*$/m' => '${1}' . $newVersion,
    "/^(\s*define\(\s*'ISW_ML_PLUGIN_VERSION',\s*')([0-9]+\.[0-9]+\.[0-9]+)('\s*\);)\s*$/m" => '${1}' . $newVersion . '${3}',
));

$updates[$readmeFile] = buildUpdatedContents($readmeFile, array(
    '/^(\s*Stable tag:\s*)([0-9]+\.[0-9]+\.[0-9]+)\s*$/m' => '${1}' . $newVersion,
));

$updates[$readmeFile] = prependReadmeSectionContents($updates[$readmeFile], $readmeFile, '== Changelog ==', '= ' . $newVersion . ' =' . PHP_EOL . '* Maintenance release.' . PHP_EOL . PHP_EOL);
$updates[$readmeFile] = prependReadmeSectionContents($updates[$readmeFile], $readmeFile, '== Upgrade Notice ==', '= ' . $newVersion . ' =' . PHP_EOL . 'Maintenance release.' . PHP_EOL . PHP_EOL);

if ( file_exists($potFile) ) {
    $updates[$potFile] = buildUpdatedContents($potFile, array(
        '/^("Project-Id-Version: ISW WP Mailing List Form )([0-9]+\.[0-9]+\.[0-9]+)(\\\\n")\r?$/m' => '${1}' . $newVersion . '${3}',
    ));
}

writeUpdatedContents($updates);

fwrite(STDOUT, "Version bumped from {$currentVersion} to {$newVersion}\n");

function readCurrentVersion(string $pluginFile): string {
    $contents = file_get_contents($pluginFile);
    if ( false === $contents || ! preg_match('/^\s*\*\s*Version:\s*([0-9]+\.[0-9]+\.[0-9]+)\s*$/m', $contents, $matches) ) {
        fwrite(STDERR, "Unable to detect current plugin version.\n");
        exit(1);
    }

    return $matches[1];
}

function bumpVersion(string $version, string $type): string {
    $parts = array_map('intval', explode('.', $version));

    if ( 3 !== count($parts) ) {
        fwrite(STDERR, "Unsupported version format: {$version}\n");
        exit(1);
    }

    switch ($type) {
        case 'major':
            $parts[0]++;
            $parts[1] = 0;
            $parts[2] = 0;
            break;
        case 'minor':
            $parts[1]++;
            $parts[2] = 0;
            break;
        case 'patch':
            $parts[2]++;
            break;
    }

    return implode('.', $parts);
}

function buildUpdatedContents(string $path, array $replacements): string {
    $contents = file_get_contents($path);
    if ( false === $contents ) {
        fwrite(STDERR, "Unable to read {$path}\n");
        exit(1);
    }

    foreach ($replacements as $pattern => $replacement) {
        $updated = preg_replace($pattern, $replacement, $contents, 1, $count);
        if ( null === $updated || 0 === $count ) {
            fwrite(STDERR, "Pattern not found while updating {$path}: {$pattern}\n");
            exit(1);
        }
        $contents = $updated;
    }

    return $contents;
}

function prependReadmeSectionContents(string $contents, string $readmeFile, string $header, string $newEntry): string {
    if ( false === strpos($contents, $header) ) {
        fwrite(STDERR, "Section {$header} not found in {$readmeFile}\n");
        exit(1);
    }

    if ( false !== strpos($contents, $newEntry) ) {
        return $contents;
    }

    $contents = preg_replace('/' . preg_quote($header, '/') . "\R\R/", $header . PHP_EOL . PHP_EOL . $newEntry, $contents, 1, $count);
    if ( null === $contents || 0 === $count ) {
        fwrite(STDERR, "Unable to update {$header} in {$readmeFile}\n");
        exit(1);
    }

    return $contents;
}

function writeUpdatedContents(array $updates): void {
    foreach ($updates as $path => $contents) {
        if ( false === file_put_contents($path, $contents) ) {
            fwrite(STDERR, "Unable to write {$path}\n");
            exit(1);
        }
    }
}