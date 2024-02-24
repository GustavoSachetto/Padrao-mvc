<?php

namespace App\Utils\Cache;

class File
{
    /**
     * Armazena a hash do cache
     * @var string
     */
    private static $hash;

    /**
     * Armazena o arquivo de cache
     * @var mixed
     */
    private static $cacheFile;

    /**
     * Armazena o tempo da expiração do arquivo de cache
     * @var mixed
     */
    private static $expiration;

    /**
     * Método responsável por validar o tempo de expiração do conteúdo no cache
     * @return boolean
     */
    private static function validateCacheExpiration()
    {
        $createTime = filectime(self::$cacheFile);
        $diffTime = time() - $createTime;
        
        if ($diffTime > self::$expiration) return false;

        return true;
    }

    /**
     * Método responsável por retornar o caminho até o arquivo de cache
     * @param string
     * @return string
     */
    private static function getFilePath() {
        $dir = getenv('CACHE_DIR');

        if (!file_exists($dir)) {
            mkdir($dir,0755,true);
        }

        return $dir.'/'.self::$hash;
    }


    /**
     * Método responsável por retornar o conteúdo gravado no cache
     * @param string $hash
     * @param int $expiration
     * @return mixed
     */
    private static function getContentCache() {
        self::$cacheFile = self::getFilePath();

        if (!file_exists(self::$cacheFile)) {
            return false;
        } elseif (!self::validateCacheExpiration()) {
            return false;
        }

        $serialize = file_get_contents(self::$cacheFile);

        return unserialize($serialize);
    }

    /**
     * Método responsável por guardar informações no cache
     * @param string $hash
     * @param mixed $content
     * @return boolean
     */
    private static function storageCache($content)
    {
        $serialize = serialize($content);
        $cacheFile = self::getFilePath();

        return file_put_contents($cacheFile,$serialize);
    }

    /**
     * Método responsável por obter uma informação do cache
     * @param string $hash
     * @param integer $espiration
     * @param Closure $function
     * @return mixed
     */
    public static function getCache($hash, $expiration, $function)
    {
        self::$hash = $hash;
        self::$expiration = $expiration;

        if ($content = self::getContentCache()) {
            return $content;
        } 

        $content = $function();
        self::storageCache($content);

        return $content;
    }
}
