<?php

class Catalog
{
    private static ?array $SectionsData = null;

    public static function GetFullInArray(?string $SectionUUID = null) : ?array{
        if(self::$SectionsData === null){
            self::$SectionsData = Kernel::DB()->getAll("SELECT `uuid`,`name`,`created`,`updated`,`description`,`parent_uuid` FROM `sections`");
        }
        $SectionsData = self::$SectionsData;
        $Elements = [];
        foreach($SectionsData as $SectionData){
            if(Kernel::ToString($SectionData['parent_uuid']) == $SectionUUID){
                $Elements[] = [
                    'uuid' => Kernel::ToString($SectionData['uuid']),
                    'name' => Kernel::ToString($SectionData['name']),
                    'created' => Kernel::ToInt($SectionData['created']),
                    'updated' => Kernel::ToInt($SectionData['updated']),
                    'description' => Kernel::ToString($SectionData['description']),
                    'parent_uuid' => Kernel::ToString($SectionData['parent_uuid']),
                    'childs' => self::GetFullInArray(Kernel::ToString($SectionData['uuid']))
                ];
            }
        }
        return $Elements;
    }

    public static function Get(?string $SectionUUID = null) : ?string{
        if($SectionUUID === null){
            $SectionsData = Kernel::DB()->getAll(
                "SELECT `uuid`,`name`,`created`,`updated`,`description`,`parent_uuid` FROM `sections` WHERE `parent_uuid` IS NULL OR `parent_uuid` = ''"
            );
        }
        else{
            $SectionsData = Kernel::DB()->getAll(
                "SELECT `uuid`,`name`,`created`,`updated`,`description`,`parent_uuid` FROM `sections` WHERE `parent_uuid` = ?s",
                $SectionUUID
            );
        }
        if($SectionUUID === null){
            $ElementsData = Kernel::DB()->getAll(
                "SELECT `uuid`,`name`,`created`,`updated`,`section_uuid`,`type` FROM `elements` WHERE `section_uuid` IS NULL OR `section_uuid` = ''"
            );
        }
        else{
            $ElementsData = Kernel::DB()->getAll(
                "SELECT `uuid`,`name`,`created`,`updated`,`section_uuid`,`type` FROM `elements` WHERE `section_uuid` = ?s",
                $SectionUUID
            );
        }

        $Elements = "";
        foreach($SectionsData as $SectionData){
            if($SectionData['updated'] == null){
                $Updated = "Никогда";
            }
            else{
                $Updated = Kernel::DateTimeModify(date("Y-m-d H:i:s", $SectionData['updated']));
            }
            $SectionTableElementVars = [
                'SECTION_UUID' => Kernel::ToString($SectionData['uuid']),
                'SECTION_NAME' => Kernel::ToString($SectionData['name']),
                'SECTION_CREATED' => Kernel::DateTimeModify(date("Y-m-d H:i:s", Kernel::ToInt($SectionData['created']))),
                'SECTION_UPDATED' => $Updated,
                'SECTION_DESCRIPTION' => Kernel::ToString($SectionData['description']),
                'SECTION_PARENT_UUID' => Kernel::ToString($SectionData['parent_uuid'])
            ];
            $Elements .= Template::LoadStyle('sections/table_element.html', $SectionTableElementVars);
        }
        foreach($ElementsData as $ElementData){
            if($ElementData['updated'] == null){
                $Updated = "Никогда";
            }
            else{
                $Updated = Kernel::DateTimeModify(date("Y-m-d H:i:s", $ElementData['updated']));
            }
            $Type = Kernel::ToString($ElementData['type']);
            switch($Type){
                case 'ARTICLE':
                    $Type = "Новость";
                    break;
                case 'PAPER':
                    $Type = "Статья";
                    break;
                case 'REVIEW':
                    $Type = "Отзыв";
                    break;
                case 'COMMENT':
                    $Type = "Комментарий";
                    break;
                default:
                    $Type = "???";
                    break;
            }
            $ElementTableElementVars = [
                'ELEMENT_UUID' => Kernel::ToString($ElementData['uuid']),
                'ELEMENT_NAME' => Kernel::ToString($ElementData['name']),
                'ELEMENT_CREATED' => Kernel::DateTimeModify(date("Y-m-d H:i:s", Kernel::ToInt($ElementData['created']))),
                'ELEMENT_UPDATED' => $Updated,
                'ELEMENT_SECTION_UUID' => Kernel::ToString($ElementData['section_uuid']),
                'ELEMENT_TYPE' => $Type
            ];
            $Elements .= Template::LoadStyle('elements/table_element.html', $ElementTableElementVars);
        }
        if($Elements == ""){
            $Elements .= Template::LoadStyle('sections/table_empty.html');
        }
        return $Elements;
    }

    public static function GetParentsArray(?string $SectionUUID) : ?array{
        if($SectionUUID == null){
            return [];
        }
        if(self::$SectionsData === null){
            self::$SectionsData = Kernel::DB()->getAll("SELECT `uuid`,`name`,`created`,`updated`,`description`,`parent_uuid` FROM `sections`");
        }
        $SectionsData = self::$SectionsData;
        $Elements = [];
        foreach($SectionsData as $SectionData){
            if(Kernel::ToString($SectionData['uuid']) == $SectionUUID){
                $Elements[] = Kernel::ToString($SectionData['parent_uuid']);
                $Elements = array_merge($Elements, self::GetParentsArray($SectionData['parent_uuid']));
            }
        }
        return $Elements;
    }

    public static function GetNames() : ?array{
        if(self::$SectionsData === null){
            self::$SectionsData = Kernel::DB()->getAll("SELECT `uuid`,`name` FROM `sections`");
        }
        $SectionsData = self::$SectionsData;
        $Elements = [];
        foreach($SectionsData as $SectionData){
            $Elements[$SectionData['uuid']] = Kernel::ToString($SectionData['name']);
        }
        return $Elements;
    }

    public static function GetBreadcrumb(?string $SectionUUID = null) : string{
        $Parents = self::GetParentsArray($SectionUUID);
        $Elements = "";
        $Names = self::GetNames();

        $ElementVars = [
            'SECTION_NAME' => "Главная",
            'SECTION_UUID' => ''
        ];
        $Elements .= Template::LoadStyle("breadcrumb/element.html", $ElementVars);

        foreach($Parents as $ParentSectionUUID){
            if(empty($ParentSectionUUID)){
                continue;
            }
            $ElementVars = [
                'SECTION_NAME' => $Names[$ParentSectionUUID],
                'SECTION_UUID' => $ParentSectionUUID
            ];
            $Elements .= Template::LoadStyle("breadcrumb/element.html", $ElementVars);
        }

        if($SectionUUID != null) {
            $ElementVars = [
                'SECTION_NAME' => $Names[$SectionUUID]
            ];
            $Elements .= Template::LoadStyle("breadcrumb/disabled_element.html", $ElementVars);
        }

        $MainVars = [
            'SECTIONS' => $Elements
        ];
        return Template::LoadStyle("breadcrumb/main.html", $MainVars);
    }
}