<?php

namespace ChrisVasey\StatamicBoost\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;
use Statamic\Facades\Fieldtype;

#[IsReadOnly]
class ListFieldtypes extends Tool
{
    protected string $description = 'List all available Statamic fieldtypes that can be used in blueprints.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'category' => $schema->string()
                ->description('Filter by fieldtype category (e.g., "text", "media", "relationship", "structured")'),
        ];
    }

    public function handle(Request $request): Response
    {
        $category = $request->get('category');

        $fieldtypes = Fieldtype::all()
            ->when($category, fn ($collection) => $collection->filter(
                fn ($fieldtype) => collect($fieldtype->categories())->contains($category)
            ))
            ->map(function ($fieldtype) {
                return [
                    'handle' => $fieldtype->handle(),
                    'title' => $fieldtype->title(),
                    'icon' => $fieldtype->icon(),
                    'categories' => $fieldtype->categories(),
                    'selectable' => $fieldtype->selectable(),
                    'config_fields' => collect($fieldtype->configFields()->all())->map(function ($field) {
                        return [
                            'handle' => $field->handle(),
                            'type' => $field->type(),
                            'display' => $field->display(),
                        ];
                    })->values()->toArray(),
                ];
            })->values()->toArray();

        return Response::json([
            'total' => count($fieldtypes),
            'fieldtypes' => $fieldtypes,
        ]);
    }
}
