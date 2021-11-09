<?php
/**
 * ItensDaVenda Active Record
 * @author  <your-name-here>
 */
class ItensDaVenda extends TRecord
{
    const TABLENAME = 'itens_da_venda';
    const PRIMARYKEY= 'venda_id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $venda;
    private $livro;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('livro_id');
        parent::addAttribute('qtd');
        parent::addAttribute('subtotal');
    }

    
    /**
     * Method set_venda
     * Sample of usage: $itens_da_venda->venda = $object;
     * @param $object Instance of Venda
     */
    public function set_venda(Venda $object)
    {
        $this->venda = $object;
        $this->venda_id = $object->id;
    }
    
    /**
     * Method get_venda
     * Sample of usage: $itens_da_venda->venda->attribute;
     * @returns Venda instance
     */
    public function get_venda()
    {
        // loads the associated object
        if (empty($this->venda))
            $this->venda = new Venda($this->venda_id);
    
        // returns the associated object
        return $this->venda;
    }
    
    
    /**
     * Method set_livro
     * Sample of usage: $itens_da_venda->livro = $object;
     * @param $object Instance of Livro
     */
    public function set_livro(Livro $object)
    {
        $this->livro = $object;
        $this->livro_id = $object->id;
    }
    
    /**
     * Method get_livro
     * Sample of usage: $itens_da_venda->livro->attribute;
     * @returns Livro instance
     */
    public function get_livro()
    {
        // loads the associated object
        if (empty($this->livro))
            $this->livro = new Livro($this->livro_id);
    
        // returns the associated object
        return $this->livro;
    }
    


}
