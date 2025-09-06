// Styles
wp_enqueue_style(
    'ielts-board',
    IELTS_MIGRATION_URL . 'public/css/board.css',
    [],
    IELTS_MIGRATION_VER
);

// Scripts (order matters)
wp_enqueue_script(
    'ielts-board-core',
    IELTS_MIGRATION_URL . 'public/js/board/core.js',
    [],
    IELTS_MIGRATION_VER,
    true
);
wp_enqueue_script(
    'ielts-board-dictation',
    IELTS_MIGRATION_URL . 'public/js/board/dictation.js',
    ['ielts-board-core'],
    IELTS_MIGRATION_VER,
    true
);
wp_enqueue_script(
    'ielts-board-speaking',
    IELTS_MIGRATION_URL . 'public/js/board/speaking.js',
    ['ielts-board-core'],
    IELTS_MIGRATION_VER,
    true
);
