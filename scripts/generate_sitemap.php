<?php
// This script generates a sitemap.xml file for the website.
// It should be run from the command line: php scripts/generate_sitemap.php

require_once __DIR__ . '/../src/db.php';

const BASE_URL = 'https://your-website.com'; // IMPORTANT: Replace with the actual domain
const SITEMAP_PATH = __DIR__ . '/../public/sitemap.xml';

function main() {
    echo "Connecting to the database...\n";
    $pdo = connect_db();

    echo "Fetching products...\n";
    $stmt = $pdo->query('SELECT id, name FROM products ORDER BY id');
    $products = $stmt->fetchAll();
    echo "Found " . count($products) . " products.\n";

    echo "Generating sitemap...\n";
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

    // Add static pages
    add_url($xml, '');
    add_url($xml, 'products');
    add_url($xml, 'login');
    add_url($xml, 'register');

    // Add product pages
    foreach ($products as $product) {
        // This assumes the URL structure is still /product?id=...
        // If we implement clean URLs, this will need to be updated.
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $product['name'])));
        add_url($xml, 'product?id=' . $product['id']);
    }

    // Save the file
    $result = $xml->asXML(SITEMAP_PATH);

    if ($result) {
        echo "Sitemap successfully generated at " . SITEMAP_PATH . "\n";
    } else {
        echo "Error: Failed to generate sitemap.\n";
    }
}

function add_url(SimpleXMLElement $xml, string $loc, string $changefreq = 'weekly', string $priority = '0.8') {
    $url = $xml->addChild('url');
    $url->addChild('loc', BASE_URL . '/' . $loc);
    $url->addChild('changefreq', $changefreq);
    $url->addChild('priority', $priority);
}

main();
