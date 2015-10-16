<?php
    /**
     * Created by PhpStorm.
     * User: Cranky4
     * Date: 14.10.2015
     * Time: 12:20
     */

    /**
     * @param $in
     *
     * @return bool
     */
    function getRandomItem($in)
    {
        //точночть
        $accuracy = 10000;

        //"складываем" элементы в массив в соответствие с их вероятностью
        $blackBox = array();
        foreach ($in as $key => $val) {
            $count = $val * $accuracy;
            for ($i = 0; $i < $count; $i++) {
                $blackBox[] = $key;
            }
        }

        //получаем случайный
        if ($count = count($blackBox)) {
            $k = rand(0, $count - 1);

            return $blackBox[$k];
        }

        return false;
    }

    //входные данные
    $in = array(
        'a' => 1 / 3,
        'b' => 1 / 6,
        'c' => 1 / 2,
    );

    $result = array();

    //кол-во повторов
    $repeat = 100;

    //проверяем распределение на $repeat выборках
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