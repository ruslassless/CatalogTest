<?php

    const ACCESS_FROM_SITE = "AFS";

    require_once("kernel/kernel.php");

    $Content = "";
    $Notifications = Notification::Show();

    if(isset($_COOKIE['ELEMENT_MOVE_UUID'])){
        try{
            $Element = Element::FromDBByUUID($_COOKIE['ELEMENT_MOVE_UUID']);
        }
        catch(Exception $e){

        }
        $MoveVars = [
            'ELEMENT_NAME' => $Element->GetName(),
            'SECTION_UUID' => Kernel::Get("section_uuid")
        ];
        $Content .= Template::LoadStyle("elements/move.html", $MoveVars);
    }
    if(isset($_COOKIE['SECTION_MOVE_UUID'])){
        try{
            $Section = Section::FromDBByUUID($_COOKIE['SECTION_MOVE_UUID']);
        }
        catch(Exception $e){

        }
        $MoveVars = [
            'SECTION_NAME' => $Section->GetName(),
            'PARENT_UUID' => Kernel::Get("section_uuid")
        ];

        $Content .= Template::LoadStyle("sections/move.html", $MoveVars);
    }

    $Page = Kernel::Get("page");

    if(Kernel::Get('page') === 'sections'){
        if(Kernel::Get("section_uuid") !== null){
            if(!Kernel::UUIDValidate(Kernel::Get("section_uuid"))){
                Kernel::MoveTo("/?page=sections");
            }
            try{
                $Section = Section::FromDBByUUID(Kernel::Get("section_uuid"));
            }
            catch(Exception $e){
                Notification::Add("", Notification::Error, "Неизвестная ошибка!");
                Kernel::MoveTo("/");
            }
            if($Section->GetUpdated() == null){
                $Updated = "Никогда";
            }
            else{
                $Updated = Kernel::DateTimeModify(date("Y-m-d H:i:s", $Section->GetUpdated()));
            }

            if(!Kernel::UUIDValidate($Section->GetParentUUID())){
                $SectionParentName = "<em>В корне каталога</em>";
                $SectionParentUUID = null;
            }
            else{
                try{
                    $ParentSection = Section::FromDBByUUID($Section->GetParentUUID());
                    $SectionParentName = $ParentSection->GetName();
                    $SectionParentUUID = $ParentSection->GetUUID();
                }
                catch(Exception $e){
                    $SectionParentName = "<em style='color:red;'>Ошибка</em>";
                    $SectionParentUUID = null;
                }
            }



            $SectionTemplateVars = [
                'SECTION_UUID' => $Section->GetUUID(),
                'SECTION_NAME' => $Section->GetName(),
                'SECTION_CREATED' => Kernel::DateTimeModify(date("Y-m-d H:i:s", $Section->GetCreated())),
                'SECTION_UPDATED' => $Updated,
                'SECTION_DESCRIPTION' => $Section->GetDescription(),
                'SECTION_PARENT_UUID' => $SectionParentUUID,
                'SECTION_PARENT_NAME' => $SectionParentName,
                'FORM_TOKEN' => FORM_TOKEN
            ];
            $Content = Template::LoadStyle("sections/edit.html", $SectionTemplateVars);
        }
        else{
            if(Kernel::Get("parent_uuid") !== null){
                if(!Kernel::UUIDValidate(Kernel::Get("parent_uuid"))){
                    Kernel::MoveTo("/?page=sections");
                }
                try{
                    $Section = Section::FromDBByUUID(Kernel::Get("parent_uuid"));
                }
                catch(Exception $e){
                    Notification::Add("", Notification::Error, "Несуществующий раздел.");
                    Kernel::MoveTo("/");
                }
                $SectionName = $Section->GetName();
                $SectionUUID = $Section->GetUUID();
            }
            else{
                $SectionName = "<em>В корень каталога</em>";
                $SectionUUID = "";
            }
            $SectionTemplateVars = [
                'SECTION_UUID' => $SectionUUID,
                'SECTION_NAME' => $SectionName,
                'FORM_TOKEN' => FORM_TOKEN
            ];
            $Content .= Template::LoadStyle("sections/create.html", $SectionTemplateVars);
        }
    }
    else if(Kernel::Get('page') === 'elements'){
        if(Kernel::Get("element_uuid") !== null){
            if(!Kernel::UUIDValidate(Kernel::Get("element_uuid"))){
                Kernel::MoveTo("/?page=elements");
            }
            try{
                $Element = Element::FromDBByUUID(Kernel::Get("element_uuid"));
            }
            catch(Exception $e){
                Notification::Add("", Notification::Error, "Неизвестная ошибка!");
                Kernel::MoveTo("/");
            }
            if($Element->GetUpdated() == null){
                $Updated = "Никогда";
            }
            else{
                $Updated = Kernel::DateTimeModify(date("Y-m-d H:i:s", $Element->GetUpdated()));
            }
            if(!Kernel::UUIDValidate($Element->GetSectionUUID())){
                $ElementSectionName = "<em>В корне каталога</em>";
                $ElementSectionUUID = null;
            }
            else{
                try{
                    $Section = Section::FromDBByUUID($Element->GetSectionUUID());
                    $ElementSectionName = $Section->GetName();
                    $ElementSectionUUID = $Section->GetUUID();
                }
                catch(Exception $e){
                    $ElementSectionName = "<em style='color:red;'>Ошибка</em>";
                    $ElementSectionUUID = null;
                }
            }

            $Type = Kernel::ToString($Element->GetType());
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

            $ElementTemplateVars = [
                'ELEMENT_UUID' => $Element->GetUUID(),
                'ELEMENT_NAME' => $Element->GetName(),
                'ELEMENT_CREATED' => Kernel::DateTimeModify(date("Y-m-d H:i:s", $Element->GetCreated())),
                'ELEMENT_UPDATED' => $Updated,
                'ELEMENT_DATA' => $Element->GetData(),
                'ELEMENT_TYPE' => $Type,
                'ELEMENT_SECTION_UUID' => $ElementSectionUUID,
                'ELEMENT_SECTION_NAME' => $ElementSectionName,
                'FORM_TOKEN' => FORM_TOKEN
            ];
            $Content = Template::LoadStyle("elements/edit.html", $ElementTemplateVars);
        }
        else{
            if(Kernel::Get("section_uuid") !== null){
                if(!Kernel::UUIDValidate(Kernel::Get("section_uuid"))){
                    Kernel::MoveTo("/?page=elements");
                }
                try{
                    $Section = Section::FromDBByUUID(Kernel::Get("section_uuid"));
                }
                catch(Exception $e){
                    Notification::Add("", Notification::Error, "Несуществующий раздел.");
                    Kernel::MoveTo("/");
                }
                $SectionName = $Section->GetName();
                $SectionUUID = $Section->GetUUID();
            }
            else{
                $SectionName = "<em>В корень каталога</em>";
                $SectionUUID = "";
            }

            $ElementTemplateVars = [
                'SECTION_UUID' => $SectionUUID,
                'SECTION_NAME' => $SectionName,
                'FORM_TOKEN' => FORM_TOKEN
            ];
            $Content .= Template::LoadStyle("elements/create.html", $ElementTemplateVars);
        }
    }
    else{
        if(Kernel::Get("section_uuid") !== null){
            if(!Kernel::UUIDValidate(Kernel::Get("section_uuid"))){
                Kernel::MoveTo("/");
            }
            try{
                $Section = Section::FromDBByUUID(Kernel::Get("section_uuid"));
            }
            catch(Exception $e){
                Notification::Add("", Notification::Error, "Несуществующий раздел.");
                Kernel::MoveTo("/");
            }
            $Content .= Catalog::GetBreadcrumb(Kernel::Get("section_uuid"));
            $Catalog = Catalog::Get(Kernel::Get("section_uuid"));
        }
        else{
            $Content .= Catalog::GetBreadcrumb();
            $Catalog = Catalog::Get();
        }
        $SectionsTemplateVars = [
            'CURRENT_SECTION_UUID' => Kernel::Get("section_uuid"),
            'SECTIONS_LIST' => $Catalog
        ];
        $Content .= Template::LoadStyle("sections/list.html", $SectionsTemplateVars);
    }

    $IndexTemplateVars = [
        'SITE_TITLE'    => "Каталог",
        'STYLE_URL'     => STYLE_URL,
        'SITE_CONTENT'  => $Content,
        'NOTIFICATIONS' => $Notifications
    ];

    echo Template::LoadStyle("index.html", $IndexTemplateVars);