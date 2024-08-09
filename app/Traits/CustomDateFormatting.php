<?php

namespace App\Traits;

trait CustomDateFormatting
{
    /**
     * If date is from the current year
     *      7 de abril
     * else
     *      7 de abril de 2011
     *
     * @return string
     */
    public function createdAtWithFormat(): string
    {
        if ($this->created_at->isoFormat('Y') === date('Y')) {
            $dateWithFormat = $this->created_at->translatedFormat(config('app.format.' . app()->getLocale() . '.datetime.without_year'));
        } else {
            $dateWithFormat = $this->created_at->translatedFormat(config('app.format.' . app()->getLocale() . '.datetime.with_year'));
        }

        return $dateWithFormat;
    }

    /**
     * Example: 11 de agosto 2024
     *
     * @return string
     */
    public function createdAtWithoutTimeAndWeekDay(): string
    {
        return $this->created_at->isoFormat('LL');
    }

    /**
     * Example: 11 de agosto 2024
     *
     * @return string
     */
    public function createdAtOnlyTime(): string
    {
        // return $this->created_at->isoFormat('LL');
        return $this->created_at->format('h:m a');
    }

    /**
     * Example: domingo, 11 de agosto de 2024 11:28
     *
     * @return string
     */
    public function createdAtWithLargeFormat()
    {
        return $this->created_at->isoFormat('LLLL');
    }

    /**
     * Example: dom., 11 de ago. de 2024 11:28
     *
     * @return string
     */
    public function createdAtWithLargeFormatAndSummaryDate(): string
    {
        return $this->created_at->isoFormat('llll');
    }

    /**
     * Example: 1sem (hace una semana)
     *
     * @return string
     */
    public function createdAtShortAbsDiffForHumans(): string
    {
        return $this->created_at->shortAbsoluteDiffForHumans();
    }

    /**
     * Example: hace una semana
     *
     * @return string
     */
    public function createdAtDiffForHumans(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function updatedAtWithLargeFormat(): string
    {
        return $this->updated_at->isoFormat('LLLL');
    }
}
