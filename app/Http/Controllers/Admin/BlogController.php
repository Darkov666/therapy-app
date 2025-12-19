<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogTopic;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Basic authorization check
        if ($user->role !== 'admin' && !$user->can_blog) {
            abort(403, 'No tienes permiso para ver esta sección.');
        }

        $query = BlogPost::query()->with(['author', 'topic']);

        // Scope: Admin sees all, others see own
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $posts = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Blog/Index', [
            'posts' => $posts,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Blog/Create', [
            'topics' => BlogTopic::all(),
            'visibilities' => ['public' => 'Público', 'auth' => 'Usuarios Registrados', 'psychologist' => 'Solo Psicólogos'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'topic_id' => 'nullable|exists:blog_topics,id',
            'new_topic' => 'nullable|string|max:255|required_without:topic_id',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:2048',
            'visibility' => 'required|in:public,auth,psychologist',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'read_time' => 'nullable|string',
        ]);

        // Handle New Topic
        $topicId = $validated['topic_id'];
        if (!$topicId && !empty($validated['new_topic'])) {
            $newTopic = BlogTopic::firstOrCreate(
                ['name' => $validated['new_topic']],
                ['slug' => Str::slug($validated['new_topic'])]
            );
            $topicId = $newTopic->id;
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('blog/covers', 'public');
        }

        $excerpt = $validated['excerpt'];
        if (empty($excerpt)) {
            $cleanContent = strip_tags($validated['content']);
            $excerpt = Str::limit($cleanContent, 150);
        }

        // Determine published_at
        $publishedAt = $validated['published_at'] ?? null;
        if ($validated['is_published'] && !$publishedAt) {
            $publishedAt = now();
        }

        BlogPost::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . uniqid(),
            'content' => $validated['content'],
            'excerpt' => $excerpt,
            'image' => $imagePath,
            'topic_id' => $topicId,
            'visibility' => $validated['visibility'],
            'is_published' => $validated['is_published'] ?? false,
            'published_at' => $publishedAt,
            'read_time' => $validated['read_time'] ?? ceil(str_word_count(strip_tags($validated['content'])) / 200) . ' min',
        ]);

        return redirect()->route('admin.blog.index')->with('success', 'Entrada creada correctamente.');
    }

    public function edit(BlogPost $post)
    {
        $this->authorizePost($post);

        return Inertia::render('Admin/Blog/Edit', [
            'post' => $post,
            'topics' => BlogTopic::all(),
            'visibilities' => ['public' => 'Público', 'auth' => 'Usuarios Registrados', 'psychologist' => 'Solo Psicólogos'],
        ]);
    }

    public function update(Request $request, BlogPost $post)
    {
        $this->authorizePost($post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'topic_id' => 'nullable|exists:blog_topics,id',
            'new_topic' => 'nullable|string|max:255|required_without:topic_id',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:2048',
            'visibility' => 'required|in:public,auth,psychologist',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'read_time' => 'nullable|string',
        ]);

        // Handle New Topic
        $topicId = $validated['topic_id'];
        if (!$topicId && !empty($validated['new_topic'])) {
            $newTopic = BlogTopic::firstOrCreate(
                ['name' => $validated['new_topic']],
                ['slug' => Str::slug($validated['new_topic'])]
            );
            $topicId = $newTopic->id;
        }

        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $post->image = $request->file('image')->store('blog/covers', 'public');
        }

        $excerpt = $validated['excerpt'];
        if (empty($excerpt)) {
            $cleanContent = strip_tags($validated['content']);
            $excerpt = Str::limit($cleanContent, 150);
        }

        // published_at logic
        $publishedAt = $validated['published_at'] ?? $post->published_at;
        // If turning on publish and no date set, update to now. 
        // But user can also set date explicitly.
        // Logic: Trust request 'published_at' if provided.
        // If 'is_published' becomes true and 'published_at' is null, set to now.
        if ($validated['is_published'] && !$post->is_published && !$publishedAt) {
            $publishedAt = now();
        }

        $post->update([
            'title' => $validated['title'],
            'topic_id' => $topicId,
            'content' => $validated['content'],
            'excerpt' => $excerpt,
            'visibility' => $validated['visibility'],
            'is_published' => $validated['is_published'],
            'published_at' => $publishedAt,
            'read_time' => $validated['read_time'] ?? ceil(str_word_count(strip_tags($validated['content'])) / 200) . ' min',
        ]);

        return redirect()->route('admin.blog.index')->with('success', 'Entrada actualizada.');
    }

    public function destroy(BlogPost $post)
    {
        $this->authorizePost($post);

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();

        return redirect()->route('admin.blog.index')->with('success', 'Entrada eliminada.');
    }

    public function uploadEditorImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blog/content', 'public');
            return response()->json(['url' => Storage::url($path)]);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }

    private function authorizePost(BlogPost $post)
    {
        $user = auth()->user();
        if ($user->role === 'admin')
            return true;
        if ($post->user_id !== $user->id) {
            abort(403, 'No tienes permiso para editar esta entrada.');
        }
    }
}
