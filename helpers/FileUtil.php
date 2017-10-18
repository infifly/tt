<?php
namespace TT\helpers;
function dump_array(Array $data, $short = null)
{
    $short = $short === null ? version_compare(File::$minPHP ?: PHP_VERSION, '5.4.0', '>=') : $short;
    $php   = $short ? "[" : "array(";
    foreach ($data as $key => $value) {
        $php .= var_export($key, true) . "=>";
        if (is_array($value)) {
            $php .= dump_array($value, $short);
        } else if(is_float($value)) {
            $php .= number_format($value, 2);
        } else {
            $php .= var_export($value, true);
        }
        $php .= ",";
    }
    if (substr($php,-1) == ',') {
        $php = substr($php, 0, -1);
    }
    $php .= $short ? "]": ")";
    return $php;
}
class FileUtil{
    public static $minPHP;
    protected static $gen;
    /**
     *  Dump array into a file. Similar to *var_dump* but the result
     *  is not human readable (reduces space by a third in large arrays)
     */
    public static function dumpArray($path, Array $data, $perm = 0644)
    {
        self::write($path, "<?php return " . dump_array($data) . ';', $perm);
    }
    /**
     *  Similar to ::write but after writing the file content
     *  it will include the newest code and return its output.
     *
     *  This function solves an HHVM issue that exists requiring
     *  the same file more than once in the same program runtime.
     *
     *  It only works with pure PHP (not with mixed HTML and PHP), and it
     *  must begin with `<?php`.
     *
     *
     *  @param  string $path    File path
     *  @param  string $code    Source code
     *  @param  array  $args    Array of variables to share with the include
     *
     *  @return mixed
     */
    public static function writeAndInclude($path, $code, Array $args = array())
    {
        self::write($path, $code);
        extract($args);
        if (defined('HHVM_VERSION')) {

            chdir(dirname($path));
            return eval(substr($code, 5));
        }
        return require $path;
    }

    /**
     * 生成目录
     * @param $path
     */
    public static function createDirs($path){
        $dir = dirname($path);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                throw new \RuntimeException("Cannot create directory {$dir}");
            }
        }
    }

    /**
     *  Writes a file atomically, using a temporary and then moving
     *  the file.
     *
     *  @param string $path     File path to write
     *  @param string $content  File content
     *  @param int    $perm     File permissions (default 0644)
     *
     *  @return bool
     */
    public static function write($path, $content, $perm = 0644)
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                throw new \RuntimeException("Cannot create directory {$dir}");
            }
        }
        $tmp = tempnam($dir, "crodas_file_");
        if (file_put_contents($tmp, $content) === false) {
            throw new \RuntimeException("Failed to write temporary file ({$tmp})");
        }
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        if (is_file($path)) {
            if (!unlink($path)) {
                throw new \RuntimeException("Failed to remove old file");
            }
        }
        if (!rename($tmp, $path)) {
            throw new \RuntimeException("Failed to move temporary file");
        }
        if (is_int($perm)) {
            chmod($path, $perm);
        }
        if (is_callable('opcache_invalidate')) {
            opcache_invalidate($path, true);
        } else if (is_callable('apc_clear_cache')) {
            apc_clear_cache();
        }
        return true;
    }
    /**
     *  Given a few identifier (1 or more) it would return
     *  a filepath that can be used consistently among requets
     *  and it is safely stored in the temp directory of the OS
     *
     *  @return string
     */
    public static function generateFilepath()
    {
        $args = func_get_args();
        if (empty($args)) {
            throw new \RuntimeException("You would need to give us at least one identifier");
        }
        $gen = self::$gen;
        $dir = $gen($args[0]);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                throw new \RuntimeException("cannot create directory $dir");
            }
        }
        $path = $dir . sha1(implode("\0", $args));

        if (!is_file($path)) {
            FileUtil::write($path, '');
        }
        return $path;
    }
    public static function overrideFilepathGenerator($fnc)
    {
        if (!is_callable($fnc)) {
            throw new \InvalidArgumentException("Expecting a callable");
        }
        self::$gen = $fnc;
    }
}
FileUtil::overrideFilepathGenerator(function($prefix) {
    return sys_get_temp_dir() . DIRECTORY_SEPARATOR . $prefix . DIRECTORY_SEPARATOR;
});