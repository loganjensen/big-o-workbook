<?php

return [
    'complexities' => [
        [
            'slug' => 'o-1',
            'title' => 'O(1) — Constant Time',
            'shortTitle' => 'O(1)',
            'description' => 'Same time regardless of input size',
        ],
        [
            'slug' => 'o-log-n',
            'title' => 'O(log n) — Logarithmic Time',
            'shortTitle' => 'O(log n)',
            'description' => 'Cuts problem in half each step',
        ],
        [
            'slug' => 'o-n',
            'title' => 'O(n) — Linear Time',
            'shortTitle' => 'O(n)',
            'description' => 'Time grows directly with input size',
        ],
        [
            'slug' => 'o-n-log-n',
            'title' => 'O(n log n) — Linearithmic Time',
            'shortTitle' => 'O(n log n)',
            'description' => 'Efficient sorting complexity',
        ],
        [
            'slug' => 'o-n-squared',
            'title' => 'O(n²) — Quadratic Time',
            'shortTitle' => 'O(n²)',
            'description' => 'Nested loops over same data',
        ],
        [
            'slug' => 'o-2-n',
            'title' => 'O(2ⁿ) — Exponential Time',
            'shortTitle' => 'O(2ⁿ)',
            'description' => 'Doubles with each additional element',
        ],
        [
            'slug' => 'o-n-factorial',
            'title' => 'O(n!) — Factorial Time',
            'shortTitle' => 'O(n!)',
            'description' => 'All possible arrangements',
        ],
    ],

    'quiz' => [
        'question_count' => env('BIG_O_QUIZ_QUESTIONS', 5),
        'cache_ttl' => env('BIG_O_QUIZ_CACHE_TTL', 604800), // 7 days in seconds
        'cache_version' => env('BIG_O_QUIZ_CACHE_VERSION', 1),
        'anthropic_model' => env('BIG_O_QUIZ_MODEL', 'claude-3-5-sonnet-20241022'),
        'max_tokens' => 4000,
        'temperature' => 0.7,
    ],
];
