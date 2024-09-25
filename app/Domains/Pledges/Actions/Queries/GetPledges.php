<?php

declare(strict_types=1);

namespace App\Domains\Pledges\Actions\Queries;

use App\Domains\Donors\Actions\Queries\GetDonors;
use App\Domains\Pledges\Pledge;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetPledges
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

    public function handle(
        ?int $per_page = 10, 
        ?string $search_term = ''
    ): Collection | LengthAwarePaginator {
        $query = Pledge::query();

        if ($search_term) {
            $query
                ->whereIn('donor_id', GetDonors::run(null, $search_term)->pluck('id'));
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

    public function jsonResponse(
        Collection | LengthAwarePaginator $pledges,
        ActionRequest $request
    ): array {
        $message = count($pledges) . ' pledges ';
        $message .= $request->search_term ? 'found' : 'fetched';

        return [
            'data' => $pledges,
            'message' => $message . ' successfully',
        ];
    }
}