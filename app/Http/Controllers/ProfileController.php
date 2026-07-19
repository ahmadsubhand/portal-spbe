<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Requests\StoreAccountDeactivationRequest;
use App\Http\Requests\StoreProfileDocumentRequest;
use App\Models\AccountDeactivationRequest;
use App\Models\ProfileDocument;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user()->load(['pendingDeactivationRequest', 'profileDocuments']),
            'documentTypes' => ProfileDocument::TYPES,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateAvatar(UpdateAvatarRequest $request): RedirectResponse
    {
        $user = $request->user();
        $oldAvatarPath = $user->avatar_path;
        $avatarPath = $request->file('avatar')->store('profile-avatars', 'public');

        $user->update(['avatar_path' => $avatarPath]);

        if ($oldAvatarPath) {
            Storage::disk('public')->delete($oldAvatarPath);
        }

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
    }

    public function destroyAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }

        return Redirect::route('profile.edit')->with('status', 'avatar-deleted');
    }

    public function storeDocument(StoreProfileDocumentRequest $request, AuditLogService $auditLogService): RedirectResponse
    {
        $file = $request->file('document');
        $path = $file->store('profile-documents/'.$request->user()->id, 'local');

        $document = $request->user()->profileDocuments()->create([
            'type' => $request->validated('type'),
            'title' => $request->validated('title'),
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
        $auditLogService->record($request->user(), 'profile_document.created', ProfileDocument::class, $document->id, [
            'type' => $document->type,
        ]);

        return Redirect::route('profile.edit')->with('status', 'document-uploaded');
    }

    public function downloadDocument(Request $request, ProfileDocument $document, AuditLogService $auditLogService)
    {
        $this->authorize('view', $document);
        abort_unless(Storage::disk('local')->exists($document->path), 404);
        $auditLogService->record($request->user(), 'profile_document.downloaded', ProfileDocument::class, $document->id);

        return Storage::disk('local')->download($document->path, $document->original_name);
    }

    public function destroyDocument(Request $request, ProfileDocument $document, AuditLogService $auditLogService): RedirectResponse
    {
        $this->authorize('delete', $document);
        Storage::disk('local')->delete($document->path);
        $documentId = $document->id;
        $document->delete();
        $auditLogService->record($request->user(), 'profile_document.deleted', ProfileDocument::class, $documentId);

        return Redirect::route('profile.edit')->with('status', 'document-deleted');
    }

    public function requestDeactivation(StoreAccountDeactivationRequest $request, AuditLogService $auditLogService): RedirectResponse
    {
        $user = $request->user();

        if ($user->pendingDeactivationRequest()->exists()) {
            return Redirect::route('profile.edit')->withErrors([
                'deactivation' => 'Pengajuan penonaktifan akun Anda masih menunggu keputusan admin.',
            ]);
        }

        $deactivationRequest = $user->deactivationRequests()->create([
            'reason' => $request->validated('reason'),
            'status' => AccountDeactivationRequest::STATUS_PENDING,
        ]);
        $auditLogService->record($user, 'account.deactivation_requested', AccountDeactivationRequest::class, $deactivationRequest->id);

        return Redirect::route('profile.edit')->with('status', 'deactivation-requested');
    }

    public function cancelDeactivationRequest(Request $request, AuditLogService $auditLogService): RedirectResponse
    {
        $deactivationRequest = $request->user()->pendingDeactivationRequest()->firstOrFail();
        $deactivationRequest->update(['status' => AccountDeactivationRequest::STATUS_CANCELLED]);
        $auditLogService->record($request->user(), 'account.deactivation_cancelled', AccountDeactivationRequest::class, $deactivationRequest->id);

        return Redirect::route('profile.edit')->with('status', 'deactivation-cancelled');
    }
}
