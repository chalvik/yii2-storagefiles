<?php

namespace  chalvik\storagefiles\intefaces;

/**
 *  
 *  Interface for Storagefiles component
 * @author Chernogor Alexey <chalvik27@mail.ru>
 * 
 */

interface iStorageFiles
{

    /**
     * Get path from directory  
     * 
     * @return string 
     */
    public function getPath(); 
    
    /**
     * Set path from directory filesystem server for upload a file
     * 
     * @param string $path  Path for upload file in a server
     * @return boolen 
     */
    public function setPath(); 
    
}