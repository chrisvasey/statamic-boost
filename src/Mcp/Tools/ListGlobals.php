<?php

namespace ChrisVasey\StatamicBoost\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;
use Statamic\Facades\GlobalSet;

#[IsReadOnly]
class ListGlobals extends Tool
{
    protected string $description = 'List all Statamic global sets with their handles, titles, and current values.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'include_values' => $schema->boolean()
                ->description('Include the current values for each global set (default: true)')
                ->default(true),
        ];
    }

    public function handle(Request $request): Response
    {
        $includeValues = $request->get('include_values', true);

        $globals = GlobalSet::all()->map(function ($global) use ($includeValues) {
            $data = [
                'handle' => $global->handle(),
                'title' => $global->title(),
                'sites' => $global->sites()->toArray(),
            ];

            if ($includeValues) {
                $data['variables'] = $global->inDefaultSite()?->data()->toArray() ?? [];
            }

            // Get blueprint fields
            $blueprint = $global->blueprint();
            if ($blueprint) {
                $data['fields'] = $blueprint->fields()->all()->map(function ($field) {
                    return [
                        'handle' => $field->handle(),
                        'type' => $field->type(),
                        'display' => $field->display(),
                    ];
                })->values()->toArray();
            }

            return $data;
        })->values()->toArray();

        return Response::json([
            'total' => count($globals),
            'globals' => $globals,
        ]);
    }
}
