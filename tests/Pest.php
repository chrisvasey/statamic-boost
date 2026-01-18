<?php

use ChrisVasey\StatamicBoost\Tests\TestCase;
use Laravel\Mcp\Response;

uses(TestCase::class)->in(__DIR__);

/**
 * Extract JSON data from an MCP Response.
 */
function getResponseData(Response $response): array
{
    return json_decode((string) $response->content(), true) ?? [];
}

/**
 * Get the raw text content from an MCP Response.
 */
function getResponseText(Response $response): string
{
    return (string) $response->content();
}
