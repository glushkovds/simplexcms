<?php

ob_start('ob_gzhandler');
header("Content-Type: text/xml");
include 'config.php';
include 'core/sfcore.class.php';
include 'core/sfdb.class.php';
include 'core/sfsitemapbase.class.php';

SFDB::connect();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\r\n";

$rows = [
    '/' => // url local (/catalog/), with domain site.ru/catalog/, with protocol https://site.ru/catalog/
    [] // optional lastmod Y-m-d format, changefreq
];

$q = "SELECT class FROM component WHERE component_id IN(SELECT DISTINCT component_id FROM menu WHERE active=1) OR class='" . SFConfig::$component_default . "'";
$coms = SFDB::assoc($q);
foreach ($coms as $com) {
    $ext = strtolower(substr($com['class'], 3));
    if (is_file('ext/' . $ext . '/sitemap.php')) {
        include 'ext/' . $ext . '/sitemap.php';
        $class = "Sitemap$ext";
        $rows += (array) (new $class())->rows();
    }
}


foreach ($rows as $url => $row) {

    $addProtocol = preg_match("@^(http|https)\:\/\/@i", $url) ? '' : SFCore::httpProtocol(true);
    $addHost = preg_match("@^((http|https)\:\/\/)?[\da-z\-]+\.[\da-z\-\.]+@i", $url) ? '' : $_SERVER['HTTP_HOST'];
    $urlNew = $addProtocol . $addHost . ($addProtocol && $addHost && (substr($url, 0, 1) != '/') ? '/' : '') . $url;

    if ($urlNew != $url) {
        unset($rows[$url]);
        $rows[$urlNew] = $row;
    }
}


foreach ($rows as $url => $row) {
    echo '<url>';
    echo "<loc>$url</loc>";
    echo empty($row['lastmod']) ? '' : "<lastmod>{$row['lastmod']}</lastmod>";
    echo empty($row['changefreq']) ? '' : "<changefreq>{$row['changefreq']}</changefreq>"; // weekly
    echo '</url>' . "\r\n";
}

echo '</urlset>';
