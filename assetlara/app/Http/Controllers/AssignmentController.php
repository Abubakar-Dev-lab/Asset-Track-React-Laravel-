<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Services\AssignmentService;
use App\Http\Requests\AssignAssetRequest;
use Exception;

class AssignmentController extends Controller
{
    protected $service;

    // Inject the Service
    public function __construct(AssignmentService $service)
    {
        $this->service = $service;
    }

    /**
     * Handles the Check-Out (Assign) action.
     * @param Asset $asset The asset is automatically fetched by Route Model Binding.
     */
    public function assign(AssignAssetRequest $request, Asset $asset)
    {
        try {
            // Controller asks the Service to do the work with validated data
            $this->service->assignAsset($asset, $request->user_id);

            return redirect()->route('assets.show', $asset)
                ->with('success', "Asset {$asset->name} assigned successfully!");
        } catch (Exception $e) {
            // Catches exceptions (e.g., "Asset is not available.")
            return redirect()->back()
                ->with('error', 'Assignment failed: ' . $e->getMessage());
        }
    }

    /**
     * Handles the Check-In (Return) action.
     */
    public function return(Asset $asset)
    {
        try {
            $this->service->returnAsset($asset);

            return redirect()->route('assets.show', $asset)
                ->with('success', "Asset {$asset->name} successfully returned to stock.");
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Return failed: ' . $e->getMessage());
        }
    }
}
