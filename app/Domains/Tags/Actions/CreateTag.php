<?php

declare(strict_types=1);

namespace App\Domains\Tags\Actions;

use App\Domains\Tags\Tag;
use App\Domains\Tags\Enums\TagModelsEnum;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateTag
{
    use AsAction;

    public function authorize(ActionRequest $request): Response
    {
        $user = $request->user();
        
        if ($user->has_general_access)
            return Response::allow();

        return Response::deny('You are unauthorised to perform this action');
    }

    public function handle(array $params): Tag
    {
        $tag = Tag::exists($params['name']);

        if ($tag === false) {
            $params['key'] = Tag::constructKey($params['name']);
            $tag = Tag::create($params);
        }

        return $tag;
    }

    public function prepareForValidation(ActionRequest $request): void
    {
        $key = $request->name;
        
        // remove multiple spaces
        $key = preg_replace('/\s+/', ' ', $key);
        // convert spaces to dashes
        $key = str_replace(' ', '-', $key);
        // lowercase the key
        $key = strtolower($key);

        $request->merge(['key' => $key]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function asController(Request $request)
    {
        return $this->handle($request->validated());
    }

    public function jsonResponse(Tag $tag, Request $request): array
    {
        return [
            'message' => 'Tag created successfully',
        ];
    }
}
