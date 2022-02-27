<?php

class DB_Interface
{
    private string $Host = "";
    private string $Login = "";
    private string $Password = "";
    private string $DataBaseName = "";

    private SafeMySQL $SafeMySQL;

    public function __construct(string $Host, string $Login, string $Password, string $DataBaseName){
        $this->Login        = $Login;
        $this->Password     = $Password;
        $this->DataBaseName = $DataBaseName;
        $this->Host         = $Host;

        $ConnectionDataArray = array(
            'host' => $this->Host,
            'user' => $this->Login,
            'pass' => $this->Password,
            'db' => $this->DataBaseName
        );

        $this->SafeMySQL = new SafeMysql($ConnectionDataArray);
    }

    public function DB() : SafeMySQL{
        return $this->SafeMySQL;
    }
}