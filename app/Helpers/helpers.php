<?php

if (!function_exists('getInitials')) {
    function getInitials($name)
    {
        $words = explode(' ', $name);
        $initials = '';
        if (isset($words[0][0])) {
            $initials .= strtoupper($words[0][0]);
        }

    
        if (count($words) > 1 && isset($words[count($words) - 1][0])) {
            $initials .= strtoupper($words[count($words) - 1][0]);
        }
        
        elseif (isset($words[0][1])) {
            $initials .= strtoupper($words[0][1]);
        }

        return $initials;
    }
}