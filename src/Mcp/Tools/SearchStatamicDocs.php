<?php

namespace ChrisVasey\StatamicBoost\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Statamic\Statamic;

#[IsReadOnly]
class SearchStatamicDocs extends Tool
{
    protected string $description = 'Search the Statamic documentation using semantic search. Use simple, topic-based queries for best results.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'queries' => $schema->array(
                $schema->string()
            )
                ->description('One or more search queries (e.g., ["antlers templating", "collections routing", "blueprints fields"])')
                ->required(),
            'limit' => $schema->integer()
                ->description('Maximum results per query (default: 5)')
                ->default(5),
        ];
    }

    public function handle(Request $request): Response
    {
        $queries = $request->get('queries', []);
        $limit = $request->get('limit', 5);

        if (empty($queries)) {
            return Response::error('At least one query is required.');
        }

        // Build package info for context
        $packages = [
            [
                'name' => 'statamic/cms',
                'version' => Statamic::version(),
            ],
        ];

        try {
            $response = Http::timeout(30)
                ->post('https://boost.laravel.com/api/search', [
                    'queries' => (array) $queries,
                    'packages' => $packages,
                    'limit' => $limit,
                ]);

            if ($response->failed()) {
                return Response::error('Failed to search documentation: '.$response->status());
            }

            $results = $response->json();

            return Response::json([
                'query_count' => count($queries),
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            return Response::error('Documentation search failed: '.$e->getMessage());
        }
    }
}
