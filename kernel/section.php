<?php

    const ACCESS_FROM_SITE = "000";

    require_once "kernel.php";

    if(Kernel::Post("RemoveSectionUUID") !== null){
        try{
            $Section = Section::FromDBByUUID(Kernel::Post("RemoveSectionUUID"));
            $Section->Remove();
            Notification::Add("Раздел успешно удален.", Notification::Success, "Удалено успешно");
            Kernel::MoveTo("/");
        }
        catch(Exception $e){
            Notification::Add("", Notification::Error, "Неизвестная ошибка!");
            Kernel::MoveTo("/");
        }
    }
    if(Kernel::Post("MoveSectionUUID") !== null){
        try{
            $Section = Section::FromDBByUUID(Kernel::Post("MoveSectionUUID"));
            $Section->StartMove();
            Kernel::MoveTo("/");
        }
        catch(Exception $e){
            Kernel::MoveTo("/");
        }
    }
    if(Kernel::Post("MovedSectionParentUUID") !== null){
        try{
            $Section = Section::FromDBByUUID($_COOKIE['SECTION_MOVE_UUID']);
            $ParentUUID = Kernel::Post("NewSectionParentUUID");
            if($ParentUUID != null){
                $NewSection = Section::FromDBByUUID($ParentUUID);
            }
            else{
                $ParentUUID = null;
            }
            if($Section->GetUUID() == $ParentUUID){
                Notification::Add("Так сделать нельзя.", Notification::Error, "Ошибка!");
                Kernel::MoveTo("/?section_uuid=".$ParentUUID);
            }
            if(in_array($Section->GetUUID(), Catalog::GetParentsArray($ParentUUID)) == 1){
                Notification::Add("Так сделать нельзя.", Notification::Error, "Ошибка!");
                Kernel::MoveTo("/?section_uuid=".$ParentUUID);
            }
            $Section->SetParentUUID($ParentUUID);
            $Section->Save();
            $Section->EndMove();
            Kernel::MoveTo("/?section_uuid=".$ParentUUID);
        }
        catch(Exception $e){
            Notification::Add("", Notification::Error, "Неизвестная ошибка!");
            Kernel::MoveTo("/");
        }
    }
    if(Kernel::Post("CancelMoveSection") !== null){
        try{
            $Section = Section::FromDBByUUID($_COOKIE['SECTION_MOVE_UUID']);
            $Section->EndMove();
            Kernel::MoveTo("/?section_uuid=".$_COOKIE['SECTION_MOVE_UUID']);
        }
        catch(Exception $e){
            Notification::Add("", Notification::Error, "Неизвестная ошибка!");
            Kernel::MoveTo("/");
        }
    }
    $ItsCreate = false;
    if(Kernel::Get("section_uuid") !== null){
        if(!Kernel::UUIDValidate(Kernel::Get("section_uuid"))){
            Kernel::MoveTo("/");
        }
        $Name        = Kernel::Post("SectionName");
        $Description = Kernel::Post("SectionDescription");
        $ParentUUID  = Kernel::Post("SectionParentUUID");
        try{
            $Section = Section::FromDBByUUID(Kernel::Get("section_uuid"));
        }
        catch(Exception $e){
            Notification::Add("", Notification::Error, "Неизвестная ошибка!");
            Kernel::MoveTo("/");
        }
    }
    else{
        $Name        = Kernel::Post("NewSectionName");
        $Description = Kernel::Post("NewSectionDescription");
        $ParentUUID  = Kernel::Post("NewSectionParentUUID");
        $Section = Section::Create();
        $ItsCreate = true;
    }
    $FormToken = Kernel::Post("FormToken");

    if(!Kernel::CheckAntiCSRFToken($FormToken)){
        Kernel::MoveTo("/");
    }
    $HaveErrors = false;
    if(count_chars(Kernel::ToString($Name)) < 1){
        Notification::Add("Поле \"Имя\" не может быть пустым.", Notification::Error, "Ошибка!");
        $HaveErrors = true;
    }
    if(count_chars(Kernel::ToString($Description)) < 1){
        Notification::Add("Поле \"Описание\" не может быть пустым.", Notification::Error, "Ошибка!");
        $HaveErrors = true;
    }
    if($ParentUUID == null){
        $ParentUUID = null;
    }
    else{
        try{
            Section::FromDBByUUID($ParentUUID);
        }
        catch(Exception $e){
            Notification::Add("Неверный идентификатор раздела.", Notification::Error, "Ошибка!");
            $HaveErrors = true;
        }
    }

    if($HaveErrors){
        Kernel::MoveTo("/");
    }

    $Section->SetName($Name);
    $Section->SetDescription($Description);
    $Section->SetParentUUID($ParentUUID);
    if($Section->Save() === false){
        if($ItsCreate){
            Notification::Add("При создании раздела произошла ошибка. Пожалуйста, повторите попытку позднее.", Notification::Error, "Ошибка!");
            Kernel::MoveTo("/");
        }
        else{
            Notification::Add("При сохранении раздела произошла ошибка. Пожалуйста, повторите попытку позднее.", Notification::Error, "Ошибка!");
            Kernel::MoveTo("/?page=sections&section_uuid=".Kernel::Get("section_uuid"));
        }
    }
    if($ItsCreate){
        Notification::Add("Раздел успешно создан.", Notification::Success, "Успешно!");
        Kernel::MoveTo("/");
    }
    else{
        Notification::Add("Изменения раздела успешно сохранены.", Notification::Success, "Успешно!");
        Kernel::MoveTo("/?page=sections&section_uuid=".Kernel::Get("section_uuid"));
    }
