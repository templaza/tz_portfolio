<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die();

use Joomla\CMS\Router\Route;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper;

$params = &$this -> params;
?>
<ul class="uk-subnav uk-subnav-pill">
<?php if($letters = $params -> get('tz_letters','a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z')):
    $letters        = explode(',',$letters);
    $availLetter    = $this -> availLetter;
?>
  <?php foreach($letters as $i => &$letter):?>
    <?php
        $letter         = trim($letter);
        $linkClass      = null;
        $activeClass    = null;
        if($availLetter[$i] != true){
            $linkClass  = ' uk-disabled';
        }else{
            $linkClass  = ' uk-link-toggle';
        }
        if($this -> char == $letter){
            $activeClass    = ' class="uk-active"';
        }

        $url    = '#';
        if($availLetter[$i] != false && $this -> char != $letter ){
            $url    = Route::_(RouteHelper::getLetterRoute('portfolio',$letter));
        }
    ?>
        <li<?php echo $activeClass;?>>
            <a href="<?php echo $url; ?>" class="<?php echo $linkClass;?>">
                <?php echo mb_strtoupper(trim($letter));?>
            </a>
        </li>
  <?php endforeach;?>
<?php endif;?>
</ul>