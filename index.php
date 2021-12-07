<?php
if (!isset($_REQUEST)) {
    return;
}

// $
//Строка для подтверждения адреса сервера из настроек Callback API
$confirmation_token = 'da7fafd1';

//Ключ доступа сообщества
$token = 'a1989f07dd855c408dbdfd7ae66a4363e4857ee587684d997cdc204435e13eee569116daabea0b4d0d69f';

//Получаем и декодируем уведомление
$data1 = file_get_contents('php://input');
$data = json_decode($data1);

$log = date('Y-m-d H:i:s') . $data1;
file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);

//Проверяем, что находится в поле "type"
switch ($data->type) {
//Если это уведомление для подтверждения адреса...
    case 'confirmation':
//...отправляем строку для подтверждения
        echo $confirmation_token;
        break;

//Если это уведомление о новом сообщении...
    case 'message_reply':
        echo('ok');
        break;
    case 'message_new':
        echo('ok');
        //Возвращаем "ok" серверу Callback API
        $i = 0;
//...получаем id его автора
        $user_id = $data->object->message->from_id;
        $group_id = $data->object->message->peer_id;
        $mess = $data->object->message->text;
        $rez = 0;
//        $log = date('Y-m-d H:i:s') . $mess;
//        file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);
        $usr = file_get_contents("https://api.vk.com/method/users.get?user_ids={$user_id}&access_token={$token}&fields=screen_name&v=5.103");
        $user_info = json_decode($usr);
//и извлекаем из ответа его имя
        $user_name = $user_info->response[0]->first_name;
        $screen_name = $user_info->response[0]->screen_name;
        $mess = " " . $mess . " ";
        $mi = array(" Ми ", " ми ", " Ми,", " ми,", " Ми.", " ми.");
//        $log = date('Y-m-d H:i:s') . $mess;
//        file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);
        foreach ($mi as $val) {
            if (stripos($mess, $val) !== false) {
                $i = 1;
            }
        }
        if ($i) {
            $output = "это имя забанено, мы его не упоминаем.";
            $request_params = array(
                'message' => "@{$screen_name}({$user_name}), {$output}",
                'peer_id' => $group_id,
                'access_token' => $token,
                'v' => '5.130',
                'random_id' => '0'
            );
            $get_params = http_build_query($request_params);
            $result = file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);
        }
        $mess = str_replace(" ", "", $mess);
        if (preg_match('@/r([\d]*)d([\d]+)@', $mess, $bel)) {
            preg_match_all("/([+]|-)*([\d]*)d([\d]+)/", $mess, $kub);
            $output = "значение бросочка (" . substr($mess, 2) . '):';
            $kub[1][0] = '+';
            foreach ($kub[2] as $key => $item) {
                $count = $item;
                $num = $kub[3][$key];
                if (!$count) {
                    $count++;
                }
                $res = array();
                $output = $output . ' (';
                for ($count; $count > 0; $count--) {
                    $res = rand(1, $num);
//                    if ($screen_name == 'laizet'){
//                        if ($num==6){
//                            $res = 4;
//                        }
//                    }
                    if ($screen_name == 'an_kanata') {
                        if ($num > 6) {
                            $res = rand($num / 2, $num);
                        }
                    }
                    if ($count != 1) {
                        switch ($res) {
                            case 1:
                                $output = $output . $res . '&#128128; ';
                                break;
                            case $num:
                                $output = $output . $res . '&#128293; ';
                                break;
                            default:
                                $output = $output . $res . ' ';
                                break;
                        }
                    } else {
                        switch ($res) {
                            case 1:
                                $output = $output . $res . '&#128128;)';
                                break;
                            case $num:
                                $output = $output . $res . '&#128293;)';
                                break;
                            default:
                                $output = $output . $res . ')';
                                break;
                        }
                    }
                    if ($kub[1][$key] == '+') {
                        $rez = $rez + $res;
                    } else {
                        $rez = $rez - $res;
                    }
                }
            }
            if (preg_match_all('@[+]([\d]+)$@', $mess, $com)) {
                $rez = $rez + $com[1][0];
                $output = $output . ' + ' . $com[1][0] . ' Сумма: ' . $rez;
            } elseif (preg_match_all('@[-]([\d]+)$@', $mess, $com)) {
                $rez = $rez - $com[1][0];
                $output = $output . ' - ' . $com[1][0] . ' Сумма: ' . $rez;
            } elseif (count($kub[1]) != 1) {
                $output = $output . ' Сумма: ' . $rez;
            }
//            if (preg_match('@[+]([\d]+)@', $mess, $bel)) {
//                $output = $output . ' + ' . $bel[1];
//            }
//            if (preg_match('@[-]([\d]+)@', $mess, $bel)) {
//                $output = $output . ' - ' . $bel[1];
//            }

//затем с помощью users.get получаем данные об авторе

            switch ($screen_name) {
                case 'laizet':
                    $user_name = 'Солнышко';
                    break;
                case 'eshnnsessyn':
                    $user_name = 'Джонни';
                    break;
                case 'dukiri':
                    $user_name = 'Батько';
                    break;
                case 'an_kanata':
                    $user_name = 'Матько';
                    break;
                case 'iqarsikachov':
                    $user_name = 'Ииигорь';
                    break;
                case 'id2868646':
                    $user_name = 'Зоюшка';
                    break;
                default:
                    break;
            }

//С помощью messages.send отправляем ответное сообщение
            $request_params = array(
                'message' => "@{$screen_name}({$user_name}), {$output}",
                'peer_id' => $group_id,
                'access_token' => $token,
                'v' => '5.130',
                'random_id' => '0'
            );

            $get_params = http_build_query($request_params);

            $result = file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);
        }
        break;
    default:
        break;

}