<?php 

namespace chalvik\storagefiles;

use yii\base\Component;
use chalvik\storagefiles\models\StorageFiles as ModelStorageFiles;
use yii\web\UploadedFile;
use Yii;
use chalvik\storagefiles\exeptions\StorageFilesExeption;

class StorageFiles extends Component
{
    
    /**
     * @var string   основная директория для хранения файлов  
     */       
    public $path;

    /**
     * @var string   base url
     */       
    public $base_url = '/uploads';
    
    
    /**
     * @var string   имя картинки по умолчанию
     */       
    public $no_image = '@app/vendor/chalvik/storagefiles/images/no_picture.jpg';
    
    
    /**
     * @var array   настройки создания поддерикторий
     * enable_id -  is true    создавать поддерикторию с ай ди ресурса
     * enable_model -  is true  создавать поддерикторию с названием сущности
     */           
    public $path_setting =[
        'enable_id'=>false,
        'enable_model'=>true,
    ];
    
    
    /**
     * Загружает файлы  для $model 
     * @param object $model
     * @param string $ptag
     * @param string $fild 
     * @return integer   
     */    
    public function upload($model,$field = 'files',$crop=null) 
    {

        if (isset($model->id)) {
            if (Yii::$app->request->isPost) {
                $files = UploadedFile::getInstances($model, $field);
                if ($files) {
                    $modelname = $this->getModelName($model);
                    $path_files =[];
                    
                    foreach ($files as $key=>$file) {
                        
                        $transaction = Yii::$app->db->beginTransaction();
                        try {        
                            
                            $path_to_save = $this->getPath($model);
                            $sf = new ModelStorageFiles();                            
                            $sf->setFile($file);
                            $sf->setBasePath($path_to_save);
                            $sf->parent_name = $this->getModelName($model);
                            $sf->parent_id = $model->id;
                            
                            if (Yii::$app->request->getIsConsoleRequest() === false) {
                                 $sf->ip = Yii::$app->request->getUserIP();
                            }
                            
                            if (! $sf->save()) {
                                
                                throw new \yii\base\Exception();
                            }
                            
                            $transaction->commit();
                            return $sf->id; 
                            
                        } catch (Exception $e) {                        
                            
                            $transaction->rollback();
                            return false;
                        }         
                    }
                }
            }            

        }
    }


    /**
     * Возвращает имя модели к которой  принадлежит объект
     * @param ActiveRecord $model
     * @return string
     */    
    private  function getModelName($model){

        $modelname = get_class($model);
        $m = explode('\\', $modelname);
        $modelname = end($m);     
        
        return mb_strtolower($modelname);
    } 
    
    
    /**
     * Возвращает путь для записи картинок 
     * @param ActiveRecord $model
     * @return mixed
     */    
    private  function getPath($model){
        
        $path = $this->path.'/'.$this->getModelName($model).'/';
        // формирование пути по правилу $path_setting ****************************************************************************
        
        
        
        return $path;                
        
    }
    
    
    
    
    /**
     * Удалить запись о файле с файлом вместе
     * @param integer $id
     * @return mixed
     */    
    public function delete($id){
        
        $output = null;
        if (($model = ModelStorageFiles::findOne($id)) !== null) {            
            $output = $model->delete($id);            
        }
        return $output;                
        
    }
    
    
    /**
     * Удаляет все записи о файле с файлами вместе
     * для заданной модели
     * 
     * @param object $model
     * @return mixed
     */    
    public function deleteAll($model){
        
        $modelname = $this->getModelName($model);  
        
        $msfiles = ModelStorageFiles::find()
            ->where([
                'model' =>  $modelname,
                'parent_id' =>  $model->id,
                ])
            ->all();
                
            if($msfiles) {
            
                $transaction = Yii::$app->db->beginTransaction();
                try {        
                    foreach ($msfiles as $msfile) { 
                        $this->delete($msfile->id);
                        $msfile->delete();
                    }
                } catch (Exception $e) {                        

                    // удаляем файлы если загрузка произошла неуспешно
                    foreach ($path_files as $file_path) {
                        unlink($file_path);                                
                    }

                    $transaction->rollback();
                    return false;
                }         
        
            }
        return true;                
    }    

    
}
