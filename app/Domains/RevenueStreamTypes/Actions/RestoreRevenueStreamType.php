<?php

declare(strict_types=1);

namespace App\Domains\RevenueStreamTypes\Actions;

use App\Domains\RevenueStreamTypes\RevenueStreamType;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreRevenueStreamType
{
    use AsAction;

    public function authorize(ActionRequest $request): Response
    {
        $user = $request->user();
        
        if ($user->has_general_access)
            return Response::allow();

        return Response::deny('You are unauthorised to perform this action');
    }

    public function handle(array $params): RevenueStreamType
    {
        $revenue_stream_type = RevenueStreamType::withTrashed()->where('id', $params['id'])->first();
        $revenue_stream_type->restore();

        return $revenue_stream_type;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:revenue_stream_types,id'],
        ];
    }

    public function asController(Request $request)
    {
        return $this->handle($request->validated());
    }

    public function jsonResponse(RevenueStreamType $revenue_stream_type, Request $request): array
    {
        return [
            'message' => 'RevenueStreamType restored successfully',
        ];
    }
}
