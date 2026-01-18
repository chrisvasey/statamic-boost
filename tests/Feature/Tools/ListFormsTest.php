<?php

use ChrisVasey\StatamicBoost\Mcp\Tools\ListForms;
use Laravel\Mcp\Request;
use Statamic\Facades\Form;

beforeEach(function () {
    Form::make('contact')
        ->title('Contact Form')
        ->save();
});

afterEach(function () {
    Form::find('contact')?->delete();
});

it('lists all forms', function () {
    $tool = new ListForms;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['forms'])->toBeArray();
    expect($data['total'])->toBeGreaterThanOrEqual(1);
});

it('includes form handles and titles', function () {
    $tool = new ListForms;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    $contact = collect($data['forms'])->firstWhere('handle', 'contact');

    expect($contact)->not->toBeNull();
    expect($contact['handle'])->toBe('contact');
    expect($contact['title'])->toBe('Contact Form');
});

it('returns empty array when no forms exist', function () {
    Form::find('contact')?->delete();

    $tool = new ListForms;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['forms'])->toBeArray();
    expect($data['total'])->toBe(0);
});
