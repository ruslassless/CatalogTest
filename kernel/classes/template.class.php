<?php

class Template
{
    private static $SavedElements = [];

    public static function GetTemplateFolders() : array{
        $TemplatesFolder = MAIN_PATH.'templates\\';
        $Array = scandir($TemplatesFolder);
        $ResultArray = [];
        foreach($Array as $Folder){
            if(is_dir($TemplatesFolder . $Folder)){
                if(($Folder !== ".")and($Folder !== "..")){
                    $ResultArray[] = $Folder;
                }
            }
        }

        return $ResultArray;
    }

    public static function LoadStyle(string $FilePath, array $VarsArray = []) : ?string{
        if(!array_key_exists($FilePath, self::$SavedElements)){
            $StyleFileCode = file_get_contents(STYLE . $FilePath);
            if(!$StyleFileCode){
                return null;
            }
            self::$SavedElements[$FilePath] = $StyleFileCode;
        }
        $KeysArray = [];
        $ValuesArray = [];
        $KeysArray[] = "\n";
        $ValuesArray[] = "";
        $KeysArray[] = "\r";
        $ValuesArray[] = "";
        $KeysArray[] = "\t";
        $ValuesArray[] = "";
        $KeysArray[] = "\x0B";
        $ValuesArray[] = "";
        $KeysArray[] = "    ";
        $ValuesArray[] = "";

        foreach($VarsArray as $Key => $Value){
            $KeysArray[]   = "{[".$Key."]}";
            $ValuesArray[] = $Value;
        }
        return str_replace($KeysArray, $ValuesArray, self::$SavedElements[$FilePath]);
    }
}