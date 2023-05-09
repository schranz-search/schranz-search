<?php

declare(strict_types=1);

it('test create', function () {
    $this->artisan('schranz:search:index-create')
        ->assertExitCode(0);
});

it('test drop', function () {
    $this->artisan('schranz:search:index-drop --force')
        ->assertExitCode(0);
});

it('test reindex', function () {
    $this->artisan('schranz:search:reindex --drop')
        ->assertExitCode(0)
        ->expectsOutputToContain('3/3');
});
