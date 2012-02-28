<?php
/**
* Smarty Internal Plugin Templatelexer
*
* This is the lexer to break the template source into tokens 
* @package Smarty
* @subpackage Compiler
* @author Uwe Tews 
*/
/**
* Smarty Internal Plugin Templatelexer
*/
class Smarty_Internal_Templatelexer
{
    public $data;
    public $counter;
    public $token;
    public $value;
    public $node;
    public $line;
    private $state = 1;
    public $smarty_token_names = array (		// Text for parser error messages
    				'IDENTITY'	=> '===',
    				'NONEIDENTITY'	=> '!==',
    				'EQUALS'	=> '==',
    				'NOTEQUALS'	=> '!=',
    				'GREATEREQUAL' => '(>=,GE)',
    				'LESSEQUAL' => '(<=,LE)',
    				'GREATERTHAN' => '(>,GT)',
    				'LESSTHAN' => '(<,LT)',
    				'NOT'			=> '(!,NOT)',
    				'LAND'		=> '(&&,AND)',
    				'LOR'			=> '(||,OR)',
    				'LXOR'			=> 'XOR',
    				'OPENP'		=> '(',
    				'CLOSEP'	=> ')',
    				'OPENB'		=> '[',
    				'CLOSEB'	=> ']',
    				'PTR'			=> '->',
    				'APTR'		=> '=>',
    				'EQUAL'		=> '=',
    				'NUMBER'	=> 'number',
    				'UNIMATH'	=> '+" , "-',
    				'MATH'		=> '*" , "/" , "%',
    				'INCDEC'	=> '++" , "--',
    				'SPACE'		=> ' ',
    				'DOLLAR'	=> '$',
    				'SEMICOLON' => ';',
    				'COLON'		=> ':',
    				'DOUBLECOLON'		=> '::',
    				'AT'		=> '@',
    				'HATCH'		=> '#',
    				'QUOTE'		=> '"',
    				'SINGLEQUOTE'		=> "'",
    				'BACKTICK'		=> '`',
    				'VERT'		=> '|',
    				'DOT'			=> '.',
    				'COMMA'		=> '","',
    				'ANDSYM'		=> '"&"',
    				'ID'			=> 'identifier',
    				'OTHER'		=> 'text',
    				'PHP'			=> 'PHP code',
    				'LDELSLASH' => 'closing tag',
    				'COMMENTSTART' => '{*',
    				'COMMENTEND' => '*}',
     				'LITERALEND' => 'literal close',
    				'AS' => 'as',
    				'NULL' => 'null',
    				'BOOLEAN' => 'boolean'
    				);
    				
    				
    function __construct($data,$smarty)
    {
        // set instance object
        self::instance($this); 
        $this->data = $data;
        $this->counter = 0;
        $this->line = 1;
        $this->smarty = $smarty;
        $this->ldel = preg_quote($this->smarty->left_delimiter); 
        $this->rdel = preg_quote($this->smarty->right_delimiter);
        $this->smarty_token_names['LDEL'] =	$this->smarty->left_delimiter;
        $this->smarty_token_names['RDEL'] =	$this->smarty->right_delimiter;
     }
    public static function &instance($new_instance = null)
    {
        static $instance = null;
        if (isset($new_instance) && is_object($new_instance))
            $instance = $new_instance;
        return $instance;
    } 



    private $_yy_state = 1;
    private $_yy_stack = array();

    function yylex()
    {
        return $this->{'yylex' . $this->_yy_state}();
    }

    function yypushstate($state)
    {
        array_push($this->_yy_stack, $this->_yy_state);
        $this->_yy_state = $state;
    }

    function yypopstate()
    {
        $this->_yy_state = array_pop($this->_yy_stack);
    }

    function yybegin($state)
    {
        $this->_yy_state = $state;
    }



    function yylex1()
    {
        $tokenMap = array (
              1 => 0,
              2 => 0,
              3 => 0,
              4 => 0,
              5 => 0,
              6 => 0,
              7 => 0,
              8 => 0,
              9 => 1,
              11 => 0,
              12 => 0,
              13 => 0,
              14 => 0,
              15 => 0,
              16 => 0,
              17 => 0,
              18 => 0,
              19 => 0,
              20 => 0,
              21 => 0,
              22 => 0,
              23 => 1,
              25 => 0,
              26 => 0,
              27 => 0,
              28 => 0,
              29 => 1,
              31 => 1,
              33 => 1,
              35 => 1,
              37 => 1,
              39 => 1,
              41 => 1,
              43 => 1,
              45 => 1,
              47 => 1,
              49 => 0,
              50 => 0,
              51 => 0,
              52 => 0,
              53 => 0,
              54 => 0,
              55 => 0,
              56 => 0,
              57 => 0,
              58 => 0,
              59 => 0,
              60 => 0,
              61 => 0,
              62 => 0,
              63 => 0,
              64 => 0,
              65 => 0,
              66 => 0,
              67 => 1,
              69 => 1,
              71 => 1,
              73 => 0,
              74 => 0,
              75 => 0,
              76 => 0,
              77 => 0,
              78 => 0,
              79 => 0,
              80 => 0,
              81 => 0,
              82 => 0,
              83 => 0,
              84 => 0,
              85 => 1,
              87 => 0,
              88 => 0,
              89 => 0,
            );
        if ($this->counter >= strlen($this->data)) {
            return false; // end of input
        }
        $yy_global_pattern = "/^(\\s*<\\?xml)|^(\\s*<\\?php.*\\?>)|^(\\s*<\\?=)|^(\\s*\\?>)|^(\\s*".$this->ldel."php".$this->rdel.")|^(".$this->ldel."\/php".$this->rdel.")|^(\\s*\\*".$this->rdel.")|^(\\s*".$this->ldel."\\*)|^((\\\\\"|\\\\'))|^(')|^(\\s*".$this->ldel."literal".$this->rdel.")|^(\\s*".$this->ldel."\/literal".$this->rdel.")|^(\\s*".$this->ldel."ldelim".$this->rdel.")|^(\\s*".$this->ldel."rdelim".$this->rdel.")|^(\\s*".$this->ldel."\\s{1,}\/)|^(\\s*".$this->ldel."\\s{1,})|^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)/";

        do {
            if (preg_match($yy_global_pattern, substr($this->data, $this->counter), $yymatches)) {
                $yysubmatches = $yymatches;
                $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        'an empty string.  Input "' . substr($this->data,
                        $this->counter, 5) . '... state START');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                if ($tokenMap[$this->token]) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                        $tokenMap[$this->token]);
                } else {
                    $yysubmatches = array();
                }
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r1_' . $this->token}($yysubmatches);
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >= strlen($this->data)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {                    $yy_yymore_patterns = array(
        1 => array(0, "^(\\s*<\\?php.*\\?>)|^(\\s*<\\?=)|^(\\s*\\?>)|^(\\s*".$this->ldel."php".$this->rdel.")|^(".$this->ldel."\/php".$this->rdel.")|^(\\s*\\*".$this->rdel.")|^(\\s*".$this->ldel."\\*)|^((\\\\\"|\\\\'))|^(')|^(\\s*".$this->ldel."literal".$this->rdel.")|^(\\s*".$this->ldel."\/literal".$this->rdel.")|^(\\s*".$this->ldel."ldelim".$this->rdel.")|^(\\s*".$this->ldel."rdelim".$this->rdel.")|^(\\s*".$this->ldel."\\s{1,}\/)|^(\\s*".$this->ldel."\\s{1,})|^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        2 => array(0, "^(\\s*<\\?=)|^(\\s*\\?>)|^(\\s*".$this->ldel."php".$this->rdel.")|^(".$this->ldel."\/php".$this->rdel.")|^(\\s*\\*".$this->rdel.")|^(\\s*".$this->ldel."\\*)|^((\\\\\"|\\\\'))|^(')|^(\\s*".$this->ldel."literal".$this->rdel.")|^(\\s*".$this->ldel."\/literal".$this->rdel.")|^(\\s*".$this->ldel."ldelim".$this->rdel.")|^(\\s*".$this->ldel."rdelim".$this->rdel.")|^(\\s*".$this->ldel."\\s{1,}\/)|^(\\s*".$this->ldel."\\s{1,})|^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        3 => array(0, "^(\\s*\\?>)|^(\\s*".$this->ldel."php".$this->rdel.")|^(".$this->ldel."\/php".$this->rdel.")|^(\\s*\\*".$this->rdel.")|^(\\s*".$this->ldel."\\*)|^((\\\\\"|\\\\'))|^(')|^(\\s*".$this->ldel."literal".$this->rdel.")|^(\\s*".$this->ldel."\/literal".$this->rdel.")|^(\\s*".$this->ldel."ldelim".$this->rdel.")|^(\\s*".$this->ldel."rdelim".$this->rdel.")|^(\\s*".$this->ldel."\\s{1,}\/)|^(\\s*".$this->ldel."\\s{1,})|^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        4 => array(0, "^(\\s*".$this->ldel."php".$this->rdel.")|^(".$this->ldel."\/php".$this->rdel.")|^(\\s*\\*".$this->rdel.")|^(\\s*".$this->ldel."\\*)|^((\\\\\"|\\\\'))|^(')|^(\\s*".$this->ldel."literal".$this->rdel.")|^(\\s*".$this->ldel."\/literal".$this->rdel.")|^(\\s*".$this->ldel."ldelim".$this->rdel.")|^(\\s*".$this->ldel."rdelim".$this->rdel.")|^(\\s*".$this->ldel."\\s{1,}\/)|^(\\s*".$this->ldel."\\s{1,})|^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        5 => array(0, "^(".$this->ldel."\/php".$this->rdel.")|^(\\s*\\*".$this->rdel.")|^(\\s*".$this->ldel."\\*)|^((\\\\\"|\\\\'))|^(')|^(\\s*".$this->ldel."literal".$this->rdel.")|^(\\s*".$this->ldel."\/literal".$this->rdel.")|^(\\s*".$this->ldel."ldelim".$this->rdel.")|^(\\s*".$this->ldel."rdelim".$this->rdel.")|^(\\s*".$this->ldel."\\s{1,}\/)|^(\\s*".$this->ldel."\\s{1,})|^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        6 => array(0, "^(\\s*\\*".$this->rdel.")|^(\\s*".$this->ldel."\\*)|^((\\\\\"|\\\\'))|^(')|^(\\s*".$this->ldel."literal".$this->rdel.")|^(\\s*".$this->ldel."\/literal".$this->rdel.")|^(\\s*".$this->ldel."ldelim".$this->rdel.")|^(\\s*".$this->ldel."rdelim".$this->rdel.")|^(\\s*".$this->ldel."\\s{1,}\/)|^(\\s*".$this->ldel."\\s{1,})|^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        7 => array(0, "^(\\s*".$this->ldel."\\*)|^((\\\\\"|\\\\'))|^(')|^(\\s*".$this->ldel."literal".$this->rdel.")|^(\\s*".$this->ldel."\/literal".$this->rdel.")|^(\\s*".$this->ldel."ldelim".$this->rdel.")|^(\\s*".$this->ldel."rdelim".$this->rdel.")|^(\\s*".$this->ldel."\\s{1,}\/)|^(\\s*".$this->ldel."\\s{1,})|^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        8 => array(0, "^((\\\\\"|\\\\'))|^(')|^(\\s*".$this->ldel."literal".$this->rdel.")|^(\\s*".$this->ldel."\/literal".$this->rdel.")|^(\\s*".$this->ldel."ldelim".$this->rdel.")|^(\\s*".$this->ldel."rdelim".$this->rdel.")|^(\\s*".$this->ldel."\\s{1,}\/)|^(\\s*".$this->ldel."\\s{1,})|^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        9 => array(1, "^(')|^(\\s*".$this->ldel."literal".$this->rdel.")|^(\\s*".$this->ldel."\/literal".$this->rdel.")|^(\\s*".$this->ldel."ldelim".$this->rdel.")|^(\\s*".$this->ldel."rdelim".$this->rdel.")|^(\\s*".$this->ldel."\\s{1,}\/)|^(\\s*".$this->ldel."\\s{1,})|^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        11 => array(1, "^(\\s*".$this->ldel."literal".$this->rdel.")|^(\\s*".$this->ldel."\/literal".$this->rdel.")|^(\\s*".$this->ldel."ldelim".$this->rdel.")|^(\\s*".$this->ldel."rdelim".$this->rdel.")|^(\\s*".$this->ldel."\\s{1,}\/)|^(\\s*".$this->ldel."\\s{1,})|^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        12 => array(1, "^(\\s*".$this->ldel."\/literal".$this->rdel.")|^(\\s*".$this->ldel."ldelim".$this->rdel.")|^(\\s*".$this->ldel."rdelim".$this->rdel.")|^(\\s*".$this->ldel."\\s{1,}\/)|^(\\s*".$this->ldel."\\s{1,})|^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        13 => array(1, "^(\\s*".$this->ldel."ldelim".$this->rdel.")|^(\\s*".$this->ldel."rdelim".$this->rdel.")|^(\\s*".$this->ldel."\\s{1,}\/)|^(\\s*".$this->ldel."\\s{1,})|^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        14 => array(1, "^(\\s*".$this->ldel."rdelim".$this->rdel.")|^(\\s*".$this->ldel."\\s{1,}\/)|^(\\s*".$this->ldel."\\s{1,})|^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        15 => array(1, "^(\\s*".$this->ldel."\\s{1,}\/)|^(\\s*".$this->ldel."\\s{1,})|^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        16 => array(1, "^(\\s*".$this->ldel."\\s{1,})|^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        17 => array(1, "^(\\s{1,}".$this->rdel.")|^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        18 => array(1, "^(\\s*".$this->ldel."\/)|^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        19 => array(1, "^(\\s*".$this->ldel.")|^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        20 => array(1, "^(".$this->rdel.")|^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        21 => array(1, "^(\\s+is\\s+in\\s+)|^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        22 => array(1, "^(\\s+(AS|as)\\s+)|^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        23 => array(2, "^(true|TRUE|True|false|FALSE|False)|^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        25 => array(2, "^(null|NULL|Null)|^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        26 => array(2, "^(\\s*===\\s*)|^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        27 => array(2, "^(\\s*!==\\s*)|^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        28 => array(2, "^(\\s*==\\s*|\\s+(EQ|eq)\\s+)|^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        29 => array(3, "^(\\s*!=\\s*|\\s*<>\\s*|\\s+(NE|ne)\\s+)|^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        31 => array(4, "^(\\s*>=\\s*|\\s+(GE|ge)\\s+)|^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        33 => array(5, "^(\\s*<=\\s*|\\s+(LE|le)\\s+)|^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        35 => array(6, "^(\\s*>\\s*|\\s+(GT|gt)\\s+)|^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        37 => array(7, "^(\\s*<\\s*|\\s+(LT|lt)\\s+)|^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        39 => array(8, "^(!\\s*|(NOT|not)\\s+)|^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        41 => array(9, "^(\\s*&&\\s*|\\s*(AND|and)\\s+)|^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        43 => array(10, "^(\\s*\\|\\|\\s*|\\s*(OR|or)\\s+)|^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        45 => array(11, "^(\\s*(XOR|xor)\\s+)|^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        47 => array(12, "^(\\s+is\\s+odd\\s+by\\s+)|^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        49 => array(12, "^(\\s+is\\s+not\\s+odd\\s+by\\s+)|^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        50 => array(12, "^(\\s+is\\s+odd)|^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        51 => array(12, "^(\\s+is\\s+not\\s+odd)|^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        52 => array(12, "^(\\s+is\\s+even\\s+by\\s+)|^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        53 => array(12, "^(\\s+is\\s+not\\s+even\\s+by\\s+)|^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        54 => array(12, "^(\\s+is\\s+even)|^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        55 => array(12, "^(\\s+is\\s+not\\s+even)|^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        56 => array(12, "^(\\s+is\\s+div\\s+by\\s+)|^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        57 => array(12, "^(\\s+is\\s+not\\s+div\\s+by\\s+)|^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        58 => array(12, "^(\\(\\s*)|^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        59 => array(12, "^(\\s*\\))|^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        60 => array(12, "^(\\[\\s*)|^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        61 => array(12, "^(\\s*\\])|^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        62 => array(12, "^(\\s*->\\s*)|^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        63 => array(12, "^(\\s*=>\\s*)|^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        64 => array(12, "^(\\s*=\\s*)|^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        65 => array(12, "^(\\d+)|^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        66 => array(12, "^((\\+\\+|--)\\s*)|^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        67 => array(13, "^(\\s*(\\+|-)\\s*)|^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        69 => array(14, "^(\\s*(\\*|\/|%)\\s*)|^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        71 => array(15, "^(\\$)|^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        73 => array(15, "^(\\s*;\\s*)|^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        74 => array(15, "^(::)|^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        75 => array(15, "^(:)|^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        76 => array(15, "^(@)|^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        77 => array(15, "^(#)|^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        78 => array(15, "^(\")|^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        79 => array(15, "^(`)|^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        80 => array(15, "^(\\|)|^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        81 => array(15, "^(\\.)|^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        82 => array(15, "^(\\s*,\\s*)|^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        83 => array(15, "^(\\s*&\\s*)|^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        84 => array(15, "^(([A-Za-z]\\w*\\s+){5,})|^(\\w+)|^(\\s+)|^(.)"),
        85 => array(16, "^(\\w+)|^(\\s+)|^(.)"),
        87 => array(16, "^(\\s+)|^(.)"),
        88 => array(16, "^(.)"),
        89 => array(16, ""),
    );

                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[$this->token][1])) {
                            throw new Exception('cannot do yymore for the last token');
                        }
                        $yysubmatches = array();
                        if (preg_match('/' . $yy_yymore_patterns[$this->token][1] . '/',
                              substr($this->data, $this->counter), $yymatches)) {
                            $yysubmatches = $yymatches;
                            $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            $this->token += key($yymatches) + $yy_yymore_patterns[$this->token][0]; // token number
                            $this->value = current($yymatches); // token value
                            $this->line = substr_count($this->value, "\n");
                            if ($tokenMap[$this->token]) {
                                // extract sub-patterns for passing to lex function
                                $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                                    $tokenMap[$this->token]);
                            } else {
                                $yysubmatches = array();
                            }
                        }
                    	$r = $this->{'yy_r1_' . $this->token}($yysubmatches);
                    } while ($r !== null && !is_bool($r));
			        if ($r === true) {
			            // we have changed state
			            // process this token in the new state
			            return $this->yylex();
                    } elseif ($r === false) {
                        $this->counter += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");
                        if ($this->counter >= strlen($this->data)) {
                            return false; // end of input
                        }
                        // skip this token
                        continue;
			        } else {
	                    // accept
	                    $this->counter += strlen($this->value);
	                    $this->line += substr_count($this->value, "\n");
	                    return true;
			        }
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->data[$this->counter]);
            }
            break;
        } while (true);

    } // end function


    const START = 1;
    function yy_r1_1($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_XML;
    }
    function yy_r1_2($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_PHP;
    }
    function yy_r1_3($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_SHORTTAGSTART;
    }
    function yy_r1_4($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_SHORTTAGEND;
    }
    function yy_r1_5($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_PHPSTART;
    }
    function yy_r1_6($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_PHPEND;
    }
    function yy_r1_7($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_COMMENTEND;
    }
    function yy_r1_8($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_COMMENTSTART;
    }
    function yy_r1_9($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_OTHER;
    }
    function yy_r1_11($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_SINGLEQUOTE;
    }
    function yy_r1_12($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_LITERALSTART;
    }
    function yy_r1_13($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_LITERALEND;
    }
    function yy_r1_14($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_LDELIMTAG;
    }
    function yy_r1_15($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_RDELIMTAG;
    }
    function yy_r1_16($yy_subpatterns)
    {

  if ($this->smarty->auto_literal) {
     $this->token = Smarty_Internal_Templateparser::TP_OTHER;
  } else {
     $this->token = Smarty_Internal_Templateparser::TP_LDELSLASH;
  }
    }
    function yy_r1_17($yy_subpatterns)
    {

  if ($this->smarty->auto_literal) {
     $this->token = Smarty_Internal_Templateparser::TP_OTHER;
  } else {
     $this->token = Smarty_Internal_Templateparser::TP_LDEL;
  }
    }
    function yy_r1_18($yy_subpatterns)
    {

  if ($this->smarty->auto_literal) {
     $this->token = Smarty_Internal_Templateparser::TP_OTHER;
  } else {
     $this->token = Smarty_Internal_Templateparser::TP_RDEL;
  }
    }
    function yy_r1_19($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_LDELSLASH;
    }
    function yy_r1_20($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_LDEL;
    }
    function yy_r1_21($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_RDEL;
    }
    function yy_r1_22($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_ISIN;
    }
    function yy_r1_23($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_AS;
    }
    function yy_r1_25($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_BOOLEAN;
    }
    function yy_r1_26($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_NULL;
    }
    function yy_r1_27($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_IDENTITY;
    }
    function yy_r1_28($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_NONEIDENTITY;
    }
    function yy_r1_29($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_EQUALS;
    }
    function yy_r1_31($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_NOTEQUALS;
    }
    function yy_r1_33($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_GREATEREQUAL;
    }
    function yy_r1_35($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_LESSEQUAL;
    }
    function yy_r1_37($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_GREATERTHAN;
    }
    function yy_r1_39($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_LESSTHAN;
    }
    function yy_r1_41($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_NOT;
    }
    function yy_r1_43($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_LAND;
    }
    function yy_r1_45($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_LOR;
    }
    function yy_r1_47($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_LXOR;
    }
    function yy_r1_49($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_ISODDBY;
    }
    function yy_r1_50($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_ISNOTODDBY;
    }
    function yy_r1_51($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_ISODD;
    }
    function yy_r1_52($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_ISNOTODD;
    }
    function yy_r1_53($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_ISEVENBY;
    }
    function yy_r1_54($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_ISNOTEVENBY;
    }
    function yy_r1_55($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_ISEVEN;
    }
    function yy_r1_56($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_ISNOTEVEN;
    }
    function yy_r1_57($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_ISDIVBY;
    }
    function yy_r1_58($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_ISNOTDIVBY;
    }
    function yy_r1_59($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_OPENP;
    }
    function yy_r1_60($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_CLOSEP;
    }
    function yy_r1_61($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_OPENB;
    }
    function yy_r1_62($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_CLOSEB;
    }
    function yy_r1_63($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_PTR; 
    }
    function yy_r1_64($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_APTR;
    }
    function yy_r1_65($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_EQUAL;
    }
    function yy_r1_66($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_INTEGER;
    }
    function yy_r1_67($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_INCDEC;
    }
    function yy_r1_69($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_UNIMATH;
    }
    function yy_r1_71($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_MATH;
    }
    function yy_r1_73($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_DOLLAR;
    }
    function yy_r1_74($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_SEMICOLON;
    }
    function yy_r1_75($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_DOUBLECOLON;
    }
    function yy_r1_76($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_COLON;
    }
    function yy_r1_77($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_AT;
    }
    function yy_r1_78($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_HATCH;
    }
    function yy_r1_79($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_QUOTE;
    }
    function yy_r1_80($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_BACKTICK;
    }
    function yy_r1_81($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_VERT;
    }
    function yy_r1_82($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_DOT;
    }
    function yy_r1_83($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_COMMA;
    }
    function yy_r1_84($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_ANDSYM;
    }
    function yy_r1_85($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_OTHER;
    }
    function yy_r1_87($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_ID;
    }
    function yy_r1_88($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_SPACE;
    }
    function yy_r1_89($yy_subpatterns)
    {

  $this->token = Smarty_Internal_Templateparser::TP_OTHER;
    }

}
