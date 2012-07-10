<?php
/**
** massResCreator module for MODX Evo
** file for inlude
** @Author:   alooze(a.looze@gmail.com)
** @Version:  0.1a
** @Lang: ru
** @Install: 1) copy project files to 'assets/modules/massResCreator/' floder
** 2) create new module 
** 3) paste in code area "include_once 'assets/modules/massResCreator/massResCreator.module.php'
** 4) save and use;
** 5) to extend functionality write additional $doc->Set() instructions (line 48 +)
**/

$mId = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '';
if ($mId == '') return;

$mess = 'Модуль запущен'; //Module started

$mBaseUrl = $modx->config['site_url'].'manager/index.php?a=112&id='.$mId;
include_once MODX_BASE_PATH.'assets/modules/massResCreator/classes/document.class.inc.php';


if (isset($_REQUEST['prnt'])) {
  $pr = intval($_REQUEST['prnt']);
} else {
  $pr = 0;
}

$text = '';
if (isset($_POST['go'])) {
  //form data
  $text = $_REQUEST['text'];
  $tid = intval($_REQUEST['tid']);
  if (trim($text) == '') {
    $mess = 'Нет данных для записи'; // No data for write
  } else if ($tid == 0) {
    $mess = 'Нет данных о шаблоне'; // No template data
  } else {
    $mess = 'Добавление данных...'; // Adding data
    $ar = explode("\n", $text);
    foreach ($ar as $title) {
      $title = trim($title);
      if ($title == '') continue;
      $mess.= "<br>\n". $title;
      
      $doc = new Document();
      $doc->Set('pagetitle', $title);
      $doc->Set('template', $tid);
      $doc->Set('parent', $pr);
      $doc->Set('hidemenu', 0);
      $doc->Set('published', '1');
      $doc->Save();
      $doc->SetAlias();
      
      $doc = new Document($pr);
      $doc->Set('isfolder', 1);
      $doc->Save();
    }
    $mess.= "<br>\n Данные сохранены"; //Data saved
    $text = '';
  }
}

$dAr = $modx->getDocument($pr);
if (!is_array($dAr)) {
  $dAr = $modx->getDocument($pr, '*', 0);
}

$pt = $dAr['pagetitle'];

if ($dAr['parent'] >= 0) {
  $list = '<li><a href="'.$mBaseUrl.'&prnt='.$dAr['parent'].'"> <b>..(Выше)</b> </a></li>'."\n<br>"; //Up
} else {
  $list = '<li> </li>'."\n";
}

$idAr = $modx->getAllChildren($pr);
foreach ($idAr as $diAr) {
  $list.= '<li><a href="'.$mBaseUrl.'&prnt='.$diAr['id'].'"> '.$diAr['pagetitle'].' </a></li>'."\n";
}

?>

<!DOCTYPE html>  
<html> 
<head>
<title>massResCreator module</title>
</head>
<body>
<?=$mess?><br><br>
<div class="wrapper">
<a href="<?=$mBaseUrl?>&prnt=0"> Начало </a> <!-- Start -->
<br/>
<ul>
<?=$list;?>
</ul>

Целевой документ: <b><?=$pt?></b> (<?=$pr?>)<br/><!-- target resource -->
Создать дочерние документы: <!-- Create child resources -->
<form action="<?=$mBaseUrl?>&prnt=<?=$pr?>" method="post">
<textarea cols="50" rows="30" name="text"><?=$text?></textarea>
<br/><br/>
ID шаблона: <input name="tid"><br><br>
<input type="submit" name="go" value="go">
</form>
</div>
</body>
</html>