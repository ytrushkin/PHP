<?php
//	Скрипт приема платежей от системы QIWI
//	Автор : Трушкин Юрий
//	ООО "Практика"
//	28.04.2017
//	Изменена проверка IP с которого происходит вход в скрипт
//	02.06.2017

date_default_timezone_set('Europe/Moscow');

//Проверяем ip откуда пришел запрос
$ip = $_SERVER['REMOTE_ADDR'];
$allowed_ips = "79.142.16.0/24,79.142.17.0/24,79.142.18.0/24,79.142.19.0/24,79.142.20.0/24,79.142.21.0/24,79.142.22.0/24,79.142.23.0/24,79.142.24.0/24,79.142.25.0/24,79.142.26.0/24,79.142.27.0/24,79.142.28.0/24,91.232.230.0/23,195.189.100.0/22,10.0.253.51";

define('DATABASENAME', 'UTM5');			//Имя базы данных
define('DATABASELOGIN', 'nnn');  		//Логин для подключения к БД
define('DATABASEPASSWORD', '240581');	//Пароль для подключения к БД
define('DATABASEHOST', '127.0.0.1');    //Хост с БД

define('DATATABLEUSER', 'users'); //Наименование таблицы с информацией по пользователям
define('DATAFIELDACCOUNT', 'basic_account'); //Имя поля с номером Л/С
define('DATAFIELDNAME', 'full_name'); //Имя поля с ФИО пользователя

unset($db);

$command = "";			//Команда переданная скрипту check/pay
$txn_id = 0;			//Идентификатор платажа в системе QIWI
$account = 0;			//id аккаунта в системе UTM
$sum = 0.0;				//Сумма платежа

$conn=FALSE;
//Проверяем ip с которого сделан запрос

$ips = explode(",",$allowed_ips);
foreach ($ips as $value)
{
	if (matchCIDR($ip, $value))
	{
		$conn = true;
		break;
	}
}

if (!$conn)
{
	EWrite(" Попытка подключения с адреса $ip");
	echo("Запрос выполнен с неразрешенного адреса.");
	die();
	exit;
};


$txn_id = mysql_escape_string($_REQUEST['txn_id']); //Получаем id платежа в QIWI
//Проверяем txn_id
	
$account = mysql_escape_string($_REQUEST['account']); //Получаем л/с пользователя в UTM
//Проверяем account

$command = mysql_escape_string($_REQUEST['command']);

//ToDo: Тут можно проверять сумму платежа на допустимость
//но пока смысла нет.
if (isset($_REQUEST['sum'])) $sum = mysql_escape_string($_REQUEST['sum']);

if ($command!=='check' && $command!=='pay')
{
	//Непонятная команда.
	//Генерируем ошибку.
	CreateResponse(300, $txn_id, '', 0.0, 'Неопределенная команда.');
    exit();
}
else if ($sum===0.0)
{
	//Отсутствует сумма платежа
	CreateResponse(300, $txn_id, '', 0.0, 'Неопределенная команда.');
    exit();
}

//Устанавливаем соединение с БД
if (!($db = Database_Connect()))
{
    //Ошибка соединения с базой данных
    //Формируем сообщение с ошибкой 1
    //Останавливаем скрипт
    CreateResponse(1, $txn_id, '', 0.0, 'База не доступна');
    exit();
}

//Проверяем наличие клиента по указанному id
$query =	"SELECT ".DATAFIELDACCOUNT.", ".DATAFIELDNAME." 
             FROM ".DATATABLEUSER." 
             WHERE ".DATAFIELDACCOUNT." = ".$account;
	
//Запрос к БД
$result = Database_Query($query);

//Ошибка запроса, саму ошибку можно посмотреть в логах
if ($result == null || $result == false)
{
    //Формируем сообщение с ошибкой 5
    //Останавливаем скрипт
    CreateResponse(4, $txn_id, '', 0.0, 'Клиент не найден.');
    exit();
}
	
$res = mysql_num_rows($result);
if ($res != 1)
{
    //Ошибка выборки, не совпадение кол-ва выбранных записей
    //формируем сообщение с ошибкой 5
    //Останавливаем скрипт
    EWrite('Не найден клиент.');
    CreateResponse(5, $txn_id, '', 0.0, 'Клиент не найден.');
    exit();
}

//Выбираем информацию из запроса
$row = mysql_fetch_assoc($result);

$command = $_REQUEST['command'];
if ($command==='check') 
{
    //Формируем положительный ответ
    CreateResponse(0, $txn_id, '', 0.0, '');
    exit();
}
else if ($command==='pay')
{
	//Обрабатываем платеж.
	
	$txn_date = $_REQUEST['txn_date'];
	//Проверяем/преобразовываем дату
	
	//Формируем имя файла для хранения платежей
    $fileName = $txn_id."_".date("YmdHis", strtotime($txn_date))."_qiwi.csv";
    try
    {
        $f = fopen($fileName, "a");
        //Формируем содержание
        $platejtext = "qiwi\t11111\t22222\t33333\t44444\t$account\t".
        date("YmdHis", strtotime($txn_date)).$txn_id."\t77777\t88888\t99999\t$sum";
        fputs($f, $platejtext);
        fclose($f);
    }
    catch (Exception $e)
    {
        CreateResponse(1, $txn_id, '', 0.0, 'Ошибка сервера.');
        exit();
    }
	//Проводим платеж
    CreateResponse(0, $txn_id, strtotime($txn_date).$txn_id, $sum,'Платеж успешно проведен.');
    exit();
}

//Закрываем соединение с БД
Database_CloseQuery();

//*************************************************************************
//Функция составления ответа
//*************************************************************************
function CreateResponse($xmlrez, $txn_id, $prv_txn, $sum, $xmlmessage)
{
    $xmlpacket =  "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
    $xmlpacket .= "<response>\n";
	if ($txn_id != 0)
		$xmlpacket .= "<osmp_txn_id>$txn_id</osmp_txn_id>\n";
	else $xmlpacket .= "<osmp_txn_id/>";
	if ($prv_txn != '') 
		$xmlpacket .= "<prv_txn>$prv_txn</prv_txn>\n";
	if ($sum != 0.0)
		$xmlpacket .= "<sum>$sum</sum>\n";
	$xmlpacket .= "<result>$xmlrez</result>\n";
    $xmlpacket .= "<comment>$xmlmessage</comment>\n";
    $xmlpacket .= "</response>\n";
    $contentlength = strlen($xmlpacket);
    header ("Connection: Keep-Alive");
    header ('Content-type: application/xml');
    header ("Content-length: ".$contentlength);
    //$xmlpacket = mb_convert_encoding($xmlpacket, "UTF-8", "Windows-1251");
	//EWrite($xmlpacket);
    echo $xmlpacket;
}

function Database_Connect()
{
    @$db = mysql_connect(DATABASEHOST, DATABASELOGIN, DATABASEPASSWORD);
    //В случае ошибки соединения
    if (!$db)
    {
        //Пишем ошибку в лог
        EWrite('Ошибка соединения с базой данных. '.mysql_error());
        return false;
    }
        
    //Выбираем базу данных для работы
    mysql_select_db(DATABASENAME);
        
    return $db;
}
    
function Database_Query($query)
{
    //Локальная переменная для выполнения запроса
    @$result = mysql_query($query);
    //Если произошла ошибка запроса
    if ($result == false)
    {
        EWrite('Ошибка выборки из базы. '.mysql_error());
        return false;
    }
    return $result;
}
    
function Database_CloseQuery()
{
    if (!mysql_close($db))
    {
        EWrite('Ошибка закрытия соединения с БД'.mysql_error());
        return false;
    }
    return true;
}

//*************************************************************************
//Функция отображения ошибок
//*************************************************************************
function EWrite($errorText)
{
    $f1 = fopen("error.log", a);
    fputs($f1, date("Y-m-d")." : ".date("H:i:s").$errorText."\n");
    fclose($f1);
}

function matchCIDR($addr,$cidr) 
{
	list($ip,$mask) = explode('/',$cidr);
	if (ip2long($addr) >> (32 - $mask) == ip2long ($ip) >> (32 - $mask))
		return true;
	else return false;
}

function GetNum($ip, $pos)
{
    //Проверяем в допустимом ли диапазоне вытаскиваемый сегмент адреса
    if ($pos > 4 || $pos < 1) return 0;
    $ipnum = explode('.', $ip);
    return $ipnum[$pos-1];
}  
?>