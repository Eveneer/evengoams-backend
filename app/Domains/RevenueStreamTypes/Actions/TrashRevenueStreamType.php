<?php

declare(strict_types=1);

namespace App\Domains\RevenueStreamTypes\Actions;

use App\Domains\RevenueStreamTypes\RevenueStreamType;
use Illuminate\Support\Facades\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class TrashRevenueStreamType
{
    use AsAction;

    public function authorize(ActionRequest $request): Response
    {
        $user = $request->user();
        
        if ($user->has_general_access)
            return Response::allow();

        return Response::deny('You are unauthorised to perform this action');
    }

    public function handle(RevenueStreamType $revenue_stream_type): bool
    {
        return $revenue_stream_type->delete();
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:revenue_stream_types,id'],
        ];
    }

    public function asController(RevenueStreamType $revenue_stream_type)
    {
        return $this->handle($revenue_stream_type);
    }

    public function jsonResponse(bool $deleted): array
    {
        $success = $deleted ? 'successful' : 'unsuccessful';

        return [
            'message' => "RevenueStreamType delete $success",
        ];
    }
}
