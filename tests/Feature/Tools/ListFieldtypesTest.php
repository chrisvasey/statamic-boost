<?php

use ChrisVasey\StatamicBoost\Mcp\Tools\ListFieldtypes;
use Laravel\Mcp\Request;

beforeEach(function () {
    if (! class_exists(\Statamic\Facades\Fieldtype::class)) {
        $this->markTestSkipped('Statamic Fieldtype facade not available');
    }
});

it('lists all fieldtypes', function () {
    $tool = new ListFieldtypes;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['fieldtypes'])->toBeArray();
    expect($data['total'])->toBeGreaterThan(0);
});

it('includes core fieldtypes', function () {
    $tool = new ListFieldtypes;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    $handles = collect($data['fieldtypes'])->pluck('handle')->toArray();

    expect($handles)->toContain('text');
    expect($handles)->toContain('textarea');
    expect($handles)->toContain('bard');
});

it('can filter by category', function () {
    $tool = new ListFieldtypes;
    $request = new Request(['category' => 'text']);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['fieldtypes'])->toBeArray();
});
