<?php

namespace App\Policies;

use App\Models\ProfileDocument;
use App\Models\User;

class ProfileDocumentPolicy
{
    public function view(User $user, ProfileDocument $document): bool
    {
        return $user->is($document->user);
    }

    public function delete(User $user, ProfileDocument $document): bool
    {
        return $user->is($document->user);
    }
}
