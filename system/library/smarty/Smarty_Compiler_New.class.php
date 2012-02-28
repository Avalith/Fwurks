<?php

require_once dirname(__FILE__).'/Smarty_Compiler.class.php'; 


class Smarty_Compiler_New extends Smarty_Compiler {

    var $_obj_call_regexp		=   null;
    
    function __construct()
    {
    	$this->Smarty_Compiler();
    	
    	$this->_var_bracket_regexp = '\[\$?[\w\.\->]+\]';
    	
    	$this->_dvar_guts_regexp = '\w+(?:' . $this->_var_bracket_regexp . ')*(?:\.\$?\w+(?:' . $this->_var_bracket_regexp . ')*)*(?:' . $this->_dvar_math_regexp . '(?:' . $this->_num_const_regexp . '|' . $this->_dvar_math_var_regexp . ')*)?';
        $this->_dvar_regexp = '\$' . $this->_dvar_guts_regexp;
    	
    	// matches all valid variables (no quotes, no modifiers)
        $this->_avar_regexp = '(?:' . $this->_dvar_regexp . '|' . $this->_cvar_regexp . '|' . $this->_svar_regexp . ')';

        // matches valid variable syntax:
        // $foo
        // $foo
        // #foo#
        // #foo#
        // "text"
        // "text"
        $this->_var_regexp = '(?:' . $this->_avar_regexp . '|' . $this->_qstr_regexp . ')';
        
         // matches valid object call (one level of object nesting allowed in parameters):
        // $foo->bar
        // $foo->bar()
        // $foo->bar("text")
        // $foo->bar($foo, $bar, "text")
        // $foo->bar($foo, "foo")
        // $foo->bar->foo()
        // $foo->bar->foo->bar()
        // $foo->bar($foo->bar)
        // $foo->bar($foo->bar())
        // $foo->bar($foo->bar($blah,$foo,44,"foo",$foo[0].bar))
        $this->_obj_ext_regexp = '\->(?:\$?' . $this->_dvar_guts_regexp . ')';
        $this->_obj_restricted_param_regexp = '(?:'
                . '(?:' . $this->_var_regexp . '|' . $this->_num_const_regexp . ')(?:' . $this->_obj_ext_regexp . '(?:\((?:(?:' . $this->_var_regexp . '|' . $this->_num_const_regexp . ')'
                . '(?:\s*,\s*(?:' . $this->_var_regexp . '|' . $this->_num_const_regexp . '))*)?\))?)*)';
        $this->_obj_single_param_regexp = '(?:\w+|' . $this->_obj_restricted_param_regexp . '(?:\s*,\s*(?:(?:\w+|' . $this->_var_regexp . $this->_obj_restricted_param_regexp . ')))*)';
        $this->_obj_params_regexp = '\((?:' . $this->_obj_single_param_regexp . '(?:\s*,\s*' . $this->_obj_single_param_regexp . ')*)?\)';
        $this->_obj_start_regexp = '(?:' . $this->_dvar_regexp . '(?:' . $this->_obj_ext_regexp . ')+)';
        $this->_obj_call_regexp = '(?:' . $this->_obj_start_regexp . '(?:' . $this->_obj_params_regexp . ')?(?:' . $this->_dvar_math_regexp . '(?:' . $this->_num_const_regexp . '|' . $this->_dvar_math_var_regexp . ')*)?)';
        
        // matches valid modifier syntax:
        // |foo
        // |@foo
        // |foo:"bar"
        // |foo:$bar
        // |foo:"bar":$foobar
        // |foo|bar
        // |foo:$foo->bar
        $this->_mod_regexp = '(?:\|@?\w+(?::(?:\w+|' . $this->_num_const_regexp . '|' . $this->_obj_call_regexp . '|' . $this->_avar_regexp . '|' . $this->_qstr_regexp .'))*)';

        // matches valid function name:
        // foo123
        // _foo_bar
        $this->_func_regexp = '[a-zA-Z_]\w*';

        // matches valid registered object:
        // foo->bar
        $this->_reg_obj_regexp = '[a-zA-Z_]\w*->[a-zA-Z_]\w*';

        // matches valid parameter values:
        // true
        // $foo
        // $foo|bar
        // #foo#
        // #foo#|bar
        // "text"
        // "text"|bar
        // $foo->bar
        $this->_param_regexp = '(?:\s*(?:' . $this->_obj_call_regexp . '|' . $this->_var_regexp . '|' . $this->_num_const_regexp  . '|\w+)(?>' . $this->_mod_regexp . '*)\s*)';

        // matches valid parenthesised function parameters:
        //
        // "text"
        //    $foo, $bar, "text"
        // $foo|bar, "foo"|bar, $foo->bar($foo)|bar
        $this->_parenth_param_regexp = '(?:\((?:\w+|' . $this->_param_regexp . '(?:\s*,\s*(?:(?:\w+|' . $this->_param_regexp . ')))*)?\))';

        // matches valid function call:
        // foo()
        // foo_bar($foo)
        // _foo_bar($foo,"bar")
        // foo123($foo,$foo->bar(),"foo")
        $this->_func_call_regexp = '(?:' . $this->_func_regexp . '\s*(?:' . $this->_parenth_param_regexp . '))';
        
        
        
		
        
        
        
		
    	// dereference
    	$this->_obj_call_regexp = '(?:' . $this->_obj_start_regexp . '(?:' . $this->_obj_params_regexp . ''.'(?:' . $this->_obj_ext_regexp . '(?:' . $this->_obj_params_regexp . '|' . $this->_obj_single_param_regexp . '(?:\s*,\s*' . $this->_obj_single_param_regexp . ')*))*' . 	')?(?:' . $this->_dvar_math_regexp . '(?:' . $this->_num_const_regexp . '|' . $this->_dvar_math_var_regexp . ')*)?)';
    }
    
    
	/**
     * parse variable expression into PHP code
     *
     * @param string $var_expr
     * @param string $output
     * @return string
     */
    function _parse_var($var_expr)
    {
        $_has_math = false;
        $_math_vars = preg_split('~('.$this->_dvar_math_regexp.'|'.$this->_qstr_regexp.')~', $var_expr, -1, PREG_SPLIT_DELIM_CAPTURE);

        if(count($_math_vars) > 1) {
            $_first_var = "";
            $_complete_var = "";
            $_output = "";
            // simple check if there is any math, to stop recursion (due to modifiers with "xx % yy" as parameter)
            foreach($_math_vars as $_k => $_math_var) {
                $_math_var = $_math_vars[$_k];

                if(!empty($_math_var) || is_numeric($_math_var)) {
                    // hit a math operator, so process the stuff which came before it
                    if(preg_match('~^' . $this->_dvar_math_regexp . '$~', $_math_var)) {
                        $_has_math = true;
                        if(!empty($_complete_var) || is_numeric($_complete_var)) {
                            $_output .= $this->_parse_var($_complete_var);
                        }

                        // just output the math operator to php
                        $_output .= $_math_var;

                        if(empty($_first_var))
                            $_first_var = $_complete_var;

                        $_complete_var = "";
                    } else {
                        $_complete_var .= $_math_var;
                    }
                }
            }
            if($_has_math) {
                if(!empty($_complete_var) || is_numeric($_complete_var))
                    $_output .= $this->_parse_var($_complete_var);

                // get the modifiers working (only the last var from math + modifier is left)
                $var_expr = $_complete_var;
            }
        }

        // prevent cutting of first digit in the number (we _definitly_ got a number if the first char is a digit)
        if(is_numeric(substr($var_expr, 0, 1)))
            $_var_ref = $var_expr;
        else
            $_var_ref = substr($var_expr, 1);
        
        if(!$_has_math) {
            
            // get [foo] and .foo and ->foo and (...) pieces
            preg_match_all('~(?:^\w+)|' . $this->_obj_params_regexp . '|(?:' . $this->_var_bracket_regexp . ')|->\$?\w+|\.\$?\w+|\S+~', $_var_ref, $match);
                        
            $_indexes = $match[0];
            $_var_name = array_shift($_indexes);

            /* Handle $smarty.* variable references as a special case. */
            if ($_var_name == 'smarty') {
                /*
                 * If the reference could be compiled, use the compiled output;
                 * otherwise, fall back on the $smarty variable generated at
                 * run-time.
                 */
                if (($smarty_ref = $this->_compile_smarty_ref($_indexes)) !== null) {
                    $_output = $smarty_ref;
                } else {
                    $_var_name = substr(array_shift($_indexes), 1);
                    $_output = "\$this->_smarty_vars['$_var_name']";
                }
            } elseif(is_numeric($_var_name) && is_numeric(substr($var_expr, 0, 1))) {
                // because . is the operator for accessing arrays thru inidizes we need to put it together again for floating point numbers
                if(count($_indexes) > 0)
                {
                    $_var_name .= implode("", $_indexes);
                    $_indexes = array();
                }
                $_output = $_var_name;
            } else {
                $_output = "\$this->_tpl_vars['$_var_name']";
            }

            foreach ($_indexes as $_index) {
                if (substr($_index, 0, 1) == '[') {
                    $_index = substr($_index, 1, -1);
                    if (is_numeric($_index)) {
                        $_output .= "[$_index]";
                    } elseif (substr($_index, 0, 1) == '$') {
                        if (strpos($_index, '.') !== false || strpos($_index, '->') !== false) {
                            $_output .= '[' . $this->_parse_var($_index) . ']';
                        } else {
                            $_output .= "[\$this->_tpl_vars['" . substr($_index, 1) . "']]";
                        }
                    } else {
                        $_var_parts = explode('.', $_index);
                        $_var_section = $_var_parts[0];
                        $_var_section_prop = isset($_var_parts[1]) ? $_var_parts[1] : 'index';
                        $_output .= "[\$this->_sections['$_var_section']['$_var_section_prop']]";
                    }
                } else if (substr($_index, 0, 1) == '.') {
                    if (substr($_index, 1, 1) == '$')
                        $_output .= "[\$this->_tpl_vars['" . substr($_index, 2) . "']]";
                    else
                        $_output .= "['" . substr($_index, 1) . "']";
                } else if (substr($_index,0,2) == '->') {
                    if(substr($_index,2,2) == '__') {
                        $this->_syntax_error('call to internal object members is not allowed', E_USER_ERROR, __FILE__, __LINE__);
                    } elseif($this->security && substr($_index, 2, 1) == '_') {
                        $this->_syntax_error('(secure) call to private object member is not allowed', E_USER_ERROR, __FILE__, __LINE__);
                    } elseif (substr($_index, 2, 1) == '$') {
                        if ($this->security) {
                            $this->_syntax_error('(secure) call to dynamic object member is not allowed', E_USER_ERROR, __FILE__, __LINE__);
                        } else {
                            $_output .= '->{(($_var=$this->_tpl_vars[\''.substr($_index,3).'\']) && substr($_var,0,2)!=\'__\') ? $_var : $this->trigger_error("cannot access property \\"$_var\\"")}';
                        }
                    } else {
                        $_output .= $_index;
                    }
                } elseif (substr($_index, 0, 1) == '(') {
                    $_index = $this->_parse_parenth_args($_index);
                    $_output .= $_index;
                } else {
                    $_output .= $_index;
                }
            }
        }

        return $_output;
    }
    
	function _parse_attrs($tag_args)
    {
    	
        /* Tokenize tag attributes. */
        preg_match_all('~(?:' . $this->_obj_call_regexp . '|' . $this->_qstr_regexp . ' | (?>[^"\'=\s]+)
                         )+ |
                         [=]
                        ~x', $tag_args, $match);
        $tokens       = $match[0];
        
        $attrs = array();
        /* Parse state:
            0 - expecting attribute name
            1 - expecting '='
            2 - expecting attribute value (not '=') */
        $state = 0;

        for($i=0, $len=count($tokens); $i<$len; $i++) 
        {
        	$token = $tokens[$i];
        	
            switch ($state) {
                case 0:
                    /* If the token is a valid identifier, we set attribute name
                       and go to state 1. */
                    if (preg_match('~^\w+$~', $token)) {
                        $attr_name = $token;
                        $state = 1;
                    } else
                        $this->_syntax_error("invalid attribute name: '$token'", E_USER_ERROR, __FILE__, __LINE__);
                    break;

                case 1:
                    /* If the token is '=', then we go to state 2. */
                	
                    if ($token == '=') {
                        $state = 2;
                    } else {
                    	$attrs[$attr_name] = true;
                    	$state = 0;
                    	$i--;
            		}
//						$this->_syntax_error("expecting '=' after attribute name '$last_token'", E_USER_ERROR, __FILE__, __LINE__);
                    break;

                case 2:
                    /* If token is not '=', we set the attribute value and go to
                       state 0. */
                    if ($token != '=')
                    {
                        /* We booleanize the token if it's a non-quoted possible
                           boolean value. */
                        if (preg_match('~^(on|yes|true)$~', $token)) {
                            $token = 'true';
                        } else if (preg_match('~^(off|no|false)$~', $token)) {
                            $token = 'false';
                        } else if ($token == 'null') {
                            $token = 'null';
                        } else if (preg_match('~^' . $this->_num_const_regexp . '|0[xX][0-9a-fA-F]+$~', $token)) {
                            /* treat integer literally */
                        } else if (!preg_match('~^' . $this->_obj_call_regexp . '|' . $this->_var_regexp . '(?:' . $this->_mod_regexp . ')*$~', $token)) {
                            /* treat as a string, double-quote it escaping quotes */
                            $token = '"'.addslashes($token).'"';
                        }

                        $attrs[$attr_name] = $token;
                        $state = 0;
                    } else
                        $this->_syntax_error("'=' cannot be an attribute value", E_USER_ERROR, __FILE__, __LINE__);
                    break;
            }
            $last_token = $token;
        }

        if($state != 0) {
            if($state == 1) {
            	$attrs[$attr_name] = true;
//				$this->_syntax_error("expecting '=' after attribute name '$last_token'", E_USER_ERROR, __FILE__, __LINE__);
            } else {
                $this->_syntax_error("missing attribute value", E_USER_ERROR, __FILE__, __LINE__);
            }
        }
		        
        $this->_parse_vars_props($attrs);

        return $attrs;
    }
}

?>
