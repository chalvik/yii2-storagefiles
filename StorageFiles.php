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
    public $no_image = 'default.jpg';
    
    
    /**
     * @var array   настройки создания поддерикторий
     * enable_id -  is true    создавать поддерикторию с ай ди ресурса
     * enable_model -  is true  создавать поддерикторию с названием сущности
     */           
    public $subdir =[
        'enable_id'=>false,
    ];
    
    
    /**
     * Загружает файлы  для $model 
     * @param object $model
     * @param string $ptag
     * @param string $fild 
     * @return boolean
     */    
    public function upload($model,$ptag = null, $field = 'files') {

        if (isset($model->id)) {
            if (Yii::$app->request->isPost) {
                $files = UploadedFile::getInstances($model, $field);
                if ($files) {
                    
                    $model_name = $this->getModelName($model);
                    
                    $path_files =[];
                    
                    foreach ($files as $key=>$file) {

                        $transaction = Yii::$app->db->beginTransaction();
                        try {        
        
                            $name = time().".". $file->extension;
                            $path = $this->saveFile($file, $modelname,$name);
                            
                            if ($path) {
                                
                                $path_files[$key] = $path;                                  
                                
                            } else {
                                
                                throw StorageFilesExeption();                                
                                
                            }
                            
                            
                            $sf = new StorageFiles();
                            
                            $sf->pname          = $model_name;
                            $sf->pid            = $model->id;
                            $sf->ptap           = $ptag;
                            $sf->type           = $file->type;
                            $sf->size           = $file->size;
                            $sf->path           = $path_files[$key];
                            $sf->name           = $name;
                            $sf->origin_name    = $file->baseName;
                            if (Yii::$app->request->getIsConsoleRequest() === false) {
                                 $sf->ip = Yii::$app->request->getUserIP();
                            }
                            
                            if (! $sf->save()) {
                                
                                throw StorageFilesExeption();
                                
                            }
                            
                            $transaction->commit();
                            
                        } catch (Exception $e) {                        
                            
                            foreach ($path_files as $file_path) {
                                
                                unlink($file_path);                                
                                
                            }
                            
                            $transaction->rollback();
                            return false;
                        }         
                    }
                }
            }            

        }
    }
    
    
    /**
     * Удалить запись о файле с файлом вместе
     * @param integer $id
     * @return mixed
     */    
    public function delete($id){
        $output = null;
        if (($model = ModelStorageFiles::findOne($id)) !== null) {
            
            $file = $model->path;
            $this->deleteFiles($file);
            
            $model->delete($id);
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
        
        $output = null;
        $modelname = $this->getModelName($model);  
        
        if (($msfiles = ModelStorageFiles::find()->where([
                'model' =>  $modelname,
                'parent_id' =>  $model->id,
            ])->all()) !== null) {
            
            
            foreach ($msfiles as $msfile) { 
                $this->delete($msfile->id);
                $msfile->delete();
            }            
        
    }
        return $output;                
    }    
    /**
     * Сохраняет файл , возвращает полный путь к файлу, если удачно 
     * не удачно null 
     * @param object file 
     * @return string
     */    
    private function saveFile($file,$modelname,$file_name){
        
        $Path = Yii::getAlias($this->path)."/".$modelname;
            
        if(!file_exists($Path)) {
            mkdir($Path,0755,TRUE);
        }
            
        $fullPath = $Path.'/'. $model->slug .".". $file->extension;
        if (! $file->saveAs($fullPath)) {
            $fullPath =null;
        }
        return $this->path."/".$modelname.'/'. $file_name;
    }    

   
    /**
     * Удаляет файлы 
     * @param $files (array) or (string) имя файла с абсолютным путем или массив имен
     * @return mixed
     */    
    private function deleteFiles($files){
        if (is_array($files)) {
            foreach ($files as $file) {
                if (file_exists($file) ) {
                    unlink($file);
                }
            }
        }
        else {
            
            if (file_exists($files) ) {
                unlink($files);
            }
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
        
    
}
