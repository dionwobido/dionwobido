<?php
/**
 * LivroReport Report
 * @author  <your name here>
 */
class LivroReport extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Livro_report');
        $this->form->setFormTitle('Livro Report');
        

        // create the form fields
        $id = new TEntry('id');
        $titulo = new TEntry('titulo');
        $preco = new TEntry('preco');
        $estoque = new TEntry('estoque');
        $genero_id = new TEntry('genero_id');
        $editora_id = new TEntry('editora_id');
        $output_type = new TRadioGroup('output_type');


        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Titulo') ], [ $titulo ] );
        $this->form->addFields( [ new TLabel('Preco') ], [ $preco ] );
        $this->form->addFields( [ new TLabel('Estoque') ], [ $estoque ] );
        $this->form->addFields( [ new TLabel('Genero_id') ], [ $genero_id ] );
        $this->form->addFields( [ new TLabel('Editora_id') ], [ $editora_id ] );
        $this->form->addFields( [ new TLabel('Output') ], [ $output_type ] );

        $output_type->addValidation('Output', new TRequiredValidator);


        // set sizes
        $id->setSize('100%');
        $titulo->setSize('100%');
        $preco->setSize('100%');
        $estoque->setSize('100%');
        $genero_id->setSize('100%');
        $editora_id->setSize('100%');
        $output_type->setSize('100%');


        
        $output_type->addItems(array('html'=>'HTML', 'pdf'=>'PDF', 'rtf'=>'RTF', 'xls' => 'XLS'));
        $output_type->setLayout('horizontal');
        $output_type->setUseButton();
        $output_type->setValue('pdf');
        $output_type->setSize(70);
        
        // add the action button
        $btn = $this->form->addAction(_t('Generate'), new TAction(array($this, 'onGenerate')), 'fa:cog');
        $btn->class = 'btn btn-sm btn-primary';
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    /**
     * Generate the report
     */
    function onGenerate()
    {
        try
        {
            // open a transaction with database 'db_livraria'
            TTransaction::open('db_livraria');
            
            // get the form data into an active record
            $data = $this->form->getData();
            
            $this->form->validate();
            
            $repository = new TRepository('Livro');
            $criteria   = new TCriteria;
            
            if ($data->id)
            {
                $criteria->add(new TFilter('id', '=', "{$data->id}"));
            }
            if ($data->titulo)
            {
                $criteria->add(new TFilter('titulo', 'like', "%{$data->titulo}%"));
            }
            if ($data->preco)
            {
                $criteria->add(new TFilter('preco', '=', "{$data->preco}"));
            }
            if ($data->estoque)
            {
                $criteria->add(new TFilter('estoque', 'like', "%{$data->estoque}%"));
            }
            if ($data->genero_id)
            {
                $criteria->add(new TFilter('genero_id', 'like', "%{$data->genero_id}%"));
            }
            if ($data->editora_id)
            {
                $criteria->add(new TFilter('editora_id', 'like', "%{$data->editora_id}%"));
            }

           
            $objects = $repository->load($criteria, FALSE);
            $format  = $data->output_type;
            
            if ($objects)
            {
                $widths = array(20,280,55,55,55,55);
                
                switch ($format)
                {
                    case 'html':
                        $tr = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $tr = new TTableWriterPDF($widths);
                        break;
                    case 'xls':
                        $tr = new TTableWriterXLS($widths);
                        break;
                    case 'rtf':
                        $tr = new TTableWriterRTF($widths);
                        break;
                }
                
                // create the document styles
                $tr->addStyle('title', 'Arial', '10', 'B',   '#ffffff', '#9898EA');
                $tr->addStyle('datap', 'Arial', '10', '',    '#000000', '#EEEEEE');
                $tr->addStyle('datai', 'Arial', '10', '',    '#000000', '#ffffff');
                $tr->addStyle('header', 'Arial', '16', '',   '#ffffff', '#494D90');
                $tr->addStyle('footer', 'Times', '10', 'I',  '#000000', '#B1B1EA');
                
                // add a header row
                $tr->addRow();
                $tr->addCell('Livro', 'center', 'header', 6);//DEFINE A QUANTIDADE DE COLUNAS QUE VAI COBRIR.
                
                // add titles row
                $tr->addRow();
                $tr->addCell('Id', 'right', 'title');
                $tr->addCell('Titulo', 'left', 'title');
                $tr->addCell('Preco', 'left', 'title');
                $tr->addCell('Estoque', 'left', 'title');
                $tr->addCell('Genero_id', 'left', 'title');
                $tr->addCell('Editora_id', 'left', 'title');

                
                // controls the background filling
                $colour= FALSE;
                
                // data rows
                foreach ($objects as $object)
                {
                    $style = $colour ? 'datap' : 'datai';
                    $tr->addRow();
                    $tr->addCell($object->id, 'right', $style);
                    $tr->addCell($object->titulo, 'left', $style);
                    $tr->addCell($object->preco, 'left', $style);
                    $tr->addCell($object->estoque, 'left', $style);
                    $tr->addCell($object->genero_id, 'left', $style);
                    $tr->addCell($object->editora_id, 'left', $style);

                    
                    $colour = !$colour;
                }
                
                // footer row
                $tr->addRow();
                $tr->addCell(date('d/m/Y h:i:s'), 'center', 'footer', 6);//DEFINE A QUANTIDADE DE COLUNAS QUE VAI COBRIR.
                
                // stores the file
                if (!file_exists("app/output/Livro.{$format}") OR is_writable("app/output/Livro.{$format}"))
                {
                    $tr->save("app/output/Livro.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/Livro.{$format}");
                }
                
                // open the report file
                parent::openFile("app/output/Livro.{$format}");
                
                // shows the success message
                new TMessage('info', 'Report generated. Please, enable popups.');
            }
            else
            {
                new TMessage('error', 'No records found');
            }
    
            // fill the form with the active record data
            $this->form->setData($data);
            
            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
}