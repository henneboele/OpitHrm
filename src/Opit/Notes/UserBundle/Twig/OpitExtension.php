<?php

/*
 *  This file is part of the Edk bundle.
 * 
 *  (c) Opit Consulting Kft. <info@opit.hu>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace Opit\Notes\UserBundle\Twig;

/**
 * Twig OpitExtension class
 *
 * @author OPIT Consulting Kft. - EDK/TAO Team - {@link http://www.opit.hu}
 * @version 1.0
 * @package OpitNotesUserBundle
 */
class OpitExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('humanize', array($this, 'humanizeFilter')),
            new \Twig_SimpleFilter('underscore', array($this, 'underscoreFilter')),
            new \Twig_SimpleFilter('camelize', array($this, 'camelizeFilter')),
            new \Twig_SimpleFilter('classname', array($this, 'classnameFilter'))
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'strpos' => new \Twig_Function_Method($this, 'strpos'),
            'splitText' => new \Twig_Function_Method($this, 'splitText')
        );
    }

    /**
     * Returns the given underscored_word_group as a Human Readable Word Group.
     * (Underscores are replaced by spaces and capitalized following words.)
     * 
     * @param string $lowerCaseAndUnderscoredWord
     * @return string Human readable string
     */
    public function humanizeFilter($lowerCaseAndUnderscoredWord)
    {
        $result = ucwords(str_replace('_', ' ', $lowerCaseAndUnderscoredWord));
        
        return $result;
    }
    
    /**
     * Returns the given lower_case_and_underscored_word as a CamelCased word.
     * 
     * @param string $lowerCaseAndUnderscoredWord
     * @return string Camelized word
     */
    public function camelizeFilter($lowerCaseAndUnderscoredWord)
    {
        $result = str_replace(' ', '', $this->humanizeFilter($lowerCaseAndUnderscoredWord));
        
        return $result;
    }
    
    /**
     * Returns the given camelCasedWord as an underscored_word.
     * 
     * @param string $camelCasedWord
     * @return string Underscore syntaxed string
     */
    public function underscoreFilter($camelCasedWord)
    {
        $result = strtolower(preg_replace('/([A-Z]?)([A-Z][a-z])/', '$1_$2', $camelCasedWord));
        
        return $result;
    }
    
    /**
     * Returns the position of where the needle exists relative to the beginning of the haystack string
     * 
     * @param type $haystack
     * @param type $needle
     * @return type
     */   
    public function strpos($haystack, $needle)
    {
       $result =  strpos($haystack, (string) $needle);
       
       return $result;
    }
    
    /**
     * 
     * @param string $text the string you want to split
     * @param sting $pattern the pattern at where you want to split the text
     * @param integer $index which index of the splitted text to return
     * @return type
     */
    public function splitText($text, $pattern, $index)
    {
        $split = split($pattern, $text);
        return $split[$index];
    }
   

    public function getName()
    {
        return 'opit_extension';
    }
}

?>