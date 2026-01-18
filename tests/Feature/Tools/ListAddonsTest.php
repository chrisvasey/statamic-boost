<?php

use ChrisVasey\StatamicBoost\Mcp\Tools\ListAddons;
use Laravel\Mcp\Request;

it('lists addons', function () {
    $tool = new ListAddons;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['addons'])->toBeArray();
    expect($data)->toHaveKey('total');
});

it('returns empty array when no addons installed', function () {
    $tool = new ListAddons;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['addons'])->toBeArray();
});
