<?php

declare(strict_types=1);

namespace App\Domains\Vendors\Actions;

use App\Domains\Tags\Actions\CreateTags;
use App\Domains\Vendors\Enums\VendorTypesEnum;
use App\Domains\Vendors\Vendor;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateVendor
{
    use AsAction;

    public function authorize(ActionRequest $request): Response
    {
        $user = $request->user();
        
        if ($user && $user->has_general_access)
            return Response::allow();

        return Response::deny('You are unauthorised to perform this action');
    }

    public function handle(string $id, array $params): Vendor
    {
        $vendor = Vendor::findOrFail($id);

        if (isset($params['tags'])) {
            $tag_ids = CreateTags::run($params['tags']);
            unset($params['tags']);
            $vendor->tags()->sync($tag_ids);
        }

        $vendor->update($params);

        return $vendor;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:vendors,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'in:' . implode(',', VendorTypesEnum::asArray())],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'min:3'],
            'contacts' => ['nullable', 'array'],
            'contacts.*.name' => ['nullable', 'string'],
            'contacts.*.phone' => ['nullable', 'string'],
            'contacts.*.email' => ['nullable', 'email'],
        ];
    }

    public function asController(string $id, ActionRequest $request)
    {
        return $this->handle($id, $request->validated());
    }

    public function jsonResponse(Vendor $vendor, Request $request): array
    {
        return [
            'message' => 'Vendor updated successfully',
        ];
    }
}
