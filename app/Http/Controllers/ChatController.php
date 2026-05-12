<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function newChatGreeting(Request $request)
    {
        $recentGreetings = collect($request->session()->get('recent_greetings', []));

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Generate one short, friendly welcome line for a new AI chat session. Keep it under 12 words, plain text only, no quotes, no emojis.',
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Give me a fresh unique opening line.',
                    ],
                ],
                'temperature' => 1.1,
                'max_tokens' => 32,
            ]);

            $data = $response->json();
            $greeting = trim((string) ($data['choices'][0]['message']['content'] ?? ''));

            if ($greeting === '' || $recentGreetings->contains($greeting)) {
                $greeting = $this->fallbackGreeting($recentGreetings->all());
            }
        } catch (\Exception $e) {
            $greeting = $this->fallbackGreeting($recentGreetings->all());
        }

        $updatedRecent = $recentGreetings->push($greeting)->take(-20)->values()->all();
        $request->session()->put('recent_greetings', $updatedRecent);

        return response()->json([
            'success' => true,
            'greeting' => $greeting,
        ]);
    }

    public function index()
    {
        $sessionId = session()->getId();

        $chats = Chat::query()
            ->when(
                Auth::check(),
                fn ($query) => $query->where('user_id', Auth::id()),
                fn ($query) => $query->where('session_id', $sessionId)->whereNull('user_id')
            )
            ->latest()
            ->get();

        $guestMessageCount = 0;
        if (! Auth::check()) {
            $guestMessageCount = Message::whereHas('chat', function ($query) use ($sessionId) {
                $query->where('session_id', $sessionId)->whereNull('user_id');
            })->where('role', 'user')->count();
        }

        return view('chat.index', compact('chats', 'guestMessageCount'));
    }

    public function loadChat($id)
    {
        $chat = Chat::with('messages')->findOrFail($id);

        if (! $this->canAccessChat($chat)) {
            abort(403);
        }

        return response()->json([
            'chat' => $chat,
        ]);
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'chat_id' => 'nullable',
        ]);

        try {
            if (! Auth::check()) {
                $sessionId = session()->getId();

                $guestMessageCount = Message::whereHas('chat', function ($query) use ($sessionId) {
                    $query->where('session_id', $sessionId)->whereNull('user_id');
                })->where('role', 'user')->count();

                if ($guestMessageCount >= 10) {
                    return response()->json([
                        'success' => false,
                        'limit_reached' => true,
                        'response' => 'Guest limit reached.',
                    ], 403);
                }
            }

            $sessionId = session()->getId();

            if ($request->chat_id) {
                $chat = Chat::findOrFail($request->chat_id);
                if (! $this->canAccessChat($chat)) {
                    abort(403);
                }
            } else {
                $chat = Chat::create([
                    'user_id' => Auth::id(),
                    'session_id' => Auth::check() ? null : $sessionId,
                    'title' => substr($request->message, 0, 40),
                ]);
            }

            Message::create([
                'chat_id' => $chat->id,
                'role' => 'user',
                'message' => $request->message,
            ]);

            $messages = [];

            $messages[] = [
                'role' => 'system',
                'content' => 'You are ABX GPT, a smart, modern, and helpful AI assistant created by Abhay Bajpai, a Software Engineer and Full Stack Developer from Uttar Pradesh, India. He completed B.Tech in Computer Science Engineering from Rama University Kanpur (2021–2025) with a CGPA of 9.29 and has experience in Laravel, Python, AI tools, machine learning, automation, and full-stack development. IMPORTANT: Only mention Abhay Bajpai or information about the creator when the user specifically asks who created you, who developed you, who owns you, or directly asks about Abhay Bajpai. For normal conversations, behave like a professional AI assistant and do not mention the creator unnecessarily.',
            ];

            foreach ($chat->messages as $msg) {
                $messages[] = [
                    'role' => $msg->role,
                    'content' => $msg->message,
                ];
            }

            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 1024,
            ]);

            $data = $response->json();

            $aiResponse = $data['choices'][0]['message']['content'] ?? 'No response generated.';

            Message::create([
                'chat_id' => $chat->id,
                'role' => 'assistant',
                'message' => $aiResponse,
            ]);

            return response()->json([
                'success' => true,
                'chat_id' => $chat->id,
                'title' => $chat->title,
                'response' => $aiResponse,
            ]);
        } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'response' => $e->getMessage(),
                ], 500);
        }
    }

    public function generateImage(Request $request)
    {
        $request->validate([
            'prompt' => 'nullable|string|max:500',
            'chat_id' => 'nullable|integer',
            'image_file' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        if (! $request->prompt && ! $request->file('image_file')) {
            return response()->json([
                'success' => false,
                'response' => 'Please provide a prompt or an image reference.',
            ], 422);
        }

        try {
            if (! Auth::check()) {
                $sessionId = session()->getId();

                $guestMessageCount = Message::whereHas('chat', function ($query) use ($sessionId) {
                    $query->where('session_id', $sessionId)->whereNull('user_id');
                })->where('role', 'user')->count();

                if ($guestMessageCount >= 10) {
                    return response()->json([
                        'success' => false,
                        'limit_reached' => true,
                        'response' => 'Guest limit reached.',
                    ], 403);
                }
            }

            $sessionId = session()->getId();

            if ($request->chat_id) {
                $chat = Chat::findOrFail($request->chat_id);
                if (! $this->canAccessChat($chat)) {
                    abort(403);
                }
            } else {
                $chat = Chat::create([
                    'user_id' => Auth::id(),
                    'session_id' => Auth::check() ? null : $sessionId,
                    'title' => substr($request->prompt ?? 'Image generation', 0, 40),
                ]);
            }

            Message::create([
                'chat_id' => $chat->id,
                'role' => 'user',
                'message' => $request->prompt ?? 'Image generation request',
            ]);

            $stabilityKey = env('STABILITY_API_KEY');
            if (! $stabilityKey) {
                return response()->json([
                    'success' => false,
                    'response' => 'Stability API key is not configured. Add STABILITY_API_KEY to .env.',
                ], 500);
            }

            $http = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $stabilityKey,
                'Accept' => 'application/json',
            ]);

            if ($request->file('image_file')) {
                $http = $http->attach(
                    'image[]',
                    file_get_contents($request->file('image_file')->getRealPath()),
                    $request->file('image_file')->getClientOriginalName()
                );
            }

            $response = $http->post('https://api.stability.ai/v2beta/stable-image/generate/core', [
                'prompt' => $request->prompt,
                'width' => 768,
                'height' => 768,
                'samples' => 1,
                'steps' => 30,
                'cfg_scale' => 7.0,
            ]);

            if ($response->failed()) {
                $error = $response->json();
                Log::error('Stability image API failed', ['status' => $response->status(), 'response' => $error]);
                return response()->json([
                    'success' => false,
                    'response' => $error['error']['message'] ?? 'Stability API request failed.',
                ], 500);
            }

            $data = $response->json();
            $artifact = $data['artifacts'][0] ?? [];
            $base64 = $artifact['base64'] ?? null;

            if (! $base64) {
                return response()->json([
                    'success' => false,
                    'response' => 'Image generation failed. Please try again.',
                ], 500);
            }

            $imagePath = 'generated-images/stability-' . uniqid() . '.png';
            Storage::disk('public')->put($imagePath, base64_decode($base64));
            $imageUrl = Storage::url($imagePath);

            Message::create([
                'chat_id' => $chat->id,
                'role' => 'assistant',
                'message' => '__IMAGE__:' . $imageUrl,
            ]);

            return response()->json([
                'success' => true,
                'chat_id' => $chat->id,
                'title' => $chat->title,
                'image_url' => $imageUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'response' => $e->getMessage(),
            ], 500);
        }
    }

    public function renameChat(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:120',
        ]);

        $chat = Chat::findOrFail($id);
        if (! $this->canAccessChat($chat)) {
            abort(403);
        }

        $chat->update([
            'title' => trim($request->title),
        ]);

        return response()->json([
            'success' => true,
            'chat_id' => $chat->id,
            'title' => $chat->title,
        ]);
    }

    public function clearChats(Request $request)
    {
        $sessionId = session()->getId();

        $query = Chat::query()
            ->when(
                Auth::check(),
                fn ($query) => $query->where('user_id', Auth::id()),
                fn ($query) => $query->where('session_id', $sessionId)->whereNull('user_id')
            );

        $query->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function deleteChat($id)
    {
        $chat = Chat::findOrFail($id);
        if (! $this->canAccessChat($chat)) {
            abort(403);
        }

        $chat->delete();

        return response()->json([
            'success' => true,
            'chat_id' => (int) $id,
        ]);
    }

    private function canAccessChat(Chat $chat): bool
    {
        if (Auth::check()) {
            return (int) $chat->user_id === (int) Auth::id();
        }

        return $chat->session_id === session()->getId() && $chat->user_id === null;
    }

    public static function migrateGuestChatsToUser(string $sessionId, int $userId): void
    {
        Chat::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->update([
                'user_id' => $userId,
                'session_id' => null,
            ]);
    }

    private function fallbackGreeting(array $recentGreetings): string
    {
        $hour = (int) now()->format('H');
        $timeGreeting = $hour < 12
            ? 'Good morning, what would you like to start with?'
            : ($hour < 17
                ? 'Good afternoon, what should we tackle first?'
                : 'Good evening, what are we focusing on tonight?');

        $pool = [
            'How can I help you today?',
            'What can I do for you right now?',
            "What's the agenda today?",
            'Ready when you are. Ask me anything.',
            'Need a quick plan or full deep dive?',
            'Tell me what you want to build.',
            'Want help with code, writing, or ideas?',
            'Drop your goal and let us get moving.',
            'What should we solve first?',
            'I am here. What are we working on?',
            'Share your task and I will assist.',
            'Let us turn your idea into action.',
            'What would you like to improve today?',
            'Need brainstorming or a concrete solution?',
            'Start with a question and we will go step by step.',
            $timeGreeting,
        ];

        $available = array_values(array_diff($pool, $recentGreetings));
        if (empty($available)) {
            $available = $pool;
        }

        return $available[array_rand($available)];
    }
}