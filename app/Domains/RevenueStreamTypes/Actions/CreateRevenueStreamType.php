<?php

declare(strict_types=1);

namespace App\Domains\RevenueStreamTypes\Actions;

use App\Domains\RevenueStreamTypes\Enums\RevenueStreamTypesEnum;
use App\Domains\RevenueStreamTypes\RevenueStreamType;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateRevenueStreamType
{
    use AsAction;

    public function authorize(ActionRequest $request): Response
    {
        $user = $request->user();
        
        if ($user && $user->has_general_access)
            return Response::allow();

        return Response::deny('You are unauthorised to perform this action');
    }

    public function handle(array $params): RevenueStreamType
    {
        return RevenueStreamType::create($params);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'properties' => ['required', 'array'],
            'properties.*.name' => ['required', 'string'],
            'properties.*.type' => ['required', 'in:' . implode(',', RevenueStreamTypesEnum::getValues())],
        ];
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle($request->validated());
    }

    public function jsonResponse(
        RevenueStreamType $revenue_stream_type, 
        Request $request
    ): array {
        return [
            'message' => 'RevenueStreamType created successfully',
        ];
    }
}
