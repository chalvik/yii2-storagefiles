<?php 

namespace chalvik\storagefiles;

use yii\base\Action;
use yii\base\Component;
use common\models\StorageFiles as ModelStorageFiles;
use yii\web\UploadedFile;
use Yii;
use yii\web\ServerErrorHttpException;
use yii\imagine\Image;
use Imagine\Image\Point;
use Imagine\Image\Box;

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
    public $no_image;
    
    /**
     * @var array   настройки создания поддерикторий
     * enable_id -  is true    создавать поддерикторию с ай ди русурса
     * enable_model -  is true  создавать поддерикторию с названием сущности
     */           
    public $subdir =[
        'enable_id'=>false,
    ];
    
    
    /**
     * Загружает файлы  для $model 
     * переданные через форму ( ActiveRecord  StorageFiles )
     * @param object $model
     * @return boolean
     */    
    public function upload($model,$model_name=null,$field = 'files'){

        if (isset($model->id)) {

            if (Yii::$app->request->isPost) {

                $files = UploadedFile::getInstances($model, $field);
                if ($files) {
                    foreach ($files as $file) {
                        
                        if (!$model_name) {
                            $model_name = $this->getModelName($model);
                        }
                        $fullPath = 'none';
                        
                        $main = ModelStorageFiles::find()
                                ->where([
                                    'model'     => $model_name,
                                    'parent_id' => $model->id,
                                    'main'      => true,
                                ])->count();
//                        

                        $sf = new ModelStorageFiles();
                        $sf->model          = $model_name;
                        $sf->parent_id      = $model->id;
                        $sf->path           = $fullPath;
                        $sf->type           = $file->type;
                        $sf->size           = $file->size;
                        $sf->origin_name    = $file->baseName;
                        $sf->main           = (int)(!$main > 0);
                        if (Yii::$app->request->getIsConsoleRequest() === false) {
                             $sf->upload_ip = Yii::$app->request->getUserIP();
                        }

                        $sf->save();

                        $fullPath = $this->saveFile($file,$sf);
                            
                        if ($fullPath) {
                            $sf->updateAttributes(['path'=>$fullPath]);
                        }
                        else {
                            $sf->delete();
                        }
                    }
                }
            }            

        }
    }
    
    
    /**
     * Загружает файлы  по имени 
     * переданные через форму ( ActiveRecord  StorageFiles )
     * @param object $model
     * @return boolean
     */    
    public function uploadByName($model,$name){

        if (isset($model->id)) {

            if (Yii::$app->request->isPost) {

                $files = UploadedFile::getInstancesByName($name);
                if ($files) {

                    $this->deleteAll($model);
                    
                    foreach ($files as $file) {
                        
                        $model_name = $this->getModelName($model);
                        $fullPath = 'none';
                        
                        $main = ModelStorageFiles::find()
                                ->where([
                                    'model'     => $model_name,
                                    'parent_id' => $model->id,
                                    'main'      => true,
                                ])->count();
//                        

                        $sf = new ModelStorageFiles();
                        $sf->model          = $model_name;
                        $sf->parent_id      = $model->id;
                        $sf->path           = $fullPath;
                        $sf->type           = $file->type;
                        $sf->size           = $file->size;
                        $sf->origin_name    = $file->baseName;
                        $sf->main           = (int)(!$main > 0);
                        if (Yii::$app->request->getIsConsoleRequest() === false) {
                             $sf->upload_ip = Yii::$app->request->getUserIP();
                        }

                        $sf->save();

                        $fullPath = $this->saveFile($file,$sf);
                            
                        if ($fullPath) {
                            $sf->updateAttributes(['path'=>$fullPath]);
                        }
                        else {
                            $sf->delete();
                        }
                    }
                }
            }            

        }
    }
    
    

    /**
     * Загружает файлы  для $model 
     * переданные через форму ( ActiveRecord  StorageFiles )
     * @param object $model
     * @param object $options ['dataX'=>'','dataX'=>'','dataWidth'=>'','dataHeight'=>'',]
     * @param object $model
     * @return boolean
     */    
    public function UploadAndCrop($model, \frontend\models\Cropper $model_crop,$field = 'file'){

        if (isset($model->id) && $model_crop->validate()) {
            
            if (Yii::$app->request->isPost) {

                $file = UploadedFile::getInstances($model_crop, $field);
                
                if (isset($file[0])) {
                    $file= $file[0];
                        $modelname = $this->getModelName($model);  

                        $fullPath = 'none';
                        
                        $main = ModelStorageFiles::find()
                                ->where([
                                    'model'     => $modelname, 
                                    'parent_id' => $model->id,
                                    'main'      => true,
                                ])->count();

                        $sf = new ModelStorageFiles();
                        $sf->model          = $modelname; 
                        $sf->parent_id      = $model->id;
                        $sf->path           = $fullPath;
                        $sf->type           = $file->type;
                        $sf->size           = $file->size;
                        $sf->origin_name    = $file->baseName;
                        $sf->main           = (int)(!$main > 0);
                        
                        if (Yii::$app->request->getIsConsoleRequest() === false) {
                             $sf->upload_ip = Yii::$app->request->getUserIP();
                        }
                        
                        $sf->save();
                        
//                        $fullPath = $this->saveFile($file,$sf);
                         $fullPath =     $this->saveCropFile($file,$sf,$model_crop);
                                
                        if ($fullPath) {
                            $sf->updateAttributes(['path'=>$fullPath]);                        
                        } 
                        else {
                            $sf->delete();
                            throw new ServerErrorHttpException(' Файл не был загружен ');
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
    private function saveFile($file,$model){
        
        $modelname = strtolower($model->model);
        
        $Path = Yii::getAlias($this->path)."/".$modelname;
            
        if(!file_exists($Path)) {
            mkdir($Path,0755,TRUE);
        }
            
        $fullPath = $Path.'/'. $model->slug .".". $file->extension;
        if (! $file->saveAs($fullPath)) {
            $fullPath =null;
        }
        return $this->path."/".$modelname.'/'. $model->slug .".". $file->extension;;
    }    

    /**
     * Сохраняет обрезанную копию загруженного файла, правила в модели $model_crop
     * не удачно null 
     * @param object file 
     * @return string
     */    
    private function saveCropFile($file,$model,$model_crop){
        
        $modelname = strtolower($model->model);
        
        $Path = Yii::getAlias($this->path)."/".$modelname;
            
        if(!file_exists($Path)) {
            mkdir($Path,0755,TRUE);
        }
            
        $fullPath = $Path.'/'. $model->slug .".". $file->extension;
        
        if (is_uploaded_file($file->tempName)) {
            
            $cropimage  =  Image::getImagine()->open($file->tempName);
            $point      =  new Point($model_crop['dataX'], $model_crop['dataY']);
            $box        =  new Box($model_crop['dataWidth'], $model_crop['dataHeight']);
            $cropimage  = $cropimage->crop($point, $box);
            $cropimage = $cropimage->save($fullPath,['quality' => 90]);
            
        }          
                
        return $fullPath;
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
    public function getModelName($model){

        $modelname = get_class($model);
        $m = explode('\\', $modelname);
        $modelname = end($m);     
        
        return $modelname;
    }    

    
//    /**
//     * Возвращает url к файлу  
//     * @param object ModelStorageFiles $file 
//     * @return string
//     */     
//    public function getUrl($file,$width,$height){
//        
//        if ($file) {
//            $output =  $this->base_url.$file->Thumb($width,$height);
//        }
//        else {
//            $file =  new ModelStorageFiles();
//            $file->path ="/nonefile";
//            $output = $this->base_url.$file->Thumb($width,$height);
//        }
//        return $output;
//    }
        
    
}
