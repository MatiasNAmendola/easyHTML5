<?php
/**
 * basic class
 *
 * @package html
 */
class html {

    /**
     * the singleton instance
     * @var object $instance
     * @access private
     * @access static
     */
    private static $instance = NULL;
    /**
     * instance of the html_head class
     *
     * @var object $head
     * @access public
     * @see html_head
     */
    public $head        = null;
    /**
     * instance of the html_body class
     *
     * @var object $body
     * @access public
     * @see html_body
     */
    public $body        = null;
    /**
     * instance of the html_foot class
     *
     * @var object $foot
     * @access public
     * @see html_foot
     */
    public $foot        = null;
    /**
     * content to return/display
     *
     * @var string $content
     * @access private
     */
    private $content     = '';

    /**
     * privatized constructor to force the usage of the init-singleton
     *
     * @access private
     * @see init()
     */
    private function __construct() {}
    /**
     * privatized magic-function to prevent external cloning of object
     *
     * @access private
     */
    private function __clone() {}
    /**
     * singleton initalizer
     *
     * @access public
     * @access static
     * @return self::$instance
     */
    public static function init() {
        if (self::$instance === NULL) {
           self::$instance = new self;
       }
       self::$instance->head = html_head::init();
       self::$instance->body = html_body::init();
       self::$instance->foot = html_foot::init();
       return self::$instance;
    }
    /**
     * build the html output
     *
     * @access public
     * @return string $content built content
     */
    public function build() {
        self::$instance->content.= self::$instance->head->build();
        self::$instance->content.= self::$instance->body->build();
        self::$instance->content.= self::$instance->foot->build();
        return self::$instance->content;
    }

}


/**
 * #########################################################################################################################
 * head
 * #########################################################################################################################
 */
/**
 * head class
 *
 * @package html
 * @subpackage html_head
 */
class html_head {

    private static $instance;

    public $css     = null;
    public $doctype = null;
    public $js      = null;
    public $meta    = null;
    public $title   = null;

    public $content = '';
    public $built   = false;

    /**
     * privatized constructor to force the usage of the init-singleton
     *
     * @access private
     * @see init()
     */
    private function __construct() {}
    /**
     * privatized magic-function to prevent external cloning of object
     *
     * @access private
     */
    private function __clone() {}
    /**
     * singleton initalizer
     *
     * @access public
     * @access static
     * @return self::$instance
     */
    public static function init() {
        if (self::$instance === NULL) {
           self::$instance = new self;
       }
       self::$instance->css     = new html_head_css();
       self::$instance->doctype = new html_head_doctype();
       self::$instance->js      = new html_head_js();
       self::$instance->meta    = new html_head_meta();
       self::$instance->title   = new html_head_title();
       return self::$instance;
    }
	/**
	 * build the content from the initialized subobjects
	 *
	 * @return string $content built content
	 */
    public function build() {

		$this->content = $this->doctype->build();

		$myHTML = new html_tag('html');
		$myHTML->noid = true;
		$myHTML->onlyopen = true;
		$myHTML->addParam('lang', easyHTML5_settings::get('system_language'));

		$myHead = new html_tag('head');
		$myHead->noid = true;
		if (!$this->title->built) {
			$myHead->addContent($this->title->build());
		}
		if (!$this->meta->built) {
			$myHead->addContent($this->meta->build());
		}
		if (!$this->css->built) {
			$myHead->addContent($this->css->build());
		}
		if (!$this->js->built) {
			$myHead->addContent($this->js->build());
		}

		$myHTML->addContent($myHead->build());

		$this->content.=$myHTML->build();

		unset($myHTML,$myHead);

		return $this->content;

    }

}
/**
 * css class
 *
 * @package html
 * @subpackage html_head
 */
class html_head_css {

    public $content = '';
    public $built   = false;

    private $css    = array();

    public function __construct() {

        return $this;
    }

    public function build() {
        if (!array_key_exists('system.css',$this->css)) {
            $this->add('system.css');
        }
        foreach ($this->css as $cssLine) {
            $this->content .= $cssLine;
        }
        return $this->content;
    }

    public function add($strCSSFile, $strMedia='screen') {
        if (substr($strCSSFile,-4)!='.css') $strCSSFile = $strCSSFile.'.css';
        if (file_exists(easyHTML5_settings::get('path_css').$strCSSFile)) {
			$myTag = new html_tag('link');
			$myTag->selfclose = true;
			$myTag->noid = true;
			$myTag->addParam('rel', 'stylesheet');
			$myTag->addParam('type', 'text/css');
			$myTag->addParam('href', easyHTML5_settings::get('web_css').$strCSSFile);
			$myTag->addParam('media', $strMedia);
            $this->css[$strCSSFile] = $myTag->build();
			unset($myTag);
        }
    }

}

/**
 * doctype class
 *
 * @package html
 * @subpackage html_head
 */
class html_head_doctype {

    public $content = '';
    public $built   = false;

    public function __construct() {
        return $this;
    }

    public function build() {
        $this->content  = '<!DOCTYPE html>'.CRLF;
        $this->built    = true;
        return $this->content;
    }


}
/**
 * js class
 *
 * @package html
 * @subpackage html_head
 */
class html_head_js {

    public $content = '';
    public $built   = false;

    private $js    = array();

    public function __construct() {

        return $this;
    }

    public function build() {
        if (!array_key_exists('kernel.js',$this->js)) {
            $this->add('kernel.js');
        }
        if (!array_key_exists('system.js',$this->js)) {
            $this->add('system.js');
        }
        foreach ($this->js as $jsLine) {
            $this->content .= $jsLine;
        }
        return $this->content;
    }

    public function add($strJSFile) {
        if (substr($strJSFile,-3)!='.js') $strJSFile = $strJSFile.'.js';
        if (file_exists(easyHTML5_settings::get('path_js').$strJSFile)) {
			$myTag = new html_tag('script');
			$myTag->noid = true;
			$myTag->addParam('type'	, 'text/javascript');
			$myTag->addParam('src'	, easyHTML5_settings::get('web_js').$strJSFile);
            $this->js[$strJSFile] = $myTag->build();
			unset($myTag);
        }
    }
}
/**
 * meta class
 *
 * @package html
 * @subpackage html_head
 */
class html_head_meta {

    public $content = '';
    public $built   = false;

    private $meta    = array();

    public function __construct() {
        return $this;
    }

    public function build() {
        foreach ($this->meta as $metaLine) {
            $this->content .= $metaLine;
        }
        return $this->content;
    }

    public function add($strName, $strValue) {
		$myTag = new html_tag('meta');
		$myTag->selfclose = true;
		$myTag->noid = true;
        switch ($strName) {
			case 'charset':
				$myTag->addParam('charset', $strValue);
				break;
            case 'content-type':
            case 'content-language':
            case 'expires':
            case 'generator':
            case 'set-cookie':
            case 'cache-control':
            case 'pragma':
            	$myTag->addParam('http-equiv', $strName);
				$myTag->addParam('content', $strValue);
                break;
            case 'author':
            case 'date':
            case 'description':
            case 'keywords':
            case 'robots':
            default:
				$myTag->addParam('name', $strName);
				$myTag->addParam('content', $strValue);
                break;
        }
		$this->meta[$strName] = $myTag->build();
		unset($myTag);
    }
}
/**
 * title class
 *
 * @package html
 * @subpackage html_head
 */
class html_head_title {

    public $content = '';
    public $built   = false;

    private $title    = '';

    public function __construct() {
        return $this;
    }

    public function build() {
        if ($this->title == '') {
            $this->title = easyHTML5_settings::get('system_title'). ' | Version ' . easyHTML5_settings::get('system_version');
        }
		$myTag = new html_tag('title');
		$myTag->addContent($this->title);
        $this->content = $myTag->build();
		unset($myTag);
		$this->built = true;
        return $this->content;
    }

    public function add($strValue) {
        $this->title.=$strValue;
    }
}

/**
 * #########################################################################################################################
 * body
 * #########################################################################################################################
 */
/**
 * body class
 *
 * @package html
 * @subpackage html_body
 */
class html_body {

    /**
     * the singleton instance
     * @var object $instance
     * @access private
     * @access static
     */
    private static $instance = NULL;


    public $content     = '';
    public $built       = false;

    public $elements    = array();

    /**
     * privatized constructor to force the usage of the init-singleton
     *
     * @access private
     * @see init()
     */
    private function __construct() {}
    /**
     * privatized magic-function to prevent external cloning of object
     *
     * @access private
     */
    private function __clone() {}
    /**
     * singleton initalizer
     *
     * @access public
     * @access static
     * @return self::$instance
     */
    public static function init() {
        if (self::$instance === NULL) {
           self::$instance = new self;
       }
       return self::$instance;
    }

    public function build() {

        $this->content = TAB.'<body>'.CRLF;
        $this->content = TAB.'<div id="doc">'.CRLF;

        foreach ($this->elements as $element) {
            if (!$element->built) {
                $this->content.= $element->build();
            }
        }

        $this->content.= TAB.'</div>'.CRLF;
        $this->content.= TAB.'</body>'.CRLF;

        return $this->content;
    }

    public function add($strType, $strTitle) {
        $this->strTitle = null;
        $myObject = 'html_body_'.strtolower($strType);
        // if elements need special features, there is a class for them
        if (class_exists($myObject,false)) {
            $this->$strTitle = new $myObject($strTitle);
        // else we got the default handler
        } else {
            $this->$strTitle = new html_body_element($strTitle,$strType);

        }
        if ($this->$strTitle) {
            $this->elements[$strTitle]  = $this->$strTitle;
        }
    }
}

/**
 * element class
 *
 * @package html
 * @subpackage html_body
 */

class html_body_element {

    public $content = '';
    public $built   = false;
    public $id      = '';
    public $type    = '';

    private $params = array();

    public function __construct($strID,$strType) {
        $this->id   = $strID;
        $this->type = $strType;
        return $this;
    }

    public function __set($name, $value) {
        $this->params[$name] = $value;
    }

    public function build() {
        $myTag = new html_tag($this->type);
        $myTag->id = $this->id;
        foreach ($this->params as $key=>$val) {
            $myTag->addParam($key, $val);
        }
        $myTag->addContent($this->content);
        $this->content = $myTag->build();
        unset($myTag);
        $this->built = true;
        return $this->content;
    }

    public function addContent($strContent) {
        $this->content.=$strContent;
    }

}
/**
 * article class
 *
 * @package html
 * @subpackage html_body
 */

class html_body_article {

    public $content     = '';
    public $built       = false;
    public $id          = '';
    public $title       = null;
    public $time        = null;
    public $author      = null;

    public function __construct($strID) {
        $this->id = $strID;
        return $this;
    }

    public function build() {
        $retVal = TAB.TAB.'<article id="'.$this->id.'">';
        if ($this->author && $this->time && $this->title) {
            $retVal.=TAB.TAB.TAB.'<header>'.CRLF;
            $retVal.=TAB.TAB.TAB.TAB.'<h2>'.$this->title.'</h2>'.CRLF;
            $retVal.=TAB.TAB.TAB.TAB.'<p>geschrieben am <time datetime="'.date('c',strtotime($this->time)).'">'.date('d.m.Y H:i:s',strtotime($this->time)).'</time> von '.$this->author.'</p>'.CRLF;
            $retVal.=TAB.TAB.TAB.'</header>'.CRLF;
        }
        $retVal.= TAB.TAB.TAB.'<p>'.$this->content.'</p>';
        $retVal.= TAB.TAB.'</article>'.CRLF;
        $this->built = true;
        return $retVal;
    }

    public function addContent($strContent) {
        $this->content.= $strContent;
    }

}
/**
 * nav class
 *
 * @package html
 * @subpackage html_body
 */

class html_body_nav {

    public $content = '';
    public $built   = false;
    public $id      = '';

    private $items  = array();

    public function __construct($strID) {
        $this->id = $strID;
        return $this;
    }

    public function build() {
        $myNav = new html_tag('nav');
        $myNav->id = $this->id;

        $myUL = new html_tag('ul');
        foreach ($this->items as $item) {
            $myUL->addContent($item);
        }
        $myNav->addContent($myUL->build());
        $this->content = $myNav->build();
        unset($myUL, $myNav);
        $this->built = true;
        return $this->content;
    }

    public function addItem($strID,$strTitle,$strLink='') {
        if ($strLink!='') {
            $myLink = new html_tag('a');
            $myLink->id = 'link_'.$strTitle;
            $myLink->addParam('href',$strLink);
            $myLink->addContent($strTitle);
            $strTitle = $myLink->build();
            unset($myLink);
        }
        $myItem = new html_tag('li');
        $myItem->id = $strID;
        $myItem->addContent($strTitle);
        $this->items[$strID] = $myItem->build();
        unset($myItem);
    }

}

//***************************************************************************************************************************
// foot
//***************************************************************************************************************************

/**
 * foot class
 *
 * @package html
 * @subpackage html_foot
 */

class html_foot {

    private static $instance = NULL;

    public $content = '';
    public $built   = false;

   /**
     * privatized constructor to force the usage of the init-singleton
     *
     * @access private
     * @see init()
     */
    private function __construct() {}
    /**
     * privatized magic-function to prevent external cloning of object
     *
     * @access private
     */
    private function __clone() {}
    /**
     * singleton initalizer
     *
     * @access public
     * @access static
     * @return self::$instance
     */
    public static function init() {
        if (self::$instance === NULL) {
           self::$instance = new self;
       }
       return self::$instance;
    }

    public function build() {
        $myTag = new html_tag('html');
	$myTag->onlyclose = true;
        $this->content.= $myTag->build().CRLF;
        unset($myTag);
        if (DEBUG) {
            $this->content.= '<!-- Build time: '.(time()-$_SESSION['timer']).'s -->';
        }
        return $this->content;
    }

}

//***************************************************************************************************************************
// Base Tag-Class
//***************************************************************************************************************************
/**
 * html_tag
 *
 * base class to build html-tags
 * @package html
 * @subpackage html_tag
 */
class html_tag {

	/**
     * $id
     * @var string $id the id of the current tag
     * @access public
     */
    public $id			= '';
    /**
     * $class
     * @var string $class the css-class of the current tag
     * @access public
     */
	public $class		= '';
    /**
     * $selfclose
     * @var boolean $selfclose indicates whether the tag should be closed in opener, e.g. <br />
     * @access public
     */
	public $selfclose	= false;
    /**
     * $onlyclose
     * @var boolean $onlyclose only generates closing tag, e.g. </tag>
     * @access public
     */
	public $onlyclose	= false;
    public $onlyopen    = false;
	public $noid		= false;
    /**
     * $type
     * @var string $type type of tag, tag name, e.g. head, html, p, ...
     * @access private
     */
	private $_type		= '';
    /**
     * $params
     * @var array $params, additional params in the tag, e.g. style,...
     * @access private
     */
	private $_params		= array();
    /**
     * $content
     * @var string $content this is the content within the tag
     * @access private
     */
	private $_content	= '';

    /**
     * __construct
     *
     * class constructor
     *
     * @param string $strType type of tag, e.g. head, html, ...
     * @return html_tag
     * @access public
     */
    public function __construct($strType) {
        $this->_type = $strType;
        return $this;
    }

    /**
     * addParam
     *
     * add several parameters to the tag
     *
     * @param string $strName the name of the parameter
     * @param string $strContent the value of the parameter. if false only name is used as param, e.g. in video-sources for "autoplay"...
     * @return void
     */
    public function addParam($strName, $strContent=false) {
	if ($strName=='class') {
            ($this->class!='') ? $this->class.=' '.$strContent : $this->class = $strContent;
	} else {
            $this->_params[$strName] = $strContent;
	}
    }

    /**
     * addContent
     *
     * guess what!?
     *
     * @param string $strContent content in the tag
     * @return void
     */
    public function addContent($strContent) {
	$this->_content.=strval($strContent);
    }

    /**
     * build
     *
     * build up the tag, incl. content
     *
     * @return string the built tag plus content
     */
    public function build() {
        $retVal = '';
        if ($this->onlyclose) {
            return CRLF.'</'.$this->_type.'>';
        }
		$retVal.=CRLF.'<'.$this->_type;
		if (!$this->noid) {
			$retVal.=' id="'.(($this->id!='') ? $this->id : $this->_type.'_'.md5(time())).'"';
		}
		if ($this->class!='') {
            $retVal.=' class="'.$this->class.'"';
        }
		foreach ($this->_params as $key=>$val) {
           if ($val) {
               $retVal.=' '.$key.'="'.$val.'"';
           } else {
               $retVal.=' '.$key;
           }
		}
		if ($this->selfclose) {
            $retVal.=' />';
            return $retVal;
        }
        $retVal.='>'.$this->_content;
        if (!$this->onlyopen) {
            $retVal.='</'.$this->_type.'>';
        }
        return $retVal;
    }

}



