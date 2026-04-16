<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WorkspaceController extends Controller
{
    use AuthorizesRequests;

    public function dashboard()
    {
        $workspaces = auth()->user()->workspaces()
            ->withCount(['stores', 'stores as products_count' => function ($query) {
                $query->join('products', 'stores.id', '=', 'products.store_id');
            }])
            ->latest()
            ->get();
            
        $stats = [
            'total_workspaces' => $workspaces->count(),
            'active_workspaces' => $workspaces->where('is_active', true)->count(),
            'total_stores' => $workspaces->sum('stores_count'),
        ];
        
        $currentWorkspaceId = session('active_workspace_id');
        
        return view('workspaces.dashboard', compact('workspaces', 'stats', 'currentWorkspaceId'));
    }
    
    public function create()
    {
        return view('workspaces.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $validated['user_id'] = auth()->id();
        
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }
        
        $workspace = Workspace::create($validated);
        
        session(['active_workspace_id' => $workspace->id]);
        session()->forget('active_store_id');
        
        return redirect()->route('workspaces.dashboard')->with('success', 'Workspace created successfully!');
    }
    
    public function edit(Workspace $workspace)
    {
        $this->authorize('update', $workspace);
        
        return view('workspaces.edit', compact('workspace'));
    }
    
    public function update(Request $request, Workspace $workspace)
    {
        $this->authorize('update', $workspace);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $workspace->update($validated);
        
        return redirect()->route('workspaces.dashboard')->with('success', 'Workspace updated successfully!');
    }
    
    public function destroy(Workspace $workspace)
    {
        $this->authorize('delete', $workspace);
        
        $storesCount = $workspace->stores()->count();
        
        if ($storesCount > 0) {
            return redirect()->route('workspaces.dashboard')
                ->with('error', 'Cannot delete workspace with existing stores. Please delete or reassign the stores first.');
        }
        
        if (session('active_workspace_id') == $workspace->id) {
            session()->forget('active_workspace_id');
            session()->forget('active_store_id');
        }
        
        $workspace->delete();
        
        return redirect()->route('workspaces.dashboard')->with('success', 'Workspace deleted successfully!');
    }
    
    public function switch(Workspace $workspace)
    {
        $this->authorize('view', $workspace);
        
        session(['active_workspace_id' => $workspace->id]);
        session()->forget('active_store_id');
        
        return redirect()->route('stores.dashboard')->with('success', 'Switched to workspace: ' . $workspace->name);
    }
}
