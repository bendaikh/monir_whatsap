<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;

class StorePolicy
{
    public function view(User $user, Store $store): bool
    {
        return $user->id === $store->user_id || $user->role === 'superadmin';
    }

    public function update(User $user, Store $store): bool
    {
        return $user->id === $store->user_id || $user->role === 'superadmin';
    }

    public function delete(User $user, Store $store): bool
    {
        return $user->id === $store->user_id || $user->role === 'superadmin';
    }
}
