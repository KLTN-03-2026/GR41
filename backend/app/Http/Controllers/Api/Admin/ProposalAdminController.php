<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\RejectProposalRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProposalAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Document::query()->with(['category', 'tags', 'proposer'])
            ->whereNotNull('proposed_by')
            ->orderByDesc('created_at');

        $status = $request->query('status', 'pending');
        if ($status !== 'all') {
            $q->where('status', $status);
        }

        if ($request->filled('q')) {
            $kw = '%' . $request->query('q') . '%';
            $q->where(fn ($b) => $b->where('title', 'like', $kw)->orWhere('author', 'like', $kw));
        }

        $paginator = $q->paginate((int) $request->query('per_page', 15));

        $pendingCount = Document::query()->whereNotNull('proposed_by')->where('status', 'pending')->count();

        $resource = ApiResponse::paginate($paginator, DocumentResource::class);
        $responseData = json_decode($resource->getContent(), true);
        $responseData['data']['pending_count'] = $pendingCount;

        return response()->json($responseData);
    }

    public function pendingCount(): JsonResponse
    {
        $count = Document::query()->whereNotNull('proposed_by')->where('status', 'pending')->count();

        return ApiResponse::success(['count' => $count]);
    }

    public function show(int $id): JsonResponse
    {
        $document = Document::query()
            ->with(['category', 'tags', 'proposer', 'reviewer'])
            ->whereNotNull('proposed_by')
            ->findOrFail($id);

        return ApiResponse::success(new DocumentResource($document));
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $admin = $request->user();
        $document = Document::query()->whereNotNull('proposed_by')->where('status', 'pending')->findOrFail($id);

        $document->update([
            'status' => 'published',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        $this->notifyProposer($document, 'approved');

        return ApiResponse::success(new DocumentResource($document->fresh()->load(['category', 'tags', 'proposer'])), 'Đã duyệt tài liệu');
    }

    public function reject(RejectProposalRequest $request, int $id): JsonResponse
    {
        $admin = $request->user();
        $document = Document::query()->whereNotNull('proposed_by')->where('status', 'pending')->findOrFail($id);

        $document->update([
            'status' => 'rejected',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'rejection_reason' => $request->validated('reason'),
        ]);

        $this->notifyProposer($document, 'rejected');

        return ApiResponse::success(new DocumentResource($document->fresh()->load(['category', 'tags', 'proposer'])), 'Đã từ chối đề xuất');
    }

    private function notifyProposer(Document $document, string $decision): void
    {
        if (! $document->proposed_by) {
            return;
        }

        if ($decision === 'approved') {
            $title = 'Đề xuất tài liệu được duyệt';
            $content = "Tài liệu \"{$document->title}\" của bạn đã được Admin duyệt và xuất hiện công khai trên hệ thống.";
        } else {
            $title = 'Đề xuất tài liệu bị từ chối';
            $content = "Tài liệu \"{$document->title}\" của bạn đã bị từ chối.\n\nLý do: {$document->rejection_reason}";
        }

        Notification::create([
            'user_id' => $document->proposed_by,
            'title' => $title,
            'content' => $content,
            'type' => 'proposal_' . $decision,
            'is_read' => false,
            'data' => $decision === 'approved'
                ? ['url' => '/documents/' . $document->slug, 'document_slug' => $document->slug]
                : null,
        ]);
    }
}
