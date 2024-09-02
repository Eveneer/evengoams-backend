<?php

declare(strict_types=1);

namespace App\Domains\Accounts\Actions\Queries;

use App\Domains\Accounts\Account;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetAccounts
{
    use AsAction;

    public function authorize(ActionRequest $request): Response
    {
        $user = $request->user();
        
        if ($user && $user->has_general_access) {
            return Response::allow();
        }

        return Response::deny('You are unauthorized to perform this action');
    }

    public function handle(?int $per_page = 10, ?string $search_term = ''): Collection | LengthAwarePaginator
    {
        $query = Account::query();

        if ($search_term) {
            $query
                ->where('name', 'like', "%$search_term%")
                ->orWhere('details', 'like', "%$search_term%");
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
        $search_term = $request->input('search_term');
        $per_page = $request->input('per_page');

        return $this->handle($search_term, $per_page);
    }

    public function jsonResponse(array $accounts, ActionRequest $request): array
    {
        $message = count($accounts) . ' accounts ';
        $message .= $request->input('search_term') ? 'found' : 'fetched';

        return [
            'data' => $accounts,
            'message' => $message . ' successfully',
        ];
    }
}
