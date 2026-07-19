<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;

class TaxonomyService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {
    }

    public function createCategory(array $data): Category
    {
        $category = Category::query()->create([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug(Category::class, $data['name']),
            'description' => $data['description'] ?? null,
        ]);

        $this->auditLogService->record(auth()->user(), 'category.created', Category::class, $category->id);

        return $category;
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $category->update([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug(Category::class, $data['name'], $category->id),
            'description' => $data['description'] ?? null,
        ]);

        $this->auditLogService->record(auth()->user(), 'category.updated', Category::class, $category->id);

        return $category->refresh();
    }

    public function createTag(array $data): Tag
    {
        $tag = Tag::query()->create([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug(Tag::class, $data['name']),
        ]);

        $this->auditLogService->record(auth()->user(), 'tag.created', Tag::class, $tag->id);

        return $tag;
    }

    public function updateTag(Tag $tag, array $data): Tag
    {
        $tag->update([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug(Tag::class, $data['name'], $tag->id),
        ]);

        $this->auditLogService->record(auth()->user(), 'tag.updated', Tag::class, $tag->id);

        return $tag->refresh();
    }

    protected function uniqueSlug(string $modelClass, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base !== '' ? $base : 'item';
        $counter = 1;

        while (
            $modelClass::query()
                ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = sprintf('%s-%d', $base !== '' ? $base : 'item', $counter);
            $counter++;
        }

        return $slug;
    }
}
