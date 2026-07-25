<?php

return [

    'company' => [
        'invalid_parent' => 'Selected parent company is invalid.',
        'self_parent' => 'A company cannot be its own parent.',
        'circular_parent' => 'Invalid parent company. You cannot assign a child company/branch as parent.',
    ],

    'customer' => [
        'invalid_parent' => 'Selected parent branch is invalid.',
        'self_parent' => 'A branch cannot be its own parent.',
        'circular_parent' => 'Invalid parent branch. You cannot assign a child branch as parent.',
    ],

    'employee' => [
        'invalid_manager' => 'Selected manager is invalid.',
        'self_report' => 'An employee cannot report to themselves.',
        'circular_report' => 'Invalid manager. You cannot assign a direct/indirect report as the manager.',
    ],

    'sequence_number' => [
        'format_not_configured' => 'No sequence number format configured for category :category.',
    ],

];
