<?php

namespace App\Policies;

use App\Models\ExportTemplate;
use App\Models\User;

class ExportTemplatePolicy
{
    /**
     * Determine if the user can view the export template.
     */
    public function view(User $user, ExportTemplate $exportTemplate): bool
    {
        return $user->id === $exportTemplate->user_id;
    }

    /**
     * Determine if the user can update the export template.
     */
    public function update(User $user, ExportTemplate $exportTemplate): bool
    {
        return $user->id === $exportTemplate->user_id;
    }

    /**
     * Determine if the user can delete the export template.
     */
    public function delete(User $user, ExportTemplate $exportTemplate): bool
    {
        return $user->id === $exportTemplate->user_id;
    }
}
