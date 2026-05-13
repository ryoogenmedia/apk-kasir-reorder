<?php

return [

    'single' => [

        'label' => 'Hapus',

        'modal' => [

            'heading' => 'Hapus :label',

            'actions' => [

                'delete' => [
                    'label' => 'Hapus',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Berhasil dihapus',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Hapus yang dipilih',

        'modal' => [

            'heading' => 'Hapus :label yang dipilih',

            'actions' => [

                'delete' => [
                    'label' => 'Hapus',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Berhasil dihapus',
            ],

            'deleted_partial' => [
                'title' => ':count dari :total berhasil dihapus',
                'missing_authorization_failure_message' => 'Anda tidak memiliki izin untuk menghapus :count.',
                'missing_processing_failure_message' => ':count tidak dapat dihapus.',
            ],

            'deleted_none' => [
                'title' => 'Gagal menghapus',
                'missing_authorization_failure_message' => 'Anda tidak memiliki izin untuk menghapus :count.',
                'missing_processing_failure_message' => ':count tidak dapat dihapus.',
            ],

        ],

    ],

];
