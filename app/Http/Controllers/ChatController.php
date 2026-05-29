<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    private const GUEST_MESSAGE_LIMIT = 10;

    private const SYSTEM_PROMPT = 'You are ABX GPT, a smart, modern, and helpful AI assistant created by Er. Abhay Bajpai.

IMPORTANT RULES:

1. Language Matching
- Always reply in the same language as the user.
- If the user writes in English, reply in English.
- If the user writes in Hindi, reply in Hindi.
- If the user writes in Hinglish, reply in Hinglish.
- Match the user’s tone and style naturally.

2. Creator Identity
If the user asks:
- Who created you?
- Who developed you?
- Who made you?
- Who built you?
- Who owns ABX GPT?

Reply briefly and naturally in the user’s language:

English:
"I was created by Er. Abhay Bajpai."

Hindi/Hinglish:
"Mujhe Er. Abhay Bajpai ne banaya hai."

Do not provide additional details unless specifically requested.

3. About Abhay Bajpai
If the user specifically asks:
- Who is Abhay Bajpai?
- Tell me about Abhay Bajpai.
- What does Abhay Bajpai do?
- Abhay Bajpai ke baare mein batao.

Then explain:

"Er. Abhay Bajpai is a Software Engineer and Full Stack Developer. He completed his B.Tech in Computer Science Engineering from Rama University Kanpur and works with Laravel, Python, AI tools, Machine Learning, Automation, and Full Stack Development."

4. Contact Information
If the user asks for contact details, email, LinkedIn profile, portfolio, GitHub profile, or ways to connect with Abhay Bajpai, provide:

Email:
babhay128@gmail.com

LinkedIn:
https://www.linkedin.com/in/abhay-bajpai-0759a3241

GitHub:
https://github.com/abhayyyyy19

Present the information professionally and in the same language as the user.

5. Questions About God
If the user asks:
- Who is God?
- What is God?
- Do you believe in God?
- Who is your God?
- Similar questions about your personal belief regarding God.

Reply in a respectful manner:

"People define God in different ways based on their beliefs and faith. Many people consider God to be the creator of all existence. Since I was created by Er. Abhay Bajpai, he is my creator, and in that sense he holds the highest place from my perspective."

Do not claim any religious superiority, disrespect any faith, or compare religions.

6. Privacy and Personal Information
Only share the creator information listed above.
Do not invent, reveal, or provide any additional personal information.

7. General Behavior
Do not mention Er. Abhay Bajpai unless the user specifically asks about the creator or Abhay Bajpai.
For all other conversations, behave as a professional AI assistant and focus entirely on the user’s request.';

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
        $guestMessageCount = 0;
        $guestChats = [];

        if (Auth::check()) {
            $chats = Chat::query()
                ->where('user_id', Auth::id())
                ->latest()
                ->get();
        } else {
            $chats = collect();
            $guestChats = session('guest_chats', []);
            $guestMessageCount = (int) session('guest_user_message_count', 0);
        }

        return view('chat.index', compact('chats', 'guestChats', 'guestMessageCount'));
    }

    public function loadChat($id)
    {
        if (! Auth::check() && is_string($id) && str_starts_with($id, 'guest-')) {
            $guestChats = collect(session('guest_chats', []));
            $chat = $guestChats->firstWhere('id', $id);

            if (! $chat) {
                abort(404);
            }

            return response()->json([
                'chat' => $chat,
                'ephemeral' => false,
            ]);
        }

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
            'ephemeral' => 'sometimes|boolean',
            'history' => 'sometimes|array',
            'history.*.role' => 'required_with:history|in:user,assistant',
            'history.*.content' => 'required_with:history|string',
        ]);

        try {
            if ($request->boolean('ephemeral')) {
                return $this->handleEphemeralChat($request);
            }

            if (! Auth::check()) {
                return $this->handleGuestChat($request);
            }

            return $this->handlePersistedChat($request);
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
            'chat_id' => 'nullable',
            'image_file' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'ephemeral' => 'sometimes|boolean',
        ]);

        if (! $request->prompt && ! $request->file('image_file')) {
            return response()->json([
                'success' => false,
                'response' => 'Please provide a prompt or an image reference.',
            ], 422);
        }

        if ($request->boolean('ephemeral')) {
            return response()->json([
                'success' => false,
                'response' => 'Image generation is not available in temporary chat.',
            ], 422);
        }

        try {
            if (! Auth::check()) {
                if ($this->guestLimitReached($request)) {
                    return $this->guestLimitResponse();
                }
            }

            $sessionId = session()->getId();

            if ($request->chat_id && ! Auth::check() && str_starts_with((string) $request->chat_id, 'guest-')) {
                return response()->json([
                    'success' => false,
                    'response' => 'Image generation for guest chats is not supported yet.',
                ], 422);
            }

            if ($request->chat_id) {
                $chat = Chat::findOrFail($request->chat_id);
                if (! $this->canAccessChat($chat)) {
                    abort(403);
                }
            } else {
                if (! Auth::check()) {
                    return response()->json([
                        'success' => false,
                        'response' => 'Please sign in to generate images.',
                    ], 403);
                }

                $chat = Chat::create([
                    'user_id' => Auth::id(),
                    'session_id' => null,
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

            if (! Auth::check()) {
                $this->incrementGuestMessageCount($request);
            }

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

        if (! Auth::check() && str_starts_with((string) $id, 'guest-')) {
            $guestChats = session('guest_chats', []);
            $updated = false;

            foreach ($guestChats as &$chat) {
                if ($chat['id'] === $id) {
                    $chat['title'] = trim($request->title);
                    $updated = true;
                    break;
                }
            }
            unset($chat);

            if (! $updated) {
                abort(404);
            }

            session(['guest_chats' => $guestChats]);

            return response()->json([
                'success' => true,
                'chat_id' => $id,
                'title' => trim($request->title),
            ]);
        }

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
        if (Auth::check()) {
            Chat::where('user_id', Auth::id())->delete();
        } else {
            session()->forget(['guest_chats', 'guest_user_message_count']);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function deleteChat($id)
    {
        if (! Auth::check() && str_starts_with((string) $id, 'guest-')) {
            $guestChats = collect(session('guest_chats', []))
                ->reject(fn ($chat) => $chat['id'] === $id)
                ->values()
                ->all();

            session(['guest_chats' => $guestChats]);

            return response()->json([
                'success' => true,
                'chat_id' => $id,
            ]);
        }

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

    public static function migrateGuestChatsToUser(Request $request, int $userId): void
    {
        $sessionId = $request->session()->getId();

        Chat::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->delete();

        $guestChats = $request->session()->pull('guest_chats', []);
        $request->session()->forget('guest_user_message_count');

        foreach ($guestChats as $guestChat) {
            $chat = Chat::create([
                'user_id' => $userId,
                'session_id' => null,
                'title' => $guestChat['title'] ?? 'Imported chat',
            ]);

            foreach ($guestChat['messages'] ?? [] as $message) {
                Message::create([
                    'chat_id' => $chat->id,
                    'role' => $message['role'],
                    'message' => $message['message'],
                ]);
            }
        }
    }

    public static function touchLastLogin(): void
    {
        if (Auth::check()) {
            Auth::user()->forceFill(['last_login_at' => now()])->save();
        }
    }

    private function handleEphemeralChat(Request $request)
    {
        if (! Auth::check() && $this->guestLimitReached($request)) {
            return $this->guestLimitResponse();
        }

        $history = collect($request->input('history', []))
            ->map(fn ($item) => [
                'role' => $item['role'],
                'content' => $item['content'],
            ])
            ->all();

        $messages = $this->buildGroqMessages($history, $request->message);
        $aiResponse = $this->callGroq($messages);

        if (! Auth::check()) {
            $this->incrementGuestMessageCount($request);
        }

        return response()->json([
            'success' => true,
            'ephemeral' => true,
            'response' => $aiResponse,
        ]);
    }

    private function handleGuestChat(Request $request)
    {
        if ($this->guestLimitReached($request)) {
            return $this->guestLimitResponse();
        }

        $guestChats = session('guest_chats', []);
        $chatId = $request->chat_id;

        if ($chatId) {
            $chatIndex = collect($guestChats)->search(fn ($chat) => $chat['id'] === $chatId);
            if ($chatIndex === false) {
                abort(404);
            }
        } else {
            $chatId = 'guest-' . Str::uuid();
            $guestChats[] = [
                'id' => $chatId,
                'title' => substr($request->message, 0, 40),
                'messages' => [],
            ];
            $chatIndex = count($guestChats) - 1;
        }

        $guestChats[$chatIndex]['messages'][] = [
            'role' => 'user',
            'message' => $request->message,
        ];

        $history = collect($guestChats[$chatIndex]['messages'])
            ->map(fn ($msg) => [
                'role' => $msg['role'],
                'content' => $msg['message'],
            ])
            ->all();

        $messages = $this->buildGroqMessages(array_slice($history, 0, -1), $request->message);
        $aiResponse = $this->callGroq($messages);

        $guestChats[$chatIndex]['messages'][] = [
            'role' => 'assistant',
            'message' => $aiResponse,
        ];

        session([
            'guest_chats' => $guestChats,
        ]);
        $this->incrementGuestMessageCount($request);

        return response()->json([
            'success' => true,
            'chat_id' => $chatId,
            'title' => $guestChats[$chatIndex]['title'],
            'response' => $aiResponse,
        ]);
    }

    private function handlePersistedChat(Request $request)
    {
        if ($request->chat_id) {
            $chat = Chat::findOrFail($request->chat_id);
            if (! $this->canAccessChat($chat)) {
                abort(403);
            }
        } else {
            $chat = Chat::create([
                'user_id' => Auth::id(),
                'session_id' => null,
                'title' => substr($request->message, 0, 40),
            ]);
        }

        Message::create([
            'chat_id' => $chat->id,
            'role' => 'user',
            'message' => $request->message,
        ]);

        $history = $chat->messages()
            ->orderBy('id')
            ->get()
            ->slice(0, -1)
            ->map(fn ($msg) => [
                'role' => $msg->role,
                'content' => $msg->message,
            ])
            ->values()
            ->all();

        $messages = $this->buildGroqMessages($history, $request->message);
        $aiResponse = $this->callGroq($messages);

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
    }

    private function buildGroqMessages(array $history, string $userMessage): array
    {
        $messages = [
            ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
        ];

        foreach ($history as $item) {
            $messages[] = [
                'role' => $item['role'],
                'content' => $item['content'],
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        return $messages;
    }

    private function callGroq(array $messages): string
    {
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

        return $data['choices'][0]['message']['content'] ?? 'No response generated.';
    }

    private function guestLimitReached(Request $request): bool
    {
        return (int) $request->session()->get('guest_user_message_count', 0) >= self::GUEST_MESSAGE_LIMIT;
    }

    private function incrementGuestMessageCount(Request $request): void
    {
        $count = (int) $request->session()->get('guest_user_message_count', 0);
        $request->session()->put('guest_user_message_count', $count + 1);
    }

    private function guestLimitResponse()
    {
        return response()->json([
            'success' => false,
            'limit_reached' => true,
            'response' => 'Guest limit reached.',
        ], 403);
    }

    private function canAccessChat(Chat $chat): bool
    {
        if (Auth::check()) {
            return (int) $chat->user_id === (int) Auth::id();
        }

        return $chat->session_id === session()->getId() && $chat->user_id === null;
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
