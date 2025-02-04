<?php

if (!function_exists('money')) {
    function money($amount)
    {
        return '$' . number_format($amount, 2);
    }
} 