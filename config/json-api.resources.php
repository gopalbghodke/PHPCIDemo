<?php

/**
 * List all models to register as VND resource
 */
return [
    'comments' => Brunoocto\Sample\Models\ProjectManagement\Comments::class,
    'files' => Brunoocto\Sample\Models\ProjectManagement\Files::class,
    'milestones' => Brunoocto\Sample\Models\ProjectManagement\Milestones::class,
    'projects' => Brunoocto\Sample\Models\ProjectManagement\Projects::class,
    'tasks' => Brunoocto\Sample\Models\ProjectManagement\Tasks::class,
    'users' => Brunoocto\Sample\Models\ProjectManagement\Users::class,
    'non_linears' => Brunoocto\Sample\Models\ProjectManagement\NonLinears::class,
    'details' => Brunoocto\Sample\Models\ProjectManagement\Details::class,
    // Fails is designed to test Failure in Unit test
    'fails' => Brunoocto\Sample\Models\ProjectManagement\Fails::class,
];
