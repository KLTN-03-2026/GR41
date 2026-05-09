<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProposalRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProposalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $q = Document::query()
            ->with(['category', 'tags'])
            ->where('proposed_by', $user->id)
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $q->where('status', $request->query('status'));
        }

        $paginator = $q->paginate((int) $request->query('per_page', 15));

        return ApiResponse::paginate($paginator, DocumentResource::class);
    }

    public function store(StoreProposalRequest $request): JsonResponse
    {
        $data = $request->validated();
        $tags = $data['tags'] ?? [];
        unset($data['tags']);

        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['proposed_by'] = $request->user()->id;
        $data['status'] = 'pending';

        $document = Document::create($data);
        $document->tags()->sync($tags);

        return ApiResponse::success(
            new DocumentResource($document->load(['category', 'tags'])),
            'Đề xuất đã được gửi, vui lòng chờ Admin duyệt',
            201
        );
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $document = Document::query()
            ->where('proposed_by', $user->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $document->delete();

        return ApiResponse::success(null, 'Đã xóa đề xuất');
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title, '-', 'vi') ?: 'tai-lieu';
        $slug = $base;
        $i = 1;
        while (Document::withTrashed()->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $i++;
        }

        return substr($slug, 0, 280);
    }
}
