<?php
    /**
     * Created by PhpStorm.
     * User: Cranky4
     * Date: 14.10.2015
     * Time: 12:20
     */

    function getRandomItem($in)
    {
        $accuracy = 100;

        $blackBox = array();
        foreach ($in as $key => $val) {
            $count = $val * $accuracy;
            for ($i = 0; $i < $count; $i++) {
                $blackBox[] = $key;
            }
        }

        if ($count = count($blackBox)) {
            $k = rand(0, $count - 1);

            return $blackBox[$k];
        }

        return false;
    }

    $in = array(
        'a' => 1 / 3,
        'b' => 1 / 6,
        'c' => 1 / 2,
    );

    $result = array();
    $repeat = 100;
    for ($i = 0; $i < $repeat; $i++) {
        $key = getRandomItem($in);
        if (!array_key_exists($key, $result)) {
            $result[$key] = 0;
        }
        $result[$key]++;
    }
    ksort($result);

    echo "IN -> ".print_r($in, true);
    echo "<br>OUT -> ".print_r($result, true);
?>