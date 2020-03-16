<?php

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\Twigs\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'resources' => [
        'budget' => ['url' => "/api/v1.0/budgets"],
        'category' => ['url' => "/api/v1.0/categories"],
        'transaction' => ['url' => "/api/v1.0/transactions"],
    ],
    'routes' => [
        [
            'name' => 'page#index',
            'url' => '/',
            'verb' => 'GET',
        ],
        [
            'name' => 'transaction#sum',
            'url' => '/api/v1.0/transactions/sum',
            'verb' => 'GET',
        ]
    ]
];
