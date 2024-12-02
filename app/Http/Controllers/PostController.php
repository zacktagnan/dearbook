<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
use App\Libs\Utilities;
use Illuminate\Http\Request;
use App\Traits\ResourcesDeletion;
use App\Traits\StorageManagement;
use App\Notifications\PostCreated;
use App\Notifications\PostDeleted;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;
use App\Http\Resources\PostResource;
use App\Http\Resources\GroupResource;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use DOMDocument;
use Inertia\Response as InertiaResponse;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    use StorageManagement;
    use ResourcesDeletion;

    public function show(User $user, int $id): InertiaResponse
    {
        $post = Post::detail(auth()->id())
            ->findOrFail($id);

        $post->group = $post->group ? new GroupResource($post->group) : null;

        return Inertia::render('Post/Detail', [
            'post' => new PostResource($post),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        $allFilePaths = [];
        $destinationFolder = '';

        try {
            $post = Post::create($request->all());
            $destinationFolder = Utilities::$postRootFolderBaseName . $post->id;

            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $request->attachments ?? [];

            foreach ($files as $file) {
                $path = $file->store($destinationFolder, 'public');
                $allFilePaths[] = $path;
                $post->attachments()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'created_by' => $request->user()->id,
                ]);
            }

            if ($post->group) {
                // Notification::send($post->group->members, new PostCreated($post, $post->group));

                $allMembersExceptPostAuthor = $post->group->members()->where('users.id', '!=', $post->user_id)->get();
                Notification::send($allMembersExceptPostAuthor, new PostCreated($post, $post->group));
            }

            $followers = $post->user->followers;
            Notification::send($followers, new PostCreated($post, null));

            DB::commit();
        } catch (\Exception $e) {
            $this->deleteAlreadyUploadedFiles($allFilePaths);
            $this->deleteFolderIfEmpty($destinationFolder);

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
        $destinationFolder = Utilities::$postRootFolderBaseName . $post->id;

        try {
            // dd($request->all());
            $post->update($request->all());

            // dd($request->deleted_file_ids);
            if ($request->deleted_file_ids) {
                $attachmentsToDelete = $post->attachments()
                    ->whereIn('id', $request->deleted_file_ids)
                    ->get();

                foreach ($attachmentsToDelete as $attachmentToDelete) {
                    $attachmentToDelete->delete();
                }

                $this->deleteFolderIfEmpty($destinationFolder);
            }

            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $request->attachments ?? [];

            foreach ($files as $file) {
                $path = $file->store($destinationFolder, 'public');
                $allFilePaths[] = $path;
                $post->attachments()->create([
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
            $this->deleteAlreadyUploadedFiles($allFilePaths);
            $this->deleteFolderIfEmpty($destinationFolder);

            DB::rollBack();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id, string $to) //: ResponseFactory|RedirectResponse
    {
        $post = Post::withArchived()->findOrFail($id);

        if ($post->isAuthor(auth()->id()) || $post->group && $post->group->isAdminOfTheGroup(auth()->id())) {
            if (!is_null($post->archived_at)) {
                $post->unArchive();
            }

            $post->deleted_by = auth()->id();
            $post->save();
            $post->delete();

            if (!$post->isAuthor(auth()->id())) {
                $post->user->notify(new PostDeleted($post->user, $post->group));
            }

            // return back();
            // // Si no se establece el BACK(), no vale el RedirectResponse
            // // y, entonces, mejor no establecer ningún tipo de RETURN

            if (str_contains($to, 'group')) {
                $groupSlug = explode('_', $to)[1];
                $to = 'group.profile';
                return to_route($to, [
                    'group' => $groupSlug,
                ], Response::HTTP_SEE_OTHER);
            } else if ($to === 'home') {
                return to_route($to, [], Response::HTTP_SEE_OTHER);
                // return url('/');
            } else {
                return to_route('archive-management.index')->with([
                    'success' => [
                        'from' => $to,
                        'message' => 'Publicación enviada a la papelera satisfactoriamente.',
                    ],
                ]);
            }
        }

        return response("You don't have permission to DELETE this post", Response::HTTP_FORBIDDEN);
    }

    public function destroyFromManagementAllSelected(Request $request)
    {
        if ($request->checked_ids) {
            $postsToDelete = Post::withArchived()
                ->whereIn('id', $request->checked_ids)
                ->get();

            foreach ($postsToDelete as $post) {
                $this->applyDeletionFromManagement($post);
            }
        }

        // return back()->with('success', 'Conjunto de publicaciones enviado a la papelera satisfactoriamente.');
        return back()->with([
            'success' => [
                'from' => $request->from,
                'message' => 'Conjunto de publicaciones enviado a la papelera satisfactoriamente.',
            ],
        ]);
    }

    public function destroyFromManagement(int $id, string $from)
    {
        $post = Post::withArchived()->findOrFail($id);
        $this->applyDeletionFromManagement($post);

        // return back()->with('success', 'Publicación enviada a la papelera satisfactoriamente.');
        return back()->with([
            'success' => [
                'from' => $from,
                'message' => 'Publicación enviada a la papelera satisfactoriamente.',
            ],
        ]);
    }

    public function applyDeletionFromManagement(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            return response("You don't have permission to PROCESS the Deletion of this post", Response::HTTP_FORBIDDEN);
        }

        DB::beginTransaction();

        try {
            if (!is_null($post->archived_at)) {
                $post->unArchive();
            }
            $post->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function restoreAllSelected(Request $request)
    {
        if ($request->checked_ids) {
            // $postsToRestore = Post::onlyTrashed()
            //     ->whereIn('id', $request->checked_ids)
            //     ->get();
            $postsToRestore = match ($request->from) {
                'archive' => Post::onlyArchived()
                    ->whereIn('id', $request->checked_ids)
                    ->get(),
                'trash' => Post::onlyTrashed()
                    ->whereIn('id', $request->checked_ids)
                    ->get(),
            };

            foreach ($postsToRestore as $post) {
                $this->applyRestoration($post, $request->from);
            }
        }

        return back();
    }

    public function restore(int $id, string $from)
    {
        // $post = Post::onlyTrashed()->findOrFail($id);
        $post = match ($from) {
            'archive' => Post::onlyArchived()->findOrFail($id),
            'trash' => Post::onlyTrashed()->findOrFail($id),
        };
        $this->applyRestoration($post, $from);

        return back();
    }

    public function applyRestoration(Post $post, string $from)
    {
        if ($this->restorationIsNotAllowed($post, $from)) {
            return response("You don't have permission to PROCESS the Restoration of this post", Response::HTTP_FORBIDDEN);
        }
        match ($from) {
            'archive' => $post->unArchive(),
            'trash' => $post->restore(),
        };

        if ($from === 'trash') {
            $post->deleted_by = null;
            $post->save();
        }
    }

    public function restorationIsNotAllowed(Post $post, string $from): bool
    {
        return match ($from) {
            'archive' => $post->user_id !== auth()->id(),
            'trash' => $post->user_id !== auth()->id() && !$post->group->isAdminOfTheGroup(auth()->id()),
        };
    }

    public function forceDestroyAllSelected(Request $request)
    {
        if ($request->checked_ids) {
            $postsToForceDelete = Post::onlyTrashed()
                ->whereIn('id', $request->checked_ids)
                ->get();

            foreach ($postsToForceDelete as $post) {
                $this->applyForceDeletion($post);
            }
        }

        // return back()->with('success', 'Conjunto de publicaciones y recursos eliminados satisfactoriamente.');
        return back()->with([
            'success' => [
                'from' => $request->from,
                'message' => 'Conjunto de publicaciones y recursos eliminados satisfactoriamente.',
            ],
        ]);
    }

    public function forceDestroy(int $id, string $from)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $this->applyForceDeletion($post);

        // return back()->with('success', 'Publicación y recursos eliminados satisfactoriamente.');
        return back()->with([
            'success' => [
                'from' => $from,
                'message' => 'Publicación y recursos eliminados satisfactoriamente.',
            ],
        ]);
    }

    public function forceDestroyFromDetail(int $id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $this->applyForceDeletion($post);

        // return Redirect::route('home');
        // return redirect()->route('home');
        return to_route('home');
    }

    public function applyForceDeletion(Post $post)
    {
        if ($this->forceDeletionIsNotAllowed($post)) {
            return response("You don't have permission to PROCESS the Total Deletion of this post", Response::HTTP_FORBIDDEN);
        }

        DB::beginTransaction();

        try {
            $this->deleteResources($post);

            $post->forceDelete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function forceDeletionIsNotAllowed(Post $post): bool
    {
        return $post->user_id !== auth()->id() && !$post->group->isAdminOfTheGroup(auth()->id());
    }

    private function deleteResources(Post $post): void
    {
        $this->processingDeleteResources(new Post, $post->id);
    }

    public function archiveAllSelected(Request $request)
    {
        if ($request->checked_ids) {
            $postsToArchive = match ($request->from) {
                'activity_log' => Post::whereIn('id', $request->checked_ids)
                    ->get(),
                'trash' => Post::onlyTrashed()
                    ->whereIn('id', $request->checked_ids)
                    ->get(),
            };

            foreach ($postsToArchive as $post) {
                $this->applyArchivation($post);
            }
        }

        // return back();
        return back()->with([
            'success' => [
                'from' => $request->from,
                'message' => 'Conjunto de publicaciones archivado satisfactoriamente.',
            ],
        ]);
    }

    public function archive(int $id, string $from)
    {
        $post = Post::withTrashed()->findOrFail($id);
        $this->applyArchivation($post);

        if ($from === 'home') {
            return back();
        } else if ($from === 'detail') {
            return to_route('home');
        } else {
            return back()->with([
                'success' => [
                    'from' => $from,
                    'message' => 'Publicación archivada satisfactoriamente.',
                ],
            ]);
        }
    }

    public function applyArchivation(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            return response("You don't have permission to PROCESS the Archivation of this post", Response::HTTP_FORBIDDEN);
        }
        if (!is_null($post->deleted_at)) {
            $post->restore();
        }
        $post->archive();
    }

    public function generateContent(Request $request)
    {
        // :: Proceso hacia OpenAI ::
        // ----------------------------------------------------
        // $prompt = $request->get('prompt');
        // $content = 'Please, generate social media post content based on the following prompt:'
        //     . PHP_EOL . PHP_EOL . $prompt;

        // $result = OpenAI::chat()->create([
        //     'model' => 'gpt-3.5-turbo',
        //     'messages' => [
        //         ['role' => 'user', 'content' => $content],
        //     ],
        // ]);

        // // echo $result->choices[0]->message->content; // Hello! How can I assist you today?
        // return response([
        //     'content' => $result->choices[0]->message->content,
        // ]);

        // :: Proceso de Respuesta Fija ::
        // ----------------------------------------------------
        return response([
            'content' => '🚀 ¡Lanzamos DearBook, nuestra nueva app de red social! Desarrollada con Laravel y Vue.js para una experiencia increíble. 📱✨ Conéctate, comparte y descubre más. ¡Únete a nosotros! #DearBook #Laravel #VueJS #RedSocial #Tecnología #Innovación #App',
        ]);
    }

    public function fetchUrlPreview(Request $request)
    {
        $url = $request->url;
        $ogTags = [];

        $html = file_get_contents($url); // string o false

        if ($html !== false) {
            // Reemplazar entidades mal formadas
            $html = preg_replace('/&([^;]+)(?![a-zA-Z0-9#])/s', '&amp;$1', $html);
            $html = preg_replace('/&(?!(amp|lt|gt|quot|#x[0-9A-Fa-f]+|#\d+);)/s', '&amp;', $html);

            // Forzar la codificación a UTF-8 si no está en UTF-8
            if (!mb_detect_encoding($html, 'UTF-8', true)) {
                $html = mb_convert_encoding($html, 'UTF-8', 'auto');
            }
            $dom = new DOMDocument();

            // Suprimiendo warnings de un malformado HTML
            libxml_use_internal_errors(true);
            // $dom->loadHTML($html);
            // Forzar la codificación al cargar el HTML
            $dom->loadHTML('<?xml encoding="UTF-8"?>' . $html);

            // $errors = libxml_get_errors();
            // foreach ($errors as $error) {
            //     echo "Error: " . $error->message . "\n";
            // }
            // libxml_clear_errors();

            // Restaurando la captura de errores a su estado anterior
            libxml_use_internal_errors(false);

            $metaTags = $dom->getElementsByTagName('meta');
            foreach ($metaTags as $tag) {
                /** @var DOMElement $tag */
                $property = $tag->getAttribute('property');
                if (str_starts_with($property, 'og:')) {
                    // $ogTags[$property] = $tag->getAttribute('content');
                    $ogTags[$property] = mb_convert_encoding($tag->getAttribute('content'), 'UTF-8', 'auto');
                }
            }
        }

        return $ogTags;
    }

    public function pinUnpin(Request $request, Post $post)
    {
        if ($post->isAuthor(auth()->id()) || $post->group && $post->group->isAdminOfTheGroup(auth()->id())) {
            if (!$post->is_pinned) {
                if ($post->group) {
                    Post::query()->where('group_id', $post->group->id)->update([
                        'is_pinned' => false,
                    ]);
                }
                Post::query()->where('user_id', auth()->id())->update([
                    'is_pinned' => false,
                ]);
            }

            $post->is_pinned = !$post->is_pinned;
            $post->save();

            return back()->with('success', trans_choice('dearbook/post/notify.pinned_state.web', ($post->is_pinned ? '1' : '0')));
        }

        return response("You don't have permission to PROCESS the Pinned/Unpinned of this post", Response::HTTP_FORBIDDEN);
    }
}
