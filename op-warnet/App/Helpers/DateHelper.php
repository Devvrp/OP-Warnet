<?php
class DateHelper {
    public static function format(?string $dateString, string $format = 'd M Y H:i'): string {
        if (empty($dateString)) return '-';
        try {
            $date = new DateTime($dateString);
            return $date->format($format);
        } catch (Exception $e) {
            return 'Invalid Date';
        }
    }
    public static function age(?string $birthdateString): ?int {
        if (empty($birthdateString)) return null;
        try {
            $birthDate = new DateTime($birthdateString);
            $today = new DateTime();
            if ($birthDate > $today) return 0;
            $age = $today->diff($birthDate);
            return $age->y;
        } catch (Exception $e) {
            return null;
        }
    }
    public static function diffForHumans(?string $dateString): string {
        if (empty($dateString)) return '-';
        try {
            $date = new DateTime($dateString);
            $now = new DateTime();
            $interval = $now->diff($date);
            if ($interval->y >= 1) return $interval->y . ' tahun lalu';
            if ($interval->m >= 1) return $interval->m . ' bulan lalu';
            if ($interval->d >= 1) return $interval->d . ' hari lalu';
            if ($interval->h >= 1) return $interval->h . ' jam lalu';
            if ($interval->i >= 1) return $interval->i . ' menit lalu';
            return 'Baru saja';
        } catch (Exception $e) {
            return 'Invalid Date';
        }
    }
    public static function toMysqlFormat(?string $dateString): ?string {
        if (empty($dateString)) return null;
        try {
            $date = new DateTime($dateString);
            return $date->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return null;
        }
    }
    public static function isWeekend(?string $dateString): bool {
        if (empty($dateString)) return false;
        try {
            $date = new DateTime($dateString);
            $dayOfWeek = $date->format('N'); // 1 (Mon) to 7 (Sun)
            return ($dayOfWeek == 6 || $dayOfWeek == 7);
        } catch (Exception $e) {
            return false;
        }
    }
}