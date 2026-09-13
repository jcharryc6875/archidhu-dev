<?php

/**
 * @author 
 * @copyright 2008
 */

 
function pager_navigation($pager, $uri, $parametros)
{
  $navigation = '';
  $navigation .= '<ul class="pagination pagination-sm">'; 
   
  if ($pager->haveToPaginate()){
    $uri .= (preg_match('/\?/', $uri) ? '&' : '?').'page=';

    // First and previous page
    if ($pager->getPage() != 1){
      $navigation .= '<li>'.link_to('<i class="entypo-left-circled tooltip-primary" data-original-title="Primera pagina" data-toggle= "tooltip"></i>', $uri.$pager->getFirstPage().$parametros).'</li>';
      $navigation .= '<li>'.link_to('<i class="entypo-left-open tooltip-primary" data-original-title="Pagina anterior" data-toggle= "tooltip"></i>', $uri.$pager->getPreviousPage().$parametros).'</li>';      
    }
 
    // Pages one by one
    $links = array();
    foreach ($pager->getLinks(10) as $page){
        if($page == $pager->getPage()):
          $links[] = "<li class='active'>".link_to_unless($page == $pager->getPage(), $page, $uri.$page.$parametros)."</li>";
        else:
          $links[] = "<li>".link_to_unless($page == $pager->getPage(), $page, $uri.$page.$parametros)."</li>";
        endif;
    }
    $navigation .= join('  ', $links);
 
    // Next and last page
    if ($pager->getPage() != $pager->getLastPage()){
      $navigation .= '<li>'.link_to('<i class="entypo-right-open tooltip-primary" data-original-title="Siguiente pagina" data-toggle= "tooltip"></i>', $uri.$pager->getNextPage().$parametros).'</li>';
      $navigation .= '<li>'.link_to('<i class="entypo-right-circled tooltip-primary" data-original-title="Ultima pagina" data-toggle= "tooltip"></i>', $uri.$pager->getLastPage().$parametros).'</li>';
    }
 
  }
  $navigation .= '</ul>';
 
  return $navigation;
}

function pager_navigation_async($pager, $uri, $parametros, $elupdate = 'table-paginate')
{
  $navigation = '';
  $navigation .= '<ul class="pagination pagination-sm">'; 
   
  if ($pager->haveToPaginate()){
    $uri .= (preg_match('/\?/', $uri) ? '&' : '?').'page=';

    // First and previous page
    if ($pager->getPage() != 1){
      $navigation .= '<li>'.jq_link_to_remote('<i class="entypo-left-circled tooltip-primary" data-original-title="Primera pagina" data-toggle= "tooltip"></i>', array(
        'update'    => $elupdate,
        'url'     => $uri.$pager->getFirstPage().$parametros,
        'loading' => "javascript:jQuery.LoadingStructData();",
        'complete' => 'javascript:jQuery.CloseLoadingStructData()',
      ),array('class'=>'btn btn-white btn-sm tooltip-primary','data-toggle'=>'tooltip', 'data-original-title'=>'Primera pagina')).'</li>';

      $navigation .= '<li>'.jq_link_to_remote('<i class="entypo-left-open tooltip-primary" data-original-title="Pagina anterior" data-toggle= "tooltip"></i>', array(
        'update'    => $elupdate,
        'url'     => $uri.$pager->getPreviousPage().$parametros,
        'loading' => "javascript:jQuery.LoadingStructData();",
        'complete' => 'javascript:jQuery.CloseLoadingStructData()',
      ),array('class'=>'btn btn-white btn-sm tooltip-primary','data-toggle'=>'tooltip', 'data-original-title'=>'Pagina anterior')).'</li>';

    }
 
    // Pages one by one
    $links = array();
    foreach ($pager->getLinks(10) as $page){
        if($page == $pager->getPage()):
          $links[] = '<li class="active" tooltip-primary" data-original-title="Pagina "'.$page.' data-toggle= "tooltip">'.link_to_unless($page == $pager->getPage(), $page, $uri.$page.$parametros)."</li>";
        else:
          $links[] = '<li>'.jq_link_to_remote($page, array(
            'update'    => $elupdate,
            'url'     => $uri.$page.$parametros,
            'loading' => "javascript:jQuery.LoadingStructData();",
            'complete' => 'javascript:jQuery.CloseLoadingStructData()',
          ),array('class'=>'btn btn-white btn-sm tooltip-primary','data-toggle'=>'tooltip', 'data-original-title'=>'Pagina '.$page)).'</li>';
        endif;
    }
    $navigation .= join('  ', $links);
 
    // Next and last page
    if ($pager->getPage() != $pager->getLastPage()){
      $navigation .= '<li>'.jq_link_to_remote('<i class="entypo-right-open tooltip-primary" data-original-title="Siguiente pagina" data-toggle= "tooltip"></i>', array(
        'update'    => $elupdate,
        'url'     => $uri.$pager->getNextPage().$parametros,
        'loading' => "javascript:jQuery.LoadingStructData();",
        'complete' => 'javascript:jQuery.CloseLoadingStructData()',
      ),array('class'=>'btn btn-white btn-sm tooltip-primary','data-toggle'=>'tooltip', 'data-original-title'=>'Siguiente pagina')).'</li>';
      
      $navigation .= '<li>'.jq_link_to_remote('<i class="entypo-right-circled tooltip-primary" data-original-title="Ultima pagina" data-toggle= "tooltip"></i>', array(
        'update'    => $elupdate,
        'url'     => $uri.$pager->getLastPage().$parametros,
        'loading' => "javascript:jQuery.LoadingStructData();",
        'complete' => 'javascript:jQuery.CloseLoadingStructData()',
      ),array('class'=>'btn btn-white btn-sm tooltip-primary','data-toggle'=>'tooltip', 'data-original-title'=>'Ultima pagina')).'</li>';      
    }
 
  }
  $navigation .= '</ul>';
 
  return $navigation;
}
?>