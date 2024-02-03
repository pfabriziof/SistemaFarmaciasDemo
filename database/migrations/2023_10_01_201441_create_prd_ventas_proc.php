<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "SELECT
            t1.month,
            ifnull(sum(t2.amount),0) AS expenses,
            ifnull(sum(t3.amount),0) AS incomes
        FROM
        (
            
            
            SELECT fechas.fecha AS month
        FROM (
        SELECT 
        (DATE(NOW())) fecha UNION
        SELECT 
        (DATE(NOW()) - INTERVAL 1 DAY) fecha UNION 
        SELECT 
        (DATE(NOW()) - INTERVAL 2 DAY) fecha UNION 
        SELECT 
        (DATE(NOW()) - INTERVAL 3 DAY) fecha UNION 
        SELECT 
        (DATE(NOW()) - INTERVAL 4 DAY) fecha UNION 
        SELECT 
        (DATE(NOW()) - INTERVAL 5 DAY) fecha UNION 
        SELECT 
        (DATE(NOW()) - INTERVAL 6 DAY) fecha UNION 
        SELECT 
        (DATE(NOW()) - INTERVAL 7 DAY) fecha) as fechas
            
            
        ) t1
        
        LEFT JOIN
        (
            
            SELECT c.`fecha_emision` AS month, ifnull(sum(total),0) AS amount
        FROM `comprobantes` as c
        GROUP BY c.`fecha_emision`
            
            
        ) t2
            ON t1.month = t2.month
        LEFT JOIN
        (
            
            
        SELECT DATE(e.`fecha_egreso`) AS month, ifnull(sum(monto),0)  AS amount
        FROM `egresos` as e
        GROUP BY DATE(e.`fecha_egreso`)
            
            
        ) t3
            ON t1.month = t3.month
        GROUP BY t1.month
        ORDER BY
        t1.month";

        // DB::unprepared($procedure);
    }
    
    public function down()
    {
        // DB::unprepared("DROP PROCEDURE IF EXISTS prd_ventas");
    }
};