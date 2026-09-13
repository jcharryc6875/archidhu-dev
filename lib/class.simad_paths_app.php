<?php
class simad_paths_app
{
    var $source_paths = array();
    var $source_alias = array();

    public function SimadPathsApp()
    {
        simad_paths_app::ReadConfigFileApp();
    }

    public static function readConfigFileApp()
    {
        try
        {
            //config file app.ini
            $app_config = sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'app.ini';
            //*************************************************************** */
            if(file_exists($app_config))
            {
                $ini_array = parse_ini_file(sfConfig::get('sf_config_dir')."/app.ini");

                $rutas_basicas = isset($ini_array['ruta_default']) ? $ini_array['ruta_default'] : null; // en la unidad solo son 5
                $rutas_archivo = isset($ini_array['ruta_archivo']) ? $ini_array['ruta_archivo'] : null; // 
                $source_paths = array_unique(array_merge($rutas_basicas, $rutas_archivo));


                $alias_default = isset($ini_array['alias_default']) ? $ini_array['alias_default'] : null; // en la unidad solo son 5
                $alias_archivo = isset($ini_array['alias_archivo']) ? $ini_array['alias_archivo'] : null; // 
                $source_alias = array_unique(array_merge($alias_archivo, $alias_default));

                $result = array( 'source_paths' => $source_paths, 'source_alias' => $source_alias );
                return $result;
            }   
            else
            {
                throw new Exception( 'Error: El archivo de configuracion app.ini no existe en la ruta ' . $app_config);

            }
        }
        catch (\Exception $th) 
        {
            return null;
        }
    }

    public static function resolveRelativePath($relativePath, $pathDefault = array())
    {
        try {
            if(empty($relativePath)){
                return null;
            }
            //******************************************************************************************
            if(empty($pathDefault)){
                $pathDefault = self::readConfigFileApp();
            }
            //******************************************************************************************
            $cusource_alias = $pathDefault['source_alias'];
            $cusource_paths = $pathDefault['source_paths'];
            //******************************************************************************************
            $furl = preg_replace('~//+~', '/', $relativePath);  
            $p3url = str_replace($cusource_alias, '/', $furl);
            //******************************************************************************************
            foreach ($cusource_paths as $fpath)  
            {
                if(file_exists($fpath . DIRECTORY_SEPARATOR . $p3url))
                {
                    return simad_util::NormalizePath($fpath . DIRECTORY_SEPARATOR . $p3url);
                }
            }
            //******************************************************************************************
            return null;
        } catch (\Exception $th) {
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }
}
?>