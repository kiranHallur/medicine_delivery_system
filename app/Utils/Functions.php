<?php

function my_func(){
    dd("Test Functions 1");
}

function normalize_str($str,$options=[]){
    $ENCLOSE_STRING = (!empty($options['ENCLOSE_STRING']))? $options['ENCLOSE_STRING'] : "DO_NOTHING";
    $patterns = [];
    $patterns[0] = '/<\?php/mi';
    $patterns[1] = '/\?>/mi';
    $patterns[2] = '/<\?=/mi';
    $patterns[3] = '/\'/mi';
    $patterns[4] = '/"/mi';
    $patterns[5] = '/\//';
    $replacements = [];
    $replacements[0] = htmlspecialchars('<?php');
    $replacements[1] = htmlspecialchars('?>');
    $replacements[2] = htmlspecialchars('<?=');
    // $replacements[3] = "'";
    $replacements[3] = "&apos;";
    $replacements[4] = "&quot;";
    $replacements[5] = "\\";
    
    $str = preg_replace($patterns, $replacements, $str);
    // $str =  str_replace('\\', '\\\\', $str);

    if($ENCLOSE_STRING=="SINGLE_QUOTES"){
        $str = "'".$str."'";
    }else if($ENCLOSE_STRING=="DOUBLE_QUOTES"){
        $str = '"'.$str.'"';
    }else if($ENCLOSE_STRING=="DO_NOTHING"){
        $str = $str;
    }
    return $str;
}

function count_objects($obj){
    $count = 0;
    if (method_exists($obj, 'count')) {
        $count = $obj->count();
    }else if(is_array($obj)) {
        $count = count($obj);
    }
    return $count;
}

function text_cap($text=""){
    return ucfirst(strtolower($text));
}

function text_cap_with_replace($text="", $search="_", $replacer=" "){
    return ucfirst(strtolower(str_ireplace($search, $replacer, $text)));
}

function num_fmt($num=0, $decimal_point=2){
    return (float)number_format($num, $decimal_point);
}

function date_fmt($date="", $format="d-m-Y"){
    if(empty($date)){
        return "";
    }
    return date($format, strtotime($date));
}