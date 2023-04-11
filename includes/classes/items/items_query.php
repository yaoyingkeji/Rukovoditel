<?php

class items_query
{
    private $entity_id, $settings, $report, $sql, $fields_in_query;
        
    function __construct($entity_id,$settings)
    {
        $this->entity_id = $entity_id;
        
        $this->sql = [
            'select' => '',
            'from' => '',
            'join' => '',
            'where' => '',
            'order' => '',
            'having' => '',
        ];
        
        $this->fields_in_query = [];
        
        
        $this->settings = new settings($settings,[
            'add_formula'=>false,
            'fields_in_query'=>'',
            'report_id'=>0,
            'add_filters'=>false,
            'add_order'=>false,
            'where' => ''
        ]);
        
        $this->sql['where'] .= $this->settings->get('where') . ' ';
        
        if($this->settings->get('report_id')>0)
        {
            $report_query = db_query("select r.id, r.listing_order_fields,r.displays_assigned_only, (select group_concat(rf.fields_id) from app_reports_filters rf where rf.reports_id=r.id) as fields_in_query from app_reports r where r.id='" . $this->settings->get('report_id') . "'");
            $this->report = db_fetch_array($report_query);
            
            if(isset($this->report['fields_in_query']) and strlen($this->report['fields_in_query']))
            {
                $this->fields_in_query = explode(',',$this->report['fields_in_query']);                
            }
        }
        else
        {
            $this->report = false;
        }
        
        $this->prepare_fields_in_query();
                
        $this->add_formulas();
                
        $this->add_filters();
        
        $this->add_order();
                
    }
    
    function get_sql()
    {        
        $sql = "select e.* {$this->sql['select']} from app_entity_{$this->entity_id} e {$this->sql['join']} {$this->sql['from']} where id>0 {$this->sql['where']} {$this->sql['having']} {$this->sql['order']}";        
        
        return $sql;
    }
    
    function prepare_fields_in_query()
    {
        if(is_array($this->settings->get('fields_in_query')))
        {
            foreach($this->settings->get('fields_in_query') as $id)
            {
                $this->fields_in_query[] = $id;
            }
        }
        elseif(strlen($this->settings->get('fields_in_query')))
        {                    
            if(strstr($this->settings->get('fields_in_query'),'['))
            {
                if(preg_match_all('/\[(\d+)\]/', $this->settings->get('fields_in_query'), $output_array))
                {
                    foreach($output_array[1] as $id)
                    {
                        $this->fields_in_query[] = $id;
                    }
                }
            }
            else
            {
                foreach(explode(',',$this->settings->get('fields_in_query')) as $id)
                {
                     $this->fields_in_query[] = trim($id); 
                }
            }
        }
        
        
        //print_rr($this->fields_in_query);
    }
    
    function add_formulas()
    {
        if($this->settings->get('add_formula'))
        {            
           $this->sql['select'] = fieldtype_formula::prepare_query_select($this->entity_id,'',false,['fields_in_query'=>implode(',',$this->fields_in_query)]); 
        }
    }
    
    function add_filters()
    {
        global $sql_query_having;
        
        $sql_query_having = [];
        
        if($this->settings->get('add_filters') and $this->report)
        {
            $this->sql['where'] .= \reports::add_filters_query($this->report['id']);

            //prepare having query for formula fields
            if (isset($sql_query_having[$this->entity_id]))
            {
                $this->sql['having'] = \reports::prepare_filters_having_query($sql_query_having[$this->entity_id]);
            }
            
            $this->sql['where'] = items::add_access_query($this->entity_id, $this->sql['where'], $this->report['displays_assigned_only']);
        }
        else
        {
            $this->sql['where'] = items::add_access_query($this->entity_id, $this->sql['where']);
        }
    }
    
    function add_order()
    {
        if($this->settings->get('add_order') and $this->report)
        {
            $info = reports::add_order_query($this->report['listing_order_fields'], $this->entity_id);
            
            $this->sql['order'] = $info['listing_sql_query'];
            $this->sql['join'] = $info['listing_sql_query_join'];
            $this->sql['from'] = $info['listing_sql_query_from'];
        }
    }
}
