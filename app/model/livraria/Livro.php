<?php
/**
 * Livro Active Record
 * @author  <your-name-here>
 */
class Livro extends TRecord
{
    const TABLENAME = 'livro';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $editora;
    private $autors;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('titulo');
        parent::addAttribute('preco');
        parent::addAttribute('estoque');
        parent::addAttribute('genero_id');
        parent::addAttribute('editora_id');
    }

    
    /**
     * Method set_editora
     * Sample of usage: $livro->editora = $object;
     * @param $object Instance of Editora
     */
    public function set_editora(Editora $object)
    {
        $this->editora = $object;
        $this->editora_id = $object->id;
    }
    
    /**
     * Method get_editora
     * Sample of usage: $livro->editora->attribute;
     * @returns Editora instance
     */
    public function get_editora()
    {
        // loads the associated object
        if (empty($this->editora))
            $this->editora = new Editora($this->editora_id);
    
        // returns the associated object
        return $this->editora;
    }
    
    
    /**
     * Method addAutor
     * Add a Autor to the Livro
     * @param $object Instance of Autor
     */
    public function addAutor(Autor $object)
    {
        $this->autors[] = $object;
    }
    
    /**
     * Method getAutors
     * Return the Livro' Autor's
     * @return Collection of Autor
     */
    public function getAutors()
    {
        return $this->autors;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->autors = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
        $this->autors = parent::loadAggregate('Autor', 'Escreve', 'livro_id', 'autor_id', $id);
    
        // load the object itself
        return parent::load($id);
    }

    /**
     * Store the object and its aggregates
     */
    public function store()
    {
        // store the object itself
        parent::store();
    
        parent::saveAggregate('Escreve', 'livro_id', 'autor_id', $this->id, $this->autors);
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        parent::deleteComposite('Escreve', 'livro_id', $id);
    
        // delete the object itself
        parent::delete($id);
    }


}
