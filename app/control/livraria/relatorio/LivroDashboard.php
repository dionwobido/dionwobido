<?php

class LivroDashboard extends TPage
    {
        /**
         * Class constructor
         * Creates the page
         */
        function __construct($show_breadcrumb)
        {
            parent::__construct();
            
            try
            {
                TTransaction::open('db_livraria');
                $conn = TTransaction::get(); // get PDO connection
                
                // run query
                $result = $conn->query('SELECT livro.preco as "preco",
                                                venda.id as "id",
                                                venda.total as "total"
                                        FROM    livro,
                                                itens_da_venda,
                                                venda
                                        WHERE   livro.id = itens_da_venda.livro_id AND 
                                                venda.id = itens_da_venda.venda_id AND
                                                livro.preco >= 10                                      
                                        ');
                
                $data = array();
                $data[] = [ 'preco', 'total'];
                
                foreach ($result as $row) 
                { 
                    $data[] = [ (float) $row['preco'], (float) $row['total'] ];
                } 
                TTransaction::close();
            }
            catch (Exception $e)
            {
                new TMessage('error', $e->getMessage());
            }
            
            $html = new THtmlRenderer('app/resources/google_column_chart.html');
            $panel = new TPanelGroup('Gráfico Preços');
            $panel->add($html);
            
            // replace the main section variables
            $html->enableSection('main', array('data'   => json_encode($data),
                                               'title'  => 'Descrição de Preços',
                                               'xtitle' => 'Livro',
                                               'ytitle' => 'Preco',
                                               'width'  => '100%',
                                               'height' => '300px'));
            
            $container = new TVBox;
            $container->style = 'width: 100%';
            if ($show_breadcrumb)
            {
                $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            }
            $container->add($html);
            parent::add($container);
            }
    }

