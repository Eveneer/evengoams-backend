<?php

declare(strict_types=1);

namespace App\Domains\RevenueStreams\Actions;

use App\Domains\RevenueStreams\RevenueStream;
use App\Domains\RevenueStreamTypes\RevenueStreamType;
use App\Domains\RevenueStreamTypes\Enums\RevenueStreamTypesEnum;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\Response;
use Illuminate\Validation\Validator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;


class CreateRevenueStream
{
    use AsAction;

    public function authorize(ActionRequest $request): Response
    {
        $user = $request->user();
        
        if ($user && $user->has_general_access)
            return Response::allow();

        return Response::deny('You are unauthorised to perform this action');
    }

    public function handle(array $params): RevenueStream
    {
        return RevenueStream::create($params);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type_id' => ['required', 'exists:revenue_stream_types,id'],
            'values' => ['required', 'array'],
        ];
    }

    public function withValidator(Validator $validator, ActionRequest $request): void
    {
        $validator->after(function (Validator $validator) use ($request) {
            if ($request->has('type_id')) {
                $type = RevenueStreamType::find($request->type_id);
                if ($type) {
                    $values = $request->input('values', []);
                    foreach ($values as $value) {
                        $rules[$value['type']] = ['required',
                         'in:' . implode(',', RevenueStreamTypesEnum::getValues())];
                        }
                    }
                }
            });
    }     

    public function asController(Request $request)
    {
        return $this->handle($request->validated());
    }

    public function jsonResponse(
        RevenueStream $revenue_stream, 
        Request $request
    ): array {
        return [
            'message' => 'RevenueStream created successfully',
        ];
    }
}
