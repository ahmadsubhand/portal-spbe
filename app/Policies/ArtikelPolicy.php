<?php

namespace App\Policies;

use App\Models\Artikel;
use App\Models\User;

class ArtikelPolicy
{
    public function viewAnyInternal(User $user): bool
    {
        return $user->isAdmin() || $user->isAsn();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isAsn();
    }

    public function update(User $user, Artikel $artikel): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isAsn()
            && $artikel->user_id === $user->id
            && ! $artikel->isPublished()
            && ! $artikel->isApproved();
    }

    public function submit(User $user, Artikel $artikel): bool
    {
        return $user->isAsn()
            && $artikel->user_id === $user->id
            && $artikel->is_draft;
    }

    public function delete(User $user, Artikel $artikel): bool
    {
        return $user->isAdmin() || ($user->isAsn()
            && $artikel->user_id === $user->id
            && $artikel->is_draft);
    }

    public function review(User $user): bool
    {
        return $user->isAdmin();
    }

    public function publish(User $user, Artikel $artikel): bool
    {
        return $user->isAdmin() && $artikel->isApproved() && ! $artikel->isPublished();
    }

    public function unpublish(User $user, Artikel $artikel): bool
    {
        return $user->isAdmin() && $artikel->isPublished();
    }
}
