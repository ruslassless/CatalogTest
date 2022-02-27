<?php

class Section
{
    private ?string $UUID = null;
    private ?string $Name = null;
    private ?int    $Created = null;
    private ?int    $Updated = null;
    private ?string $Description = null;
    private ?string $ParentUUID = null;

    private function __construct(?string $UUID, ?string $Name, ?int $Created, ?int $Updated, ?string $Description, ?string $ParentUUID){
        $this->UUID  = $UUID;
        $this->Name  = $Name;
        $this->Created = $Created;
        $this->Updated = $Updated;
        $this->Description = $Description;
        $this->ParentUUID = $ParentUUID;
    }

    public static function Create() : ?static{
        return new static(null, null, null, null, null, null);
    }

    public static function FromDBByUUID(string $SectionUUID) : ?static{
        if(!Kernel::UUIDValidate($SectionUUID)){
            throw new Exception("INVALID_SECTION_UUID");
        }
        $SectionData = Kernel::DB()->getRow(
            "SELECT `name`,`created`,`updated`,`description`,`parent_uuid` FROM `sections` WHERE `uuid` = ?s",
            $SectionUUID
        );

        if($SectionData == false){
            throw new Exception("SECTION_BY_UUID_NOT_FOUND");
        }
        $Name        = Kernel::ToString($SectionData['name']);
        $Created     = Kernel::ToInt($SectionData['created']);
        $Updated     = Kernel::ToInt($SectionData['updated']);
        $Description = Kernel::ToString($SectionData['description']);
        $ParentUUID  = Kernel::ToString($SectionData['parent_uuid']);
        return new static($SectionUUID, $Name, $Created, $Updated, $Description, $ParentUUID);
    }

    public function GetUUID(): ?string{
        return $this->UUID;
    }

    public function SetUUID(?string $UUID) : void{
        $this->UUID = $UUID;
    }

    public function GetName(): ?string{
        return $this->Name;
    }

    public function SetName(?string $Name): void{
        $this->Name = $Name;
    }

    public function GetCreated(): ?int{
        return $this->Created;
    }

    public function GetUpdated(): ?int{
        return $this->Updated;
    }

    public function GetDescription(): ?string{
        return $this->Description;
    }

    public function SetDescription(?string $Description): void{
        $this->Description = $Description;
    }

    public function GetParentUUID(): ?string{
        return $this->ParentUUID;
    }

    public function SetParentUUID(?string $ParentUUID): void{
        $this->ParentUUID = $ParentUUID;
    }

    public function Save() : bool{
        $IsNew = true;
        if($this->UUID === null){
            $this->UUID = Kernel::GenerateUUIDv4();
            $this->Created = time();
        }
        else{
            $this->Updated = time();
            $IsNew = false;
        }
        if($this->Name === null){
            return false;
        }
        if($IsNew){
            $InsertData = [
                'uuid'        => $this->UUID,
                'name'        => $this->Name,
                'created'     => $this->Created,
                'updated'     => $this->Updated,
                'description' => $this->Description,
                'parent_uuid' => $this->ParentUUID,
            ];

            $InsertResult = Kernel::DB()->query("INSERT INTO `sections` SET ?u", $InsertData);
            if($InsertResult == false){
                return false;
            }
        }
        else{
            $UpdateData = [
                'name'        => $this->Name,
                'updated'     => $this->Updated,
                'description' => $this->Description,
                'parent_uuid' => $this->ParentUUID,
            ];

            $UpdateResult = Kernel::DB()->query("UPDATE `sections` SET ?u WHERE `uuid` = ?s", $UpdateData, $this->UUID);
            if($UpdateResult == false){
                return false;
            }
        }
        return true;
    }

    public function Remove(){
        if($this->UUID === null){
            throw new Exception("SECTION_IS_NULL");
        }
        $ElementsRemoveResult = Kernel::DB()->query("DELETE FROM `elements` WHERE `section_uuid` = ?s", $this->UUID);
        $ChildSectionsRemoveResult = Kernel::DB()->query("DELETE FROM `sections` WHERE `parent_uuid` = ?s", $this->UUID);
        $SectionRemoveResult = Kernel::DB()->query("DELETE FROM `sections` WHERE `uuid` = ?s", $this->UUID);
        if($SectionRemoveResult == false){
            throw new Exception("SECTION_REMOVE_ERROR");
        }
    }

    public function StartMove(){
        setcookie("SECTION_MOVE_UUID", $this->UUID, time() + 3600, "/", "", false, true);
        setcookie("ELEMENT_MOVE_UUID", null, time() - 1000, "/", "", false, true);
    }

    public function EndMove(){
        setcookie("SECTION_MOVE_UUID", $this->UUID, time() - 1000, "/", "", false, true);
    }
}