<?php
/**
** massResCreator module for MODX Evo
** file for inlude
** @Author:   alooze(a.looze@gmail.com)
** @Version:  0.1a
**/

$mId = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '';
if ($mId == '') return;

$mess = 'Модуль запущен';

$mBaseUrl = $modx->config['site_url'].'manager/index.php?a=112&id='.$mId;
include_once MODX_BASE_PATH.'assets/modules/massResCreator/classes/document.class.inc.php';

/*echo '<pre>';
print_r($_REQUEST);
echo '</pre>';*/

if (isset($_REQUEST['prnt'])) {
  $pr = intval($_REQUEST['prnt']);
} else {
  $pr = 0;
}

$text = '';
if (isset($_POST['go'])) {
  //данные из формы
  $text = $_REQUEST['text'];
  $tid = intval($_REQUEST['tid']);
  if (trim($text) == '') {
    $mess = 'Нет данных для записи';
  } else if ($tid == 0) {
    $mess = 'Нет данных о шаблоне';
  } else {
    $mess = 'Добавление данных...';
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
      //$doc->Save();
      
      $doc = new Document($pr);
      $doc->Set('isfolder', 1);
      $doc->Save();
    }
    $mess.= "<br>\n Данные сохранены";
    $text = '';
  }
  //$do = $modx->sendRedirect($mBaseUrl.'&prnt='.$pr);
}

$dAr = $modx->getDocument($pr);
if (!is_array($dAr)) {
  $dAr = $modx->getDocument($pr, '*', 0);
}

$pt = $dAr['pagetitle'];

if ($dAr['parent'] >= 0) {
  $list = '<li><a href="'.$mBaseUrl.'&prnt='.$dAr['parent'].'"> <b>..(Выше)</b> </a></li>'."\n<br>";
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
<a href="<?=$mBaseUrl?>&prnt=0"> Начало </a>
<br/>
<ul>
<?=$list;?>
</ul>

Целевой документ: <b><?=$pt?></b> (<?=$pr?>)<br/>
Создать дочерние документы:
<form action="<?=$mBaseUrl?>&prnt=<?=$pr?>" method="post">
<textarea cols="50" rows="30" name="text"><?=$text?></textarea>
<br/><br/>
ID шаблона: <input name="tid"><br><br>
<input type="submit" name="go" value="go">
</form>
</div>
</body>
</html>