<?php
error_reporting(E_ALL & ~E_NOTICE); 

  //*************
  //Качаем файл
  //*************
  $url = 'http://www.teleguide.info/download/new3/xmltv.xml.gz';
  
  $fileContent = '';
  
  if (($fp = fopen($url, "r")) != FALSE)
  {
    while (!feof($fp)) 
            {
                $fileContent .= fread($fp, 16384);
            }    
  }
  else die ("Ошибка скачки файла");
  
  fclose($fp);
  
  $fp = fopen('./in/xmltv.gz', 'wb') or die ("Ошибка");
  fwrite($fp, $fileContent);
  fclose($fp);
  
  $idmk = array("1"=>"1", 	    //Первый канал                  +
                "2"=>"2",	    //Россия                        +
                "3"=>"8",	    //ТВЦ                           +
                "4"=>"7",	    //НТВ                           +
                "5"=>"3",	    //Культура                      +
                "101"=>"9",	    //ТНТ                           +
                "102"=>"16",	//Домашний                      +
                "103"=>"11",	//РЕН ТВ                        +
                "104"=>"10",	//СТС                           +
                "105"=>"34",	//ТВ-3                          +
                "107"=>"33",	//МТВ                           +
                "108"=>"32",	//Муз ТВ			            +
                "109"=>"36",	//ДТВ //Перец                   +                          
                "206"=>"19",    //Eurosport                     +
                "218"=>"61",	//Nikelodon                     +
		        "222"=>"17",	//Спас ТВ                       +
                "235"=>"4",	    //Россия Спорт                  +
                "255"=>"6",	    //5 канал                       +
                "276"=>"65",	//Канал 2х2                     +
                "288"=>"54",    //TV1000                        +
                "289"=>"37",	//ТВ 21                         +
                "291"=>"44",	//Комедия                       +
                "324"=>"55",    //Viasat Explorer               +
                "325"=>"102",   //Viasat History                +
                "326"=>"12",	//РБК                           +
                "330"=>"13",	//Звезда                        +
                "369"=>"30",	//О2 ТВ                         +
                "432"=>"50",	//Усадьба                       +
                "503"=>"28",	//А-One                         +
                "528"=>"23",	//Драйв                         +
                "529"=>"51",	//Здоровое ТВ                   +
                "530"=>"20",	//Охота и Рыбалка               +
                "541"=>"43",	//Индия                         +
                "553"=>"22",	//Авто плюс                     +
                "554"=>"21",	//Боец                          +
                "555"=>"66",	//Русская ночь                  +
		        "556"=>"47",	//365 дней ТВ		            +
                "557"=>"26",	//Ля минор                      +
		        "558"=>"81",	//Много ТВ			            +
                "568"=>"108",   //HD Life                       +
                "584"=>"46",	//Интересное ТВ                 +
                "585"=>"49",	//Кухня ТВ			            +
		        "620"=>"48",    //Телекафе                      +
		        "663"=>"31",	//Zoo ТВ		                +
			    "662"=>"68",	//Телепутешествия               +
			    "665"=>"29",	//Teen TV                       +
		        "666"=>"63",	//Кинопоказ                     +
                "676"=>"5",     //Россия 24                     +
		        "689"=>"42",	//Sony entertayment             +
		        "790"=>"79",    //Тонус				            +
                "940"=>"83",    //Спорт 1                       +
                "946"=>"85",    //8 канал                       +
                "1009"=>"57",   //Viasat Nature                 +
                "1354"=>"84",   //Спорт 2                       +
                "100002"=>"35",	//Дом кино                      +
                "100008"=>"78", //Eurosport 2                   +
                "100025"=>"45",	//Время                         +
                "100032"=>"24",	//Музыка первого                +
		        "100043"=>"58", //SyFy Universal                +
                "100045"=>"59", //Fashion TV		            +
		        "100046"=>"67", //RU.TV                         +
		        "100050"=>"72", //Universal channel             +
		        "100055"=>"18",	//Disney Channel                +
                "300000"=>"53", //TV1000 Русское кино           +
                "300001"=>"41",	//AXN Sci-fi                    +
                "300002"=>"62", //Viasat Sport                  +
                "300007"=>"14",	//Карусель                      +
                "300010"=>"39",	//Fox Crime                     +
                "300015"=>"15",	//Мать и дитя                   +
		        "300018"=>"75", //24 Техно                      +
		        "300020"=>"64", //Детский                       +   
                "300033"=>"38",	//НСТВ                          +
                "300037"=>"27",	//Bridge TV                     +
                "300047"=>"87", //TV1000 action                 +
                "300056"=>"40",	//Fox life                      +
		        "300057"=>"52", //РТР Планета                   +
		        "300012"=>"25", //Мультимания                   +
		        "300081"=>"77", //Улыбка ребенка                +
		        "300088"=>"107",//Женский мир			        +
		        "300091"=>"69", //Охотник и Рыболов             +
                "300095"=>"104",//Кинопоказ HD-1                +
                "300096"=>"105",//Кинопоказ HD-2                +
                "300097"=>"106",//Телепутешествия HD            +
                "300105"=>"100",//Наука 2.0                     +
                "300108"=>"56", //Дождь                         +
                "300109"=>"80", //RTG                           +
		        "400006"=>"82",	//Ночной клуб			        +
                );
  
  $channelId = array();
  $curr = 0;
  $idList = array();
  $chanName = FALSE;
  $chanTitle = FALSE;
  $chanDesc = FALSE;
  $prevId = 0;
  $count = 0;
    
function stringElement($parser, $str) 
{
global $channelId;    
global $chanName;
global $chanTitle;
global $curr;
global $count;
global $chanDesc;
    if ($str != "\n") 
    {
        if ($chanTitle == TRUE)
            $channelId[$curr][$count]["NameProg"] .= $str;
        if ($chanDesc == TRUE)
        {
            $channelId[$curr][$count]["Desc"] .= $str;
        }
    }
}
  
function startElement($parser, $name, $attrs)
{
global $channelId;
global $chanName;
global $chanTitle;
global $chanDesc;
global $count;
global $curr;
global $idList;
global $prevId;
    switch($name)
    {
        case 'CHANNEL':
            $idList[] = $attrs['ID'];
            $curr = $attrs['ID'];
            break;
        case 'DISPLAY-NAME':
            $chanName = TRUE;        
            break;
        case 'PROGRAMME':
            $curr = $attrs['CHANNEL'];
            if ($attrs['CHANNEL'] != $prevId) 
            {
                $count = 0;
                $prevId = $attrs['CHANNEL'];
            }
            
            $channelId[$attrs["CHANNEL"]][$count]["Start"] = $attrs["START"];
            $channelId[$attrs["CHANNEL"]][$count]["End"] = $attrs["STOP"];
            break;
        case 'TITLE':
            $chanTitle = TRUE;
            break;
        case 'DESC':
            $chanDesc = TRUE;        
            break;
    }
}

function endElement($parser, $name)
{
global $chanName;
global $curr;
global $chanTitle;
global $chanDesc;
global $count;
    switch($name)
    {
        case 'CHANNEL':
            break;
        case 'DISPLAY-NAME':
                $chanName = FALSE;
            break;
        case 'PROGRAMME':
                $count++;
            break;
        case 'TITLE':
                $chanTitle = FALSE;
            break;
        case 'DESC':
                $chanDesc = FALSE;
            break;
    }
    
}
    
    $XMLParser = xml_parser_create('UTF-8');
    unset($filexml);
    echo date("H:i:s", time())."\n";
    xml_set_element_handler($XMLParser, 'startElement', 'endElement');
    xml_set_character_data_handler($XMLParser, 'stringElement');
  
  $f = gzopen('./in/xmltv.gz', 'r') or die ("Ошибка");
    
    while (!feof($f))
    {
        $filexml = fgets($f);
        xml_parse($XMLParser, $filexml);
    }
    echo date("H:i:s", time());
  
  xml_parser_free($XMLParser);

  gzclose($f);
  
  $strToAndroid = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
  $strToAndroid .= "<programGuide>\n";
  
  for ($j=0; $j<count($channelId); $j++)
  {
      //Проверить следующую строчку необходимо мне
      if ($idmk[$idList[$j]][0] != 0) $mediacon = $idmk[$idList[$j]];
        else continue;
      
      $f = fopen("./out/".$idList[$j].".xml", "wt");
      if ($f==0) continue;
      
      $strToFile = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
      $strToFile .= '<transaction xmlns="http://www.netup.ru/transaction/2.0">'."\n";
      $strToFile .= "\t".'<events>'."\n";
      $z = $idList[$j];
    
      //Добавляем данные для android IPTV
      $strToAndroid .= "\t<channel>\n";
      $strToAndroid .= "\t\t<id>".$idmk[$idList[$j]]."</id>\n";
   
      //Добавляем программы
      for ($i=0; $i<count($channelId[$z]); $i++)
      {
          //Формирование строки для приставок
          $strToFile .= "\t\t".'<updated_media_program family="netup:iptv" version="1.0">'."\n";   
          $progrlength = strtotime($channelId[$z][$i]["End"]) - strtotime($channelId[$z][$i]["Start"]);
            
          $strToFile .= "\t\t".'<duration type="long">'.$progrlength.'</duration>'."\n";
          $strToFile .= "\t\t".'<info type="string">'.$channelId[$z][$i]["Desc"].' </info>'."\n";
          $strToFile .= "\t\t".'<mark type="string">-7804117065568595609</mark>'."\n";
          $strToFile .= "\t\t".'<media_content_code type="int">'.$mediacon.'</media_content_code>'."\n";
          $strToFile .= "\t\t".'<media_program_code type="long">'.strtotime($channelId[$z][$i]["Start"]).'</media_program_code>'."\n";
          $strToFile .= "\t\t".'<since type="long">'.strtotime($channelId[$z][$i]["Start"]).'</since>'."\n";
          $strToFile .= "\t\t".'<title type="string">'.$channelId[$z][$i]["NameProg"].'</title>'."\n";
          $strToFile .= "\t\t".'</updated_media_program>'."\n\n";
            
          //формирование строки для android приставок
          //Если время начала программы меньше текущего (округлунного до часов) и окончание программы больше
          //то включаем в программу. Если начало программы меньше текущего времени (округленного до часов)+2 и больше текущего 
          //то тоже включаем.
          
          //Получаем текущее время округленное до часов (в меньшую сторону)
          $time = time();
          $startTime = $time-($time-(int)($time/3600)*3600);
          
          if ((strtotime($channelId[$z][$i]["Start"]) <= $startTime && strtotime($channelId[$z][$i]["End"]) >= $startTime)  ||
            (strtotime($channelId[$z][$i]["Start"]) >= $startTime && strtotime($channelId[$z][$i]["Start"]) <= $startTime+7200))
          {
              $strToAndroid .= "\t\t<program>\n";
              $strToAndroid .= "\t\t\t<since>".strtotime($channelId[$z][$i]["Start"])."</since>\n";
              $strToAndroid .= "\t\t\t<duration>$progrlength</duration>\n";
              $strToAndroid .= "\t\t\t<title>".$channelId[$z][$i]["NameProg"]."</title>\n";
              $strToAndroid .= "\t\t\t<info>".$channelId[$z][$i]["Desc"]."</info>\n";
              $strToAndroid .= "\t\t</program>\n";
          }
        }
        
    $strToFile .= "\t".'</events>'."\n";
    $strToFile .= '</transaction>'."\n";
    fwrite($f,$strToFile);

    fclose($f);
    
    $strToAndroid .= "\t</channel>\n";
    
  }
  
  $strToAndroid .= "</programGuide>\n";
  
  $f = fopen("./out/andrProgr.xml", "w");
  fwrite($f,$strToAndroid);
  fclose($f);
  
  //Список каналов для android
  
  $fNameAdr = "./in/chanAddress.csv";

  unset ($fileContent);
  unset ($addresses);

//*****************************************************************************
//**************** Секция загрузки необходимых данных *************************
//*****************************************************************************


  //Открываем список соотвествия id канала - адрес
  $fa = fopen($fNameAdr, "r") or die ("Ошибка открытия файла соотвествия");
  $fileContent = explode("\n", fread($fa, filesize($fNameAdr)));
  fclose($fa);

//Парсим адреса каналов
unset ($tmp);
for ($i=0; $i<count($fileContent); $i++)
{
    $tmp["id"] = trim(strtok($fileContent[$i], ","));
    $tmp["address"] = trim(strtok(","));
    $addresses[$tmp["id"]] = str_replace("@", "", $tmp["address"]);
}

$fname = './in/chan.csv';
unset ($fileContent);

//Открываем текстовый файл со списком каналов
$f = fopen($fname, 'r') or die ("Файл со списком каналов не найден. Завершение работы скрипта.");
$fileContent = explode("\n", iconv("utf-8", "windows-1251", fread($f, filesize($fname))));
fclose($f);

unset ($infoChannelVideo);
unset ($infoChannelRadio);
unset ($channel);

//Парсим полученные данные
for ($i=0; $i<count($fileContent); $i++)
{
    $channel["id"] = trim(strtok($fileContent[$i], ","));
    $channel["name"] = trim(strtok(","));
    $channel["type"] = trim(strtok(","));
    $channel["group"] = trim(strtok(","));
    
    if (is_numeric($channel["id"]))
    {
        if ($channel["type"] == "TV")
            $infoChannelVideo[] = $channel;
        else
            $infoChannelRadio[] = $channel;
    }
}

//*****************************************************************************
//**************** Секция формирования выходных данных ************************
//*****************************************************************************

//Создаем XML файлик со списком
$strToFile  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
$strToFile .= "<channelLists>\n";
$strToFile .= "<timestamp>";
$strToFile .= mktime();
$strToFile .= "</timestamp>\n";

$strToFile .= "\t<channelList>\n";
$strToFile .= "\t\t<name>Video</name>\n";

for ($i=0; $i<count($infoChannelVideo); $i++)
{
    $strToFile .= "\t\t<channel>\n";
    $strToFile .= "\t\t\t<type>".$infoChannelVideo[$i]['type']."</type>\n";
    $strToFile .= "\t\t\t<name>".$infoChannelVideo[$i]['name']."</name>\n";
    $strToFile .= "\t\t\t<quality>".$infoChannelVideo[$i]['group']."</quality>\n";
    $strToFile .= "\t\t\t<address>".$addresses[$infoChannelVideo[$i]['id']]."</address>\n";
    $strToFile .= "\t\t\t<id>".$infoChannelVideo[$i]['id']."</id>\n";
    $strToFile .= "\t\t\t<encrypted>no</encrypted>\n";
    $strToFile .= "\t\t\t<iconUrl/>\n";
    $strToFile .= "\t\t</channel>\n";
}

$strToFile .= "\t</channelList>\n";

$strToFile .= "</channelLists>\n";

//Сохраняем список каналов
$f = fopen("./out/channel.xml", "wt");
fwrite($f, iconv("windows-1251", "utf-8", $strToFile));
fclose($f);
  
?>
