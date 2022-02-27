<?php

class Notification
{
    private static $SessionVarName = "NOTIFICATIONS";
    const Error   = 0;
    const Success = 1;
    const Info    = 2;
    const Warning = 3;

    public static function Add(string $Text, int $Type = 0, ?string $Header = null, ?string $Object = null) : bool{
        $Text = Kernel::ToString($Text);
        if($Type > 3 || $Type < 0){
            return false;
        }
        $Header = Kernel::ToString($Header);
        if($Header === ""){
            $Header = null;
        }

        $_SESSION[self::$SessionVarName][] = [
            'TEXT'   => $Text,
            'TYPE'   => $Type,
            'HEADER' => $Header,
            'OBJECT' => $Object
        ];

        return true;
    }

    public static function Show() : ?string{
        if(empty($_SESSION[self::$SessionVarName])){
            return null;
        }
        $Notifications = "";
        for($Number = 0; $Number < count($_SESSION[self::$SessionVarName]); $Number++){
            $Notification = $_SESSION[self::$SessionVarName][$Number];
            $Type = $Notification['TYPE'];
            switch($Type){
                case 0:
                    $FileName = "error";
                    break;
                case 1:
                    $FileName = "success";
                    break;
                case 2:
                    $FileName = "info";
                    break;
                case 3:
                    $FileName = "warning";
                    break;
            }
            $NotificationVars = [
                'NOTIFICATION_TEXT'   => $Notification['TEXT'],
                'NOTIFICATION_HEADER' => $Notification['HEADER']
            ];
            $Notifications .= Template::LoadStyle("notifications\\".$FileName.".html", $NotificationVars);
        }
        $_SESSION[self::$SessionVarName] = null;
        return $Notifications;
    }
}