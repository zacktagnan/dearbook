<?php

namespace App\Traits;

trait CustomDateFormatting
{
    public function createdAtWithFormat()
    {
        if ($this->created_at->isoFormat('Y') === date('Y')) {
            $dateWithFormat = $this->created_at->translatedFormat(config('app.format.' . app()->getLocale() . '.datetime.without_year'));
        } else {
            $dateWithFormat = $this->created_at->translatedFormat(config('app.format.' . app()->getLocale() . '.datetime.with_year'));
        }

        return $dateWithFormat;
    }

    public function createdAtWithShortFormat()
    {
        return $this->created_at->isoFormat('llll');
    }

    public function createdAtWithLargeFormat()
    {
        return $this->created_at->isoFormat('LLLL');
    }

    public function createdAtDiffForHumans()
    {
        return $this->created_at->diffForHumans();
    }

    public function updatedAtWithLargeFormat()
    {
        return $this->updated_at->isoFormat('LLLL');
    }
}
