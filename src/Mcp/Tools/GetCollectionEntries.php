<?php

namespace ChrisVasey\StatamicBoost\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;
use Statamic\Facades\Entry;

#[IsReadOnly]
class GetCollectionEntries extends Tool
{
    protected string $description = 'Query Statamic collection entries with optional filtering by collection, status, and limit.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'collection' => $schema->string()
                ->description('Filter entries by collection handle (e.g., "blog", "pages")'),
            'status' => $schema->string()
                ->description('Filter by status: "published", "draft", or "scheduled"')
                ->enum(['published', 'draft', 'scheduled']),
            'limit' => $schema->integer()
                ->description('Maximum number of entries to return (default: 50)')
                ->default(50),
        ];
    }

    public function handle(Request $request): Response
    {
        $query = Entry::query();

        if ($collection = $request->get('collection')) {
            $query->where('collection', $collection);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $limit = $request->get('limit', 50);
        $entries = $query->limit($limit)->get();

        $data = $entries->map(function ($entry) {
            return [
                'id' => $entry->id(),
                'title' => $entry->get('title'),
                'slug' => $entry->slug(),
                'uri' => $entry->uri(),
                'url' => $entry->url(),
                'collection' => $entry->collectionHandle(),
                'blueprint' => $entry->blueprint()->handle(),
                'status' => $entry->status(),
                'published' => $entry->published(),
                'date' => $entry->date()?->toDateTimeString(),
                'last_modified' => $entry->lastModified()?->toDateTimeString(),
            ];
        })->values()->toArray();

        return Response::json([
            'total' => count($data),
            'entries' => $data,
        ]);
    }
}
