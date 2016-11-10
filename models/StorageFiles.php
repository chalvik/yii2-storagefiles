<?php

namespace chalvik\storagefiles\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\imagine\Image;
use Imagine\Image\Point;
use Imagine\Image\Box;
use yii\web\UploadedFile;
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
     * @var ActiveRecord  Object for which the upload  file
     */
    private  $model;
    
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
            
            [['path', 'type', 'size', 'ip','origin_name'], 'required'],
            [['size'], 'integer'],
            [['name', 'path', 'type', 'ip'], 'string', 'max' => 255],
            [['file'], 'validateFile'],
            
        ];
    }

    
    /**
     *  Set Uploadfile object 
     *  
     * @var $file UploadFiles  
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file; 
    }
    

    
    /**
     * Validate  object Uploadfile
     * 
     */    
    public function validateFile()
    {
        if (!$this->file) {
            $this->addError('file', 'Empty object Uploadfiles');
        } elseif ($this->file->error) {
            $this->addError('file', 'field error is true  from object Uploadfiles');
        } elseif ($this->file->name) {
            $this->addError('file', 'Empty field name from object Uploadfiles');
        } elseif ($this->file->tempName) {
            $this->addError('file', 'Empty field tempName from object Uploadfiles');
        }
        
    }    
    
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => "ID",
            'file'          => "Object Uploadfiles",
            'path'          => "Path to file",
            'type'          => "Type file",
            'size'          => "Size file",
            'name'          => "Translate name file",
            'origin_name'   => "Origin name file",
            'ip'            => "ip uploader",
            'created_at'    => "Create date",
        ];
    }
    

    /**
     *  Before Save
     * 
     * @inheritdoc
     */
    public  function beforeSave($insert) {
        
        if (parent::beforeSave($insert)) {
            
            $name = time().".". $this->file->extension;
            $this->type         = $this->file->type;
            $this->size         = $this->file->size;
            $this->path         = $path_files[$key];
            $this->name         = $name;
            $this->origin_name  = $this->file->baseName;        

            // сохранить файл 
            // заполнить 
            
            $event = new ModelEvent;
            
        return $event->isValid;
        }

        
        
    }
        
        
    /**
     * Возвращает имя модели к которой  принадлежит объект
     * @param object $model 
     * @return string
     */    
    private  function getModelName($model){

        $modelname = get_class($model);
        $m = explode('\\', $modelname);
        $modelname = end($m);     
        
        return $modelname;
    }          
        
        
        















//
//                            $name = time().".". $file->extension;
//                            $path = $this->saveFile($file, $modelname,$name);
//                            
//                            if ($path) {
//                                $path_files[$key] = $path;                                  
//                            } else {
//                                throw StorageFilesExeption();                                
//                            }






















    /**
    *  Сохраняет файл  
    */      
    private function saveFile($model,$file) 
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
