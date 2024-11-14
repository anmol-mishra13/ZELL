<?php
// assets/vendor/setup.php

// Define base paths
define('VENDOR_PATH', 'assets/vendor/');

// Array of vendor files and their CDN fallbacks
$vendor_files = [
    'bootstrap' => [
        'css' => [
            'local' => VENDOR_PATH . 'bootstrap/css/bootstrap.min.css',
            'cdn' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css'
        ],
        'js' => [
            'local' => VENDOR_PATH . 'bootstrap/js/bootstrap.bundle.min.js',
            'cdn' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'
        ]
    ],
    'bootstrap-icons' => [
        'css' => [
            'local' => VENDOR_PATH . 'bootstrap-icons/bootstrap-icons.css',
            'cdn' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css'
        ]
    ],
    'aos' => [
        'css' => [
            'local' => VENDOR_PATH . 'aos/aos.css',
            'cdn' => 'https://unpkg.com/aos@2.3.1/dist/aos.css'
        ],
        'js' => [
            'local' => VENDOR_PATH . 'aos/aos.js',
            'cdn' => 'https://unpkg.com/aos@2.3.1/dist/aos.js'
        ]
    ],
    'glightbox' => [
        'css' => [
            'local' => VENDOR_PATH . 'glightbox/css/glightbox.min.css',
            'cdn' => 'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css'
        ],
        'js' => [
            'local' => VENDOR_PATH . 'glightbox/js/glightbox.min.js',
            'cdn' => 'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js'
        ]
    ],
    'swiper' => [
        'css' => [
            'local' => VENDOR_PATH . 'swiper/swiper-bundle.min.css',
            'cdn' => 'https://unpkg.com/swiper@8/swiper-bundle.min.css'
        ],
        'js' => [
            'local' => VENDOR_PATH . 'swiper/swiper-bundle.min.js',
            'cdn' => 'https://unpkg.com/swiper@8/swiper-bundle.min.js'
        ]
    ]
];

// Function to get asset URL (falls back to CDN if local file doesn't exist)
function get_asset_url($vendor, $type) {
    global $vendor_files;
    
    if (isset($vendor_files[$vendor][$type])) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $vendor_files[$vendor][$type]['local'])) {
            return $vendor_files[$vendor][$type]['local'];
        }
        return $vendor_files[$vendor][$type]['cdn'];
    }
    return null;
}
?>