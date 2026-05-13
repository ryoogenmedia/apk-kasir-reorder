<?php

return [

    'column_manager' => [

        'heading' => 'Kolom',

        'actions' => [

            'apply' => [
                'label' => 'Terapkan kolom',
            ],

            'reset' => [
                'label' => 'Atur ulang',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Aksi',
        ],

        'select' => [

            'loading_message' => 'Memuat...',

            'no_options_message' => 'Tidak ada opsi tersedia.',

            'no_search_results_message' => 'Tidak ada opsi yang cocok.',

            'placeholder' => 'Pilih opsi',

            'searching_message' => 'Mencari...',

            'search_prompt' => 'Ketik untuk mencari...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Tampilkan :count lebih sedikit',
                'expand_list' => 'Tampilkan :count lebih banyak',
            ],

            'more_list_items' => 'dan :count lainnya',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Pilih/batalkan semua item untuk aksi massal.',
        ],

        'bulk_select_record' => [
            'label' => 'Pilih/batalkan item :key untuk aksi massal.',
        ],

        'bulk_select_group' => [
            'label' => 'Pilih/batalkan grup :title untuk aksi massal.',
        ],

        'search' => [
            'label' => 'Cari',
            'placeholder' => 'Cari',
            'indicator' => 'Cari',
        ],

    ],

    'summary' => [

        'heading' => 'Ringkasan',

        'subheadings' => [
            'all' => 'Semua :label',
            'group' => 'Ringkasan :group',
            'page' => 'Halaman ini',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Rata-rata',
            ],

            'count' => [
                'label' => 'Jumlah',
            ],

            'sum' => [
                'label' => 'Total',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Selesai mengurutkan',
        ],

        'enable_reordering' => [
            'label' => 'Urutkan data',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'group' => [
            'label' => 'Kelompokkan',
        ],

        'open_bulk_actions' => [
            'label' => 'Aksi massal',
        ],

        'column_manager' => [
            'label' => 'Kelola kolom',
        ],

    ],

    'empty' => [

        'heading' => 'Tidak ada :model',

        'description' => 'Tambah :model untuk memulai.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Terapkan filter',
            ],

            'remove' => [
                'label' => 'Hapus filter',
            ],

            'remove_all' => [
                'label' => 'Hapus semua filter',
                'tooltip' => 'Hapus semua filter',
            ],

            'reset' => [
                'label' => 'Atur ulang',
            ],

        ],

        'heading' => 'Filter',

        'indicator' => 'Filter aktif',

        'multi_select' => [
            'placeholder' => 'Semua',
        ],

        'select' => [

            'placeholder' => 'Semua',

            'relationship' => [
                'empty_option_label' => 'Tidak ada',
            ],

        ],

        'trashed' => [

            'label' => 'Data yang dihapus',

            'only_trashed' => 'Hanya data yang dihapus',

            'with_trashed' => 'Dengan data yang dihapus',

            'without_trashed' => 'Tanpa data yang dihapus',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Kelompokkan berdasarkan',
            ],

            'direction' => [

                'label' => 'Arah kelompok',

                'options' => [
                    'asc' => 'Naik',
                    'desc' => 'Turun',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Seret dan lepas untuk mengurutkan data.',

    'selection_indicator' => [

        'selected_count' => '1 data dipilih|:count data dipilih',

        'actions' => [

            'select_all' => [
                'label' => 'Pilih semua :count',
            ],

            'deselect_all' => [
                'label' => 'Batalkan semua pilihan',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Urutkan berdasarkan',
            ],

            'direction' => [

                'label' => 'Arah pengurutan',

                'options' => [
                    'asc' => 'Naik',
                    'desc' => 'Turun',
                ],

            ],

        ],

    ],

    'default_model_label' => 'data',

];
