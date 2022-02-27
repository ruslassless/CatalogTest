<?php

    const ACCESS_FROM_SITE = "000";

    require_once "kernel.php";

    if(Kernel::Post("RemoveElementUUID") !== null){
        try{
            $Element = Element::FromDBByUUID(Kernel::Post("RemoveElementUUID"));
            $Element->Remove();
            Notification::Add("Элемент успешно удален.", Notification::Success, "Удалено успешно");
            Kernel::MoveTo("/");
        }
        catch(Exception $e){
            Notification::Add("", Notification::Error, "Неизвестная ошибка!");
            Kernel::MoveTo("/");
        }
    }
    if(Kernel::Post("MoveElementUUID") !== null){
        try{
            $Element = Element::FromDBByUUID(Kernel::Post("MoveElementUUID"));
            $Element->StartMove();
            Kernel::MoveTo("/");
        }
        catch(Exception $e){
            Kernel::MoveTo("/");
        }
    }
    if(Kernel::Post("MovedElementSectionUUID") !== null){
        try{
            $Element = Element::FromDBByUUID($_COOKIE['ELEMENT_MOVE_UUID']);
            $SectionUUID = Kernel::Post("NewElementSectionUUID");
            if($SectionUUID != null){
                $NewSection = Section::FromDBByUUID($SectionUUID);
            }
            else{
                $SectionUUID = null;
            }
            $Element->SetSectionUUID($SectionUUID);
            $Element->Save();
            $Element->EndMove();
            Kernel::MoveTo("/?section_uuid=".$SectionUUID);
        }
        catch(Exception $e){
            Notification::Add("", Notification::Error, "Неизвестная ошибка!");
            Kernel::MoveTo("/");
        }
    }
    if(Kernel::Post("CancelMoveElement") !== null){
        try{
            $Element = Element::FromDBByUUID($_COOKIE['ELEMENT_MOVE_UUID']);
            $Element->EndMove();
            Kernel::MoveTo("/");
        }
        catch(Exception $e){
            Notification::Add("", Notification::Error, "Неизвестная ошибка!");
            Kernel::MoveTo("/");
        }
    }
    $ItsCreate = false;
    if(Kernel::Get("element_uuid") !== null){
        if(!Kernel::UUIDValidate(Kernel::Get("element_uuid"))){
            Kernel::MoveTo("/");
        }
        $Name        = Kernel::Post("ElementName");
        $Data        = Kernel::Post("ElementData");
        $SectionUUID = Kernel::Post("ElementSectionUUID");
        $Type        = Kernel::Post("ElementType");
        try{
            $Element = Element::FromDBByUUID(Kernel::Get("element_uuid"));
        }
        catch(Exception $e){
            Notification::Add("", Notification::Error, "Неизвестная ошибка!");
            Kernel::MoveTo("/");
        }
    }
    else{
        $Name        = Kernel::Post("NewElementName");
        $Data        = Kernel::Post("NewElementData");
        $SectionUUID = Kernel::Post("NewElementSectionUUID");
        $Type        = Kernel::Post("NewElementType");

        $Element = Element::Create();
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
    if(count_chars(Kernel::ToString($Data)) < 1){
        Notification::Add("Поле \"Содержание\" не может быть пустым.", Notification::Error, "Ошибка!");
        $HaveErrors = true;
    }
    if($ItsCreate){
        switch($Type){
            case 'Article':
                $Type = "ARTICLE";
                break;
            case 'Paper':
                $Type = "PAPER";
                break;
            case 'Review':
                $Type = "REVIEW";
                break;
            case 'Comment':
                $Type = "COMMENT";
                break;
            default:
                Notification::Add("Поле \"Тип\" содержит неверное значение.", Notification::Error, "Ошибка!");
                $HaveErrors = true;
                break;
        }
    }

    if($SectionUUID == null){
        $SectionUUID = null;
    }
    else{
        try{
            Section::FromDBByUUID($SectionUUID);
        }
        catch(Exception $e){
            Notification::Add("Неверный идентификатор раздела.", Notification::Error, "Ошибка!");
            $HaveErrors = true;
        }
    }

    if($HaveErrors){
        Kernel::MoveTo("/");
    }

    $Element->SetName($Name);
    $Element->SetData($Data);
    $Element->SetSectionUUID($SectionUUID);
    if($ItsCreate){
        $Element->SetType($Type);
    }
    if($Element->Save() === false){
        if($ItsCreate){
            Notification::Add("При создании элемента произошла ошибка. Пожалуйста, повторите попытку позднее.", Notification::Error, "Ошибка!");
            Kernel::MoveTo("/");
        }
        else{
            Notification::Add("При сохранении элемента произошла ошибка. Пожалуйста, повторите попытку позднее.", Notification::Error, "Ошибка!");
            Kernel::MoveTo("/?page=elements&element_uuid=".Kernel::Get("element_uuid"));
        }
    }
    if($ItsCreate){
        Notification::Add("Элемент успешно создан.", Notification::Success, "Успешно!");
        Kernel::MoveTo("/");
    }
    else{
        Notification::Add("Изменения элемента успешно сохранены.", Notification::Success, "Успешно!");
        Kernel::MoveTo("/?page=elements&element_uuid=".Kernel::Get("element_uuid"));
    }
