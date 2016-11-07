<?php
namespace chalvik\storagefiles\helpers\File;

use Yii;
use chalvik\storagefiles\models\StorageFiles;

/**
 * 
 * Helper for StorageFiles 
 * @author Alexey Chernogor 
 * 
 */

class File
{
    /*
     * Возвращает адрес к картинке, если  $width и $height то возвращаем адрес оригинальной картинки
     * @param object  $file     - Объект StorageFiles
     * @param integer $width    - Логин пользователя в системе
     * @param integer $height   - Логин пользователя в системе
     * @param string $none_file - путь к файлу, относительно паки /uploads/
     * @param $string url img
     */
    public static function url($file, $width = null, $height = null, $none_file = null)
    {
        if (!$file) {
            $file = new StorageFiles();
            $file->path = "/nonefile";
        }
        
        $base_url = Yii::$app->storagefiles->base_url;
        
        if (!$width && !$height) {
            $output = $base_url . $file->Url();
        } else {
            $output = $base_url . $file->Thumb($width, $height);
        }
        
        return $output;
    }
}
