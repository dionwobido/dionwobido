<?php

class LivrariaDashboard extends TPage
{
    function __construct()
    {
        parent::__construct();
        
        try
        {
            $html = new THtmlRenderer('app/resources/livraria_dashboard.html');
            

            TTransaction::open('db_livraria');

            $preco = Livro::where('preco', '>=',0) ->sumby('preco');

            $indicator1 = new THtmlRenderer('app/resources/info-box.html');
            $indicator2 = new THtmlRenderer('app/resources/info-box.html');
            $indicator3 = new THtmlRenderer('app/resources/info-box.html');
            $indicator4 = new THtmlRenderer('app/resources/info-box.html');
            $indicator5 = new THtmlRenderer('app/resources/info-box.html');

            
            $indicator1->enableSection('main', ['title' => ('Cliente'),    'icon' => 'users',       'background' => 'orange', 'value' => Cliente::count()]);
            $indicator2->enableSection('main', ['title' => ('Livro'),   'icon' => 'book',      'background' => 'blue',   'value' => Livro::count()]);
            $indicator3->enableSection('main', ['title' => ('Autor'),    'icon' => 'pen',       'background' => 'green', 'value' => Autor::count()]);
            $indicator4->enableSection('main', ['title' => ('Editora'),   'icon' => 'building',      'background' => 'red',   'value' => Editora::count()]);
            $indicator5->enableSection('main', ['title' => ('Total Valor'),   'icon' => 'boxes',      'background' => 'red',   'value' => 'R$' . number_format($preco,2,',','.')]);
            
            
            $chart1 = new THtmlRenderer('app/resources/google_column_chart.html');
            $data1 = [];
            $data1[] = [ 'Livro', 'Cliente' ];
            
            $stats1 = Livro::groupBy('id')->countBy('id', 'count');
            if ($stats1)
            {
                foreach ($stats1 as $row)
                {
                    $data1[] = [ Livro::find($row->id)->name, (int) $row->count];
                }
            }
            
            
            $html->enableSection('main', ['indicator1' => $indicator1,
                                          'indicator2' => $indicator2,
                                          'indicator3' => $indicator3,
                                          'indicator4' => $indicator4,
                                          'indicator5' => $indicator5,
                                          'chart1'     => $chart1,
                                          //'chart2'     => $chart2
                                        ]);
            
            $container = new TVBox;
            $container->style = 'width: 100%';
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($html);
            
            parent::add($container);
            TTransaction::close();
        }
        catch (Exception $e)
        {
            parent::add($e->getMessage());
        }
    }
}
