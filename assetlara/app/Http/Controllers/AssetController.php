<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asset;
use App\Models\Category;
use App\Services\AssetService;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;

class AssetController extends Controller
{
    public function __construct(
        protected AssetService $assetService
    ) {
        $this->authorizeResource(Asset::class, 'asset');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assets = Asset::with('category')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('assets.index', compact('assets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        return view('assets.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssetRequest $request)
    {
        $this->assetService->store(
            $request->validated(),
            $request->file('image')
        );

        return redirect()->route('assets.index')
            ->with('success', 'Asset created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        $asset->load('assignments.user', 'category', 'assignments.admin');
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        return view('assets.show', compact('asset', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     * 🟢 NEW
     */
    public function edit(Asset $asset)
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        return view('assets.edit', compact('asset', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $this->assetService->update(
            $asset,
            $request->validated(),
            $request->file('image')
        );

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset details updated successfully!');
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy(Asset $asset)
    {
        if (!$this->assetService->delete($asset)) {
            return redirect()->back()
                ->with('error', 'Cannot delete an asset that is currently assigned. Please return it first.');
        }

        return redirect()->route('assets.index')
            ->with('success', "Asset '{$asset->name}' has been soft deleted.");
    }


    /**
     * Display assets currently held by the logged-in user (Employee View).
     */
    public function myAssets()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $myAssets = $this->assetService->getUserAssets($user);

        return view('assets.my-assets', compact('myAssets'));
    }
}
