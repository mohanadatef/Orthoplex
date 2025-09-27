<?php
return [
  'generate_always' => env('SWAGGER_GENERATE_ALWAYS', false),
  'documentations' => [
    'default' => [
      'api' => [
        'title' => 'Orthoplex API',
      ],
      'routes' => [
        'api' => 'api/documentation',
      ],
      'paths' => [
        'docs_json' => 'api-docs.json',
        'docs_yaml' => 'api-docs.yaml',
        'format_to_use_for_docs' => 'yaml',
        'annotations' => [base_path('app'), base_path('routes'), base_path('docs')],
      ],
    ],
  ],
];
