<?php

class Element
{
    private ?string $UUID = null;
    private ?string $Name = null;
    private ?int    $Created = null;
    private ?int    $Updated = null;
    private ?string $Data = null;
    private ?string $SectionUUID = null;
    private ?string $Type = null;

    private function __construct(?string $UUID, ?string $Name, ?int $Created, ?int $Updated, ?string $Data, ?string $SectionUUID, ?string $Type){
        $this->UUID  = $UUID;
        $this->Name  = $Name;
        $this->Created = $Created;
        $this->Updated = $Updated;
        $this->Data = $Data;
        $this->SectionUUID = $SectionUUID;
        $this->Type = $Type;
    }

    public static function Create() : ?static{
        return new static(null, null, null, null, null, null, null);
    }

    public static function FromDBByUUID(string $ElementUUID) : ?static{
        if(!Kernel::UUIDValidate($ElementUUID)){
            throw new Exception("INVALID_ELEMENT_UUID");
        }
        $ElementData = Kernel::DB()->getRow(
            "SELECT `name`,`created`,`updated`,`data`,`section_uuid`,`type` FROM `elements` WHERE `uuid` = ?s",
            $ElementUUID
        );

        if($ElementData == false){
            throw new Exception("ELEMENT_BY_UUID_NOT_FOUND");
        }
        $Name        = Kernel::ToString($ElementData['name']);
        $Created     = Kernel::ToInt($ElementData['created']);
        $Updated     = Kernel::ToInt($ElementData['updated']);
        $Data        = Kernel::ToString($ElementData['data']);
        $SectionUUID = Kernel::ToString($ElementData['section_uuid']);
        $Type        = Kernel::ToString($ElementData['type']);
        return new static($ElementUUID, $Name, $Created, $Updated, $Data, $SectionUUID, $Type);
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

    public function GetType(): ?string{
        return $this->Type;
    }

    public function SetType(?string $Type): void{
        $this->Type = $Type;
    }

    public function GetCreated(): ?int{
        return $this->Created;
    }

    public function GetUpdated(): ?int{
        return $this->Updated;
    }

    public function GetData(): ?string{
        return $this->Data;
    }

    public function SetData(?string $Data): void{
        $this->Data = $Data;
    }

    public function GetSectionUUID(): ?string{
        return $this->SectionUUID;
    }

    public function SetSectionUUID(?string $SectionUUID): void{
        $this->SectionUUID = $SectionUUID;
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
                'uuid'         => $this->UUID,
                'name'         => $this->Name,
                'created'      => $this->Created,
                'updated'      => $this->Updated,
                'data'         => $this->Data,
                'section_uuid' => $this->SectionUUID,
                'type'         => $this->Type
            ];

            $InsertResult = Kernel::DB()->query("INSERT INTO `elements` SET ?u", $InsertData);
            if($InsertResult == false){
                return false;
            }
        }
        else{
            $UpdateData = [
                'name'         => $this->Name,
                'updated'      => $this->Updated,
                'data'         => $this->Data,
                'section_uuid' => $this->SectionUUID,
                'type'         => $this->Type
            ];

            $UpdateResult = Kernel::DB()->query("UPDATE `elements` SET ?u WHERE `uuid` = ?s", $UpdateData, $this->UUID);
            if($UpdateResult == false){
                return false;
            }
        }
        return true;
    }

    public function Remove(){
        if($this->UUID === null){
            throw new Exception("ELEMENT_IS_NULL");
        }
        $RemoveResult = Kernel::DB()->query("DELETE FROM `elements` WHERE `uuid` = ?s", $this->UUID);
        if($RemoveResult == false){
            throw new Exception("ELEMENT_REMOVE_ERROR");
        }
    }

    public function StartMove(){
        setcookie("ELEMENT_MOVE_UUID", $this->UUID, time() + 3600, "/", "", false, true);
        setcookie("SECTION_MOVE_UUID", null, time() - 1000, "/", "", false, true);
    }

    public function EndMove(){
        setcookie("ELEMENT_MOVE_UUID", $this->UUID, time() - 1000, "/", "", false, true);
    }
}