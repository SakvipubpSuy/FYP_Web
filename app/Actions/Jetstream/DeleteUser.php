<?php

namespace App\Actions\Jetstream;

use App\Models\Admin;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     */
    public function delete(Admin $admin): void
    {
        $admin->deleteProfilePhoto();
        $admin->tokens->each->delete();
        $admin->delete();
    }
}
