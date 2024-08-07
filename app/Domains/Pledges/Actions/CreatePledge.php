<?php

declare(strict_types=1);

namespace App\Domains\Pledges\Actions;

use App\Domains\Pledges\Pledge;
use App\Domains\Pledges\Enums\PledgeRecursEnum;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreatePledge
{
    use AsAction;

    public function authorize(ActionRequest $request): Response
    {
        $user = $request->user();
        
        if ($user->has_general_access)
            return Response::allow();

        return Response::deny('You are unauthorised to perform this action');
    }

    public function handle(array $params): Pledge
    {
        return Pledge::create($params);
    }

    public function rules(): array
    {
        return [
            'donor_id' => ['required', 'exists:donors,id'],
            'amount' => ['nullable', 'integer', 'min:0'],
            'recurs' => ['required', 'in:' . implode(',', PledgeRecursEnum::getValues())],
            'due_date' => ['nullable', 'date'],
        ];
    }

    public function asController(Request $request)
    {
        return $this->handle($request->validated());
    }

    public function jsonResponse(Pledge $pledge, Request $request): array
    {
        return [
            'message' => 'Pledge created successfully',
        ];
    }
}
