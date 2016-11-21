<?php

namespace chalvik\storagefiles\models;

use Yii;
use yii\imagine\Image;
use Imagine\Image\Point;
use Imagine\Image\Box;
use chalvik\storagefiles\exeptions\StorageFilesExeption; 



/**
 * This is the model class for table "storage_files".
 * 
 */
class CropImage
{
    /**
     * Объект сохраненного файла
     * @var StorageFiles 
     */
    
    private $storagefiles;
    
    private $path;
    
    /**
     * Название  директории в которую будет нарезатся данная картинка
     * @var string 
     */
//    private $alias;

    
    
    function __construct(StorageFiles $storagefiles) {
        
        $this->storagefiles = $storagefiles; 
        
    }
    
    /**
     *  Устанавливает путь для хранения нарезанных картинок
     * @param type $path
     */
    function setPath($path) {
        
        $this->storagefiles = $storagefiles; 
        
    }

    /**
     *  Возвращает путь, где хранятся нарезанные картиноки
     * @param type $path
     */    
    function getPath() {
        $path = '/thumb/'; 
        if ($this->storagefiles) {
            $path = $this->storagefiles;
        }
        
        $this->storagefiles = $storagefiles; 
        
    }
    
    
    /**
    * Возвращает путь к thumb картинки
    *  если thumb картинки нет то она создается
    *  Если картинка не задана возвращает thumb  картинк по умолчанию
    * @param integer $width  ширина thumb
    * @param integer $height высота  thumb
    * @param sring $image путь к файлу
    * @return mixed
    * 
    */  
    public function Thumb($width=null,$height=null,$getpath=false)
    {
        $file_name = "";
        $image_default = false;
        
        
        $relative_path = '/default/';

        $path  = Yii::$app->storagefiles->path;

        $image =  Yii::getAlias($path)."/default/".$this->no_image;
        $modelname = '';
        if ($this->model) {
            $modelname = strtolower($this->model);  
        }    
        // Если есть  картинка на диске
        
         $this->path = Yii::getAlias($this->path);
        if ( file_exists($this->path) ) {
            $thumbsPath = Yii::getAlias($path)."/thumbs/".$modelname;
            $relative_path = '/thumbs/'.$modelname;
            if(!file_exists($thumbsPath)) {
                mkdir($thumbsPath,0755,TRUE);
                chmod($thumbsPath, 0777);
            }            
            $image = $this->path;            
        }        
        elseif (file_exists($image))  {
            $thumbsPath = Yii::getAlias($path)."/thumbs/default";
            $relative_path = "/thumbs/default";
            if(!file_exists($thumbsPath)) {
                mkdir($thumbsPath,0755,TRUE);
                chmod($thumbsPath, 0777);
            }
            $image_default = true;
        }
        else {
            $image = null;
        }
        
        if ($image) {

            /**Формирование  имени нового thumb файла */
            
            if ($height && $width) {
                $file_name = $width.'x'.$height;                 
            } elseif($height) {
                $file_name ='_x'.$height ;
            } else {
                $file_name  = $width.'x_' ;              
            }
            
            if ($image_default) {
                $file_name = $file_name.'-'.$this->no_image.'.png';                                
            }
            else {
                $file_name = $file_name.'-'.$this->slug.'.png';                
            }
            /*    --- Конец формирование имени ---   */

            
            $file_path = $thumbsPath.'/'.$file_name;

            if (!file_exists($file_path)) 
                {
                    //        $watermark = Image::getImagine()->open(Yii::getAlias('@webroot/img/logo_diolls.png'))->resize(new Box(50, 50));
                                        
                    if ($width && $height) { 
                        $cropimage =  $this->CropImage($image, $width, $height);
                    } else {
                        $cropimage =  $this->ResizeImage($image, $width);                        
                    }
                    
                    
                    $cropimage = $cropimage->save($file_path,['quality' => 90, 'format'=>'png']);
            }
        } 
        else {
            $file_path = $image;
        }
        
        return ($getpath)?$file_path:$relative_path.'/'.$file_name; 
    }    

    /**
     *  
     * возвразает объект Image кропнутой картинки 
     * @param object $image путь до оригинальной картинки   
     * @return Image object
     */      
    private function CropImage($image, $width,$height)            
    {
        
        
        $cropimage =  Image::getImagine()->open($image);
        
        $size = getimagesize($image);
//        $Wimage =  new Image();
//        $box = new Box($width, $height);
        
        $x=0;
        $y=0;
        $w = $size[0];
        $h = $size[1];

        $hx = $height;
        $wx = round($hx*$w/$h);
        if ($wx<$width) {
            $wx = $width; 
            $hx = round($wx*$h/$w);                 
            $y = round($hx/2-$height/2);                  
        } else {
            $x = round($wx/2-$width/2);                  
        }

        $point =  new Point($x, $y);
        $cropimage = $cropimage->resize(new Box($wx,$hx));
        $cropimage = $cropimage->crop($point, new Box($width, $height));        
      
        return $cropimage;
    }

    /**
     *  
     * возвразает объект Image кропнутой картинки 
     * @param object $from_model  
     * @return Image object
     */      
    private function ResizeImage($image,$width=null,$height=null)            
    {
        
//        $resImage = null; 
            $cropimage =  Image::getImagine()->open($image);
        
        if ($width || $height) {
            
            $cropimage =  Image::getImagine()->open($image);
            $size = getimagesize($image);
        
            $x=0;
            $y=0;
            $w = $size[0];
            $h = $size[1];


//        print_r($width);
//        print_r($height);
        
            if ($width) {
                $wx = $width;
                $hx = round($wx*$h/$w);                
            }
            
            if ($height) {
                $hx = $height;
                $wx = round($hx*$w/$h);
                
            }
  
//            print_r($wx);
//            print_r($hx);
//            die();
            $cropimage = $cropimage->resize(new Box($wx,$hx));            

        } 
        
        return $cropimage;
        
    }
    
    
    /**
    * Возвращает url к оригинальной картинке
    *  Если картинка не задана возвращает thumb  картинк по умолчанию
    * @return mixed
    * 
    */  
    public function Url()
    {
        // формируем url   /
        
//        $path  = Yii::$app->storagefiles->path;
//        print_r($path);
        
        $str = explode(Yii::$app->storagefiles->base_url,$this->path);
        
//        echo "<pre>";
//        print_r($str);
//        echo "</pre>";
//        
//        die();
        
        return $str[1];
    }    



    
    /**
    *  Удаляет файлы с диска 
    * 
    */      
    public function removeFile() 
    {
        $path  = Yii::$app->storagefiles->path;
        
        if ($this->model) {
            $modelname = strtolower($this->model);  
        }         
        $thumbsPath = Yii::getAlias($path)."/thumbs/".$modelname;
        array_map("unlink", glob($thumbsPath."/*".$this->slug.".*"));
        
        if (file_exists($this->path)) {
            unlink($this->path);
        }    
        $this->delete();
        
    }    
    
    
    /**
    *  Копирует записи файлов которые принадлежат  модели $from_model
    *  для модели $to_model
    * @param object $from_model  
    * @param object $to_model 
    * @return mixed
    * 
    */  
   static function copyToModel($from_model,$to_model) 
    {
       
        $from_modelname = get_class($from_model);
        $m = explode('\\', $from_modelname);
        $from_modelname = end($m);       
       
        $to_modelname = get_class($to_model);
        $m = explode('\\', $to_modelname);
        $to_modelname = end($m);       
        
        $storagefiles = StorageFiles::find() 
              ->where([
                  'model'     => $from_modelname,
                  'parent_id' => $from_model->id,
              ])
              ->all();  
        
        foreach ($storagefiles as $sf) {
            $to_sf = StorageFiles::find() 
                  ->where([
                      'model'     => $to_modelname,
                      'parent_id' => $sf->parent_id,
                      'path'      => $sf->path,
                  ])
                  ->count();
            if(!$to_sf) {
                $to_sf = new StorageFiles();
                $to_sf->setAttributes($sf->getAttributes());
                $to_sf->model=$to_modelname;
                $to_sf->save();
            }
            
        }        
    
        
    }        
/**
    *  Удаляет файлы с диска  привязанные к заданной записи 
    * 
    */      
    static function removeFiles($model, $parent_id) 
    {
        $storagefiles = \common\models\StorageFiles::find()
                ->where([
                  'model'      => $model,  
                  'parent_id'  => $parent_id,  
                ])
                ->all();
        foreach ($storagefiles as $file) {
            $file->removeFile();
        }
    }        
    
}
