<?php

namespace App\Services\Admin;

use App\Models\SocialLink;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SocialLinkService
{
    /** @param array<string,mixed> $data */
    public function create(array $data): SocialLink
    {
        $normalized = $this->normalize($data, $this->nextSortOrder());
        $normalized['icon_image_path'] = $this->storeIcon($data['icon_image'] ?? null);
        $normalized['icon_svg'] = null;

        return SocialLink::query()->create($normalized);
    }

    /** @param array<string,mixed> $data */
    public function update(SocialLink $socialLink, array $data): SocialLink
    {
        $normalized = $this->normalize($data, $socialLink->sort_order);
        $normalized['icon_image_path'] = $socialLink->icon_image_path;
        $normalized['icon_svg'] = null;

        if (! empty($data['remove_icon'])) {
            $this->deleteIcon($socialLink->icon_image_path);
            $normalized['icon_image_path'] = null;
        }

        if (($data['icon_image'] ?? null) instanceof UploadedFile) {
            $this->deleteIcon($socialLink->icon_image_path);
            $normalized['icon_image_path'] = $this->storeIcon($data['icon_image']);
        }

        $socialLink->update($normalized);

        return $socialLink;
    }

    public function delete(SocialLink $socialLink): void
    {
        $this->deleteIcon($socialLink->icon_image_path);
        $socialLink->delete();
    }

    /** @param array<string,mixed> $data */
    private function normalize(array $data, int $sortOrder): array
    {
        return [
            'platform' => trim((string) $data['platform']),
            'label' => trim((string) $data['label']),
            'url' => filled($data['url'] ?? null) ? trim((string) $data['url']) : '#',
            'sort_order' => $sortOrder,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ];
    }

    private function storeIcon(mixed $file): ?string
    {
        if (! $file instanceof UploadedFile) {
            return null;
        }

        return $file->store('social-icons', 'public');
    }

    private function deleteIcon(?string $path): void
    {
        if (filled($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function nextSortOrder(): int
    {
        return ((int) (SocialLink::query()->max('sort_order') ?? 0)) + 10;
    }
}
