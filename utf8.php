<?php
  /**
   * Tests whether a string is UTF8
   */
  function is_utf8($string) {
    if(function_exists('mb_detect_encoding')) {
      return mb_detect_encoding($string, 'UTF-8, ISO-8859-1') === 'UTF-8';
    } else {
     
     // From http://w3.org/International/questions/qa-forms-utf-8.html
     return preg_match('%^(?:
           [\x09\x0A\x0D\x20-\x7E]            # ASCII
         | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
         |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
         | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
         |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
         |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
         | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
         |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
     )*$%xs', $string);
    }
  }

  /**
   * Decodes the string from UTF8, if it indeed is an UTF8 string
   */  
  function selective_decode_utf8($string) {
    if(is_utf8($string)) {
      return utf8_decode($string);
    }
    return $string;
  }
  
  /**
   * Encodes the string to UTF8, if it not already an UTF8 string
   */  
  function selective_encode_utf8($string) {
    if(!is_utf8($string)) {
      return utf8_encode($string);
    }
    return $string;
  }  
?>