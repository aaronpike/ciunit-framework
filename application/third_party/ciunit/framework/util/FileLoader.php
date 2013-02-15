<?php
/**
 * CIUnit
 *
 * Copyright (c) 2012, Agop Seropyan <agopseropyan@gmail.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Agop Seropyan nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    CIUnit
 * @subpackage Util
 * @author     Agop Seropyan <agopseropyan@gmail.com>
 * @copyright  2012, Agop Seropyan <agopseropyan@gmail.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @since      File available since Release 1.0.0
 */

class CIUnit_Util_FileLoader
{
    public static function checkAndLoad($filename)
    {
        $testsAvailable = self::directory_map(TRUE);
        
        if(FALSE === $testsAvailable)
            throw new CIUnit_Framework_Exception_CIUnitException("CIUnit can't");
        
        foreach ($testsAvailable as $test) {
            self::load($test);
        }
    }   
    
    public static function load($filePath)
    { 
        if(!file_exists($filePath) and is_readable($filePath))
            throw new CIUnit_Framework_Exception_CIUnitException(sprintf("CIUnit can't open file %s for reading!", $filePath));
            
        include_once $filePath; 
        
        return $filePath;
    } 
    
    public static function directory_map($fullPath = FALSE, $directory_depth = 0, $hidden = FALSE)
    {
        $ci =& get_instance();
        $ci->load->add_package_path(APPPATH.'third_party/ciunit', FALSE);
        $ci->config->load('config');
        $source = $ci->config->item('test_dir');
        $source_dir = APPPATH . $source;
        
        if(!file_exists($source_dir) || !is_readable($source_dir))
            throw new CIUnit_Framework_Exception_CIUnitException(sprintf("CIUnit can't open or read your %s folder", $source));
        
        if ($fp = @opendir($source_dir))
        {
            $filedata	= array();
            $new_depth	= $directory_depth - 1;
            $source_dir	= rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    
            while (FALSE !== ($file = readdir($fp))){
                // Remove '.', '..', and hidden files [optional]
                if ( ! trim($file, '.') OR ($hidden == FALSE && $file[0] == '.')) {
                    continue;
                } 
                
                if(substr(strrchr($file,'.'),1) == 'php') {
                    if($fullPath) { 
                        $filedata[] = $source_dir . $file;
                    }
                    else {
                        $filedata[] = substr($file, 0, strrpos($file, '.'));
                    } 
                }
            }
    
            closedir($fp);
            return $filedata;
        }
    
        return FALSE;
    }
}

?>