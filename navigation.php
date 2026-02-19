<?php

// ENHANCED NAVIGATION SYSTEM: Komponen navigasi yang konsisten dengan breadcrumb dan back button

// Fungsi untuk membuat breadcrumb dengan struktur yang lebih baik
function breadcrumb_enhanced($judul_halaman, $links = [])
{
	echo '
    <nav class="breadcrumb-nav" aria-label="breadcrumb">
        <div class="breadcrumb-container">
            <ol class="breadcrumb-list">';

	// Home link (selalu ada)
	echo '
                <li class="breadcrumb-item">
                    <a href="index.php" class="breadcrumb-link">
                        <svg class="breadcrumb-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span>Home</span>
                    </a>
                </li>';

	// Links tambahan (opsional)
	foreach ($links as $url => $label) {
		echo '
                <li class="breadcrumb-separator">/</li>
                <li class="breadcrumb-item">
                    <a href="' . $url . '" class="breadcrumb-link">' . $label . '</a>
                </li>';
	}

	// Halaman aktif (tidak bisa diklik)
	echo '
                <li class="breadcrumb-separator">/</li>
                <li class="breadcrumb-item breadcrumb-active" aria-current="page">
                    ' . $judul_halaman . '
                </li>
            </ol>
        </div>
    </nav>';
}

// Fungsi untuk membuat back button dengan history.back()
function back_button($url = null, $label = "Kembali")
{
	if ($url) {
		// Back button dengan URL spesifik
		echo '
        <div class="back-button-container">
            <a href="' . $url . '" class="back-button">
                <svg class="back-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                <span>' . $label . '</span>
            </a>
        </div>';
	} else {
		// Smart back button dengan history.back()
		echo '
        <div class="back-button-container">
            <button type="button" class="back-button" onclick="history.back()">
                <svg class="back-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                <span>' . $label . '</span>
            </button>
        </div>';
	}
}

// Fungsi untuk membuat page header dengan breadcrumb dan back button
function page_header($title, $breadcrumb_links = [], $show_back = true, $back_url = null)
{
	echo '<div class="page-header-section">';

	if ($show_back) {
		back_button($back_url);
	}

	breadcrumb_enhanced($title, $breadcrumb_links);

	echo '
        <h1 class="page-main-title">' . $title . '</h1>
    </div>';
}
