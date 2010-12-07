<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | TemplatePower:                                                       |
// | offers you the ability to separate your PHP code and your HTML       |
// +----------------------------------------------------------------------+
// |                                                                      |
// | Copyright (C) 2001,2002  R.P.J. Velzeboer, The Netherlands           |
// |                                                                      |
// | This program is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU General Public License          |
// | as published by the Free Software Foundation; either version 2       |
// | of the License, or (at your option) any later version.               |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA            |
// | 02111-1307, USA.                                                     |
// |                                                                      |
// | Author: R.P.J. Velzeboer, rovel@codocad.nl   The Netherlands         |
// |                                                                      |
// +----------------------------------------------------------------------+
// | http://templatepower.codocad.com                                     |
// +----------------------------------------------------------------------+
//
// $Id: Version 3.0.2$

define("T_BYFILE",              0);
define("T_BYVAR",               1);

define("TP_ROOTBLOCK",    '_ROOT');

class templateParser
{
  var $tpl_base;              //Array( [filename/varcontent], [T_BYFILE/T_BYVAR] )
  var $tpl_include;           //Array( [filename/varcontent], [T_BYFILE/T_BYVAR] )
  var $tpl_count;

  var $parent   = Array();    // $parent[{blockname}] = {parentblockname}
  var $defBlock = Array();

  var $rootBlockName;
  var $ignore_stack;

  var $version;
  protected $included_files;

   /**
    * TemplatePowerParser::TemplatePowerParser()
    *
    * @param $tpl_file
    * @param $type
    * @return
	*
	* @access private
    */
   function templateParser( $tpl_file, $type )
   {
       $tpl_file = __DATA__ . 'application/views/' . $tpl_file . '.tpl';
       $this->version        = '3.0.2';

       $this->tpl_base       = Array( $tpl_file, $type );
       $this->tpl_count      = 0;
       $this->ignore_stack   = Array( false );
	   $this -> cache = false;
	   $this -> echo_queries = false;
   }

   /**
    * TemplatePowerParser::__errorAlert()
    *
    * @param $message
    * @return
	*
	* @access private
    */
   function __errorAlert( $message )
   {
       print( '<br>'. $message .'<br>'."\r\n");
   }

   /**
    * TemplatePowerParser::__prepare()
    *
    * @return
	*
	* @access private
    */
   function __prepare()
   {
       $this->defBlock[ TP_ROOTBLOCK ] = Array();
       $tplvar = $this->__prepareTemplate( $this->tpl_base[0], $this->tpl_base[1]  );

       $initdev["varrow"]  = 0;
       $initdev["coderow"] = 0;
       $initdev["index"]   = 0;
       $initdev["ignore"]  = false;

       $this->__parseTemplate( $tplvar, TP_ROOTBLOCK, $initdev );
       $this->__cleanUp();
   }

    /**
     * TemplatePowerParser::__cleanUp()
     *
     * @return
	 *
	 * @access private
     */
    function __cleanUp()
    {
        for( $i=0; $i <= $this->tpl_count; $i++ )
        {
            $tplvar = 'tpl_rawContent'. $i;
            unset( $this->{$tplvar} );
        }
    }

    /**
     * TemplatePowerParser::__prepareTemplate()
     *
     * @param $tpl_file
     * @param $type
     * @return
	 *
	 * @access private
     */
    function __prepareTemplate( $tpl_file, $type )
    {

        $tplvar = 'tpl_rawContent'. $this->tpl_count;

        if( $type == T_BYVAR )
        {
            $this->{$tplvar}["content"] = preg_split("/\n/", $tpl_file, -1, PREG_SPLIT_DELIM_CAPTURE);
        }
        else
        {
            $this->{$tplvar}["content"] = @file( $tpl_file ) or
                die( $this->__errorAlert('Template fout, kon de template "'. $tpl_file .'" niet vinden!'));
        }

        $this->{$tplvar}["size"]    = sizeof( $this->{$tplvar}["content"] );

        $this->tpl_count++;
        return $tplvar;
    }

    /**
     * TemplatePowerParser::__parseTemplate()
     *
     * @param $tplvar
     * @param $blockname
     * @param $initdev
     * @return
	 *
	 * @access private
     */
    function __parseTemplate( $tplvar, $blockname, $initdev )
    {
        $coderow = $initdev["coderow"];
        $varrow  = $initdev["varrow"];
        $index   = $initdev["index"];
        $ignore  = $initdev["ignore"];

        while( $index < $this->{$tplvar}["size"] )
        {
            if ( preg_match('/<!--[ ]?(START|END) IGNORE -->/', $this->{$tplvar}["content"][$index], $ignreg) )
            {
                if( $ignreg[1] == 'START')
                {
                    //$ignore = true;
					          array_push( $this->ignore_stack, true );
                }
                else
                {
                    //$ignore = false;
					          array_pop( $this->ignore_stack );
                }
            }
            else
            {
                if( !end( $this->ignore_stack ) )
                {
				
					if(preg_match('/\[MYSQL_QUERIES\]/', $this->{$tplvar}["content"][$index], $regs)) {
						$this -> echo_queries = true;
					}
	
                    if( preg_match('/\[[ ]?(START|END|INCLUDE|INCLUDESCRIPT|REUSE|MYSQL_QUERIES)(.*?)\]/', $this->{$tplvar}["content"][$index], $regs))
                    {
                       //remove trailing and leading spaces
                        $regs[2] = trim( $regs[2] );

                        if( $regs[1] == 'INCLUDE')
                        {
                            $include_defined = true;
                            array_push($this->included_files, $regs[2]);

                           //check if the include file is assigned
                            if (!isset( $this->tpl_include[ $regs[2] ]) ) {
                                templateParser::assignInclude($regs[2]);
                            }
                            if( isset( $this->tpl_include[ $regs[2] ]) )
                            {
                                $type   = $this->tpl_include[ $regs[2] ][1];
                            }
                            else
                            if (file_exists( __DATA__ . 'application/views/' . $regs[2] . '.tpl' ))    //check if defined as constant in template
                            {
                                $tpl_file = $regs[2];
                                $type     = T_BYFILE;
                            }
                            else
                            {echo 'falernd';
                                $include_defined = false;
                            }

                            if( $include_defined )
                            {
                               //initialize startvalues for recursive call
                                $initdev["varrow"]  = $varrow;
                                $initdev["coderow"] = $coderow;
                                $initdev["index"]   = 0;
                                $initdev["ignore"]  = false;

                                $tpl_file = __DATA__ . 'application/views/' . $regs[2] . '.tpl';
                                $tplvar2 = $this->__prepareTemplate( $tpl_file, $type );
                                $initdev = $this->__parseTemplate( $tplvar2, $blockname, $initdev );

                                $coderow = $initdev["coderow"];
                                $varrow  = $initdev["varrow"];
                            }
                        }
                        else
                        if( $regs[1] == 'INCLUDESCRIPT' )
                        {
                            $include_defined = true;

                           //check if the includescript file is assigned by the assignInclude function
                            if( isset( $this->tpl_include[ $regs[2] ]) )
                            {
                                $include_file = $this->tpl_include[ $regs[2] ][0];
								                $type         = $this->tpl_include[ $regs[2] ][1];
                            }
                            else
                            if (file_exists( $regs[2] ))    //check if defined as constant in template
                            {
                                $include_file = $regs[2];
								                $type         = T_BYFILE;
                            }
                            else
                            {
                                $include_defined = false;
                            }

                            if( $include_defined )
                            {
                                ob_start();

								                if( $type == T_BYFILE )
								                {
                                    if( !@include_once( $include_file ) )
                                    {
                                        $this->__errorAlert( 'TemplatePower Error: Couldn\'t include script [ '. $include_file .' ]!' );
										                    exit();
                                    }
								                }
								                else
								                {
								                    eval( "?>" . $include_file );
								                }

                                $this->defBlock[$blockname]["_C:$coderow"] = ob_get_contents();
                                $coderow++;

                                ob_end_clean();
                            }
                        }
                        else
                        if( $regs[1] == 'REUSE' )
                        {
                           //do match for 'AS'
                            if (preg_match('/(.+) AS (.+)/', $regs[2], $reuse_regs))
                            {
                                $originalbname = trim( $reuse_regs[1] );
                                $copybname     = trim( $reuse_regs[2] );

                               //test if original block exist
                                if (isset( $this->defBlock[ $originalbname ] ))
                                {
                                   //copy block
                                    $this->defBlock[ $copybname ] = $this->defBlock[ $originalbname ];

                                   //tell the parent that he has a child block
                                    $this->defBlock[ $blockname ]["_B:". $copybname ] = '';

                                   //create index and parent info
                                    $this->index[ $copybname ]  = 0;
                                    $this->parent[ $copybname ] = $blockname;
                                }
                                else
                                {
                                    $this->__errorAlert('TemplatePower Error: Can\'t find block \''. $originalbname .'\' to REUSE as \''. $copybname .'\'');
                                }
                            }
                            else
                            {
                               //so it isn't a correct REUSE tag, save as code
                                $this->defBlock[$blockname]["_C:$coderow"] = $this->{$tplvar}["content"][$index];
                                $coderow++;
                            }
                        }
                        else
                        {
                            if( $regs[2] == $blockname )     //is it the end of a block
                            {
                                break;
                            }
                            else                             //its the start of a block
                            {
                               //make a child block and tell the parent that he has a child
                                $this->defBlock[ $regs[2] ] = Array();
                                $this->defBlock[ $blockname ]["_B:". $regs[2]] = '';

                               //set some vars that we need for the assign functions etc.
                                $this->index[ $regs[2] ]  = 0;
                                $this->parent[ $regs[2] ] = $blockname;

                               //prepare for the recursive call
                                $index++;
                                $initdev["varrow"]  = 0;
                                $initdev["coderow"] = 0;
                                $initdev["index"]   = $index;
                                $initdev["ignore"]  = false;

                                $initdev = $this->__parseTemplate( $tplvar, $regs[2], $initdev );

                                $index = $initdev["index"];
                            }
                        }
                    }
                    else                                                        //is it code and/or var(s)
                    {
                       //explode current template line on the curly bracket '{'
                        $sstr = explode( '{', $this->{$tplvar}["content"][$index] );

						            reset( $sstr );

                        if (current($sstr) != '')
                        {
                           //the template didn't start with a '{',
                           //so the first element of the array $sstr is just code
                            $this->defBlock[$blockname]["_C:$coderow"] = current( $sstr );
                            $coderow++;
                        }

                        while (next($sstr))
                        {
                           //find the position of the end curly bracket '}'
                            $pos = strpos( current($sstr), "}" );

                            if ( ($pos !== false) && ($pos > 0) )
                            {
                              //a curly bracket '}' is found
                              //and at least on position 1, to eliminate '{}'

                              //note: position 1 taken without '{', because we did explode on '{'

                                $strlength = strlen( current($sstr) );
                                $varname   = substr( current($sstr), 0, $pos );

                                if (strstr( $varname, ' ' ))
                                {
                                   //the varname contains one or more spaces
                                   //so, it isn't a variable, save as code
                                    $this->defBlock[$blockname]["_C:$coderow"] = '{'. current( $sstr );
                                    $coderow++;
                                }
                                else
                                {
                                   //save the variable
                                    $this->defBlock[$blockname]["_V:$varrow" ] = $varname;
                                    $varrow++;

                                   //is there some code after the varname left?
                                    if( ($pos + 1) != $strlength )
                                    {
                                       //yes, save that code
                                        $this->defBlock[$blockname]["_C:$coderow"] = substr( current( $sstr ), ($pos + 1), ($strlength - ($pos + 1)) );
                                        $coderow++;
                                    }
                                }
                            }
                            else
                            {
                               //no end curly bracket '}' found
                               //so, the curly bracket is part of the text. Save as code, with the '{'
                                $this->defBlock[$blockname]["_C:$coderow"] = '{'. current( $sstr );
                                $coderow++;
                            }
                        }
                    }
                }
                else
                {
                    $this->defBlock[$blockname]["_C:$coderow"] = $this->{$tplvar}["content"][$index];
                    $coderow++;
                }
            }

            $index++;
        }

        $initdev["varrow"]  = $varrow;
        $initdev["coderow"] = $coderow;
        $initdev["index"]   = $index;

        return $initdev;
    }


    /**
     * TemplatePowerParser::version()
     *
     * @return
	 * @access public
     */
    function version()
    {
        return $this->version;
    }

    /**
     * TemplatePowerParser::assignInclude()
     *
     * @param $iblockname
     * @param $value
     * @param $type
	 *
	 * @return
	 *
	 * @access public
     */
    function assignInclude( $iblockname, $type=T_BYFILE )
    {
        $value = __DATA__ . 'application/views/' . $iblockname . '.tpl';
        $this->tpl_include["$iblockname"] = Array( $value, $type );
    }
	
	function echo_queries() {
		return ($this -> echo_queries ? true : false);
	}
}

class template extends templateParser
{
  var $index    = Array();        // $index[{blockname}]  = {indexnumber}
  var $content  = Array();

  var $currentBlock;
  var $showUnAssigned;
  var $serialized;
  var $globalvars = Array();
  var $prepared;
  var $render = true;

  protected $included_files;

    /**
     * TemplatePower::TemplatePower()
     *
     * @param $tpl_file
     * @param $type
     * @return
	 *
	 * @access public
     */
    public function __construct( $tpl_file='', $type= T_BYFILE )
    {
        templateParser::templateParser( $tpl_file, $type );

        $this->prepared       = true;
        $this->showUnAssigned = false;
	$this->serialized     = false;  //added: 26 April 2002
        $this->included_files = array('haha');
        $this->controller = $tpl_file;
        $this -> noFlash = false;

        self::prepare();
    }

    /**
     * TemplatePower::__deSerializeTPL()
     *
     * @param $stpl_file
     * @param $tplvar
     * @return
	 *
	 * @access private
     */
    function __deSerializeTPL( $stpl_file, $type )
    {
        if( $type == T_BYFILE )
        {
            $serializedTPL = @file( $stpl_file ) or
                die( $this->__errorAlert('TemplatePower Error: Can\'t open [ '. $stpl_file .' ]!'));
        }
        else
        {
            $serializedTPL = $stpl_file;
        }

        $serializedStuff = unserialize( join ('', $serializedTPL) );

        $this->defBlock = $serializedStuff["defBlock"];
        $this->index    = $serializedStuff["index"];
        $this->parent   = $serializedStuff["parent"];
    }

    /**
     * TemplatePower::__makeContentRoot()
     *
     * @return
	 *
	 * @access private
     */
    function __makeContentRoot()
    {
        $this->content[ TP_ROOTBLOCK ."_0"  ][0] = Array( TP_ROOTBLOCK );
        $this->currentBlock = &$this->content[ TP_ROOTBLOCK ."_0" ][0];
    }

    /**
     * TemplatePower::__assign()
     *
     * @param $varname
     * @param $value
     * @return
	 *
	 * @access private
     */
    function __assign( $varname, $value)
    {
        if( sizeof( $regs = explode('.', $varname ) ) == 2 )  //this is faster then preg_match
        {
	          $ind_blockname = $regs[0] .'_'. $this->index[ $regs[0] ];

            $lastitem = sizeof( $this->content[ $ind_blockname ] );

            $lastitem > 1 ? $lastitem-- : $lastitem = 0;

            $block = &$this->content[ $ind_blockname ][ $lastitem ];
            $varname = $regs[1];
        }
        else
        {
            $block = &$this->currentBlock;
        }

        $block["_V:$varname"] = $value;

    }

    /**
     * TemplatePower::__assignGlobal()
     *
     * @param $varname
     * @param $value
     * @return
	 *
	 * @access private
     */
    function __assignGlobal( $varname, $value )
    {
        $this->globalvars[ $varname ] = $value;
    }


    /**
     * TemplatePower::__outputContent()
     *
     * @param $blockname
     * @return
	 *
	 * @access private
     */
    function __outputContent( $blockname )
    {
        $numrows = sizeof( $this->content[ $blockname ] );

        for( $i=0; $i < $numrows; $i++)
        {
            $defblockname = $this->content[ $blockname ][$i][0];

            for( reset( $this->defBlock[ $defblockname ]);  $k = key( $this->defBlock[ $defblockname ]);  next( $this->defBlock[ $defblockname ] ) )
            {
                if ($k[1] == 'C')
                {
                    print( $this->defBlock[ $defblockname ][$k] );
                }
                else
                if ($k[1] == 'V')
                {
                    $defValue = $this->defBlock[ $defblockname ][$k];

                    if( !isset( $this->content[ $blockname ][$i][ "_V:". $defValue ] ) )
                    {
                        if( isset( $this->globalvars[ $defValue ] ) )
                        {
                            $value = $this->globalvars[ $defValue ];
                        }
                        else
                        {
                            if( $this->showUnAssigned )
                            {
                                //$value = '{'. $this->defBlock[ $defblockname ][$k] .'}';
                                $value = '{'. $defValue .'}';
                            }
                            else
                            {
                                $value = '';
                            }
                        }
                    }
                    else
                    {
                        $value = $this->content[ $blockname ][$i][ "_V:". $defValue ];
                    }

                    print( $value );

                }
                else
                if ($k[1] == 'B')
                {
                    if( isset( $this->content[ $blockname ][$i][$k] ) )
                    {
                        $this->__outputContent( $this->content[ $blockname ][$i][$k] );
                    }
                }
            }
        }
    }

    function __printVars()
    {
        var_dump($this->defBlock);
        print("<br>--------------------<br>");
        var_dump($this->content);
    }


  /**********
      public members
            ***********/

    /**
     * TemplatePower::serializedBase()
     *
     * @return
	 *
	 * @access public
     */
    function serializedBase()
    {
        $this->serialized = true;
        $this->__deSerializeTPL( $this->tpl_base[0], $this->tpl_base[1] );
    }

    /**
     * TemplatePower::showUnAssigned()
     *
     * @param $state
     * @return
	 *
	 * @access public
     */
    function showUnAssigned( $state = true )
    {
        $this->showUnAssigned = $state;
    }

    /**
     * TemplatePower::prepare()
     *
     * @return
	 *
	 * @access public
     */
    function prepare()
    {
        if (!$this->serialized)
        {
            templateParser::__prepare();
        }

        $this->prepared = true;

        $this->index[ TP_ROOTBLOCK ]    = 0;
        $this->__makeContentRoot();
    }

    /**
     * TemplatePower::newBlock()
     *
     * @param $blockname
     * @return
	 *
	 * @access public
     */
    function newBlock( $blockname )
    {
        $parent = &$this->content[ $this->parent[$blockname] .'_'. $this->index[$this->parent[$blockname]] ];

		    $lastitem = sizeof( $parent );
        $lastitem > 1 ? $lastitem-- : $lastitem = 0;

		    $ind_blockname = $blockname .'_'. $this->index[ $blockname ];

        if ( !isset( $parent[ $lastitem ]["_B:$blockname"] ))
        {
           //ok, there is no block found in the parentblock with the name of {$blockname}

           //so, increase the index counter and create a new {$blockname} block
            $this->index[ $blockname ] += 1;

            $ind_blockname = $blockname .'_'. $this->index[ $blockname ];

            if (!isset( $this->content[ $ind_blockname ] ) )
            {
                 $this->content[ $ind_blockname ] = Array();
            }

           //tell the parent where his (possible) children are located
            $parent[ $lastitem ]["_B:$blockname"] = $ind_blockname;
        }

       //now, make a copy of the block defenition
        $blocksize = sizeof( $this->content[ $ind_blockname ] );

        $this->content[ $ind_blockname ][ $blocksize ] = Array( $blockname );

       //link the current block to the block we just created
        $this->currentBlock = &$this->content[ $ind_blockname ][ $blocksize ];
    }

    /**
     * TemplatePower::assignGlobal()
     *
     * @param $varname
     * @param $value
     * @return
	 *
	 * @access public
     */
    function assignGlobal( $varname, $value='' )
    {
        if (is_array( $varname ))
        {
            foreach($varname as $var => $value)
            {
                $this->__assignGlobal( $var, $value );
            }
        }
        else
        {
            $this->__assignGlobal( $varname, $value );
        }
    }


    /**
     * TemplatePower::assign()
     *
     * @param $varname
     * @param $value
     * @return
	 *
	 * @access public
     */
    function assign( $varname, $value='' )
    {
        if (is_array( $varname ))
        {
            foreach($varname as $var => $value)
            {
                $this->__assign( $var, $value );
            }
        }
        else
        {
            $this->__assign( $varname, $value );
        }
    }

    /**
     * TemplatePower::gotoBlock()
     *
     * @param $blockname
     * @return
	 *
	 * @access public
     */
    function gotoBlock( $blockname )
    {
        if ( isset( $this->defBlock[ $blockname ] ) )
        {
		       $ind_blockname = $blockname .'_'. $this->index[ $blockname ];

           //get lastitem indexnumber
            $lastitem = sizeof( $this->content[ $ind_blockname ] );

            $lastitem > 1 ? $lastitem-- : $lastitem = 0;

           //link the current block
            $this->currentBlock = &$this->content[ $ind_blockname ][ $lastitem ];
        }
    }

    /**
     * TemplatePower::getVarValue()
     *
     * @param $varname
     * @return
	 *
	 * @access public
     */
    function getVarValue( $varname )
    {
        if( sizeof( $regs = explode('.', $varname ) ) == 2 )  //this is faster then preg_match
        {
		        $ind_blockname = $regs[0] .'_'. $this->index[ $regs[0] ];

            $lastitem = sizeof( $this->content[ $ind_blockname ] );

            $lastitem > 1 ? $lastitem-- : $lastitem = 0;

            $block = &$this->content[ $ind_blockname ][ $lastitem ];
            $varname = $regs[1];
        }
        else
        {
            $block = &$this->currentBlock;
        }

        return $block["_V:$varname"];
    }

    /**
     * TemplatePower::printToScreen()
     *
     * @return
	 *
	 * @access public
     */
    function printToScreen()
    {
        if ($this->prepared)
        {
            $this->__outputContent( TP_ROOTBLOCK .'_0' );
        }

    }

    /**
     * TemplatePower::getOutputContent()
     *
     * @return
	 *
	 * @access public
     */
    function getOutputContent()
    {
        ob_start();

        $this->printToScreen();

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }

    public function css($href) {
        if (is_array($href)) {
            foreach($href as $var) {
                self::newBlock('html_css');
                self::assign('href', $var . '.css');
            }
        } else {
            self::newBlock('html_css');
            self::assign('href', $href . '.css');
        }

        self::root();
    }

    public function cufon($name) {
        self::javascript('cufon/cufon');

        if (is_array($name)) {
            foreach($name as $var) {
                self::javascript('cufon/' . $var);
            }
        } else {
            self::javascript('cufon/' . $name);
        }

        self::root();
    }

    public function javascript($name, $default=true) {
        $link = ($default ? __DOMAIN__ . 'js/' : __DOMAIN__);
        
        if (is_array($name)) {
            foreach($name as $var) {
                self::newBlock('html_javascript');
                self::assign('src', $link . $var);
            }
        } else {
            self::newBlock('html_javascript');
            self::assign('src', $link . $name);
        }

        self::root();
    }

    public function html($html) {
        foreach($html as $key=>$value) {
            self::assignGlobal('html_' . $key, $value);
        }
    }

    public function root() {
        self::goToBlock('_ROOT');
    }

    public function sessionFlash($type, $msg, $fade=false) {
        $_SESSION['flash'] = array($type, $msg, $fade);
        $this -> flashSession = 1;
    }

    public function setFlash($type, $msg, $fade='') {
        self::newBlock('flashMsg');
        self::assign( array('msg' => $msg, 'type' => $type) );
    }

    public function cache($time) {
        $this -> cache = true;

        $file = __DATA__ . 'application/views/tmp/' . $this -> controller . '/' . md5(__URL__) . '.tmp';
        /*if (file_exists($file)) {
            $handle = fopen($file, 'r');
            $read = fread($handle, filesize($file));
            
            echo $read . 'cache';
            exit;
        }*/

        echo $this -> controller;
    }
	
	public function plugin($name) {
		self::javascript('plugins/'. $name .'/' . $name);
		
		// De CSS wordt geïncluded
		$dir = __DATA__ . 'public/css/plugins/' . $name . '/';
		if (is_dir($dir)) {

			// De map wordt uitgelezen
			$files = scandir($dir);
		
			foreach($files as $file) {
				$ext = end(explode('.', $file));
				if ($ext == 'css') {
					$to_include = str_replace('.css', '', $file);
					self::css('plugins/'. $name .'/'. $to_include);
				}
			}
		}
	}


    public function __destruct() {
        self::assignGlobal('http', __DOMAIN__);

          if (isset($_SESSION['template'])) {
               $session = $_SESSION['template'];
               if (isset($session['plugin'])) {
                    foreach($session['plugin']['script'] as $plugin) {
                         self::javascript($plugin, false);
                    }
               }

               unset($_SESSION['template']);
          }


        // De Flash messages worden weergegeven indien aanwezig.
        if (isset($_SESSION['flash']) && !$this -> noFlash) {
            self::setFlash($_SESSION['flash'][0], $_SESSION['flash'][1], $_SESSION['flash'][2]);
            unset($_SESSION['flash']);
        }
		
        if (!$this -> cache && $this -> render) {
            self::printToScreen();
        }

        /*if ($this -> cache) {
            if (!is_dir(__DATA__ . 'application/views/tmp/' . $this -> controller)) {
                mkdir(__DATA__ . 'application/views/tmp/' . $this -> controller);
            }

            $file = __DATA__ . 'application/views/tmp/' . $this -> controller . '/' . md5(__URL__) . '.tmp';
            if (file_exists($file)) {
            //unlink($file);
            } else {
                $handle = fopen($file, 'w');
                fwrite($handle, self::getOutputContent());
            }
        }*/
		
		
		// Er wordt gekeken of de Queries moeten worden weergegeven
		if (templateParser::echo_queries()) {
			$queries = $this -> model -> executed_queries();

			echo '<div style="width: 100%; margin: 10px 0 0 0; background: #ffffff">';
			echo '<table style="font: 12px arial">';
			echo '<tr height="30">
					  <td width="50%"><b>Query</b></td>
					  <td width="20%"><b>Error</b></td>
					  <td width="10%"><b>Affected</b></td>
					  <td width="10%"><b>Rows</b></td>
					  <td width="10%"><b>Time</b></td>
				  </tr>';
			$i = 0;
			foreach($queries as $query) {
				++$i;
				
				$style =(1&$i ? 'style="background: #bce5ff;"' : '');
				echo '<tr height="40" '. $style .'><td>'. $query['query'] .'</td>
									  <td>'. ($query['error'] ? $query['error'] : 'None') .'</td>
									  <td>'. $query['affected'] .'</td>
									  <td>'. $query['rows'] .'</td>
									  <td>'. $query['took'] .'</tr>';
			}
			echo '</table>';
			echo '</div>';
		}

    }
	
	public function setModel($model) {
		$this -> model = $model;
	}
	
	public function noRender() {
		$this -> render = false;
	}

    public function alert($message) {
        self::newBlock('alert');
        self::assign('message', $message);
    }

}
?>