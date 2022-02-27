<?php

class Kernel
{
    private static DB_Interface $DBInterface;

    public static function SetDBInterface(DB_Interface $DBInterface){
        self::$DBInterface = $DBInterface;
    }

    public static function DB() : SafeMySQL{
        return self::$DBInterface->DB();
    }

    public static function Get(string $Index) : ?string{
        return $_GET[$Index] ?? null;
    }

    public static function Post(string $Index) : ?string{
        return $_POST[$Index] ?? null;
    }

    public static function ToInt($Data) : int{
        return (int) $Data;
    }

    public static function ToString($Data) : string{
        return (string) $Data;
    }

    public static function ToFloat($Data) : float{
        return (float) $Data;
    }

    public static function EmailValidate(string $Email) : bool{
        return filter_var($Email, FILTER_VALIDATE_EMAIL);
    }

    public static function PhoneValidate(string $Phone) : bool{
        $First = substr($Phone, "0",1);
        if(($First != 7) && ($First != 8)){
            return false;
        }
        if(!preg_match("/^[0-9]{11,11}+$/", $Phone)){
            return false;
        }
        return true;
    }

    public static function GenerateUUIDv4() : string{
        $data = random_bytes(16);
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function UUIDValidate(string $UUID) : bool{
        $UUIDMask = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';

        if(preg_match($UUIDMask, $UUID) !== 1){
            return false;
        }

        return true;
    }

    public static function PasswordHash(string $Password) : string{
        return password_hash($Password, PASSWORD_DEFAULT);
    }

    public static function PasswordCheck(string $Password, string $PasswordHash) : bool{
        return password_verify($Password, $PasswordHash);
    }

    public static function MoveTo(string $Link){
        header("Location: ".$Link);
        exit();
    }

    public static function CreateAntiCSRFToken() : string{
        if(empty($_COOKIE['FormToken'])){
            $Data = random_bytes(random_int(10, 17));
            $Token = md5($Data);
            setcookie("FormToken", $Token, time() + 7200, "/", "", false, true);
            return $Token;
        }
        return $_COOKIE['FormToken'];
    }

    public static function CheckAntiCSRFToken(?string $Token) : bool{
        $Result = hash_equals($_COOKIE['FormToken'], $Token);
        setcookie("FormToken", "123", time() - 2345, "/", "", false, true);
        return $Result;
    }

    private static function EndOfTimeWord(string $Type_Of_Word, int $Number) : string{
        if(($Number < 11)or($Number > 14)){
            $Number = $Number % 10;
        }
        else{
            $Number = $Number % 100;
        }

        $Year   = array('год','года','лет');
        $Month  = array('месяц','месяца','месяцев');
        $Day    = array('день','дня','дней');
        $Hour   = array('час','часа','часов');
        $Minute = array('минуту','минуты','минут');
        $Second = array('секунду','секунды','секунд');
        $Word = 0;

        switch($Number){
            case 1:
                $Word = 0;
                break;
            case '2':case '3':case '4':
            $Word = 1;
            break;
            case '5':case '6':case '7':case '8':case '9':case '0':case '11':case'12':case '13':case '14':
            $Word = 2;
            break;
        }

        switch($Type_Of_Word){
            case 'year':
                return $Year[$Word];
            case 'month':
                return $Month[$Word];
            case 'day':
                return $Day[$Word];
            case 'hour':
                return $Hour[$Word];
            case 'minute':
                return $Minute[$Word];
            case 'second':
                return $Second[$Word];
        }
    }

    private static function NamesOfMonth(int $Number_Of_Month) : string{
        switch($Number_Of_Month){
            case 1:
                return 'Января';
            case 2:
                return 'Февраля';
            case 3:
                return 'Марта';
            case 4:
                return 'Апреля';
            case 5:
                return 'Мая';
            case 6:
                return 'Июня';
            case 7:
                return 'Июля';
            case 8:
                return 'Августа';
            case 9:
                return 'Сентября';
            case 10:
                return 'Октября';
            case 11:
                return 'Ноября';
            case 12:
                return 'Декабря';
        }
    }

    public static function DateTimeModify(string $DateTime, bool $Last = true) : string{
        $Unix_DateTime = strtotime($DateTime);

        $Date = date("Y-m-d", $Unix_DateTime);
        $Time = date("H:i", $Unix_DateTime);

        $Year = date("Y", $Unix_DateTime);

        $Month = date("m", $Unix_DateTime);
        $Day = date("j", $Unix_DateTime);

        $Today    = new DateTime(date("Y-m-d H:i:s"));
        $DateTime = new DateTime($DateTime);

        $TD_Day = $Today->format("d");
        $TD_Month = $Today->format("m");
        $TD_Year = $Today->format("Y");

        $Difference = $Today->diff($DateTime);

        $Diff_Days = $Difference->d;
        //echo $Diff_Days;
        if(($Today >= $DateTime)and($Last)){
            if(($Diff_Days == 0)and($TD_Month == $Month)and($TD_Year == $Year)and($TD_Day == $Day)){
                $Diff_Minutes = $Difference->i;
                $Diff_Hours = $Difference->h;
                //echo " / ".$Diff_Hours."//";
                //echo $Diff_Minutes;
                if($Diff_Hours > 0){
                    if($Diff_Hours < 7){
                        $Result_Text = $Diff_Hours.' '.self::EndOfTimeWord('hour', $Diff_Hours).' назад';
                    }
                    else{
                        $Result_Text = 'Сегодня в '.$Time;
                    }
                }
                else if($Diff_Minutes == 0){
                    $Diff_Seconds = $Difference->s;
                    if($Diff_Seconds == 0){
                        $Result_Text = 'Только что';
                    }
                    else{
                        $Result_Text = $Diff_Seconds.' '.self::EndOfTimeWord('second', $Diff_Seconds).' назад';
                    }
                }
                else{
                    $Result_Text = $Diff_Minutes.' '.self::EndOfTimeWord('minute', $Diff_Minutes).' назад';
                }
            }
            else if(($Diff_Days == 1)and($TD_Month == $Month)and($TD_Year == $Year)and($TD_Day == ($Day + 1))){
                $Result_Text = 'Вчера в '.$Time;
            }
            else{
                $Diff_Years = $Difference->y;
                if($Diff_Years == 0){
                    $Result_Text = $Day.' '.self::NamesOfMonth($Month).' в '.$Time;
                }
                else{
                    $Result_Text = $Day.' '.self::NamesOfMonth($Month).' '.$Year.' в '.$Time;
                }
            }
        }
        else{
            $Diff_Years = $Difference->y;
            if(($Diff_Years == 0)and($TD_Year == $Year)){
                $Result_Text = $Day.' '.self::NamesOfMonth($Month).' в '.$Time;
            }
            else{
                $Result_Text = $Day.' '.self::NamesOfMonth($Month).' '.$Year.' в '.$Time;
            }
        }
        return $Result_Text;
    }
}