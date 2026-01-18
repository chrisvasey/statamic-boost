<?php

use ChrisVasey\StatamicBoost\Mcp\Tools\StacheInfo;
use Laravel\Mcp\Request;

it('returns stache information', function () {
    $tool = new StacheInfo;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data)->toHaveKey('stores');
    expect($data['stores'])->toBeArray();
});

it('includes store statistics', function () {
    $tool = new StacheInfo;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    foreach ($data['stores'] as $store) {
        expect($store)->toHaveKey('key');
    }
});
