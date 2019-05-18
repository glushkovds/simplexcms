<?php

class PlugDeclension {

    /**
     * Выдает слово из параметров $form1 - $form3 в зависимости от числа $count
     * @param int $count
     * @param string $form1 Пример: 'день', 'рубль'
     * @param string $form2 Пример: 'дня', 'рубля'
     * @param string $form3 Пример: 'дней', 'рублей'
     * @return string
     */
    public static function byCount($count, $form1, $form2, $form3) {
        $count = abs($count) % 100;
        $lcount = $count % 10;
        if ($count >= 11 && $count <= 19){
            return $form3;
        }
        if ($lcount >= 2 && $lcount <= 4){
            return $form2;
        }
        if ($lcount == 1){
            return $form1;
        }
        return $form3;
    }
    
    /**
     * Возвращает словоформу слова день в зависимости от количества дней
     * @param int $count Количество дней
     * @return string
     */
    public static function byCountDays($count){
        return self::byCount($count, 'день', 'дня', 'дней');
    }

    /**
     * Возвращает словоформу слова день в зависимости от количества дней
     * @param int $count Количество дней
     * @return string
     */
    public static function byCountWorkDays($count){
        return self::byCount($count, 'рабочий день', 'рабочих дня', 'рабочих дней');
    }

    /**
     * Возвращает словоформу слова месяц в зависимости от количества месяцев
     * @param int $count Количество месяцев
     * @return string
     */
    public static function byCountMonths($count){
        return self::byCount($count, 'месяц', 'месяца', 'месяцев');
    }

}
