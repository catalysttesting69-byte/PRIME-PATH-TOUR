<?php
/**
 * api/get_all_trips.php
 * ─────────────────────────────────────────────────────────────
 * Fetches both Packages and Excursions from the database
 * and returns them in a format suitable for script.js
 * ─────────────────────────────────────────────────────────────
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

try {
    // 1. Fetch Packages
    $stmtPkg = $pdo->query("SELECT * FROM packages WHERE active = 1 ORDER BY created_at DESC");
    $packages = $stmtPkg->fetchAll(PDO::FETCH_ASSOC);

    // 2. Fetch Excursions
    $stmtExc = $pdo->query("SELECT * FROM excursions WHERE active = 1 ORDER BY created_at DESC");
    $excursions = $stmtExc->fetchAll(PDO::FETCH_ASSOC);

    // Prepare for front-end structure
    $trips = [];

    foreach ($packages as $p) {
        $trips[] = [
            'id'          => $p['id'],
            'title'       => $p['title'],
            'url'         => '#', // Usually links to details, can be custom
            'image'       => $p['image_url'],
            'price'       => '$' . number_format($p['price']),
            'priceNum'    => (float)$p['price'],
            'priceLabel'  => 'from',
            'highlights'  => explode(',', $p['highlights']),
            'route'       => explode(',', $p['route']),
            'type'        => $p['type'] // safari, zanzibar, combined
        ];
    }

    foreach ($excursions as $e) {
        $trips[] = [
            'id'          => $e['id'],
            'title'       => $e['name'],
            'url'         => '#',
            'image'       => $e['image_url'],
            'price'       => $e['price'], // Already formatted in DB
            'priceNum'    => (int)preg_replace('/[^0-9]/', '', $e['price']),
            'priceLabel'  => 'from',
            'highlights'  => [$e['category']],
            'route'       => [$e['category']],
            'type'        => 'excursions',
            'description' => $e['description']
        ];
    }

    echo json_encode([
        'success' => true,
        'trips'   => $trips
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
