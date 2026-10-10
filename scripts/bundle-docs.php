<?php

use ChrisVasey\StatamicBoost\Scripts\DocsBundle;

require dirname(__DIR__).'/vendor/autoload.php';
require __DIR__.'/DocsBundle.php';

if ($argc !== 5) {
    fwrite(STDERR, "Usage: php scripts/bundle-docs.php SOURCE_DIR MAJOR COMMIT_SHA OUTPUT_FILE\n");
    exit(1);
}

try {
    $result = (new DocsBundle)->write($argv[1], $argv[2], $argv[3], $argv[4]);
    fwrite(STDOUT, json_encode($result, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)."\n");
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage()."\n");
    exit(1);
}
