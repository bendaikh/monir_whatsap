<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireActiveWorkspace
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $activeWorkspaceId = session('active_workspace_id');
            
            if (!$activeWorkspaceId) {
                return redirect()->route('workspaces.dashboard')
                    ->with('warning', 'Please select a workspace to manage first.');
            }
            
            $workspace = \App\Models\Workspace::find($activeWorkspaceId);
            
            if (!$workspace || ($workspace->user_id !== auth()->id() && auth()->user()->role !== 'superadmin')) {
                session()->forget('active_workspace_id');
                session()->forget('active_store_id');
                return redirect()->route('workspaces.dashboard')
                    ->with('error', 'Invalid workspace selection. Please select a valid workspace.');
            }
            
            view()->share('activeWorkspace', $workspace);
        }
        
        return $next($request);
    }
}
