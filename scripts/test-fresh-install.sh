#!/usr/bin/env bash
set -euo pipefail

package_dir=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
fresh_install_root=$(mktemp -d "${TMPDIR:-/tmp}/statamic-boost-fresh.XXXXXX")
trap 'if [[ "${KEEP_FRESH_INSTALL:-0}" == 1 ]]; then echo "Fresh install retained: $fresh_install_root"; else rm -rf "$fresh_install_root"; fi' EXIT

mkdir "$fresh_install_root/package"
cp -R "$package_dir/src" "$package_dir/config" "$package_dir/resources" "$package_dir/docs" "$package_dir/.ai" "$package_dir/composer.json" "$package_dir/LICENSE.md" "$fresh_install_root/package/"
composer create-project statamic/statamic "$fresh_install_root/app" '^6.0' --no-install --no-scripts --prefer-dist --no-interaction
cd "$fresh_install_root/app"

php -r '
$composer = json_decode(file_get_contents("composer.json"), true, flags: JSON_THROW_ON_ERROR);
$composer["require"]["php"] = "~8.5.0";
$composer["require"]["laravel/framework"] = "^13.0";
$composer["require"]["statamic/cms"] = "^6.0";
$composer["require-dev"]["laravel/boost"] = "^2.0";
$composer["require-dev"]["chrisvasey/statamic-boost"] = "dev-ci";
$composer["repositories"] = [["type" => "path", "url" => $argv[1], "options" => ["symlink" => false, "versions" => ["chrisvasey/statamic-boost" => "dev-ci"]]]];
file_put_contents("composer.json", json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);
' "$fresh_install_root/package"

test ! -d vendor
test ! -f composer.lock
cp .env.example .env
composer update --prefer-dist --no-interaction --no-progress
php artisan key:generate --no-interaction

# Seed the project choices normally made interactively; Claude writes only project files.
cat > boost.json <<'JSON'
{
    "agents": ["claude_code"],
    "packages": ["chrisvasey/statamic-boost"],
    "guidelines": true,
    "mcp": true,
    "cloud": false,
    "nightwatch": false
}
JSON

php artisan statamic-boost:install --env=statamic --no-interaction
php -d zend.assertions=1 "$package_dir/scripts/assert-fresh-install.php"
php artisan statamic-boost:install --env=statamic --no-interaction
php -d zend.assertions=1 "$package_dir/scripts/assert-fresh-install.php"
composer show --direct
