<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InicioTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*Compra::truncate();
        Figura::truncate();
        Planificacion::truncate();*/
        Color::truncate();


       /*$figura=new Figura();
        $figura->nombre="ondulada";
        $figura->tamaño=90;
        $figura->precio_metro=32;
        $figura->save();*/

        $color=new Color();
        $color->color="rojo";
        $color->base="#546878";
        $color->save();

       /* $compra=new Compra();
        $compra->color_id='1';
        $compra->tamaño="2000";
        $compra->peso="20";
        $compra->grosor="0.36";
        $compra->codigoBobina="A2352";
        $compra->cantidad="1";
        $compra->total="2000";
        $compra->save();

        $stock=new Stock();
        $stock->color="rojo";
        $stock->cantidad=2;
        $stock->total=2000;
        $stock->totalDisponible=2000;
        $stock->save();*/


       /* $plan=new Planificacion();
        $plan->item='gh4es';
        $plan->tamaño_largo='15';
        $plan->tamaño_requerido='17';
        $plan->tipo='personalizado';
        $plan->tamañopequeño='8';
        $plan->totallongitud='170';
        $plan->costototal='520';
        $plan->figura_id='1';
        $plan->stock_id='1';
        $plan->estado='en proceso';
        $plan->user_id='1';
        $plan->save();*/
    
    }
}
