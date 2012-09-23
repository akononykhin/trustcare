<?php


function parseLangStr($data)
{
    $result = array();
    $data = explode('||', $data);
    for ($i = 0;$i < count($data); $i++) {
        $temp = explode(':=', $data[$i]);
        
        $temp[0] = trim($temp[0]);
        $temp[1] = trim($temp[1]);
        
        $result[$temp[0]] = $temp[1];
    }
    return $result;
}

function getEnglish($data)
{
    $result = '';
    
    $langData = parseLangStr($data);
    foreach($langData as $key => $text) {
        if ('en' == $key) {
            $result = $text;
            break;
        }
    }
    return $result;
}

$dbh = null;
$fh = null;

$dbh = mysqli_init();

try {
    $fh = fopen('countries.sql', "w");
    if(is_null($fh)) {
        throw new Exception("Can't open file for writing");
    }

    if(!mysqli_real_connect($dbh, 'localhost', 'root', 'MANAGER', 'mgline')) {
        throw new Exception(sprintf("Can't open connect. %s (%s)", mysqli_connect_errno(), mysqli_connect_error()));
    }
    mysqli_set_charset($dbh, 'utf8');
    mysqli_query($dbh, "set names 'utf8'");
    
    $query = sprintf("
select * from cc_country order by id;");
    $resultSlct = mysqli_query($dbh, $query);
    if(!$resultSlct) {
        throw new Exception(sprintf("Query %s failed. %s (%s)", $query, mysqli_errno($dbh), mysqli_error($dbh)));
    }
    while(($row = mysqli_fetch_assoc($resultSlct))) {
        $str = sprintf("insert into country(id, iso_3166, name) values(%d, '%s', '%s');\n", $row['id'], addslashes($row['code']), addslashes(getEnglish($row['name'])));
        fwrite($fh, $str);
    }

    
}
catch(Exception $ex) {
    printf("\nERROR: %s\n", $ex->getMessage());
}

if(!is_null($fh)) {
    fclose($fh);
}
if(!is_null($dbh)) {
    mysqli_close($dbh);
}

