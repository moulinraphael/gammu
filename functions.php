<?php

function sendPost($url, $post = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        return !curl_errno($ch) ? $response : curl_error($ch);
}

function repairNumber($number) {
	$number = str_replace(' ', '', $number);
        return str_replace('+33', '0', $number);
}

function secure($string) {
        return trim(htmlspecialchars(addslashes($string)));
}

function removeAccents($txt){
    $txt = str_replace('œ', 'oe', $txt);
    $txt = str_replace('Œ', 'Oe', $txt);
    $txt = str_replace('æ', 'ae', $txt);
    $txt = str_replace('Æ', 'Ae', $txt);
    mb_regex_encoding('UTF-8');
    $txt = mb_ereg_replace('[ÀÁÂÃÄÅĀĂǍẠẢẤẦẨẪẬẮẰẲẴẶǺĄ]', 'A', $txt);
    $txt = mb_ereg_replace('[àáâãäåāăǎạảấầẩẫậắằẳẵặǻą]', 'a', $txt);
    $txt = mb_ereg_replace('[ÇĆĈĊČ]', 'C', $txt);
    $txt = mb_ereg_replace('[çćĉċč]', 'c', $txt);
    $txt = mb_ereg_replace('[ÐĎĐ]', 'D', $txt);
    $txt = mb_ereg_replace('[ďđ]', 'd', $txt);
    $txt = mb_ereg_replace('[ÈÉÊËĒĔĖĘĚẸẺẼẾỀỂỄỆ]', 'E', $txt);
    $txt = mb_ereg_replace('[èéêëēĕėęěẹẻẽếềểễệ]', 'e', $txt);
    $txt = mb_ereg_replace('[ĜĞĠĢ]', 'G', $txt);
    $txt = mb_ereg_replace('[ĝğġģ]', 'g', $txt);
    $txt = mb_ereg_replace('[ĤĦ]', 'H', $txt);
    $txt = mb_ereg_replace('[ĥħ]', 'h', $txt);
    $txt = mb_ereg_replace('[ÌÍÎÏĨĪĬĮİǏỈỊ]', 'I', $txt);
    $txt = mb_ereg_replace('[ìíîïĩīĭįıǐỉị]', 'i', $txt);
    $txt = str_replace('Ĵ', 'J', $txt);
    $txt = str_replace('ĵ', 'j', $txt);
    $txt = str_replace('Ķ', 'K', $txt);
    $txt = str_replace('ķ', 'k', $txt);
    $txt = mb_ereg_replace('[ĹĻĽĿŁ]', 'L', $txt);
    $txt = mb_ereg_replace('[ĺļľŀł]', 'l', $txt);
    $txt = mb_ereg_replace('[ÑŃŅŇ]', 'N', $txt);
    $txt = mb_ereg_replace('[ñńņňŉ]', 'n', $txt);
    $txt = mb_ereg_replace('[ÒÓÔÕÖØŌŎŐƠǑǾỌỎỐỒỔỖỘỚỜỞỠỢ]', 'O', $txt);
    $txt = mb_ereg_replace('[òóôõöøōŏőơǒǿọỏốồổỗộớờởỡợð]', 'o', $txt);
    $txt = mb_ereg_replace('[ŔŖŘ]', 'R', $txt);
    $txt = mb_ereg_replace('[ŕŗř]', 'r', $txt);
    $txt = mb_ereg_replace('[ŚŜŞŠ]', 'S', $txt);
    $txt = mb_ereg_replace('[śŝşš]', 's', $txt);
    $txt = mb_ereg_replace('[ŢŤŦ]', 'T', $txt);
    $txt = mb_ereg_replace('[ţťŧ]', 't', $txt);
    $txt = mb_ereg_replace('[ÙÚÛÜŨŪŬŮŰŲƯǓǕǗǙǛỤỦỨỪỬỮỰ]', 'U', $txt);
    $txt = mb_ereg_replace('[ùúûüũūŭůűųưǔǖǘǚǜụủứừửữự]', 'u', $txt);
    $txt = mb_ereg_replace('[ŴẀẂẄ]', 'W', $txt);
    $txt = mb_ereg_replace('[ŵẁẃẅ]', 'w', $txt);
    $txt = mb_ereg_replace('[ÝŶŸỲỸỶỴ]', 'Y', $txt);
    $txt = mb_ereg_replace('[ýÿŷỹỵỷỳ]', 'y', $txt);
    $txt = mb_ereg_replace('[ŹŻŽ]', 'Z', $txt);
    $txt = mb_ereg_replace('[źżž]', 'z', $txt);
    return $txt;
}

function onlyLetters($string, $strong = false) {
    if ($strong)
        return preg_replace('/[^\p{L}\p{N} ]+/', '', $string);

    return preg_replace('/[^\p{L}\p{N} \'.-]+/', '', $string);
}
