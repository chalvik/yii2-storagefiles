<?php

namespace chalvik\storagefiles\models;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\UploadedFile;
use chalvik\storagefiles\exeptions\StorageFilesExeption;
use Yii;
//use yii\web\UploadedFile;
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
    public  $file;
    

    /**
     * @var string Базовый путь для сохранения файлов 
     */
    public  $base_path;

    
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
            
            [['parent_name','parent_id','base_path'], 'required'],
            [['ip'], 'string', 'max' => 255],
            [['file'], 'validateFile'],
            [['parent_model','path','type','size','name','origin_name','created_at'], 'safe']
            
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
     *  Set Uploadfile object 
     *  
     * @var $file UploadFiles  
     */
    public function setBasePath($base_path)
    {
        $this->base_path = $base_path; 
    }
    
    /**
     * Validate  object Uploadfile
     * 
     */    
    public function validateFile()
    {
//        if (!$this->file) {
//            $this->addError('file', 'Empty object Uploadfiles');
//        } elseif ($this->file->error) {
//            $this->addError('file', 'field error is true  from object Uploadfiles');
//        } elseif ($this->file->name) {
//            $this->addError('file', 'Empty field name from object Uploadfiles');
//        } elseif ($this->file->tempName) {
//            $this->addError('file', 'Empty field tempName from object Uploadfiles');
//        }
        
    }    

    /**
     * Validate  object Uploadfile
     * 
     */    
    public function validateParentModel()
    {
        if (!$this->parent_model) {
            $this->addError('parent_model', 'Empty object Parent Model');
        } 
        
    }    

    
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => "ID",
            'parent_name'   => "Parent Name",
            'parent_id'     => "Parent Id",
            'path'          => "Path to file",
            'type'          => "Type file",
            'size'          => "Size file",
            'name'          => "Translate name file",
            'origin_name'   => "Origin name file",
            'ip'            => "ip uploader",
            'created_at'    => "Create date",
            'file'          => "Object Uploadfiles",
        ];
    }
    

    /**
     *  Before Save
     * 
     * @inheritdoc
     */
    public  function beforeSave($insert) {
        
        $this->type             = $this->file->type;
        $this->size             = $this->file->size;
        $this->name             = time();
        $this->origin_name      = $this->file->baseName;        

        
        // сохранить файл 
        $path = $this->saveFile($this->name);
        
        
        if ($path) {
            $this->path = $path;
        } else {
            $this->addError('file',"Fail save file ");
            return false;
        }

        return parent::beforeSave($insert);
        
    }

    /**
     *  Before Delete
     * 
     * @inheritdoc
     */
    public  function beforeDelete() {
        if (parent::beforeDelete()) {
            
            return $this->removeFile();
            
        }            
    }

    
    /**
    *  Сохраняет файл  
    */      
    private function saveFile($name) 
    {
        
        $base_path = Yii::getAlias($this->base_path);
        
        $result = false;
        
        if(!file_exists($base_path)) {
            if (! mkdir($base_path,0755,TRUE) ) {
                return false;
            }
        }
        
        $relative_path =  $name;
        
        $fullPath = $base_path.$relative_path;
        
        if ($this->file->saveAs($fullPath)) {
            
           $result = $this->base_path.$relative_path; 
            
        } 
        
        return $result;
        
    }        
        
        

    
    /**
    *  Удаляет файлы с диска 
    * @return boolean 
    */      
    public function removeFile() 
    {
        $result = false; 
        if (file_exists($this->path)) {
            $result = unlink($this->path);
        }            
        
        return $result;
    }    
    

    
}
