<?php

return [
    'show_custom_fields' => true,
    'custom_fields' => [
        'occupation' => [
            'type' => 'text',
            'label' => 'Occupation',
            'placeholder' => 'Input your occupation',
            'required' => true,
            'rules' => 'required|string|max:255',
        ],
    ]
];
