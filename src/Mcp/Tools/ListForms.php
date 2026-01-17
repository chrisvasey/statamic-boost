<?php

namespace ChrisVasey\StatamicBoost\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;
use Statamic\Facades\Form;

#[IsReadOnly]
class ListForms extends Tool
{
    protected string $description = 'List all Statamic forms with their handles, fields, and submission counts.';

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Response
    {
        $forms = Form::all()->map(function ($form) {
            $blueprint = $form->blueprint();
            $fields = $blueprint ? $blueprint->fields()->all()->map(function ($field) {
                return [
                    'handle' => $field->handle(),
                    'type' => $field->type(),
                    'display' => $field->display(),
                    'required' => $field->isRequired(),
                    'instructions' => $field->instructions(),
                ];
            })->values()->toArray() : [];

            return [
                'handle' => $form->handle(),
                'title' => $form->title(),
                'honeypot' => $form->honeypot(),
                'store' => $form->store(),
                'email' => $form->email(),
                'submission_count' => $form->submissions()->count(),
                'fields' => $fields,
            ];
        })->values()->toArray();

        return Response::json([
            'total' => count($forms),
            'forms' => $forms,
        ]);
    }
}
