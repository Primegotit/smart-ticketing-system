<?php
namespace app\helpers;

/**
 * Shared validation for user identity fields.
 * Used by registration, admin create-user and admin edit-user.
 */
class Validator {

    /**
     * A value is rejected if it is empty or contains no letter at all
     * (so "12345", "007", "___", "123-456" are all refused).
     */
    public static function hasLetter($value) {
        return (bool) preg_match('/\p{L}/u', (string) $value);
    }

    /** First / last name: letters, spaces, apostrophes, hyphens, dots; at least one letter; no digits-only. */
    public static function validName($value, $label) {
        $value = trim((string) $value);
        if ($value === '') {
            return "$label is required.";
        }
        if (mb_strlen($value) > 50) {
            return "$label must be 50 characters or fewer.";
        }
        if (!self::hasLetter($value)) {
            return "$label cannot consist of numbers only.";
        }
        if (!preg_match("/^[\p{L}][\p{L}\s.'\-]*$/u", $value)) {
            return "$label may only contain letters, spaces, hyphens, apostrophes and dots, and must start with a letter.";
        }
        return null;
    }

    /** Username: 3-50 chars, letters/numbers/._- allowed, but must contain at least one letter (not numbers only). */
    public static function validUsername($value) {
        $value = trim((string) $value);
        if ($value === '') {
            return 'Username is required.';
        }
        if (mb_strlen($value) < 3 || mb_strlen($value) > 50) {
            return 'Username must be between 3 and 50 characters.';
        }
        if (!self::hasLetter($value)) {
            return 'Username cannot consist of numbers only - it must contain at least one letter.';
        }
        if (!preg_match('/^[\p{L}0-9._\-]+$/u', $value)) {
            return 'Username may only contain letters, numbers, dots, underscores and hyphens.';
        }
        return null;
    }

    /** Returns an array of error messages for the three identity fields. */
    public static function identityErrors($username, $firstName, $lastName, $requireUsername = true) {
        $errors = [];
        if ($requireUsername) {
            if ($e = self::validUsername($username)) $errors[] = $e;
        }
        if ($e = self::validName($firstName, 'First name')) $errors[] = $e;
        if ($e = self::validName($lastName, 'Surname'))     $errors[] = $e;
        return $errors;
    }
}
