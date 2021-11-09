<?php
/**
 * Editora Active Record
 * @author  <your-name-here>
 */
class Editora extends TRecord
{
    const TABLENAME = 'editora';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('fone');
    }


}
