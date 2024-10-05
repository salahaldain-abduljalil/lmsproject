
<?php

use Illuminate\Support\Str;


/**calculate readable human time */

if (!function_exists('timeago')) {
    function timeago($timestamp)
    {
        $timeDifference = time() - strtotime($timestamp);
        $second = $timeDifference;
        $minutes = round($timeDifference / 60);
        $hour = round($timeDifference / 3600);
        $day = round($timeDifference / 86400);

        if ($second <= 60) {
            if ($second <= 1) {
                return "a second ago";
            }
            return $second . "s ago";
        } elseif ($minutes <= 60) {

            return $minutes . "m ago";
        } elseif ($hour <= 24) {

            return $hour . "m ago";
        } else {

            return date('j m y', strtotime($timestamp));
        }
    }
}

/** truncate string */
if (!function_exists('truncate')) {
    function truncate($str, $limit = 18)
    {
        return Str::limit($str, $limit, '...');
    }
}
