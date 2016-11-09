<?php

namespace chalvik\storagefiles\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\imagine\Image;
use Imagine\Image\Point;
use Imagine\Image\Box;

/**
 * This is the model class for table "storage_files".
 *
 * @property integer $id
 * @property string $pname
 * @property integer $pid
 * @property string $ptag
 * @property string $path
 * @property string $type
 * @property integer $size
 * @property string $name
 * @property string $origin_name
 * @property integer $order
 * @property string $ip
 * @property string $created_at
 * 
 */
class StorageFiles extends ActiveRecord
{
    
    /**
     * @var yii\web\UploadedFile  Object file for upload and save 
     */
    private  $file;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'storagefiles';
    }

    public function behaviors()
    {
        return array(
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => new Expression('NOW()'),
                'attributes' => array(
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ),
            ],     
        );
    }    
    
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        
        return [
            
            [['pname','pid', 'path', 'type', 'size', 'ip','origin_name'], 'required'],
            [['pid', 'size', 'order'], 'integer'],
            [['created_at'], 'safe'],
            [['pname','name', 'path', 'type', 'ip'], 'string', 'max' => 255],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => "ID",
            'pname'         => "Parent name Model",
            'pid'           => "id  parent Model",
            'path'          => "Path to file",
            'type'          => "Type file",
            'size'          => "Size file",
            'name'          => "Translate name file",
            'origin_name'   => "Origin name file",
            'order'         => "Order by",
            'ip'            => "ip uploader",
            'created_at'    => "Create date",
        ];
    }
    


    
    
    /**
    *  Сохраняет файл  
    */      
    static function saveFile($model,$file) 
    {
        
        
        
    }        

    
    /**
     * Удаляет файлы 
     * @param $files (array) or (string) имя файла с абсолютным путем или массив имен
     * @return mixed
     */    
    private function deleteFiles($files){
//        if (is_array($files)) {
//            foreach ($files as $file) {
//                if (file_exists($file) ) {
//                    unlink($file);
//                }
//            }
//        }
//        else {
//            
//            if (file_exists($files) ) {
//                unlink($files);
//            }
//        }
    }    
    
    
    /**
    *  Удаляет файлы с диска 
    * 
    */      
    public function removeFile() 
    {
//        $path  = Yii::$app->storagefiles->path;
//        
//        if ($this->model) {
//            $modelname = strtolower($this->model);  
//        }         
//        $thumbsPath = Yii::getAlias($path)."/thumbs/".$modelname;
//        array_map("unlink", glob($thumbsPath."/*".$this->slug.".*"));
//        
//        if (file_exists($this->path)) {
//            unlink($this->path);
//        }    
//        $this->delete();
        
    }    
    
    
/**
    *  Удаляет файлы с диска  привязанные к заданной записи 
    * 
    */      
    static function removeFiles($model, $parent_id) 
    {
//        $storagefiles = \common\models\StorageFiles::find()
//                ->where([
//                  'model'      => $model,  
//                  'parent_id'  => $parent_id,  
//                ])
//                ->all();
//        foreach ($storagefiles as $file) {
//            $file->removeFile();
//        }
    }        

    
}
