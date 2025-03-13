<?php
// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = UserNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('notifications.index', compact('notifications'));
    }
    
    /**
     * Display unread notifications count
     */
    public function count()
    {
        $user = Auth::user();
        $count = UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
            
        return response()->json(['count' => $count]);
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = UserNotification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'Notifikasi ditandai sebagai telah dibaca.');
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        return redirect()->back()->with('success', 'Semua notifikasi ditandai sebagai telah dibaca.');
    }
    
    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = UserNotification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        $notification->delete();
        
        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
    }
    
    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        $user = Auth::user();
        UserNotification::where('user_id', $user->id)->delete();
            
        return redirect()->back()->with('success', 'Semua notifikasi berhasil dihapus.');
    }
    
    /**
     * Filter notifications by type
     */
    public function filter(Request $request)
    {
        $user = Auth::user();
        $query = UserNotification::where('user_id', $user->id);
        
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }
        
        if ($request->has('read_status')) {
            if ($request->read_status == 'read') {
                $query->where('is_read', true);
            } elseif ($request->read_status == 'unread') {
                $query->where('is_read', false);
            }
        }
        
        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }
}