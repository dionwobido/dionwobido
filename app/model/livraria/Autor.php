<?php
/**
 * Autor Active Record
 * @author  <your-name-here>
 */
class Autor extends TRecord
{
    const TABLENAME = 'autor';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('email');
    }


}
