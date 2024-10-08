<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Prospects;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProspectsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_prospects');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Prospects $prospects): bool
    {
        return $user->can('view_prospects');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_prospects');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Prospects $prospects): bool
    {
        return $user->can('update_prospects');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Prospects $prospects): bool
    {
        return $user->can('delete_prospects');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_prospects');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Prospects $prospects): bool
    {
        return $user->can('force_delete_prospects');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_prospects');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Prospects $prospects): bool
    {
        return $user->can('restore_prospects');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_prospects');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Prospects $prospects): bool
    {
        return $user->can('replicate_prospects');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_prospects');
    }
}
