<?php
// app/Http/Controllers/ChatController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Events\NewChatMessage;

class ChatController extends Controller
{
    /**
     * Display chat interface
     */
    public function index()
    {
        $user = Auth::user();
        
        // If user is admin, show list of user conversations
        if ($user->role === 'admin') {
            $conversations = User::whereHas('chatMessages', function ($query) {
                $query->where('is_from_admin', false);
            })
            ->withCount(['chatMessages' => function ($query) {
                $query->where('is_read', false)
                      ->where('is_from_admin', false);
            }])
            ->orderByDesc('chat_messages_count')
            ->get();
            
            return view('chat.admin_index', compact('conversations'));
        }
        
        // Regular user view
        $messages = ChatMessage::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Mark all messages from admin as read
        ChatMessage::where('user_id', $user->id)
            ->where('is_from_admin', true)
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        return view('chat.index', compact('messages'));
    }
    
    /**
     * Show admin chat with specific user
     */
    public function adminChat($userId)
    {
        $admin = Auth::user();
        
        // Only admin can access this
        if ($admin->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        $user = User::findOrFail($userId);
        $messages = ChatMessage::where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Mark all messages from user as read
        ChatMessage::where('user_id', $userId)
            ->where('is_from_admin', false)
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        return view('chat.admin_chat', compact('user', 'messages'));
    }
    
    /**
     * Send a new message as user
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);
        
        $user = Auth::user();
        
        // Find an admin to assign to this chat
        $admin = User::where('role', 'admin')->first();
        
        $message = ChatMessage::create([
            'user_id' => $user->id,
            'admin_id' => $admin ? $admin->id : null,
            'message' => $request->message,
            'is_from_admin' => false,
            'is_read' => false,
        ]);
        
        // Broadcast event for real-time updates
        broadcast(new NewChatMessage($message))->toOthers();
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        
        return redirect()->back();
    }
    
    /**
     * Send a new message as admin
     */
    public function sendAdminMessage(Request $request, $userId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);
        
        $admin = Auth::user();
        
        // Only admin can access this
        if ($admin->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $message = ChatMessage::create([
            'user_id' => $userId,
            'admin_id' => $admin->id,
            'message' => $request->message,
            'is_from_admin' => true,
            'is_read' => false,
        ]);
        
        // Broadcast event for real-time updates
        broadcast(new NewChatMessage($message))->toOthers();
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        
        return redirect()->back();
    }
    
    /**
     * Get unread messages count
     */
    public function unreadCount()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            // For admin: count of all unread messages from users
            $count = ChatMessage::where('is_from_admin', false)
                ->where('is_read', false)
                ->count();
        } else {
            // For user: count of unread messages from admin
            $count = ChatMessage::where('user_id', $user->id)
                ->where('is_from_admin', true)
                ->where('is_read', false)
                ->count();
        }
        
        return response()->json(['count' => $count]);
    }
    
    /**
     * Get new messages since last check
     */
    public function getNewMessages(Request $request)
    {
        $user = Auth::user();
        $lastId = $request->last_id ?? 0;
        
        if ($user->role === 'admin' && $request->has('user_id')) {
            // Admin checking for new messages from a specific user
            $messages = ChatMessage::where('user_id', $request->user_id)
                ->where('id', '>', $lastId)
                ->orderBy('created_at', 'asc')
                ->get();
                
            // Mark messages as read
            ChatMessage::where('user_id', $request->user_id)
                ->where('is_from_admin', false)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        } else {
            // User checking for new messages
            $messages = ChatMessage::where('user_id', $user->id)
                ->where('id', '>', $lastId)
                ->orderBy('created_at', 'asc')
                ->get();
                
            // Mark messages as read
            ChatMessage::where('user_id', $user->id)
                ->where('is_from_admin', true)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }
        
        return response()->json(['messages' => $messages]);
    }
}