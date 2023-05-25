<?php

namespace CMSMSStripe;

final class utils
{
    private function __construct() {}

    public static function find_layout_template($params, $paramname, $typename)
    {
        $paramname = (string) $paramname;
        $typename = (string) $typename;
        if ( !is_array($params) || !($thetemplate = \xt_param::get_string($params,$paramname)) ) {
            $tpl = \CmsLayoutTemplate::load_dflt_by_type($typename);
            if ( !is_object($tpl) ) {
                audit('', 'CMSMSStripe', 'No default '.$typename.' template found');
                return;
            }
            $thetemplate = $tpl->get_name();
            unset($tpl);
        }
        return $thetemplate;
    }

    /**
     *  Touch menu cache files - core will refresh (v2.0+ )
     */
    public static function touch_cache()
    {
        foreach ( glob(cms_join_path(TMP_CACHE_LOCATION, 'cache*.cms')) as $filename ) {
            touch( $filename, time() - 360000 );
        }
    }


    /**
     *  Create a module template type \CmsLayoutTemplateType
     *  @param $type_name - string
     *  @param $mod - \CMSModule
     */
    public static function create_template_type($type_name, $mod) {
        if ( !is_object($mod) ) return false;
        try {  
            $module_name = $mod->GetName();
            $tpl_type = new \CmsLayoutTemplateType();
            $tpl_type->set_originator($module_name);
            $tpl_type->set_dflt_flag();
            $tpl_type->set_name($type_name);
            $tpl_type->set_lang_callback($mod->GetName().'::page_type_lang_callback');
            $tpl_type->set_help_callback($mod->GetName().'::template_help_callback');
            $tpl_type->set_content_callback($mod->GetName().'::reset_page_type_defaults');
            $tpl_type->reset_content_to_factory();
            $tpl_type->save();
        } catch( \CmsException $e ) {
            self::log_exception($e);
            audit('', 'CMSMSStripe', 'Install error: '.$e->GetMessage());
        }

        $tpl_type = \CmsLayoutTemplateType::load($module_name.'::'.$type_name);
        return $tpl_type;
    }


    /**
     *  Create a module template of the given type \CmsLayoutTemplate
     *  @param $type_ob - CmsLayoutTemplateType
     *  @param $name - of new template to be created
     *  @param $contents - of the smarty template
     *  @param $dflt (false) - if this is to be set as default template of this type 
     */
    public static function create_template_of_type( $type_ob, $name, $contents, $dflt = false ) 
    {
        $ob = new \CmsLayoutTemplate();
        $ob->set_type( $type_ob );
        $ob->set_content( $contents );
        $ob->set_owner( get_userid() );
        $ob->set_type_dflt( $dflt );
        $new_name = $ob->generate_unique_name( $name );
        $ob->set_name( $new_name );
        $ob->save();
    }

    /**
     * Dump an exception to the error log. (from CMSMSExt)
     *
     * @param Exception $e
     */
    public static function log_exception(\Exception $e)
    {
        $out = '-- EXCEPTION DUMP --'."\n";
        $out .= "TYPE: ".get_class($e)."\n";
        $out .= "MESSAGE: ".$e->getMessage()."\n";
        $out .= "FILE: ".$e->getFile().':'.$e->GetLine()."\n";
        $out .= "TREACE:\n";
        $out .= $e->getTraceAsString();
        debug_to_log($out,'-- '.__METHOD__.' --');
    }

    public static function centsToDollars($cents){
        return number_format(($cents /100), 2, '.', ' ');
    }

    public static function get_currencies($mod)
    {
        $fn = $mod->GetModulePath().'/etc/currencies.json';
        if( $fn ) return json_decode(file_get_contents($fn), TRUE);
    }

	public static function get_currency($item,$mod)
    {
        $fn = $mod->GetModulePath().'/etc/currencies.json';
        if( $fn ) {
			$json = json_decode(file_get_contents($fn), TRUE);
			return $json[strtoupper($item)];
		}
		return false;
    }


    public static function module_action_link($params, $smarty)
    {
        $gCms = cmsms();
        $inline = FALSE;
        $urlonly = true;
        $mid = 'm1_';

        $module = $smarty->get_template_vars('module');
        if( !$module ) $module = $smarty->get_template_vars('actionmodule');
        $module = get_parameter_value($params,'module',$module);
        if( !$module ) $module = $smarty->getTemplateVars('module');
        if( !$module ) $module = $smarty->getTemplateVars('actionmodule');
        if( !$module ) $module = $smarty->getTemplateVars('_module');
    
        if( !$module ) return;
        unset($params['module']);

        $obj = \cms_utils::get_module($module);
        if( !is_object($obj) ) return;

        $action = 'default';
        if( isset($params['action']) ) {
            $action = $params['action'];
            unset($params['action']);
        }

        $text = $module;
        if( isset($params['text']) ) {
            $text = trim($params['text']);
            unset($params['text']);
        }

        $title = '';
        if( isset($params['title']) ) {
            $title = trim($params['title']);
            unset($params['title']);
        }

        $frontend = \xt_utils::to_bool(\xt_utils::get_param($params,'frontend',false));
        if( $frontend ) {
            $frontend = true;
            $mid = 'cntnt01';
            unset($params['frontend']);
        }

        $confmessage = '';

        $pageid = \cms_utils::get_current_pageid();
        if( isset($params['page']) ) {
            // convert the page alias to an id
            $manager = $gCms->GetHierarchyManager();
            $node = $manager->sureGetNodeByAlias($params['page']);
            if (isset($node)) {
                $content = $node->GetContent();
                if (isset($content)) $pageid = $content->Id();
            }
            else {
                $node = $manager->sureGetNodeById($params['page']);
                if (isset($node)) $pageid = $params['page'];
            }
            unset($params['page']);
        }


        $jsfriendly = \xt_utils::to_bool(\xt_utils::get_param($params,'jsfriendly',false));
        if( $jsfriendly ) {
            $jsfriendly = true;
            unset($params['jsfriendly']);
        }

        $forjs = \xt_utils::to_bool(\xt_utils::get_param($params,'forjs',false));
        if( $forjs ) {
            $jsfriendly = true;
            unset($params['forjs']);
        }

        $forajax = \xt_utils::to_bool(\xt_utils::get_param($params,'forajax',false));
        $forajax = \xt_utils::to_bool(\xt_utils::get_param($params,'for_ajax',$forajax));
        if( $forajax ) {
            $jsfriendly = true;
            $forajax = true;
            unset($params['forajax']);
            unset($params['for_ajax']);
        }

        $assign = '';
        if( isset($params['assign']) ) {
            $assign = trim($params['assign']);
            unset($params['assign']);
        }

        //$this->CreateLink($id, 'admin_approvecategory', $returnid, '', array('approve' => 0,'category_id' => $category_id), '', true);
        $output = $obj->CreateLink($mid,$action,$pageid,$text,$params,$confmessage,$urlonly,$inline,$addtext);
        $output = str_replace('amp;','',$output);
        if( $forajax ) {
            if( strpos($output,'?') === FALSE ) {
                $output .= '?showtemplate=false';
            }
            else {
                $output .= '&showtemplate=false';
            }
        }
        
        // all done
        if( !empty($assign) ) {
            $smarty->assign($assign,$output);
            return;
        }
        return $output;
    }


}
