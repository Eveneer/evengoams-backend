<?php

declare(strict_types=1);

namespace App\Domains\Tags\Actions\Queries;

use App\Domains\Tags\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Auth\Access\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;


class GetTags
{
    use AsAction;

    public function authorize(ActionRequest $request): Response
    {
        $user = $request->user();
        
        if ($user->has_general_access) {
            return Response::allow();
        }

        return Response::deny('You are unauthorized to perform this action');
    }

    public function handle(
        ?int $per_page = 10, 
        ?string $search_term = ''
    ): Collection | LengthAwarePaginator {
        $query = Tag::query();

        if ($search_term) {
            $search_key = Tag::constructKey($search_term);
            $query
                ->where('key', 'like', "%$search_key%");
        }
    
        return $per_page === null ?
            $query->get() :
            $query->paginate($per_page);
    }

    public function rules(): array
    {
        return [
            'search_term' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle(
            $request->per_page,
            $request->search_term,
        );
    }

    public function jsonResponse(array $tags, ActionRequest $request): array
    {
        $message = count($tags) . ' tags ';
        $message .= $request->search_term ? 'found' : 'fetched';

        return [
            'data' => $tags,
            'message' => $message . ' successfully',
        ];
    }
}