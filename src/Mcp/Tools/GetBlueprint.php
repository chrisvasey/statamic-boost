<?php

namespace ChrisVasey\StatamicBoost\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Statamic\Facades\Blueprint;

#[IsReadOnly]
class GetBlueprint extends Tool
{
    protected string $description = 'Get the field definitions for a specific blueprint. Blueprints define the content structure for collections, taxonomies, globals, and other content types.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'namespace' => $schema->string()
                ->description('The blueprint namespace (e.g., "collections.blog", "taxonomies.tags", "globals.site")')
                ->required(),
            'handle' => $schema->string()
                ->description('The blueprint handle within the namespace')
                ->required(),
        ];
    }

    public function handle(Request $request): Response
    {
        $namespace = $request->get('namespace');
        $handle = $request->get('handle');

        $blueprint = Blueprint::find("{$namespace}.{$handle}");

        if (! $blueprint) {
            return Response::error("Blueprint not found: {$namespace}.{$handle}");
        }

        $fields = $blueprint->fields()->all()->map(function ($field) {
            return [
                'handle' => $field->handle(),
                'type' => $field->type(),
                'display' => $field->display(),
                'instructions' => $field->instructions(),
                'required' => $field->isRequired(),
                'localizable' => $field->isLocalizable(),
                'config' => $field->config(),
            ];
        })->values()->toArray();

        return Response::json([
            'handle' => $blueprint->handle(),
            'title' => $blueprint->title(),
            'namespace' => $blueprint->namespace(),
            'hidden' => $blueprint->hidden(),
            'fields' => $fields,
            'tabs' => collect($blueprint->tabs())->map(fn ($tab) => [
                'handle' => $tab->handle(),
                'display' => $tab->display(),
            ])->values()->toArray(),
        ]);
    }
}
