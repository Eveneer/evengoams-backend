<?php

declare(strict_types=1);

namespace App\Domains\Users\Actions;

use App\Domains\Users\User;
use Illuminate\Auth\Access\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class TrashUser
{
    use AsAction;

    public function authorize(ActionRequest $request): Response
    {
        $user = $request->user();
        
        if ($user && $user->has_general_access)
            return Response::allow();

        return Response::deny('You are unauthorised to perform this action');
    }

    public function handle(string $id): bool
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }

    public function asController(string $id)
    {
        return $this->handle($id);
    }

    public function jsonResponse(bool $deleted): array
    {
        $success = $deleted ? 'successful' : 'unsuccessful';

        return [
            'message' => "User delete $success",
        ];
    }
}
