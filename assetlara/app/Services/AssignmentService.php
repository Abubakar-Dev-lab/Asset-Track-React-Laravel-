<?php

namespace App\Services;

use Exception;
use App\Models\Asset;
use App\Models\Assignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\AssetAssigned;
use App\Events\AssetReturned;

class AssignmentService
{
    /**
     * Handles the logic to check out an Asset to a User.
     * @throws Exception if business rules are violated.
     */
    public function assignAsset(Asset $asset, int $userId): Assignment
    {


        // 🟢 1. BUSINESS RULE CHECK
        if ($asset->status !== 'available') {
            throw new Exception("Asset is currently '{$asset->status}'. Cannot assign.");
        }

        // 🟢 2. DATABASE TRANSACTION (CRITICAL: Prevents Data Corruption)
        return DB::transaction(function () use ($asset, $userId) {
            
            // 2a. Create the History Log (The new record)
            $assignment = Assignment::create([
                'asset_id' => $asset->id,
                'user_id' => $userId,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
            ]);

            // 2b. Update the Asset State (The Lock)
            $asset->update(['status' => 'assigned']);

            // 🟢 Dispatch event for notifications, logs, etc.
            AssetAssigned::dispatch($assignment);

            return $assignment;
        });
    }

    /**
     * Handles the logic to check in an Asset.
     * @throws Exception if no active assignment is found.
     */
    public function returnAsset(Asset $asset): Assignment
    {
        // 1. Find the active assignment (where returned_at is NULL)
        $activeAssignment = $asset->assignments()
            ->whereNull('returned_at')
            ->first();

        if (!$activeAssignment) {
            throw new Exception("Asset is not currently checked out.");
        }

        // 2. Database Transaction for safe update
        return DB::transaction(function () use ($asset, $activeAssignment) {

            // Update the History Log (The End Date)
            $activeAssignment->update([
                'returned_at' => now(),
            ]);

            // Update the Asset State (The Unlock)
            $asset->update(['status' => 'available']);

            // 🟢 Dispatch event for notifications, logs, etc.
            AssetReturned::dispatch($activeAssignment);

            return $activeAssignment;
        });
    }
}
