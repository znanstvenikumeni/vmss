<?php


namespace vmss\Client;



use vmss\Server\Error;

class ClientManager
{
    public function addClient($name){
        $Config = new \vmss\Server\Config();
        $PublicKey = bin2hex(openssl_random_pseudo_bytes($Config->randomStringLength));
        $SecretKey = bin2hex(openssl_random_pseudo_bytes($Config->randomStringLength));
        $Hash = password_hash($SecretKey, $Config->privateKeyAlgorithm);
        $Client = new \vmss\Client\Client(-1, $name, bin2hex(openssl_random_pseudo_bytes($Config->randomStringLength)), $Hash);
        $PDOFactory = new \vmss\Server\PDOConnectionFactory();
        $PDO = $PDOFactory->connect();
        $SQLQuery = "INSERT INTO allowedClients VALUES (null, :name, :publicKey, :secretKey)";
        $Params['name'] = $name;
        $Params['publicKey'] = $PublicKey;
        $Params['secretKey'] = $Hash;
        $Statement = $PDO->prepare($SQLQuery);
        try {
            $Statement->execute($Params);
        }
        catch(\Exception $e){
            $Return['result'] = 'error';
            $Return['exception'] = $e;
            return $Return;
        }
        $Return['result'] = 'success';
        $Return['name'] = $name;
        $Return['publicKey'] = $PublicKey;
        $Return['secretKey'] = $SecretKey;
        return $Return;
    }
    public function searchByPublicKey($publicKey){
        $PDOFactory = new \vmss\Server\PDOConnectionFactory();
        $PDO = $PDOFactory->connect();
        $PDO->setAttribute(\PDO::ATTR_EMULATE_PREPARES,false);
        $SQLQuery = "SELECT * FROM allowedClients WHERE publicKey = :publicKey";
        $Params['publicKey'] = $publicKey;
        $Statement = $PDO->prepare($SQLQuery);

        try {
            $Res = $Statement->execute($Params);
            $ResultSet = $Statement->fetch(\PDO::FETCH_ASSOC);

        }
        catch(\Exception $e){
            new Error('Internal vmss error', 'vmss\Client\ClientManager\searchByPublicKey');
        }
        if(!$ResultSet) return new Client(0, 0, 0, 0);
        $Client = new Client($ResultSet['id'], $ResultSet['name'], $ResultSet['publicKey'], $ResultSet['secretKey']);
        return $Client;
    }
}