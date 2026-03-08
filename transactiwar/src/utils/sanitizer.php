<?php

class Sanitizer {

    public static function escape($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}