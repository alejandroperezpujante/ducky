<?php

/**
 * Emits a <link rel="preload"> for the Nunito latin subset, resolving the URL
 * via the Vite dev-server bridge (in dev) or the manifest (in prod).
 *
 * Returns nothing in testing or when neither bridge nor manifest is present.
 */

use Tempest\Core\Environment;
use Tempest\Support\Html\HtmlString;
use Tempest\Vite\ViteConfig;

use function Tempest\Container\get;
use function Tempest\root_path;
use function Tempest\Support\Json\decode;

$environment = get(Environment::class);
$viteConfig = get(ViteConfig::class);

$fontKey = 'app/Resources/fonts/nunito-latin.woff2';
$url = null;

$bridgePath = root_path('public', $viteConfig->bridgeFileName);
$manifestPath = root_path('public', $viteConfig->buildDirectory, $viteConfig->manifest);

if (! $environment->isTesting() && is_file($bridgePath)) {
    // Dev: mirror DevelopmentTagsResolver::createDevelopmentTag
    $bridge = decode(file_get_contents($bridgePath));
    $base = rtrim($bridge['url'], '/');
    $url = $base . '/' . ltrim($fontKey, '/');
} elseif (! $environment->isTesting() && is_file($manifestPath)) {
    // Prod: mirror ManifestTagsResolver::getAssetPath
    $manifest = decode(file_get_contents($manifestPath));
    if (isset($manifest[$fontKey]['file'])) {
        $url = '/' . $viteConfig->buildDirectory . '/' . $manifest[$fontKey]['file'];
    }
}

$html = $url !== null
    ? new HtmlString('<link rel="preload" href="' . $url . '" as="font" type="font/woff2" crossorigin>')
    : new HtmlString('');
?>

{!! $html !!}
