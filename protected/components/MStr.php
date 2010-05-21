<?php
/* 
 * MStr class file.
 * Some static functions to do string manipulations.
 */

class MStr {

    /**
     * Shorten a text aften the maximum specified length and adds "..." at its end.
     * @param <string> $str text to be shorten
     * @param <int> $maxlength how many characters to return
     * @return <string>
     */
    public static function shorten($str,$maxlength) {
        if (strlen($str) > $maxlength) {
            $str = substr($str,0,$maxlength);
            $str = strripos($str,' ') == false ?: substr($str,0,strripos($str,' '));
            $str = $str.' ...';
        }
        return $str;
    }

    /**
     * Removes withespaces and other unwanted characters to clean a string (to do SEO)
     * @param <string> $str string to be cleaned
     * @return <string> cleaned string
     */
    public static function seoFormat($str) {
        $table = array(
                ' '=>'-','Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
                'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
                'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
                'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
                'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
                'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
                'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
        );
        $str = strtr(trim($str), $table);
        $str = preg_replace('`[^\w-]`i', '', $str);
        return CHtml::encode(trim($str,'-'));
    }
}