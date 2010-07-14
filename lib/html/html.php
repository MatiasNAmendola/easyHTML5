<?php
/**
 * 
 *
 * @author Jens
 */
class html {

    public $head        = null;
    public $body        = null;
    public $foot        = null;

    public $content     = '';

    public function __construct() {
        $this->head = new html_head();
        $this->body = new html_body();
        $this->foot = new html_foot();
        return $this;
    }

    public function build() {
        $this->content.= $this->head->build();
        $this->content.= $this->body->build();
        $this->content.= $this->foot->build();
        echo $this->content;
    }

}


/**
 * #########################################################################################################################
 * head
 * #########################################################################################################################
 */
class html_head {

    public $css     = null;
    public $doctype = null;
    public $js      = null;
    public $meta    = null;
    public $title   = null;
    
    public $content = '';
    public $built   = false;

    public function __construct() {
        $this->css      = new html_head_css();
        $this->doctype  = new html_head_doctype();
        $this->js       = new html_head_js();
        $this->meta     = new html_head_meta();
        $this->title    = new html_head_title();

        return $this;
    }

    public function build() {
        $this->content = '';
        $this->content .= $this->doctype->build();
        $this->content .= '<html lang="de">'.CRLF;
        $this->content .= TAB.'<head>'.CRLF;
        $this->content .= $this->title->build();
        $this->content .= $this->meta->build();
        $this->content .= $this->css->build();
        $this->content .= $this->js->build();
        $this->content .= TAB.'</head>'.CRLF;
        return $this->content;

    }

}

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
            $this->content .= TAB.TAB.$cssLine.CRLF;
        }
        return $this->content;
    }

    public function add($strCSSFile, $strMedia='screen') {
        if (substr($strCSSFile,-4)!='.css') $strCSSFile = $strCSSFile.'.css';
        if (file_exists(settings::get('path_css').$strCSSFile)) {
            $this->css[$strCSSFile] = '<link rel="stylesheet" type="text/css" href="'.settings::get('web_css').$strCSSFile.'" media="'.$strMedia.'" />';
        }
    }

}

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
            $this->content .= TAB.TAB.$jsLine.CRLF;
        }
        return $this->content;
    }

    public function add($strJSFile) {
        if (substr($strJSFile,-3)!='.js') $strJSFile = $strJSFile.'.js';
        if (file_exists(settings::get('path_js').$strJSFile)) {
            $this->js[$strJSFile] = '<script type="text/javascript" src="'.settings::get('web_css').$strJSFile.'"></script>';
        }
    }
}

class html_head_meta {

    public $content = '';
    public $built   = false;

    private $meta    = array();

    public function __construct() {
        return $this;
    }

    public function build() {
        foreach ($this->meta as $metaLine) {
            $this->content .= TAB.TAB.$metaLine.CRLF;
        }
        return $this->content;
    }

    public function add($strName, $strValue) {
        switch ($strName) {
			case 'charset':
				$this->meta[$strName] = '<meta charset="'.$strValue.'" />';
				break;
            case 'content-type':
            case 'content-language':
            case 'expires':
            case 'generator':
            case 'set-cookie':
            case 'cache-control':
            case 'pragma':
            	$this->meta[$strName] = '<meta http-equiv="'.$strName.'" content="'.$strValue.'" />';
                break;
            case 'author':
            case 'date':
            case 'description':
            case 'keywords':
            case 'robots':
            default:
            	$this->meta[$strName] = '<meta name="'.$strName.'" content="'.$strValue.'" />';
                break;
        }
    }
}

class html_head_title {

    public $content = '';
    public $built   = false;

    private $title    = '';

    public function __construct() {
        return $this;
    }

    public function build() {
        if ($this->title == '') {
            $this->title = settings::get('system_title'). ' | Version ' . settings::get('system_version');
        }
        $this->content = TAB.TAB.'<title>'.$this->title.'</title>'.CRLF;
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
class html_body {

    public $content     = '';
    public $built       = false;

    public $elements    = array();

    public function __construct() {
        return $this;
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


class html_body_element {


    public $content = '';
    public $built   = false;
    public $id      = '';
    public $type    = '';

    public function __construct($strID,$strType) {
        $this->id   = $strID;
        $this->type = $strType;
        return $this;
    }

    public function build() {
        $this->content = TAB.TAB.'<'.$this->type.' id="'.$this->id.'">'.CRLF.$this->content;
        $this->content.= TAB.TAB.'</'.$this->type.'>'.CRLF;
        $this->built = true;
        return $this->content;
    }

    public function addContent($strContent) {
        $this->content.=$strContent;
    }

}

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
        $this->content = TAB.TAB.'<nav id="'.$this->id.'">'.CRLF;
        $this->content.= TAB.TAB.TAB.'<ul>'.CRLF;
        foreach ($this->items as $item) {
            $this->content.= TAB.TAB.TAB.TAB.$item.CRLF;
        }
        $this->content.= TAB.TAB.TAB.'</ul>'.CRLF;
        $this->content.= TAB.TAB.'</nav>'.CRLF;
        $this->built = true;
        return $this->content;
    }

    public function addItem($strID,$strTitle,$strLink='') {
        if ($strLink!='') {
            $strTitle = '<a href="'.$strLink.'">'.$strTitle.'</a>';
        }
        $this->items[$strID] = '<li id="'.$strID.'">'.$strTitle.'</li>';
    }

}

/**
 * #########################################################################################################################
 * foot
 * #########################################################################################################################
 */
class html_foot {

    public $content = '';
    public $built   = false;

    public function __construct() {

        return $this;

    }

    public function build() {
		$myTag = new html_tag('html');
		$
        $this->content.= '</html>'.CRLF;
        if (DEBUG) {
            $this->content.= '<!-- Build time: '.(time()-$_SESSION['timer']).'s -->';
        }
        return $this->content;
    }

}


class html_tag {

	public $built		= false;
	public $id			= '';
	public $class		= '';
	public $selfclose	= false;
	public $onlyclose	= false;

	private $type		= '';

	private $params		= array();
	private $content	= '';

	public function __construct($strType) {
		$this->type = $strType;
		return $this;
	}

	public function addParam($strName, $strContent) {
		if ($strName=='class') {
			($this->class!='') ? $this->class.=' '.$strContent : $this->class = $strContent;
		} else {
			$this->params[$strName] = $strContent;
		}
	}

	public function addContent($strContent) {
		$this->content.=$strContent;
	}

	public function build() {
		$retVal = '';
		if ($this->onlyclose) {
			return '</'.$this->type.'>';
		}
		$retVal.='<'.$this->type.' id="'.(($this->id!='') ? $this->id : $this->type.'_'.md5(time())).'"';

		if ($this->class!='')
			$retVal.=' class="'.$this->class.'"';

		foreach ($this->params as $key=>$val) {
			$retVal.=' '.$key.'="'.$val.'"';
		}

		if ($this->selfclose) {
			$retVal.=' />';
			return $retVal;
		}

		$retVal.='>'.$this->content.'</'.$this->type.'>';
		return $retVal;
	}

}
?>
