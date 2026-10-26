<?php

namespace App\Support;

class StatusLabel
{
    /**
     * Canonical status label/colour map shared by the CMS and Herafy badges.
     *
     * Keys are raw database values; values are the Arabic label and the
     * Bootstrap badge classes used to render them.
     *
     * @var array<int|string, array{label: string, class: string}>
     */
    private const STATES = [
        'pending' => ['label' => 'قيد الانتظار', 'class' => 'bg-warning text-dark'],
        'approved' => ['label' => 'معتمد', 'class' => 'bg-success'],
        'rejected' => ['label' => 'مرفوض', 'class' => 'bg-danger'],
        'reviewing' => ['label' => 'قيد المراجعة', 'class' => 'bg-info text-dark'],
        'resolved' => ['label' => 'تم الحل', 'class' => 'bg-success'],
        'accepted' => ['label' => 'مقبول', 'class' => 'bg-primary'],
        'in_progress' => ['label' => 'قيد التنفيذ', 'class' => 'bg-primary'],
        'completed' => ['label' => 'مكتمل', 'class' => 'bg-success'],
        'cancelled' => ['label' => 'ملغى', 'class' => 'bg-danger'],
        'active' => ['label' => 'نشط', 'class' => 'bg-success'],
        'inactive' => ['label' => 'غير نشط', 'class' => 'bg-secondary'],
        'in_active' => ['label' => 'غير نشط', 'class' => 'bg-secondary'],
        1 => ['label' => 'نشط', 'class' => 'bg-success'],
        0 => ['label' => 'غير نشط', 'class' => 'bg-secondary'],
        'blocked' => ['label' => 'محظور', 'class' => 'bg-dark'],
    ];

    /**
     * Resolve a raw status value to its Arabic label and badge classes.
     *
     * Unknown values fall back to a neutral badge so no raw machine value is
     * ever rendered to an end user.
     *
     * @return array{label: string, class: string}
     */
    public static function resolve(int|string|null $status): array
    {
        if ($status === null) {
            return ['label' => 'غير معروف', 'class' => 'bg-secondary'];
        }

        return self::STATES[$status] ?? ['label' => 'غير معروف', 'class' => 'bg-secondary'];
    }

    /**
     * Arabic label for a raw status value.
     */
    public static function label(int|string|null $status): string
    {
        return self::resolve($status)['label'];
    }

    /**
     * Bootstrap badge classes for a raw status value.
     */
    public static function badgeClass(int|string|null $status): string
    {
        return self::resolve($status)['class'];
    }

    /**
     * Bootstrap 5.2 solid badge classes ("text-bg-*") for a raw status value.
     *
     * Derived from the same canonical map so both badge variants stay in sync.
     */
    public static function solidBadgeClass(int|string|null $status): string
    {
        preg_match('/bg-[a-z]+/', self::resolve($status)['class'], $matches);

        return str_replace('bg-', 'text-bg-', $matches[0] ?? 'bg-secondary');
    }
}
