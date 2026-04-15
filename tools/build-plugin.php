<?php

declare(strict_types=1);

$pluginSlug = 'isw-wp-mailing-list-form';
$rootDir = dirname(__DIR__);
$distDir = $rootDir . DIRECTORY_SEPARATOR . 'dist';
$zipPath = $distDir . DIRECTORY_SEPARATOR . $pluginSlug . '.zip';

$includePaths = array(
    'export-handler.php',
    'includes',
    'isw-wp-mailing-list-form-admin.css',
    'isw-wp-mailing-list-form-frontend.js',
    'isw-wp-mailing-list-form.css',
    'isw-wp-mailing-list-form.js',
    'isw-wp-mailing-list-form.php',
    'languages',
    'LICENSE',
    'README.md',
    'readme.txt',
    'uninstall.php',
);

if ( ! extension_loaded( 'zip' ) ) {
    fwrite( STDERR, "The PHP zip extension is required to build the plugin archive.\n" );
    exit( 1 );
}

if ( ! is_dir( $distDir ) && ! mkdir( $distDir, 0777, true ) && ! is_dir( $distDir ) ) {
    fwrite( STDERR, "Unable to create dist directory at {$distDir}.\n" );
    exit( 1 );
}

if ( file_exists( $zipPath ) && ! unlink( $zipPath ) ) {
    fwrite( STDERR, "Unable to replace existing archive at {$zipPath}.\n" );
    exit( 1 );
}

$zip = new ZipArchive();
if ( true !== $zip->open( $zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE ) ) {
    fwrite( STDERR, "Unable to create zip archive at {$zipPath}.\n" );
    exit( 1 );
}

foreach ( $includePaths as $relativePath ) {
    $absolutePath = $rootDir . DIRECTORY_SEPARATOR . $relativePath;

    if ( ! file_exists( $absolutePath ) ) {
        fwrite( STDERR, "Missing required path: {$relativePath}\n" );
        $zip->close();
        exit( 1 );
    }

    addPathToArchive( $zip, $absolutePath, $pluginSlug . '/' . str_replace( '\\', '/', $relativePath ) );
}

$zip->close();

fwrite( STDOUT, "Created {$zipPath}\n" );

function addPathToArchive( ZipArchive $zip, string $absolutePath, string $archivePath ): void {
    if ( is_dir( $absolutePath ) ) {
        $zip->addEmptyDir( rtrim( $archivePath, '/' ) );

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $absolutePath, FilesystemIterator::SKIP_DOTS ),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ( $iterator as $item ) {
            $itemAbsolutePath = $item->getPathname();
            $itemRelativePath = str_replace( '\\', '/', substr( $itemAbsolutePath, strlen( $absolutePath ) + 1 ) );
            $itemArchivePath = rtrim( $archivePath, '/' ) . '/' . $itemRelativePath;

            if ( $item->isDir() ) {
                $zip->addEmptyDir( $itemArchivePath );
                continue;
            }

            $zip->addFile( $itemAbsolutePath, $itemArchivePath );
        }

        return;
    }

    $zip->addFile( $absolutePath, $archivePath );
}