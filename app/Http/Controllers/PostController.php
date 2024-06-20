<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostReactionRequest;
use App\Models\Post;
use App\Models\PostAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\PostStoreRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PostUpdateRequest;
use App\Models\PostReaction;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request): RedirectResponse
    {
        // dd($request);
        // dd($request->attachments);

        DB::beginTransaction();

        $allFilePaths = [];

        try {
            $post = Post::create($request->all());

            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $request->attachments ?? [];

            foreach ($files as $file) {
                $path = $file->store('attachments/post-' . $post->id, 'public');
                $allFilePaths[] = $path;
                PostAttachment::create([
                    'post_id' => $post->id,
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'created_by' => $request->user()->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            foreach ($allFilePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            DB::rollBack();
        }

        return back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, Post $post): void
    {
        DB::beginTransaction();

        $allFilePaths = [];

        try {
            // dd($request->all());
            $post->update($request->all());

            // dd($request->deleted_file_ids);
            if ($request->deleted_file_ids) {
                $attachmentsToDelete = PostAttachment::where('post_id', $post->id)
                    ->whereIn('id', $request->deleted_file_ids)
                    ->get();

                foreach ($attachmentsToDelete as $attachmentToDelete) {
                    $attachmentToDelete->delete();
                }
            }

            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $request->attachments ?? [];

            foreach ($files as $file) {
                $path = $file->store('attachments/post-' . $post->id, 'public');
                $allFilePaths[] = $path;
                PostAttachment::create([
                    'post_id' => $post->id,
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'created_by' => $request->user()->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            // dd($e->getMessage());
            foreach ($allFilePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            DB::rollBack();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post) //: ResponseFactory|RedirectResponse
    {
        if ($post->user_id !== auth()->id()) {
            return response("You don't have permission to DELETE this post", Response::HTTP_FORBIDDEN);
        }
        $post->delete();
        return back();
        // Si no se establece el BACK(), no vale el RedirectResponse
        // y, entonces, mejor no establecer ningún tipo de RETURN
    }

    public function downloadAttachment(PostAttachment $attachment)
    {
        // return response()->download(Storage::disk('public')->path($attachment->path), $attachment->name);
        // o, sino,
        return response()->download(storage_path('app/public/' . $attachment->path), $attachment->name);
    }

    public function reaction(PostReactionRequest $request, Post $post): JsonResponse
    {
        // dd('REQUEST', $request->all());

        // if (!$request->current_reaction_type) {
        //     dd('CURRENT_TYPE es NULL');
        // }
        // dd('CURRENT_TYPE', $request->current_reaction_type);

        // if (!$this->reactionExists($post->id)) {

        if (!$request->current_reaction_type) {
            $hasReaction = true;
            $this->createReaction($post->id, $request->reaction_type);
            // PostReaction::create([
            //     'post_id' => $post->id,
            //     'user_id' => auth()->id(),
            //     'type' => $request->reaction_type,
            // ]);
            $type = $request->reaction_type;
        } else {
            if ($request->from_main_reaction_button) {
                if ($this->reactionExists($post->id)) {
                    $hasReaction = false;
                    $this->deleteReaction($post->id);
                    $type = '';
                } else {
                    $hasReaction = true;
                    $this->createReaction($post->id, $request->reaction_type);
                    $type = $request->reaction_type;
                }
            } else {
                $hasReaction = true;
                $this->updateReaction($post->id, $request->reaction_type);
                $type = $request->reaction_type;
            }
        }

        // if ($this->reactionExists($post->id)) {
        //     $hasReaction = false;
        //     $this->deleteReaction($post->id);
        //     $type = '';
        // } else {
        //     $hasReaction = true;
        //     PostReaction::create([
        //         'post_id' => $post->id,
        //         'user_id' => auth()->id(),
        //         'type' => $request->reaction,
        //     ]);
        //     $type = $request->reaction;
        // }

        $reactions = PostReaction::where('post_id', $post->id)->count();

        return response()->json([
            'total_of_reactions' => $reactions,
            'current_user_has_reaction' => $hasReaction,
            'current_user_type_reaction' => $type,
        ], Response::HTTP_OK);
    }

    public function reactionExists(int $postId): bool
    {
        $reaction = PostReaction::where('user_id', auth()->id())
            ->where('post_id', $postId)
            ->first();
        if ($reaction) {
            return true;
        } else {
            return false;
        }
    }

    public function createReaction(int $postId, string $type): void
    {
        PostReaction::create([
            'post_id' => $postId,
            'user_id' => auth()->id(),
            'type' => $type,
        ]);
    }

    public function updateReaction(int $postId, string $type): void
    {
        $reaction = PostReaction::where('user_id', auth()->id())
            ->where('post_id', $postId)
            ->first();

        $reaction->update([
            'type' => $type,
        ]);
    }

    public function deleteReaction(int $postId): void
    {
        $reaction = PostReaction::where('user_id', auth()->id())
            ->where('post_id', $postId)
            ->first();

        $reaction->delete();
    }

    public function allReactionsUsers(Post $post): array
    {
        $reactions = PostReaction::where('post_id', $post->id)
            ->where('user_id', '<>', auth()->id())->get();

        $usersThatReactToPost = $this->getReactionUsers($reactions);

        return $usersThatReactToPost;
    }

    public function typeReactionsUsers(Post $post, string $type): array
    {
        $reactions = PostReaction::where('post_id', $post->id)
            ->where('type', $type)
            ->where('user_id', '<>', auth()->id())->get();

        $usersThatReactToPost = $this->getReactionUsers($reactions);

        return $usersThatReactToPost;
    }

    private function getReactionUsers(Collection $reactions): array
    {
        $usersThatReactToPost = [];
        foreach ($reactions as $reaction) {
            // $usersThatReactToPost[] = $reaction->user->name;
            $usersThatReactToPost[] = [
                'name' => $reaction->user->name,
            ];
        }

        return $usersThatReactToPost;
    }
}
