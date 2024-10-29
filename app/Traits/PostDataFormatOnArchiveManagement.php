<?php

namespace App\Traits;

use App\Http\Resources\GroupResource;
use Illuminate\Database\Eloquent\Collection;

trait PostDataFormatOnArchiveManagement
{
    public function processCollection(Collection $collection, string $optionProcess)
    {
        $collection = match ($optionProcess) {
            'only_attachment' => $this->formatOnlyAttachment($collection),
            'only_group' => $this->formatOnlyGroup($collection),
            'attachment_and_group' => $this->formatAttachmentAndGroup($collection),
        };

        return $this->groupData($collection);
    }

    private function formatOnlyAttachment(Collection $collection)
    {
        return $collection->each(function ($item) {
            if ($item->attachments()->count() > 0) {
                $item->attachments = [$item->attachments()->first()];
            } else {
                $item->attachments = [];
            }
        });
    }

    private function formatOnlyGroup(Collection $collection)
    {
        return $collection->each(function ($item) {
            if ($item->group) {
                $item->group = new GroupResource($item->group, false);
                $item->group = $item->group->toArray(request());
            }
        });
    }

    private function formatAttachmentAndGroup(Collection $collection)
    {
        return $collection->each(function ($item) {
            // Al ser attachments una relación de tipo MorphMany, no se puede aplicar el ->isNotEmpty()
            // if ($item->attachments()->isNotEmpty()) {
            if ($item->attachments()->count() > 0) {
                // Asigna solo el primer attachment si existe
                $item->attachments = [$item->attachments()->first()];
            } else {
                // Si no tiene attachments, se asigna null o un arreglo vacío
                $item->attachments = []; // establecer null o []
            }

            if ($item->group) {
                $item->group = new GroupResource($item->group, false);
                $item->group = $item->group->toArray(request());
            }
        });
    }

    private function groupData($collection)
    {
        return $collection->groupBy(fn($item) => $item->createdAtWithoutTimeAndWeekDay());
    }
}
